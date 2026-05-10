<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$errors = [];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(404);
    echo 'ID jenis produksi tidak valid.';
    exit;
}

try {
    $selectStmt = $pdo->prepare('SELECT id_jenis_produksi, nama_produksi, satuan FROM tb_jenis_produksi WHERE id_jenis_produksi = :id_jenis_produksi');
    $selectStmt->execute(['id_jenis_produksi' => $id]);
    $jenisProduksi = $selectStmt->fetch();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data jenis produksi: ' . $exception->getMessage());
    echo 'Gagal memuat data jenis produksi. Silakan coba lagi nanti.';
    exit;
}

if (!$jenisProduksi) {
    http_response_code(404);
    echo 'Data jenis produksi tidak ditemukan.';
    exit;
}

$formData = [
    'nama_produksi' => $jenisProduksi['nama_produksi'] ?? '',
    'satuan' => $jenisProduksi['satuan'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['nama_produksi'] = trim((string) ($_POST['nama_produksi'] ?? $formData['nama_produksi']));
    $formData['satuan'] = trim((string) ($_POST['satuan'] ?? $formData['satuan']));

    if ($formData['nama_produksi'] === '') {
        $errors[] = 'Nama produksi wajib diisi.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($formData['nama_produksi'], 'UTF-8') : strlen($formData['nama_produksi'])) > 50) {
        $errors[] = 'Nama produksi maksimal 50 karakter.';
    }

    if ($formData['satuan'] === '') {
        $errors[] = 'Satuan wajib diisi.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($formData['satuan'], 'UTF-8') : strlen($formData['satuan'])) > 25) {
        $errors[] = 'Satuan maksimal 25 karakter.';
    }

    if ($errors === []) {
        try {
            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM tb_jenis_produksi WHERE LOWER(nama_produksi) = LOWER(:nama_produksi) AND LOWER(satuan) = LOWER(:satuan) AND id_jenis_produksi != :id_jenis_produksi');
            $checkStmt->execute(['nama_produksi' => $formData['nama_produksi'], 'satuan' => $formData['satuan'], 'id_jenis_produksi' => $id]);
            if ((int) $checkStmt->fetchColumn() > 0) {
                $errors[] = 'Jenis produksi dengan satuan tersebut sudah terdaftar.';
            }
        } catch (Throwable $exception) {
            error_log('Gagal memeriksa duplikat jenis produksi: ' . $exception->getMessage());
            $errors[] = 'Gagal memeriksa data.';
        }
    }

    if ($errors === []) {
        try {
            $updateStmt = $pdo->prepare('UPDATE tb_jenis_produksi SET nama_produksi = :nama_produksi, satuan = :satuan WHERE id_jenis_produksi = :id_jenis_produksi');
            $updateStmt->execute(['nama_produksi' => $formData['nama_produksi'], 'satuan' => $formData['satuan'], 'id_jenis_produksi' => $id]);
            header('Location: index.php?success=Data jenis produksi berhasil diubah');
            exit;
        } catch (Throwable $exception) {
            error_log('Gagal memperbarui jenis produksi: ' . $exception->getMessage());
            $errors[] = 'Gagal menyimpan data jenis produksi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Jenis Produksi — LivestockID</title>
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
                <ul style="list-style:none;padding:0;margin:0;">
                    <li class="nav-item"><a href="../index.php" class="nav-link-item"><i class="bi bi-speedometer2"></i><span>Overview</span></a></li>
                    <li class="nav-item"><a href="../ternak/index.php" class="nav-link-item"><i class="bi bi-box-seam"></i><span>Ternak</span></a></li>
                    <li class="nav-item"><a href="../kandang/index.php" class="nav-link-item"><i class="bi bi-house-door"></i><span>Kandang</span></a></li>
                    <li class="nav-item"><a href="../petugas/index.php" class="nav-link-item"><i class="bi bi-people"></i><span>Petugas</span></a></li>
                </ul>
                <p class="nav-section-label">Pengaturan</p>
                <ul style="list-style:none;padding:0;margin:0;">
                    <li class="nav-item"><a href="../tindakan/index.php" class="nav-link-item"><i class="bi bi-bandaid"></i><span>Tindakan</span></a></li>
                    <li class="nav-item"><a href="../jenis-produksi/index.php" class="nav-link-item active"><i class="bi bi-journal-richtext"></i><span>Jenis Produksi</span></a></li>
                </ul>
                <p class="nav-section-label">Pencatatan</p>
                <ul style="list-style:none;padding:0;margin:0;">
                    <li class="nav-item"><a href="../rekam-kesehatan/index.php" class="nav-link-item"><i class="bi bi-heart-pulse"></i><span>Rekam Kesehatan</span></a></li>
                    <li class="nav-item"><a href="../catatan-produksi/index.php" class="nav-link-item"><i class="bi bi-droplet-half"></i><span>Catatan Produksi</span></a></li>
                </ul>
            </nav>
        </aside>
        <div class="main-area">
            <header class="topbar"><button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button><span class="topbar-title">Edit Jenis Produksi</span></header>
            <main class="page-content">
                <div class="page-header">
                    <h1>Edit Jenis Produksi</h1>
                    <p>Perbarui master data jenis produksi.</p>
                </div><?php if ($errors !== []): ?><div class="card-panel" style="border-left:4px solid #e05252;margin-bottom:16px;">
                        <p style="margin:0 0 8px;font-weight:600;color:#9f1f1f;">Terjadi kesalahan:</p>
                        <ul style="margin:0;padding-left:20px;color:#6b2121;"><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul>
                    </div><?php endif; ?><form method="POST">
                    <div class="form-card">
                        <p class="form-section-title">Informasi Jenis Produksi</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div><label class="form-label" for="nama_produksi">Nama Produksi <span style="color:#e05252">*</span></label><input type="text" id="nama_produksi" name="nama_produksi" class="form-control-custom" maxlength="50" required value="<?php echo e($formData['nama_produksi']); ?>" /></div>
                            <div><label class="form-label" for="satuan">Satuan <span style="color:#e05252">*</span></label><input type="text" id="satuan" name="satuan" class="form-control-custom" maxlength="25" required value="<?php echo e($formData['satuan']); ?>" /></div>
                        </div>
                        <div class="form-actions"><button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Simpan Perubahan</button><a href="index.php" class="btn-secondary-custom"><i class="bi bi-x-lg"></i> Batal</a></div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>

</html>