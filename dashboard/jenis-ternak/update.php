<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$errors = [];
$successMessage = '';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(404);
    echo 'ID jenis ternak tidak valid.';
    exit;
}

try {
    $selectStmt = $pdo->prepare('SELECT id_jenis_ternak, nama_jenis FROM tb_jenis_ternak WHERE id_jenis_ternak = :id_jenis_ternak');
    $selectStmt->execute(['id_jenis_ternak' => $id]);
    $jenisTernak = $selectStmt->fetch();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data jenis ternak: ' . $exception->getMessage());
    echo 'Gagal memuat data jenis ternak. Silakan coba lagi nanti.';
    exit;
}

if (!$jenisTernak) {
    http_response_code(404);
    echo 'Data jenis ternak tidak ditemukan.';
    exit;
}

$formData = ['nama_jenis' => $jenisTernak['nama_jenis'] ?? ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['nama_jenis'] = trim((string) ($_POST['nama_jenis'] ?? $formData['nama_jenis']));

    if ($formData['nama_jenis'] === '') {
        $errors[] = 'Nama jenis ternak wajib diisi.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($formData['nama_jenis'], 'UTF-8') : strlen($formData['nama_jenis'])) > 25) {
        $errors[] = 'Nama jenis ternak maksimal 25 karakter.';
    }

    if ($errors === []) {
        try {
            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM tb_jenis_ternak WHERE LOWER(nama_jenis) = LOWER(:nama_jenis) AND id_jenis_ternak != :id_jenis_ternak');
            $checkStmt->execute(['nama_jenis' => $formData['nama_jenis'], 'id_jenis_ternak' => $id]);
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
            $updateStmt = $pdo->prepare('UPDATE tb_jenis_ternak SET nama_jenis = :nama_jenis WHERE id_jenis_ternak = :id_jenis_ternak');
            $updateStmt->execute(['nama_jenis' => $formData['nama_jenis'], 'id_jenis_ternak' => $id]);
            header('Location: index.php?success=Data jenis ternak berhasil diubah');
            exit;
        } catch (Throwable $exception) {
            error_log('Gagal memperbarui data jenis ternak: ' . $exception->getMessage());
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
    <title>Edit Jenis Ternak — LivestockID</title>
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
            <header class="topbar"><button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button><span class="topbar-title">Edit Jenis Ternak</span></header>
            <main class="page-content">
                <div class="page-header">
                    <h1>Edit Jenis Ternak</h1>
                    <p>Perbarui master data jenis ternak yang digunakan pada ternak.</p>
                </div>
                <?php if ($errors !== []): ?><div class="card-panel" style="border-left:4px solid #e05252;margin-bottom:16px;">
                        <p style="margin:0 0 8px;font-weight:600;color:#9f1f1f;">Terjadi kesalahan:</p>
                        <ul style="margin:0;padding-left:20px;color:#6b2121;"><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul>
                    </div><?php endif; ?>
                <form method="POST">
                    <div class="form-card">
                        <p class="form-section-title">Informasi Jenis Ternak</p>
                        <div style="display:grid;grid-template-columns:1fr;gap:16px;">
                            <div><label class="form-label" for="nama_jenis">Nama Jenis <span style="color:#e05252">*</span></label><input type="text" id="nama_jenis" name="nama_jenis" class="form-control-custom" maxlength="25" required value="<?php echo e($formData['nama_jenis']); ?>" /></div>
                        </div>
                        <div class="form-actions"><button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Simpan Perubahan</button><a href="index.php" class="btn-secondary-custom"><i class="bi bi-x-lg"></i> Batal</a></div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>

</html>