<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$successMessage = '';
$errorMessage = '';

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deleteId = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);

    if ($deleteId) {
        try {
            // Check if jabatan is used in tb_petugas
            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM tb_petugas WHERE id_jabatan = :id_jabatan');
            $checkStmt->execute(['id_jabatan' => $deleteId]);
            $count = (int) $checkStmt->fetchColumn();

            if ($count > 0) {
                $errorMessage = 'Tidak bisa menghapus jabatan karena masih digunakan oleh ' . $count . ' petugas.';
            } else {
                $deleteStmt = $pdo->prepare(
                    'DELETE FROM tb_jabatan WHERE id_jabatan = :id_jabatan'
                );
                $deleteStmt->execute(['id_jabatan' => $deleteId]);

                header('Location: index.php?success=Data jabatan berhasil dihapus');
                exit;
            }
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
            id_jabatan,
            nama_jabatan
        FROM tb_jabatan";

$conditions = [];
$params = [];

// SEARCH
if ($q !== '') {
    $conditions[] = "(
        CAST(id_jabatan AS CHAR) LIKE :q OR 
        nama_jabatan LIKE :q
    )";
    $params['q'] = '%' . $q . '%';
}

// APPLY FILTER
if (!empty($conditions)) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

// ORDER
$sql .= ' ORDER BY id_jabatan ASC';

// EXECUTE
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jabatanList = $stmt->fetchAll();

// TOTAL
$totalStmt = $pdo->query('SELECT COUNT(*) FROM tb_jabatan');
$totalJabatan = (int) $totalStmt->fetchColumn();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Jabatan — LivestockID</title>
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
                <span class="topbar-title">Jabatan</span>
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
                        <h2>Daftar Jabatan</h2>
                        <p style="margin: 4px 0 0; font-size: 13px; color: #7c8493">
                            Total <?php echo e((string) $totalJabatan); ?> jabatan terdaftar
                        </p>
                    </div>
                    <a href="create.php" class="btn-primary-custom"><i class="bi bi-plus-lg"></i> Tambah Jabatan</a>
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
                                placeholder="Cari nama jabatan..."
                                id="searchInput" />
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Jabatan</th>
                                    <th>Jumlah Petugas</th>
                                    <th style="text-align: center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($jabatanList)): ?>
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 24px; color: #7c8493;">
                                            Tidak ada data jabatan.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($jabatanList as $item): ?>
                                        <?php
                                        // Count petugas dengan jabatan ini
                                        $countStmt = $pdo->prepare('SELECT COUNT(*) FROM tb_petugas WHERE id_jabatan = :id_jabatan');
                                        $countStmt->execute(['id_jabatan' => $item['id_jabatan']]);
                                        $petugasCount = (int) $countStmt->fetchColumn();
                                        ?>
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px">
                                                    <div class="petugas-avatar" style="width: 34px; height: 34px; font-size: 12px; background-color: #e8eef7;">
                                                        <i class="bi bi-briefcase" style="color: #4f46e5;"></i>
                                                    </div>
                                                    <div>
                                                        <p style="margin: 0; font-size: 13px; font-weight: 600; color: #000005;">
                                                            <?php echo e($item['nama_jabatan']); ?>
                                                        </p>
                                                        <p style="margin: 0; font-size: 11px; color: #7c8493">
                                                            #JAB-<?php echo e(str_pad((string) $item['id_jabatan'], 3, '0', STR_PAD_LEFT)); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span style="background-color: #e8eef7; color: #4f46e5; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                    <?php echo e((string) $petugasCount); ?> petugas
                                                </span>
                                            </td>
                                            <td style="text-align: center">
                                                <div style="display: flex; gap: 6px; justify-content: center;">
                                                    <a href="update.php?id=<?php echo e((string) $item['id_jabatan']); ?>" class="action-btn" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                        <input type="hidden" name="delete_id" value="<?php echo e((string) $item['id_jabatan']); ?>" />
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
                        <?php if (!empty($jabatanList)): ?>
                            <div class="table-footer">
                                <span>Menampilkan <?php echo e((string) count($jabatanList)); ?> dari <?php echo e((string) $totalJabatan); ?> data</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>