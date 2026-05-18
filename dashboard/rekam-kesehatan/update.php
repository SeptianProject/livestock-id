<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$errors = [];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(404);
    echo 'ID rekam kesehatan tidak valid.';
    exit;
}

$ternakList = $pdo->query("SELECT id_ternak, CONCAT('TRN-', LPAD(id_ternak, 3, '0')) AS kode_ternak FROM tb_ternak ORDER BY id_ternak DESC")->fetchAll();
$petugasList = $pdo->query('SELECT id_petugas, nama_petugas FROM tb_petugas ORDER BY nama_petugas ASC')->fetchAll();
$tindakanList = $pdo->query('SELECT id_tindakan, nama_tindakan FROM tb_tindakan ORDER BY nama_tindakan ASC')->fetchAll();

try {
    $selectStmt = $pdo->prepare('SELECT id_kesehatan, id_ternak, id_petugas, id_tindakan, tgl_pemeriksaan, diagnosa, berat_badan, suhu_badan FROM tb_kesehatan WHERE id_kesehatan = :id_kesehatan');
    $selectStmt->execute(['id_kesehatan' => $id]);
    $kesehatan = $selectStmt->fetch();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data kesehatan: ' . $exception->getMessage());
    echo 'Gagal memuat data kesehatan. Silakan coba lagi nanti.';
    exit;
}

if (!$kesehatan) {
    http_response_code(404);
    echo 'Data rekam kesehatan tidak ditemukan.';
    exit;
}

$formData = [
    'id_ternak' => (string) $kesehatan['id_ternak'],
    'id_petugas' => (string) $kesehatan['id_petugas'],
    'id_tindakan' => (string) $kesehatan['id_tindakan'],
    'tgl_pemeriksaan' => $kesehatan['tgl_pemeriksaan'],
    'diagnosa' => $kesehatan['diagnosa'] ?? '',
    'berat_badan' => $kesehatan['berat_badan'] !== null ? (string) $kesehatan['berat_badan'] : '',
    'suhu_badan' => $kesehatan['suhu_badan'] !== null ? (string) $kesehatan['suhu_badan'] : '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($formData as $key => $defaultValue) {
        $formData[$key] = trim((string) ($_POST[$key] ?? $defaultValue));
    }

    if (!ctype_digit($formData['id_ternak'])) {
        $errors[] = 'Ternak wajib dipilih.';
    }
    if (!ctype_digit($formData['id_petugas'])) {
        $errors[] = 'Petugas wajib dipilih.';
    }
    if (!ctype_digit($formData['id_tindakan'])) {
        $errors[] = 'Tindakan wajib dipilih.';
    }
    if ($formData['tgl_pemeriksaan'] === '') {
        $errors[] = 'Tanggal pemeriksaan wajib diisi.';
    }
    if ($formData['diagnosa'] !== '' && (function_exists('mb_strlen') ? mb_strlen($formData['diagnosa'], 'UTF-8') : strlen($formData['diagnosa'])) > 500) {
        $errors[] = 'Diagnosa maksimal 500 karakter.';
    }
    if ($formData['berat_badan'] !== '' && !is_numeric($formData['berat_badan'])) {
        $errors[] = 'Berat badan harus berupa angka.';
    }
    if ($formData['suhu_badan'] !== '' && !is_numeric($formData['suhu_badan'])) {
        $errors[] = 'Suhu badan harus berupa angka.';
    }

    if ($errors === []) {
        try {
            $updateStmt = $pdo->prepare('UPDATE tb_kesehatan SET id_ternak = :id_ternak, id_petugas = :id_petugas, id_tindakan = :id_tindakan, tgl_pemeriksaan = :tgl_pemeriksaan, diagnosa = :diagnosa, berat_badan = :berat_badan, suhu_badan = :suhu_badan WHERE id_kesehatan = :id_kesehatan');
            $updateStmt->execute([
                'id_ternak' => (int) $formData['id_ternak'],
                'id_petugas' => (int) $formData['id_petugas'],
                'id_tindakan' => (int) $formData['id_tindakan'],
                'tgl_pemeriksaan' => $formData['tgl_pemeriksaan'],
                'diagnosa' => $formData['diagnosa'] !== '' ? $formData['diagnosa'] : null,
                'berat_badan' => $formData['berat_badan'] !== '' ? (float) $formData['berat_badan'] : null,
                'suhu_badan' => $formData['suhu_badan'] !== '' ? (float) $formData['suhu_badan'] : null,
                'id_kesehatan' => $id,
            ]);
            header('Location: index.php?success=Data rekam kesehatan berhasil diubah');
            exit;
        } catch (Throwable $exception) {
            error_log('Gagal memperbarui rekam kesehatan: ' . $exception->getMessage());
            $errors[] = 'Gagal menyimpan data rekam kesehatan.';
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Rekam Kesehatan — LivestockID</title>
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
                <p class="nav-section-label">Pencatatan</p>
                <ul style="list-style:none;padding:0;margin:0;">
                    <li class="nav-item"><a href="index.php" class="nav-link-item active"><i class="bi bi-heart-pulse"></i><span>Rekam Kesehatan</span></a></li>
                    <li class="nav-item" style="margin-left: 20px;"><a href="../tindakan/index.php" class="nav-link-item"><i class="bi bi-bandaid"></i><span>Tindakan</span></a></li>
                    <li class="nav-item"><a href="../catatan-produksi/index.php" class="nav-link-item"><i class="bi bi-journal-richtext"></i><span>Catatan Produksi</span></a></li>
                </ul>
            </nav>
        </aside>
        <div class="main-area">
            <header class="topbar"><button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button><span class="topbar-title">Edit Rekam Kesehatan</span></header>
            <main class="page-content">
                <div class="page-header">
                    <h1>Edit Rekam Kesehatan</h1>
                    <p>Perbarui data pemeriksaan kesehatan ternak.</p>
                </div><?php if ($errors !== []): ?><div class="card-panel" style="border-left:4px solid #e05252;margin-bottom:16px;">
                        <p style="margin:0 0 8px;font-weight:600;color:#9f1f1f;">Terjadi kesalahan:</p>
                        <ul style="margin:0;padding-left:20px;color:#6b2121;"><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul>
                    </div><?php endif; ?><form method="POST">
                    <div class="form-card">
                        <p class="form-section-title">Informasi Pemeriksaan</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div><label class="form-label" for="id_ternak">Ternak <span style="color:#e05252">*</span></label><select id="id_ternak" name="id_ternak" class="form-control-custom" required>
                                    <option value="" disabled <?php echo e($formData['id_ternak'] === '' ? 'selected' : ''); ?>>Pilih ternak</option><?php foreach ($ternakList as $ternak): ?><option value="<?php echo e((string) $ternak['id_ternak']); ?>" <?php echo e($formData['id_ternak'] === (string) $ternak['id_ternak'] ? 'selected' : ''); ?>><?php echo e($ternak['kode_ternak']); ?></option><?php endforeach; ?>
                                </select></div>
                            <div><label class="form-label" for="id_petugas">Petugas <span style="color:#e05252">*</span></label><select id="id_petugas" name="id_petugas" class="form-control-custom" required>
                                    <option value="" disabled <?php echo e($formData['id_petugas'] === '' ? 'selected' : ''); ?>>Pilih petugas</option><?php foreach ($petugasList as $petugas): ?><option value="<?php echo e((string) $petugas['id_petugas']); ?>" <?php echo e($formData['id_petugas'] === (string) $petugas['id_petugas'] ? 'selected' : ''); ?>><?php echo e($petugas['nama_petugas']); ?></option><?php endforeach; ?>
                                </select></div>
                            <div><label class="form-label" for="id_tindakan">Tindakan <span style="color:#e05252">*</span></label><select id="id_tindakan" name="id_tindakan" class="form-control-custom" required>
                                    <option value="" disabled <?php echo e($formData['id_tindakan'] === '' ? 'selected' : ''); ?>>Pilih tindakan</option><?php foreach ($tindakanList as $tindakan): ?><option value="<?php echo e((string) $tindakan['id_tindakan']); ?>" <?php echo e($formData['id_tindakan'] === (string) $tindakan['id_tindakan'] ? 'selected' : ''); ?>><?php echo e($tindakan['nama_tindakan']); ?></option><?php endforeach; ?>
                                </select></div>
                            <div><label class="form-label" for="tgl_pemeriksaan">Tanggal Pemeriksaan <span style="color:#e05252">*</span></label><input type="date" id="tgl_pemeriksaan" name="tgl_pemeriksaan" class="form-control-custom" required value="<?php echo e($formData['tgl_pemeriksaan']); ?>" /></div>
                            <div><label class="form-label" for="berat_badan">Berat Badan (kg)</label><input type="number" step="0.01" id="berat_badan" name="berat_badan" class="form-control-custom" value="<?php echo e($formData['berat_badan']); ?>" /></div>
                            <div><label class="form-label" for="suhu_badan">Suhu Badan (°C)</label><input type="number" step="0.01" id="suhu_badan" name="suhu_badan" class="form-control-custom" value="<?php echo e($formData['suhu_badan']); ?>" /></div>
                            <div style="grid-column:1 / -1;"><label class="form-label" for="diagnosa">Diagnosa</label><textarea id="diagnosa" name="diagnosa" class="form-control-custom" rows="4"><?php echo e($formData['diagnosa']); ?></textarea></div>
                        </div>
                        <div class="form-actions"><button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Simpan Perubahan</button><a href="index.php" class="btn-secondary-custom"><i class="bi bi-x-lg"></i> Batal</a></div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>

</html>