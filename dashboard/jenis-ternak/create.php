<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$errors = [];
$successMessage = '';
$formData = ['nama_jenis' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string) ($_POST['action'] ?? 'save'));

    foreach ($formData as $key => $defaultValue) {
        $formData[$key] = trim((string) ($_POST[$key] ?? $defaultValue));
    }

    if ($formData['nama_jenis'] === '') {
        $errors[] = 'Nama jenis ternak wajib diisi.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($formData['nama_jenis'], 'UTF-8') : strlen($formData['nama_jenis'])) > 25) {
        $errors[] = 'Nama jenis ternak maksimal 25 karakter.';
    }

    if ($errors === []) {
        try {
            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM tb_jenis_ternak WHERE LOWER(nama_jenis) = LOWER(:nama_jenis)');
            $checkStmt->execute(['nama_jenis' => $formData['nama_jenis']]);

            if ((int) $checkStmt->fetchColumn() > 0) {
                $errors[] = 'Nama jenis ternak sudah terdaftar.';
            }
        } catch (Throwable $exception) {
            error_log('Gagal memeriksa duplikat jenis ternak: ' . $exception->getMessage());
            $errors[] = 'Gagal memeriksa data. Silakan coba lagi.';
        }
    }

    if ($errors === []) {
        try {
            $insertStmt = $pdo->prepare('INSERT INTO tb_jenis_ternak (nama_jenis) VALUES (:nama_jenis)');
            $insertStmt->execute(['nama_jenis' => $formData['nama_jenis']]);

            if ($action === 'save_new') {
                $successMessage = 'Data jenis ternak berhasil disimpan. Silakan tambah data baru.';
                $formData = ['nama_jenis' => ''];
            } else {
                header('Location: index.php?success=Data jenis ternak berhasil ditambahkan');
                exit;
            }
        } catch (Throwable $exception) {
            error_log('Gagal menyimpan data jenis ternak: ' . $exception->getMessage());
            $errors[] = 'Gagal menyimpan data jenis ternak.';
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tambah Jenis Ternak — LivestockID</title>
    <link rel="stylesheet" href="../style.css" />
</head>

<body>
    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon"><i class="bi bi-grid-fill"></i></div>
                <div class="brand-name">Livestock<span>ID</span></div>
            </div>
            <nav class="sidebar-nav">
                <p class="nav-section-label">Menu Utama</p>
                <ul style="list-style: none; padding: 0; margin: 0">
                    <li class="nav-item"><a href="../index.php" class="nav-link-item"><i class="bi bi-speedometer2"></i><span>Overview</span></a></li>
                    <li class="nav-item"><a href="../ternak/index.php" class="nav-link-item"><i class="bi bi-box-seam"></i><span>Ternak</span></a></li>
                    <li class="nav-item"><a href="../kandang/index.php" class="nav-link-item"><i class="bi bi-house-door"></i><span>Kandang</span></a></li>
                    <li class="nav-item"><a href="../petugas/index.php" class="nav-link-item"><i class="bi bi-people"></i><span>Petugas</span></a></li>
                </ul>
                <p class="nav-section-label">Pengaturan</p>
                <ul style="list-style: none; padding: 0; margin: 0">
                    <li class="nav-item"><a href="index.php" class="nav-link-item active"><i class="bi bi-tags"></i><span>Jenis Ternak</span></a></li>
                </ul>
            </nav>
        </aside>
        <div class="main-area">
            <header class="topbar"><button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button><span class="topbar-title">Tambah Jenis Ternak</span></header>
            <main class="page-content">
                <div class="page-header">
                    <h1>Tambah Jenis Ternak Baru</h1>
                    <p>Tambahkan master data jenis ternak untuk digunakan di form ternak.</p>
                </div>
                <?php if ($errors !== []): ?><div class="card-panel" style="border-left:4px solid #e05252;margin-bottom:16px;">
                        <p style="margin:0 0 8px;font-weight:600;color:#9f1f1f;">Terjadi kesalahan:</p>
                        <ul style="margin:0;padding-left:20px;color:#6b2121;"><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul>
                    </div><?php endif; ?>
                <?php if ($successMessage !== ''): ?><div class="card-panel" style="border-left:4px solid #2f7d32;margin-bottom:16px;color:#1f5f24;"><?php echo e($successMessage); ?></div><?php endif; ?>
                <form method="POST">
                    <div class="form-card">
                        <p class="form-section-title">Informasi Jenis Ternak</p>
                        <div style="display:grid;grid-template-columns:1fr;gap:16px;">
                            <div><label class="form-label" for="nama_jenis">Nama Jenis <span style="color:#e05252">*</span></label><input type="text" id="nama_jenis" name="nama_jenis" class="form-control-custom" maxlength="25" placeholder="Contoh: Sapi Perah" required value="<?php echo e($formData['nama_jenis']); ?>" /></div>
                        </div>
                        <div class="form-actions"><button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Simpan Jenis</button><button type="submit" name="action" value="save_new" class="btn-secondary-custom"><i class="bi bi-plus-circle"></i> Simpan &amp; Buat Baru</button><a href="index.php" class="btn-secondary-custom"><i class="bi bi-x-lg"></i> Batal</a></div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>

</html>