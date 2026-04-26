<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$errors = [];
$formData = [
    'id_kandang' => '',
    'nama_kandang' => '',
    'lokasi' => '',
    'kapasitas' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($formData as $key => $defaultValue) {
        $formData[$key] = trim((string) ($_POST[$key] ?? $defaultValue));
    }

    if (!ctype_digit($formData['id_kandang']) || (int) $formData['id_kandang'] <= 0) {
        $errors[] = 'ID kandang harus berupa angka lebih dari 0.';
    }

    if ($formData['nama_kandang'] === '') {
        $errors[] = 'Nama kandang wajib diisi.';
    }

    if ($formData['lokasi'] === '') {
        $errors[] = 'Lokasi wajib diisi.';
    }

    if (!ctype_digit($formData['kapasitas']) || (int) $formData['kapasitas'] <= 0) {
        $errors[] = 'Kapasitas harus berupa angka lebih dari 0.';
    }

    if ($errors === []) {
        try {
            $insertStmt = $pdo->prepare(
                'INSERT INTO tb_kandang (id_kandang, nama_kandang, lokasi, kapasitas)
                 VALUES (:id_kandang, :nama_kandang, :lokasi, :kapasitas)'
            );

            $insertStmt->execute([
                'id_kandang' => (int) $formData['id_kandang'],
                'nama_kandang' => $formData['nama_kandang'],
                'lokasi' => $formData['lokasi'],
                'kapasitas' => (int) $formData['kapasitas'],
            ]);

            header('Location: index.php?success=Data kandang berhasil ditambahkan');
            exit;
        } catch (Throwable $exception) {
            $errors[] = 'Gagal menyimpan data: ' . $exception->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tambah Kandang - LivestockID</title>
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
                    <li class="nav-item"><a href="index.php" class="nav-link-item active"><i class="bi bi-house-door"></i><span>Kandang</span></a></li>
                    <li class="nav-item"><a href="../petugas/index.php" class="nav-link-item"><i class="bi bi-people"></i><span>Petugas</span></a></li>
                </ul>
            </nav>
        </aside>

        <div class="main-area">
            <header class="topbar">
                <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button>
                <span class="topbar-title">Tambah Kandang</span>
            </header>

            <main class="page-content">
                <div class="page-header">
                    <h1>Tambah Kandang Baru</h1>
                    <p>Isi data kandang untuk menambahkan record baru.</p>
                </div>

                <?php if ($errors !== []): ?>
                    <div style="margin-bottom:12px;padding:10px 12px;border-radius:10px;background:#fef2f2;color:#991b1b;font-size:13px;">
                        <?php foreach ($errors as $error): ?>
                            <div><?php echo e($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-card">
                        <p class="form-section-title">Informasi Kandang</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div>
                                <label class="form-label" for="id_kandang">ID Kandang</label>
                                <input type="number" id="id_kandang" name="id_kandang" class="form-control-custom" min="1" required value="<?php echo e($formData['id_kandang']); ?>" />
                            </div>
                            <div>
                                <label class="form-label" for="nama_kandang">Nama Kandang</label>
                                <input type="text" id="nama_kandang" name="nama_kandang" class="form-control-custom" required value="<?php echo e($formData['nama_kandang']); ?>" />
                            </div>
                            <div>
                                <label class="form-label" for="lokasi">Lokasi</label>
                                <input type="text" id="lokasi" name="lokasi" class="form-control-custom" required value="<?php echo e($formData['lokasi']); ?>" />
                            </div>
                            <div>
                                <label class="form-label" for="kapasitas">Kapasitas</label>
                                <input type="number" id="kapasitas" name="kapasitas" min="1" class="form-control-custom" required value="<?php echo e($formData['kapasitas']); ?>" />
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Simpan</button>
                            <a href="index.php" class="btn-secondary-custom"><i class="bi bi-x-lg"></i> Batal</a>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>

</html>