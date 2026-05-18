<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$errors = [];
$successMessage = '';
$petugas = null;
$jabatans = [];

// Get ID dari URL
$petugas_id = (int) ($_GET['id'] ?? 0);

if ($petugas_id <= 0) {
    http_response_code(404);
    error_log('ID petugas tidak valid');
    echo 'ID petugas tidak ditemukan.';
    exit;
}

// Load data jabatan
try {
    $jabatanStmt = $pdo->query('SELECT * FROM tb_jabatan ORDER BY nama_jabatan ASC');
    $jabatans = $jabatanStmt->fetchAll();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data jabatan: ' . $exception->getMessage());
    echo 'Gagal memuat data jabatan. Silakan coba lagi nanti.';
    exit;
}

// Load data petugas
try {
    $petugasStmt = $pdo->prepare('SELECT * FROM tb_petugas WHERE id_petugas = :id_petugas');
    $petugasStmt->execute(['id_petugas' => $petugas_id]);
    $petugas = $petugasStmt->fetch();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data petugas: ' . $exception->getMessage());
    echo 'Gagal memuat data petugas. Silakan coba lagi nanti.';
    exit;
}

if (!$petugas) {
    http_response_code(404);
    error_log('Petugas dengan ID ' . $petugas_id . ' tidak ditemukan');
    echo 'Petugas tidak ditemukan.';
    exit;
}

$formData = [
    'nama_petugas' => $petugas['nama_petugas'] ?? '',
    'id_jabatan' => (string) ($petugas['id_jabatan'] ?? ''),
    'id_user' => (string) ($petugas['id_user'] ?? ''),
    'no_telp' => $petugas['no_telp'] ?? '',
];

$users = [];
try {
    $currentUserId = $formData['id_user'] !== '' ? (int) $formData['id_user'] : 0;
    $userStmt = $pdo->prepare(
        "SELECT u.id_user, u.username, u.role
         FROM tb_user u
         LEFT JOIN tb_petugas p ON p.id_user = u.id_user AND p.id_petugas <> :id_petugas
         WHERE u.role <> 'admin' AND (p.id_user IS NULL OR u.id_user = :current_user_id)
         ORDER BY u.username ASC"
    );
    $userStmt->execute([
        'id_petugas' => $petugas_id,
        'current_user_id' => $currentUserId,
    ]);
    $users = $userStmt->fetchAll();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('Gagal memuat data user: ' . $exception->getMessage());
    echo 'Gagal memuat data user. Silakan coba lagi nanti.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string) ($_POST['action'] ?? 'save'));

    foreach ($formData as $key => $defaultValue) {
        $formData[$key] = trim((string) ($_POST[$key] ?? $defaultValue));
    }

    if ($formData['nama_petugas'] === '') {
        $errors[] = 'Nama petugas wajib diisi.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($formData['nama_petugas'], 'UTF-8') : strlen($formData['nama_petugas'])) > 100) {
        $errors[] = 'Nama petugas maksimal 100 karakter.';
    }

    if ($formData['id_jabatan'] === '') {
        $errors[] = 'Jabatan wajib diisi.';
    }

    if ($formData['id_user'] !== '' && !ctype_digit($formData['id_user'])) {
        $errors[] = 'Akun user tidak valid.';
    }

    if ($formData['no_telp'] === '') {
        $errors[] = 'Nomor telepon wajib diisi.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($formData['no_telp'], 'UTF-8') : strlen($formData['no_telp'])) > 20) {
        $errors[] = 'Nomor telepon maksimal 20 karakter.';
    }

    if ($errors === []) {
        try {
            $updateStmt = $pdo->prepare(
                'UPDATE tb_petugas 
                 SET nama_petugas = :nama_petugas, id_jabatan = :id_jabatan, id_user = :id_user, no_telp = :no_telp 
                 WHERE id_petugas = :id_petugas'
            );

            $updateStmt->execute([
                'nama_petugas' => $formData['nama_petugas'],
                'id_jabatan' => $formData['id_jabatan'],
                'id_user' => $formData['id_user'] !== '' ? (int) $formData['id_user'] : null,
                'no_telp' => $formData['no_telp'],
                'id_petugas' => $petugas_id,
            ]);

            header('Location: index.php?success=Data petugas berhasil diubah');
            exit;
        } catch (Throwable $exception) {
            error_log('Gagal memperbarui data petugas: ' . $exception->getMessage());
            $errors[] = 'Gagal menyimpan data petugas. Pastikan nama petugas maksimal 100 karakter, nomor telepon maksimal 20 karakter, dan jabatan dipilih dengan benar.';
        }
    }
}

?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Petugas — LivestockID</title>
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
                        <a href="index.php" class="nav-link-item active"><i class="bi bi-people"></i><span>Petugas</span></a>
                    </li>
                    <li class="nav-item" style="margin-left: 20px;">
                        <a href="../petugas/jabatan/index.php" class="nav-link-item"><i class="bi bi-briefcase"></i><span>Jabatan</span></a>
                    </li>
                </ul>
                <p class="nav-section-label">Pencatatan</p>
                <ul style="list-style: none; padding: 0; margin: 0">
                    <li class="nav-item">
                        <a href="../rekam-kesehatan/index.php" class="nav-link-item"><i class="bi bi-heart-pulse"></i><span>Rekam Kesehatan</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="../catatan-produksi/index.php" class="nav-link-item"><i class="bi bi-journal-text"></i><span>Catatan Produksi</span></a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../../auth/login.php" class="nav-link-item"><i class="bi bi-box-arrow-left"></i><span>Keluar</span></a>
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
                  "><i class="bi bi-chevron-left"></i> Petugas</a>
                    <i
                        class="bi bi-chevron-right"
                        style="font-size: 11px; color: #b0b8c4"></i>
                    <span class="topbar-title" style="font-size: 15px">Edit Petugas</span>
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
                    <h1>Edit Petugas</h1>
                    <p>
                        Perbarui informasi petugas yang sudah terdaftar di sistem LivestockID.
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
                        <p class="form-section-title">Data Pribadi</p>
                        <div
                            style="
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 16px;
                      ">
                            <div>
                                <label class="form-label" for="nama_petugas">Nama Lengkap <span style="color: #e05252">*</span></label>
                                <input
                                    type="text"
                                    id="nama_petugas"
                                    name="nama_petugas"
                                    class="form-control-custom"
                                    placeholder="Masukkan nama petugas"
                                    maxlength="100"
                                    required
                                    value="<?php echo e($formData['nama_petugas']); ?>" />
                            </div>
                            <div>
                                <label class="form-label" for="id_jabatan">Jabatan <span style="color: #e05252">*</span></label>
                                <select
                                    id="id_jabatan"
                                    name="id_jabatan"
                                    class="form-control-custom"
                                    required>
                                    <option value="" disabled <?php echo e($formData['id_jabatan'] === '' ? 'selected' : ''); ?>>Pilih jabatan</option>
                                    <?php foreach ($jabatans as $jabatan): ?>
                                        <option value="<?php echo e((string) $jabatan['id_jabatan']); ?>" <?php echo e($formData['id_jabatan'] === (string) $jabatan['id_jabatan'] ? 'selected' : ''); ?>>
                                            <?php echo e($jabatan['nama_jabatan']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="form-label" for="id_user">Akun User</label>
                                <select
                                    id="id_user"
                                    name="id_user"
                                    class="form-control-custom">
                                    <option value="" <?php echo e($formData['id_user'] === '' ? 'selected' : ''); ?>>Tanpa akun</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo e((string) $user['id_user']); ?>" <?php echo e($formData['id_user'] === (string) $user['id_user'] ? 'selected' : ''); ?>>
                                            <?php echo e($user['username'] . ' (' . $user['role'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="form-label" for="no_telp">No. Telepon <span style="color: #e05252">*</span></label>
                                <input
                                    type="tel"
                                    id="no_telp"
                                    name="no_telp"
                                    class="form-control-custom"
                                    placeholder="Contoh: +62 812-3456-7890"
                                    maxlength="20"
                                    required
                                    value="<?php echo e($formData['no_telp']); ?>" />
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