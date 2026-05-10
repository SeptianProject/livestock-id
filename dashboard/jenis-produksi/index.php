<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$successMessage = trim((string) ($_GET['success'] ?? ''));
$errorMessage = '';
$q = trim((string) ($_GET['q'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deleteId = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);

    if ($deleteId) {
        try {
            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM tb_produksi WHERE id_jenis_produksi = :id_jenis_produksi');
            $checkStmt->execute(['id_jenis_produksi' => $deleteId]);

            if ((int) $checkStmt->fetchColumn() > 0) {
                $errorMessage = 'Jenis produksi tidak bisa dihapus karena masih dipakai pada catatan produksi.';
            } else {
                $deleteStmt = $pdo->prepare('DELETE FROM tb_jenis_produksi WHERE id_jenis_produksi = :id_jenis_produksi');
                $deleteStmt->execute(['id_jenis_produksi' => $deleteId]);
                header('Location: index.php?success=Data jenis produksi berhasil dihapus');
                exit;
            }
        } catch (Throwable $exception) {
            $errorMessage = 'Gagal menghapus data: ' . $exception->getMessage();
        }
    }
}

$sql = 'SELECT id_jenis_produksi, nama_produksi, satuan FROM tb_jenis_produksi';
$params = [];
if ($q !== '') {
    $sql .= ' WHERE (CAST(id_jenis_produksi AS CHAR) LIKE :q OR nama_produksi LIKE :q OR satuan LIKE :q)';
    $params['q'] = '%' . $q . '%';
}
$sql .= ' ORDER BY nama_produksi ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jenisProduksiList = $stmt->fetchAll();

$totalStmt = $pdo->query('SELECT COUNT(*) FROM tb_jenis_produksi');
$totalJenisProduksi = (int) $totalStmt->fetchColumn();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Jenis Produksi — LivestockID</title>
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
            <div class="sidebar-footer"><a href="../../auth/login.php" class="nav-link-item"><i class="bi bi-box-arrow-left"></i><span>Keluar</span></a></div>
        </aside>
        <div class="main-area">
            <header class="topbar"><button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button><span class="topbar-title">Jenis Produksi</span></header>
            <main class="page-content">
                <div class="list-header">
                    <div>
                        <h2>Daftar Jenis Produksi</h2>
                        <p style="margin:4px 0 0;font-size:13px;color:#7c8493;">Total <?php echo e((string) $totalJenisProduksi); ?> jenis produksi terdaftar</p>
                    </div><a href="create.php" class="btn-primary-custom"><i class="bi bi-plus-lg"></i> Tambah Jenis</a>
                </div><?php if ($successMessage !== ''): ?><div class="card-panel" style="border-left:4px solid #2f7d32;margin-bottom:16px;color:#1f5f24;"><?php echo e($successMessage); ?></div><?php endif; ?><?php if ($errorMessage !== ''): ?><div class="card-panel" style="border-left:4px solid #e05252;margin-bottom:16px;">
                        <p style="margin:0;font-weight:600;color:#9f1f1f;">Terjadi kesalahan:</p>
                        <p style="margin:4px 0 0;color:#6b2121;"><?php echo e($errorMessage); ?></p>
                    </div><?php endif; ?><div class="card-panel" style="margin-bottom:0">
                    <form method="GET" class="table-toolbar">
                        <div class="search-box"><i class="bi bi-search"></i><input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari nama atau satuan..." /></div><button type="submit" class="btn-secondary-custom">Cari</button>
                    </form>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Jenis Produksi</th>
                                    <th>Satuan</th>
                                    <th style="text-align:center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody><?php if ($jenisProduksiList === []): ?><tr>
                                        <td colspan="3" style="text-align:center;padding:20px;color:#7c8493;">Belum ada data jenis produksi.</td>
                                    </tr><?php else: ?><?php foreach ($jenisProduksiList as $jenis): ?><tr>
                                        <td><?php echo e($jenis['nama_produksi']); ?></td>
                                        <td><?php echo e($jenis['satuan']); ?></td>
                                        <td style="text-align:center;">
                                            <div style="display:flex;gap:6px;justify-content:center;"><a href="update.php?id=<?php echo (int) $jenis['id_jenis_produksi']; ?>" class="action-btn" title="Edit"><i class="bi bi-pencil"></i></a>
                                                <form method="POST" style="margin:0;" onsubmit="return confirm('Hapus jenis produksi ini?');"><input type="hidden" name="delete_id" value="<?php echo (int) $jenis['id_jenis_produksi']; ?>" /><button type="submit" class="action-btn danger" title="Hapus"><i class="bi bi-trash"></i></button></form>
                                            </div>
                                        </td>
                                    </tr><?php endforeach; ?><?php endif; ?></tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>