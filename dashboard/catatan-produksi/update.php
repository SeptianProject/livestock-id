<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$errors = [];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(404);
    echo 'ID catatan produksi tidak valid.';
    exit;
}

$ternakList = $pdo->query("SELECT id_ternak, CONCAT('TRN-', LPAD(id_ternak, 3, '0')) AS kode_ternak FROM tb_ternak ORDER BY id_ternak DESC")->fetchAll();
$jenisProduksiList = $pdo->query('SELECT id_jenis_produksi, nama_produksi, satuan FROM tb_jenis_produksi ORDER BY nama_produksi ASC')->fetchAll();

try {
    $selectStmt = $pdo->prepare('SELECT id_produksi, id_ternak, tgl_produksi, id_jenis_produksi, jumlah_produksi FROM tb_produksi WHERE id_produksi = :id_produksi');
    $selectStmt->execute(['id_produksi' => $id]);
    $produksi = $selectStmt->fetch();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data produksi: ' . $exception->getMessage());
    echo 'Gagal memuat data produksi. Silakan coba lagi nanti.';
    exit;
}

if (!$produksi) {
    http_response_code(404);
    echo 'Data catatan produksi tidak ditemukan.';
    exit;
}

$formData = [
    'id_ternak' => (string) $produksi['id_ternak'],
    'tgl_produksi' => $produksi['tgl_produksi'],
    'id_jenis_produksi' => (string) $produksi['id_jenis_produksi'],
    'jumlah_produksi' => (string) $produksi['jumlah_produksi'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($formData as $key => $defaultValue) {
        $formData[$key] = trim((string) ($_POST[$key] ?? $defaultValue));
    }

    if (!ctype_digit($formData['id_ternak'])) {
        $errors[] = 'Ternak wajib dipilih.';
    }
    if (!ctype_digit($formData['id_jenis_produksi'])) {
        $errors[] = 'Jenis produksi wajib dipilih.';
    }
    if ($formData['tgl_produksi'] === '') {
        $errors[] = 'Tanggal produksi wajib diisi.';
    }
    if ($formData['jumlah_produksi'] === '' || !is_numeric($formData['jumlah_produksi'])) {
        $errors[] = 'Jumlah produksi harus berupa angka.';
    }

    if ($errors === []) {
        try {
            $updateStmt = $pdo->prepare('UPDATE tb_produksi SET id_ternak = :id_ternak, tgl_produksi = :tgl_produksi, id_jenis_produksi = :id_jenis_produksi, jumlah_produksi = :jumlah_produksi WHERE id_produksi = :id_produksi');
            $updateStmt->execute([
                'id_ternak' => (int) $formData['id_ternak'],
                'tgl_produksi' => $formData['tgl_produksi'],
                'id_jenis_produksi' => (int) $formData['id_jenis_produksi'],
                'jumlah_produksi' => (float) $formData['jumlah_produksi'],
                'id_produksi' => $id,
            ]);
            header('Location: index.php?success=Data catatan produksi berhasil diubah');
            exit;
        } catch (Throwable $exception) {
            error_log('Gagal memperbarui catatan produksi: ' . $exception->getMessage());
            $errors[] = 'Gagal menyimpan data catatan produksi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Catatan Produksi — LivestockID</title>
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
                    <li class="nav-item"><a href="../jenis-produksi/index.php" class="nav-link-item"><i class="bi bi-journal-richtext"></i><span>Jenis Produksi</span></a></li>
                </ul>
                <p class="nav-section-label">Pencatatan</p>
                <ul style="list-style:none;padding:0;margin:0;">
                    <li class="nav-item"><a href="../rekam-kesehatan/index.php" class="nav-link-item"><i class="bi bi-heart-pulse"></i><span>Rekam Kesehatan</span></a></li>
                    <li class="nav-item"><a href="index.php" class="nav-link-item active"><i class="bi bi-droplet-half"></i><span>Catatan Produksi</span></a></li>
                </ul>
            </nav>
        </aside>
        <div class="main-area">
            <header class="topbar"><button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button><span class="topbar-title">Edit Catatan Produksi</span></header>
            <main class="page-content">
                <div class="page-header">
                    <h1>Edit Catatan Produksi</h1>
                    <p>Perbarui data produksi ternak.</p>
                </div><?php if ($errors !== []): ?><div class="card-panel" style="border-left:4px solid #e05252;margin-bottom:16px;">
                        <p style="margin:0 0 8px;font-weight:600;color:#9f1f1f;">Terjadi kesalahan:</p>
                        <ul style="margin:0;padding-left:20px;color:#6b2121;"><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul>
                    </div><?php endif; ?><form method="POST">
                    <div class="form-card">
                        <p class="form-section-title">Informasi Produksi</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div><label class="form-label" for="id_ternak">Ternak <span style="color:#e05252">*</span></label><select id="id_ternak" name="id_ternak" class="form-control-custom" required>
                                    <option value="" disabled <?php echo e($formData['id_ternak'] === '' ? 'selected' : ''); ?>>Pilih ternak</option><?php foreach ($ternakList as $ternak): ?><option value="<?php echo e((string) $ternak['id_ternak']); ?>" <?php echo e($formData['id_ternak'] === (string) $ternak['id_ternak'] ? 'selected' : ''); ?>><?php echo e($ternak['kode_ternak']); ?></option><?php endforeach; ?>
                                </select></div>
                            <div><label class="form-label" for="id_jenis_produksi">Jenis Produksi <span style="color:#e05252">*</span></label><select id="id_jenis_produksi" name="id_jenis_produksi" class="form-control-custom" required>
                                    <option value="" disabled <?php echo e($formData['id_jenis_produksi'] === '' ? 'selected' : ''); ?>>Pilih jenis produksi</option><?php foreach ($jenisProduksiList as $jenis): ?><option value="<?php echo e((string) $jenis['id_jenis_produksi']); ?>" <?php echo e($formData['id_jenis_produksi'] === (string) $jenis['id_jenis_produksi'] ? 'selected' : ''); ?>><?php echo e($jenis['nama_produksi'] . ' (' . $jenis['satuan'] . ')'); ?></option><?php endforeach; ?>
                                </select></div>
                            <div><label class="form-label" for="tgl_produksi">Tanggal Produksi <span style="color:#e05252">*</span></label><input type="date" id="tgl_produksi" name="tgl_produksi" class="form-control-custom" required value="<?php echo e($formData['tgl_produksi']); ?>" /></div>
                            <div><label class="form-label" for="jumlah_produksi">Jumlah Produksi <span style="color:#e05252">*</span></label><input type="number" step="0.01" id="jumlah_produksi" name="jumlah_produksi" class="form-control-custom" required value="<?php echo e($formData['jumlah_produksi']); ?>" /></div>
                        </div>
                        <div class="form-actions"><button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Simpan Perubahan</button><a href="index.php" class="btn-secondary-custom"><i class="bi bi-x-lg"></i> Batal</a></div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>

</html>