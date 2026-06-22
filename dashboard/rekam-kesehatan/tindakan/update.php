<?php

declare(strict_types=1);

require_once __DIR__ . '../../../../config/database.php';
require_once __DIR__ . '../../../../config/helpers.php';

$errors = [];
$successMessage = '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(404);
    echo 'ID tindakan tidak valid.';
    exit;
}

try {
    $selectStmt = $pdo->prepare('SELECT id_tindakan, nama_tindakan FROM tb_tindakan WHERE id_tindakan = :id_tindakan');
    $selectStmt->execute(['id_tindakan' => $id]);
    $tindakan = $selectStmt->fetch();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data tindakan: ' . $exception->getMessage());
    echo 'Gagal memuat data tindakan. Silakan coba lagi nanti.';
    exit;
}

if (!$tindakan) {
    http_response_code(404);
    echo 'Data tindakan tidak ditemukan.';
    exit;
}

$formData = ['nama_tindakan' => $tindakan['nama_tindakan'] ?? ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['nama_tindakan'] = trim((string) ($_POST['nama_tindakan'] ?? $formData['nama_tindakan']));

    if ($formData['nama_tindakan'] === '') {
        $errors[] = 'Nama tindakan wajib diisi.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($formData['nama_tindakan'], 'UTF-8') : strlen($formData['nama_tindakan'])) > 50) {
        $errors[] = 'Nama tindakan maksimal 50 karakter.';
    }

    if ($errors === []) {
        try {
            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM tb_tindakan WHERE LOWER(nama_tindakan) = LOWER(:nama_tindakan) AND id_tindakan != :id_tindakan');
            $checkStmt->execute(['nama_tindakan' => $formData['nama_tindakan'], 'id_tindakan' => $id]);
            if ((int) $checkStmt->fetchColumn() > 0) {
                $errors[] = 'Nama tindakan sudah terdaftar.';
            }
        } catch (Throwable $exception) {
            error_log('Gagal memeriksa duplikat tindakan: ' . $exception->getMessage());
            $errors[] = 'Gagal memeriksa data.';
        }
    }

    if ($errors === []) {
        try {
            $updateStmt = $pdo->prepare('UPDATE tb_tindakan SET nama_tindakan = :nama_tindakan WHERE id_tindakan = :id_tindakan');
            $updateStmt->execute(['nama_tindakan' => $formData['nama_tindakan'], 'id_tindakan' => $id]);
            header('Location: index.php?success=Data tindakan berhasil diubah');
            exit;
        } catch (Throwable $exception) {
            error_log('Gagal memperbarui tindakan: ' . $exception->getMessage());
            $errors[] = 'Gagal menyimpan data tindakan.';
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Tindakan — LivestockID</title>
    <link rel="stylesheet" href="../../style.css" />
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
                <ul style="list-style:none;padding:0;margin:0;">
                    <li class="nav-item"><a href="../../index.php" class="nav-link-item"><i class="bi bi-speedometer2"></i><span>Overview</span></a></li>
                    <li class="nav-item"><a href="../ternak/index.php" class="nav-link-item"><i class="bi bi-box-seam"></i><span>Ternak</span></a></li>
                    <li class="nav-item"><a href="../kandang/index.php" class="nav-link-item"><i class="bi bi-house-door"></i><span>Kandang</span></a></li>
                    <li class="nav-item"><a href="../petugas/index.php" class="nav-link-item"><i class="bi bi-people"></i><span>Petugas</span></a></li>
                </ul>
                <p class="nav-section-label">Pencatatan</p>
                <ul style="list-style:none;padding:0;margin:0;">
                    <li class="nav-item"><a href="../index.php" class="nav-link-item"><i class="bi bi-heart-pulse"></i><span>Rekam Kesehatan</span></a></li>
                    <li class="nav-item" style="margin-left: 20px;"><a href="index.php" class="nav-link-item active"><i class="bi bi-bandaid"></i><span>Tindakan</span></a></li>
                    <li class="nav-item"><a href="../../catatan-produksi/index.php" class="nav-link-item"><i class="bi bi-journal-richtext"></i><span>Catatan Produksi</span></a></li>
                </ul>
            </nav>
        </aside>
        <div class="main-area">
            <header class="topbar"><button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button><span class="topbar-title">Edit Tindakan</span></header>
            <main class="page-content">
                <div class="page-header">
                    <h1>Edit Tindakan</h1>
                    <p>Perbarui master data tindakan.</p>
                </div><?php if ($errors !== []): ?><div class="card-panel" style="border-left:4px solid #e05252;margin-bottom:16px;">
                        <p style="margin:0 0 8px;font-weight:600;color:#9f1f1f;">Terjadi kesalahan:</p>
                        <ul style="margin:0;padding-left:20px;color:#6b2121;"><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul>
                    </div><?php endif; ?><form method="POST">
                    <div class="form-card">
                        <p class="form-section-title">Informasi Tindakan</p>
                        <div style="display:grid;grid-template-columns:1fr;gap:16px;">
                            <div><label class="form-label" for="nama_tindakan">Nama Tindakan <span style="color:#e05252">*</span></label><input type="text" id="nama_tindakan" name="nama_tindakan" class="form-control-custom" maxlength="50" required value="<?php echo e($formData['nama_tindakan']); ?>" /></div>
                        </div>
                        <div class="form-actions"><button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Simpan Perubahan</button><a href="index.php" class="btn-secondary-custom"><i class="bi bi-x-lg"></i> Batal</a></div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>

</html>