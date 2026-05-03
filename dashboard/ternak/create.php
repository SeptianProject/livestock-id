<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$errors = [];
$successMessage = '';
$formData = [
    'id_jenis_ternak' => '',
    'tgl_lahir' => '',
    'jenis_kelamin' => '',
    'id_kandang' => '',
];

$jenisTernakList = [];
$kandangList = [];

try {
    $jenisStmt = $pdo->query('SELECT id_jenis_ternak, nama_jenis FROM tb_jenis_ternak ORDER BY nama_jenis ASC');
    $jenisTernakList = $jenisStmt->fetchAll();

    $kandangStmt = $pdo->query('SELECT id_kandang, nama_kandang FROM tb_kandang ORDER BY nama_kandang ASC');
    $kandangList = $kandangStmt->fetchAll();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data referensi ternak: ' . $exception->getMessage());
    echo 'Gagal memuat data referensi ternak. Silakan coba lagi nanti.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string) ($_POST['action'] ?? 'save'));

    foreach ($formData as $key => $defaultValue) {
        $formData[$key] = trim((string) ($_POST[$key] ?? $defaultValue));
    }

    if (!ctype_digit($formData['id_jenis_ternak'])) {
        $errors[] = 'Jenis ternak wajib dipilih.';
    }

    if ($formData['tgl_lahir'] === '') {
        $errors[] = 'Tanggal lahir wajib diisi.';
    }

    if (!in_array($formData['jenis_kelamin'], ['jantan', 'betina'], true)) {
        $errors[] = 'Jenis kelamin wajib dipilih.';
    }

    if (!ctype_digit($formData['id_kandang'])) {
        $errors[] = 'Kandang wajib dipilih.';
    }

    if ($errors === []) {
        try {
            $insertStmt = $pdo->prepare(
                'INSERT INTO tb_ternak (id_jenis_ternak, tgl_lahir, jenis_kelamin, id_kandang)
                 VALUES (:id_jenis_ternak, :tgl_lahir, :jenis_kelamin, :id_kandang)'
            );

            $insertStmt->execute([
                'id_jenis_ternak' => (int) $formData['id_jenis_ternak'],
                'tgl_lahir' => $formData['tgl_lahir'],
                'jenis_kelamin' => $formData['jenis_kelamin'],
                'id_kandang' => (int) $formData['id_kandang'],
            ]);

            if ($action === 'save_new') {
                $successMessage = 'Data ternak berhasil disimpan. Silakan tambah data baru.';
                $formData = [
                    'id_jenis_ternak' => '',
                    'tgl_lahir' => '',
                    'jenis_kelamin' => '',
                    'id_kandang' => '',
                ];
            } else {
                header('Location: index.php?success=Data ternak berhasil ditambahkan');
                exit;
            }
        } catch (Throwable $exception) {
            error_log('Gagal menyimpan data ternak: ' . $exception->getMessage());
            $errors[] = 'Gagal menyimpan data ternak. Pastikan referensi jenis ternak dan kandang valid.';
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tambah Ternak — LivestockID</title>
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
                    <li class="nav-item"><a href="index.php" class="nav-link-item active"><i class="bi bi-box-seam"></i><span>Ternak</span></a></li>
                    <li class="nav-item"><a href="../kandang/index.php" class="nav-link-item"><i class="bi bi-house-door"></i><span>Kandang</span></a></li>
                    <li class="nav-item"><a href="../petugas/index.php" class="nav-link-item"><i class="bi bi-people"></i><span>Petugas</span></a></li>
                </ul>
                <p class="nav-section-label">Pengaturan</p>
                <ul style="list-style: none; padding: 0; margin: 0">
                    <li class="nav-item"><a href="../jenis-ternak/index.php" class="nav-link-item"><i class="bi bi-tags"></i><span>Jenis Ternak</span></a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../../auth/login.php" class="nav-link-item"><i class="bi bi-box-arrow-left"></i><span>Keluar</span></a>
            </div>
        </aside>

        <div class="main-area">
            <header class="topbar">
                <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button>
                <span class="topbar-title">Tambah Ternak</span>
            </header>

            <main class="page-content">
                <div class="page-header">
                    <h1>Tambah Ternak Baru</h1>
                    <p>Isi data ternak berdasarkan jenis ternak, kandang, tanggal lahir, dan jenis kelamin.</p>
                </div>

                <?php if ($errors !== []): ?>
                    <div class="card-panel" style="border-left:4px solid #e05252;margin-bottom:16px;">
                        <p style="margin:0 0 8px;font-weight:600;color:#9f1f1f;">Terjadi kesalahan:</p>
                        <ul style="margin:0;padding-left:20px;color:#6b2121;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($successMessage !== ''): ?>
                    <div class="card-panel" style="border-left:4px solid #2f7d32;margin-bottom:16px;color:#1f5f24;">
                        <?php echo e($successMessage); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-card">
                        <p class="form-section-title">Informasi Ternak</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div>
                                <label class="form-label" for="id_jenis_ternak">Jenis Ternak <span style="color:#e05252">*</span></label>
                                <select id="id_jenis_ternak" name="id_jenis_ternak" class="form-control-custom" required>
                                    <option value="" disabled <?php echo e($formData['id_jenis_ternak'] === '' ? 'selected' : ''); ?>>Pilih jenis ternak</option>
                                    <?php foreach ($jenisTernakList as $jenis): ?>
                                        <option value="<?php echo e((string) $jenis['id_jenis_ternak']); ?>" <?php echo e($formData['id_jenis_ternak'] === (string) $jenis['id_jenis_ternak'] ? 'selected' : ''); ?>><?php echo e($jenis['nama_jenis']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="form-label" for="id_kandang">Kandang <span style="color:#e05252">*</span></label>
                                <select id="id_kandang" name="id_kandang" class="form-control-custom" required>
                                    <option value="" disabled <?php echo e($formData['id_kandang'] === '' ? 'selected' : ''); ?>>Pilih kandang</option>
                                    <?php foreach ($kandangList as $kandang): ?>
                                        <option value="<?php echo e((string) $kandang['id_kandang']); ?>" <?php echo e($formData['id_kandang'] === (string) $kandang['id_kandang'] ? 'selected' : ''); ?>><?php echo e($kandang['nama_kandang']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="form-label" for="tgl_lahir">Tanggal Lahir <span style="color:#e05252">*</span></label>
                                <input type="date" id="tgl_lahir" name="tgl_lahir" class="form-control-custom" required value="<?php echo e($formData['tgl_lahir']); ?>" />
                            </div>
                            <div>
                                <label class="form-label" for="jenis_kelamin">Jenis Kelamin <span style="color:#e05252">*</span></label>
                                <select id="jenis_kelamin" name="jenis_kelamin" class="form-control-custom" required>
                                    <option value="" disabled <?php echo e($formData['jenis_kelamin'] === '' ? 'selected' : ''); ?>>Pilih jenis kelamin</option>
                                    <option value="jantan" <?php echo e($formData['jenis_kelamin'] === 'jantan' ? 'selected' : ''); ?>>Jantan</option>
                                    <option value="betina" <?php echo e($formData['jenis_kelamin'] === 'betina' ? 'selected' : ''); ?>>Betina</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Simpan Ternak</button>
                            <button type="submit" name="action" value="save_new" class="btn-secondary-custom"><i class="bi bi-plus-circle"></i> Simpan &amp; Buat Baru</button>
                            <a href="index.php" class="btn-secondary-custom"><i class="bi bi-x-lg"></i> Batal</a>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>

</html>