<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deleteId = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);

    if ($deleteId) {
        try {
            $deleteStmt = $pdo->prepare('DELETE FROM tb_kandang WHERE id_kandang = :id_kandang');
            $deleteStmt->execute(['id_kandang' => $deleteId]);
            header('Location: index.php?success=Data kandang berhasil dihapus');
            exit;
        } catch (Throwable $exception) {
            $errorMessage = 'Gagal menghapus data: ' . $exception->getMessage();
        }
    }
}

$successMessage = trim((string) ($_GET['success'] ?? ''));
$q = trim((string) ($_GET['q'] ?? ''));

$sql = "SELECT id_kandang, CONCAT('K', LPAD(id_kandang, 2, '0')) AS kode_kandang, nama_kandang, lokasi, kapasitas FROM tb_kandang";
$conditions = [];
$params = [];

if ($q !== '') {
    $conditions[] = "(CAST(id_kandang AS CHAR) LIKE :q OR CONCAT('K', LPAD(id_kandang, 2, '0')) LIKE :q OR nama_kandang LIKE :q OR lokasi LIKE :q)";
    $params['q'] = '%' . $q . '%';
}

if ($conditions !== []) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$kandangList = $stmt->fetchAll();

$totalStmt = $pdo->query('SELECT COUNT(*) FROM tb_kandang');
$totalKandang = (int) $totalStmt->fetchColumn();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kandang - LivestockID</title>
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
                    <li class="nav-item">
                        <a href="../index.php" class="nav-link-item"><i class="bi bi-speedometer2"></i><span>Overview</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="../ternak/index.php" class="nav-link-item"><i class="bi bi-box-seam"></i><span>Ternak</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php" class="nav-link-item active"><i class="bi bi-house-door"></i><span>Kandang</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="../petugas/index.php" class="nav-link-item"><i class="bi bi-people"></i><span>Petugas</span></a>
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

        <div class="main-area">
            <header class="topbar">
                <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                    <i class="bi bi-list"></i>
                </button>
                <span class="topbar-title">Kandang</span>
                <div class="topbar-actions">
                    <a href="../profile/index.php" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                        <div class="topbar-user-info">
                            <span class="name">Admin</span><span class="role">Administrator</span>
                        </div>
                        <div class="topbar-avatar">AD</div>
                    </a>
                </div>
            </header>

            <main class="page-content">
                <div class="list-header">
                    <div>
                        <h2>Daftar Kandang</h2>
                        <p style="margin: 4px 0 0; font-size: 13px; color: #7c8493;">Total <?php echo $totalKandang; ?> kandang terdaftar</p>
                    </div>
                    <a href="create.php" class="btn-primary-custom"><i class="bi bi-plus-lg"></i> Tambah Kandang</a>
                </div>

                <?php if ($successMessage !== ''): ?>
                    <div style="margin-bottom:12px;padding:10px 12px;border-radius:10px;background:#ecfdf3;color:#116534;font-size:13px;">
                        <?php echo e($successMessage); ?>
                    </div>
                <?php endif; ?>

                <?php if ($errorMessage !== ''): ?>
                    <div style="margin-bottom:12px;padding:10px 12px;border-radius:10px;background:#fef2f2;color:#991b1b;font-size:13px;">
                        <?php echo e($errorMessage); ?>
                    </div>
                <?php endif; ?>

                <div class="card-panel" style="margin-bottom: 0">
                    <form method="GET" class="table-toolbar">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari kode (K01), nama, atau lokasi kandang..." />
                        </div>
                        <button type="submit" class="btn-secondary-custom">Filter</button>
                    </form>

                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kode Kandang</th>
                                    <th>Nama Kandang</th>
                                    <th>Lokasi</th>
                                    <th>Kapasitas</th>
                                    <th style="text-align: center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($kandangList === []): ?>
                                    <tr>
                                        <td colspan="5" style="text-align:center;padding:18px;">Belum ada data kandang.</td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($kandangList as $kandang): ?>
                                    <tr>
                                        <td><strong><?php echo e($kandang['kode_kandang']); ?></strong></td>
                                        <td><?php echo e($kandang['nama_kandang']); ?></td>
                                        <td><?php echo e($kandang['lokasi']); ?></td>
                                        <td><?php echo (int) $kandang['kapasitas']; ?> ekor</td>
                                        <td style="text-align:center;">
                                            <div style="display:flex;gap:6px;justify-content:center;">
                                                <a class="action-btn" title="Edit" href="update.php?id=<?php echo (int) $kandang['id_kandang']; ?>"><i class="bi bi-pencil"></i></a>
                                                <form method="POST" onsubmit="return confirm('Hapus data kandang ini?');" style="margin:0;">
                                                    <input type="hidden" name="delete_id" value="<?php echo (int) $kandang['id_kandang']; ?>" />
                                                    <button class="action-btn danger" title="Hapus" type="submit"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>