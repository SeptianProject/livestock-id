<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$errors = [];
$successMessage = '';
$jabatan = null;

// Get ID dari URL
$jabatan_id = (int) ($_GET['id'] ?? 0);

if ($jabatan_id <= 0) {
    http_response_code(404);
    error_log('ID jabatan tidak valid');
    echo 'ID jabatan tidak ditemukan.';
    exit;
}

// Load data jabatan
try {
    $jabatanStmt = $pdo->prepare('SELECT * FROM tb_jabatan WHERE id_jabatan = :id_jabatan');
    $jabatanStmt->execute(['id_jabatan' => $jabatan_id]);
    $jabatan = $jabatanStmt->fetch();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data jabatan: ' . $exception->getMessage());
    echo 'Gagal memuat data jabatan. Silakan coba lagi nanti.';
    exit;
}

if (!$jabatan) {
    http_response_code(404);
    error_log('Jabatan dengan ID ' . $jabatan_id . ' tidak ditemukan');
    echo 'Jabatan tidak ditemukan.';
    exit;
}

$formData = [
    'nama_jabatan' => $jabatan['nama_jabatan'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($formData as $key => $defaultValue) {
        $formData[$key] = trim((string) ($_POST[$key] ?? $defaultValue));
    }

    if ($formData['nama_jabatan'] === '') {
        $errors[] = 'Nama jabatan wajib diisi.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($formData['nama_jabatan'], 'UTF-8') : strlen($formData['nama_jabatan'])) > 25) {
        $errors[] = 'Nama jabatan maksimal 25 karakter.';
    }

    // Check duplicate (excluding current jabatan)
    if ($errors === []) {
        try {
            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM tb_jabatan WHERE LOWER(nama_jabatan) = LOWER(:nama_jabatan) AND id_jabatan != :id_jabatan');
            $checkStmt->execute(['nama_jabatan' => $formData['nama_jabatan'], 'id_jabatan' => $jabatan_id]);
            $count = (int) $checkStmt->fetchColumn();

            if ($count > 0) {
                $errors[] = 'Nama jabatan sudah terdaftar.';
            }
        } catch (Throwable $exception) {
            error_log('Gagal memeriksa duplikat jabatan: ' . $exception->getMessage());
            $errors[] = 'Gagal memeriksa data. Silakan coba lagi.';
        }
    }

    if ($errors === []) {
        try {
            $updateStmt = $pdo->prepare(
                'UPDATE tb_jabatan 
                 SET nama_jabatan = :nama_jabatan 
                 WHERE id_jabatan = :id_jabatan'
            );

            $updateStmt->execute([
                'nama_jabatan' => $formData['nama_jabatan'],
                'id_jabatan' => $jabatan_id,
            ]);

            header('Location: index.php?success=Data jabatan berhasil diubah');
            exit;
        } catch (Throwable $exception) {
            error_log('Gagal memperbarui data jabatan: ' . $exception->getMessage());
            $errors[] = 'Gagal menyimpan data jabatan. Pastikan nama jabatan maksimal 25 karakter dan belum digunakan.';
        }
    }
}

?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Jabatan — LivestockID</title>
    <link rel="stylesheet" href="../style.css" />
</head>

<body>
    <div class="layout">
        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon"><i class="bi bi-grid-fill"></i></div>
                <div class="brand-name">Livestock<span>ID</span></div>
            </div>
            <nav class="sidebar-nav">
                <p class="nav-section-label">Menu Utama</p>
                <ul style="list-style: none; padding: 0; margin: 0">
                    <li class="nav-item">
                        <a href="../index.php" class="nav-link-item"><i class="bi bi-speedometer2"></i><span>Overview</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="../ternak/index.php" class="nav-link-item"><i class="bi bi-box-seam"></i><span>Ternak</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="../kandang/index.php" class="nav-link-item"><i class="bi bi-house-door"></i><span>Kandang</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="../petugas/index.php" class="nav-link-item"><i class="bi bi-people"></i><span>Petugas</span></a>
                    </li>
                </ul>
                <p class="nav-section-label">Pengaturan</p>
                <ul style="list-style: none; padding: 0; margin: 0">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link-item active"><i class="bi bi-briefcase"></i><span>Jabatan</span></a>
                    </li>
                </ul>
                <p class="nav-section-label">Pencatatan</p>
                <ul style="list-style: none; padding: 0; margin: 0">
                    <li class="nav-item">
                        <a href="#" class="nav-link-item"><i class="bi bi-heart-pulse"></i><span>Rekam Kesehatan</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link-item"><i class="bi bi-journal-text"></i><span>Catatan Produksi</span></a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../../auth/login.html" class="nav-link-item"><i class="bi bi-box-arrow-left"></i><span>Keluar</span></a>
            </div>
        </aside>

        <!-- MAIN AREA -->
        <div class="main-area">
            <header class="topbar">
                <button
                    class="sidebar-toggle"
                    onclick="
                  document.getElementById('sidebar').classList.toggle('open')
                ">
                    <i class="bi bi-list"></i>
                </button>
                <div style="display: flex; align-items: center; gap: 8px; flex: 1">
                    <a
                        href="index.php"
                        style="
                    color: #7c8493;
                    font-size: 13px;
                    display: flex;
                    align-items: center;
                    gap: 4px;
                  "><i class="bi bi-chevron-left"></i> Jabatan</a>
                    <i
                        class="bi bi-chevron-right"
                        style="font-size: 11px; color: #b0b8c4"></i>
                    <span class="topbar-title" style="font-size: 15px">Edit Jabatan</span>
                </div>
                <div class="topbar-actions">
                    <div class="topbar-notif">
                        <i class="bi bi-bell"></i><span class="notif-badge"></span>
                    </div>
                    <a
                        href="../profile/index.php"
                        style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    text-decoration: none;
                  ">
                        <div class="topbar-user-info">
                            <span class="name">Admin</span><span class="role">Administrator</span>
                        </div>
                        <div class="topbar-avatar">AS</div>
                    </a>
                </div>
            </header>

            <main class="page-content">
                <div class="page-header">
                    <h1>Edit Jabatan</h1>
                    <p>
                        Perbarui informasi jabatan yang sudah terdaftar di sistem LivestockID.
                    </p>
                </div>

                <?php if ($errors !== []): ?>
                    <div class="card-panel" style="border-left: 4px solid #e05252; margin-bottom: 16px;">
                        <p style="margin: 0 0 8px; font-weight: 600; color: #9f1f1f;">Terjadi kesalahan:</p>
                        <ul style="margin: 0; padding-left: 20px; color: #6b2121;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($successMessage !== ''): ?>
                    <div class="card-panel" style="border-left: 4px solid #2f7d32; margin-bottom: 16px; color: #1f5f24;">
                        <?php echo e($successMessage); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-card">
                        <p class="form-section-title">Informasi Jabatan</p>
                        <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                            <div>
                                <label class="form-label" for="nama_jabatan">Nama Jabatan <span style="color: #e05252">*</span></label>
                                <input
                                    type="text"
                                    id="nama_jabatan"
                                    name="nama_jabatan"
                                    class="form-control-custom"
                                    placeholder="Contoh: Dokter Hewan"
                                    maxlength="25"
                                    required
                                    value="<?php echo e($formData['nama_jabatan']); ?>" />
                                <p style="margin: 4px 0 0; font-size: 12px; color: #7c8493;">Masukkan nama jabatan yang jelas dan unik.</p>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary-custom">
                                <i class="bi bi-check-lg"></i> Simpan Perubahan
                            </button>
                            <a href="index.php" class="btn-secondary-custom"><i class="bi bi-x-lg"></i> Batal</a>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>

</html>