<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$successMessage = '';
$errorMessage = '';

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deleteId = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);

    if ($deleteId) {
        try {
            $deleteStmt = $pdo->prepare(
                'DELETE FROM tb_petugas WHERE id_petugas = :id_petugas'
            );
            $deleteStmt->execute(['id_petugas' => $deleteId]);

            header('Location: index.php?success=Data petugas berhasil dihapus');
            exit;
        } catch (Throwable $exception) {
            $errorMessage = 'Gagal menghapus data: ' . $exception->getMessage();
        }
    }
}

// MESSAGE & SEARCH
$successMessage = trim((string) ($_GET['success'] ?? ''));
$q = trim((string) ($_GET['q'] ?? ''));

// QUERY
$sql = "SELECT 
            id_petugas,
            nama_petugas,
            id_jabatan,
            no_telp,
            id_user
        FROM tb_petugas";

$conditions = [];
$params = [];

// SEARCH
if ($q !== '') {
    $conditions[] = "(
        CAST(id_petugas AS CHAR) LIKE :q OR 
        nama_petugas LIKE :q OR 
        no_telp LIKE :q
    )";
    $params['q'] = '%' . $q . '%';
}

// APPLY FILTER
if (!empty($conditions)) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

// ORDER
$sql .= ' ORDER BY id_petugas DESC';

// EXECUTE
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$petugasList = $stmt->fetchAll();

// TOTAL
$totalStmt = $pdo->query('SELECT COUNT(*) FROM tb_petugas');
$totalPetugas = (int) $totalStmt->fetchColumn();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Petugas — LivestockID</title>
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
                <span class="topbar-title">Petugas</span>
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
                <div class="list-header">
                    <div>
                        <h2>Daftar Petugas</h2>
                        <p style="margin: 4px 0 0; font-size: 13px; color: #7c8493">
                            Total <?php echo e((string) $totalPetugas); ?> petugas terdaftar
                        </p>
                    </div>
                    <a href="create.php" class="btn-primary-custom"><i class="bi bi-plus-lg"></i> Tambah Petugas</a>
                </div>

                <?php if ($successMessage !== ''): ?>
                    <div class="card-panel" style="border-left: 4px solid #2f7d32; margin-bottom: 16px; color: #1f5f24;">
                        <i class="bi bi-check-circle" style="margin-right: 8px;"></i> <?php echo e($successMessage); ?>
                    </div>
                <?php endif; ?>

                <?php if ($errorMessage !== ''): ?>
                    <div class="card-panel" style="border-left: 4px solid #e05252; margin-bottom: 16px;">
                        <p style="margin: 0; font-weight: 600; color: #9f1f1f;">Terjadi kesalahan:</p>
                        <p style="margin: 4px 0 0; color: #6b2121;"><?php echo e($errorMessage); ?></p>
                    </div>
                <?php endif; ?>

                <div class="card-panel" style="margin-bottom: 0">
                    <div class="table-toolbar">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input
                                type="text"
                                placeholder="Cari nama atau nomor telepon petugas..."
                                id="searchInput" />
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Petugas</th>
                                    <th>Jabatan</th>
                                    <th>Akun</th>
                                    <th>No. Telepon</th>
                                    <th style="text-align: center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($petugasList)): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 24px; color: #7c8493;">
                                        <td colspan="5" style="text-align: center; padding: 24px; color: #7c8493;">
                                            Tidak ada data petugas.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($petugasList as $item): ?>
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px">
                                                    <div class="petugas-avatar" style="width: 34px; height: 34px; font-size: 12px">
                                                        <?php
                                                        $initials = '';
                                                        $nameParts = explode(' ', trim($item['nama_petugas']));
                                                        foreach ($nameParts as $part) {
                                                            $initials .= substr($part, 0, 1);
                                                        }
                                                        echo e(strtoupper(substr($initials, 0, 2)));
                                                        ?>
                                                    </div>
                                                    <div>
                                                        <p style="margin: 0; font-size: 13px; font-weight: 600; color: #000005;">
                                                            <?php echo e($item['nama_petugas']); ?>
                                                        </p>
                                                        <p style="margin: 0; font-size: 11px; color: #7c8493">
                                                            #PTG-<?php echo e(str_pad((string) $item['id_petugas'], 3, '0', STR_PAD_LEFT)); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                // Cari nama jabatan berdasarkan id_jabatan
                                                $jabatanName = 'N/A';
                                                try {
                                                    $jabStmt = $pdo->prepare('SELECT nama_jabatan FROM tb_jabatan WHERE id_jabatan = :id_jabatan');
                                                    $jabStmt->execute(['id_jabatan' => $item['id_jabatan']]);
                                                    $jabResult = $jabStmt->fetch();
                                                    if ($jabResult) {
                                                        $jabatanName = $jabResult['nama_jabatan'];
                                                    }
                                                } catch (Throwable $e) {
                                                    error_log('Gagal memuat nama jabatan: ' . $e->getMessage());
                                                }
                                                echo e($jabatanName);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $userLabel = '-';
                                                if (!empty($item['id_user'])) {
                                                    try {
                                                        $userStmt = $pdo->prepare('SELECT username, role FROM tb_user WHERE id_user = :id_user');
                                                        $userStmt->execute(['id_user' => $item['id_user']]);
                                                        $user = $userStmt->fetch();

                                                        if ($user) {
                                                            $userLabel = $user['username'] . ' (' . $user['role'] . ')';
                                                        }
                                                    } catch (Throwable $exception) {
                                                        $userLabel = 'Error';
                                                    }
                                                }

                                                echo e($userLabel);
                                                ?>
                                            </td>
                                            <td><?php echo e($item['no_telp']); ?></td>
                                            <td style="text-align: center">
                                                <div style="display: flex; gap: 6px; justify-content: center;">
                                                    <a href="update.php?id=<?php echo e((string) $item['id_petugas']); ?>" class="action-btn" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                        <input type="hidden" name="delete_id" value="<?php echo e((string) $item['id_petugas']); ?>" />
                                                        <button type="submit" class="action-btn danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php if (!empty($petugasList)): ?>
                            <div class="table-footer">
                                <span>Menampilkan <?php echo e((string) count($petugasList)); ?> dari <?php echo e((string) $totalPetugas); ?> data</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>