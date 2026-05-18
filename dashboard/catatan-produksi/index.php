<?php

declare(strict_types=1);

require_once __DIR__ . '../../../config/database.php';
require_once __DIR__ . '../../../config/helpers.php';

$successMessage = trim((string) ($_GET['success'] ?? ''));
$errorMessage = '';
$q = trim((string) ($_GET['q'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deleteId = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);

    if ($deleteId) {
        try {
            $deleteStmt = $pdo->prepare('DELETE FROM tb_produksi WHERE id_produksi = :id_produksi');
            $deleteStmt->execute(['id_produksi' => $deleteId]);
            header('Location: index.php?success=Data catatan produksi berhasil dihapus');
            exit;
        } catch (Throwable $exception) {
            $errorMessage = 'Gagal menghapus data: ' . $exception->getMessage();
        }
    }
}

$sql = "SELECT 
			pr.id_produksi,
			pr.tgl_produksi,
			CONCAT('TRN-', LPAD(t.id_ternak, 3, '0')) AS kode_ternak,
			jt.nama_jenis,
			jp.nama_produksi,
			jp.satuan,
			pr.jumlah_produksi
		FROM tb_produksi pr
		INNER JOIN tb_ternak t ON t.id_ternak = pr.id_ternak
		INNER JOIN tb_jenis_ternak jt ON jt.id_jenis_ternak = t.id_jenis_ternak
		INNER JOIN tb_jenis_produksi jp ON jp.id_jenis_produksi = pr.id_jenis_produksi";

$conditions = [];
$params = [];
if ($q !== '') {
    $conditions[] = "(
		CAST(pr.id_produksi AS CHAR) LIKE :q OR
		CONCAT('TRN-', LPAD(t.id_ternak, 3, '0')) LIKE :q OR
		jt.nama_jenis LIKE :q OR
		jp.nama_produksi LIKE :q
	)";
    $params['q'] = '%' . $q . '%';
}
if ($conditions !== []) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY pr.id_produksi DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produksiList = $stmt->fetchAll();

$totalStmt = $pdo->query('SELECT COUNT(*) FROM tb_produksi');
$totalProduksi = (int) $totalStmt->fetchColumn();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Catatan Produksi — LivestockID</title>
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
                    <li class="nav-item"><a href="../rekam-kesehatan/index.php" class="nav-link-item"><i class="bi bi-heart-pulse"></i><span>Rekam Kesehatan</span></a></li>
                    <li class="nav-item"><a href="../index.php" class="nav-link-item active"><i class="bi bi-journal-richtext"></i><span>Catatan Produksi</span></a></li>
                    <li class="nav-item" style="margin-left: 20px;"><a href="../catatan-produksi/jenis-produksi/index.php" class="nav-link-item"><i class="bi bi-droplet-half"></i><span>Jenis Produksi</span></a></li>
                </ul>
            </nav>
            <div class="sidebar-footer"><a href="../../auth/login.php" class="nav-link-item"><i class="bi bi-box-arrow-left"></i><span>Keluar</span></a></div>
        </aside>
        <div class="main-area">
            <header class="topbar"><button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button><span class="topbar-title">Catatan Produksi</span></header>
            <main class="page-content">
                <div class="list-header">
                    <div>
                        <h2>Catatan Produksi</h2>
                        <p style="margin:4px 0 0;font-size:13px;color:#7c8493;">Total <?php echo e((string) $totalProduksi); ?> catatan produksi</p>
                    </div><a href="create.php" class="btn-primary-custom"><i class="bi bi-plus-lg"></i> Tambah Catatan</a>
                </div><?php if ($successMessage !== ''): ?><div class="card-panel" style="border-left:4px solid #2f7d32;margin-bottom:16px;color:#1f5f24;"><?php echo e($successMessage); ?></div><?php endif; ?><?php if ($errorMessage !== ''): ?><div class="card-panel" style="border-left:4px solid #e05252;margin-bottom:16px;">
                        <p style="margin:0;font-weight:600;color:#9f1f1f;">Terjadi kesalahan:</p>
                        <p style="margin:4px 0 0;color:#6b2121;"><?php echo e($errorMessage); ?></p>
                    </div><?php endif; ?><div class="card-panel" style="margin-bottom:0">
                    <form method="GET" class="table-toolbar">
                        <div class="search-box"><i class="bi bi-search"></i><input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari ternak atau jenis produksi..." /></div><button type="submit" class="btn-secondary-custom">Cari</button>
                    </form>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Ternak</th>
                                    <th>Jenis Produksi</th>
                                    <th>Jumlah</th>
                                    <th style="text-align:center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody><?php if ($produksiList === []): ?><tr>
                                        <td colspan="5" style="text-align:center;padding:20px;color:#7c8493;">Belum ada data catatan produksi.</td>
                                    </tr><?php else: ?><?php foreach ($produksiList as $item): ?><tr>
                                        <td><?php echo e(date('d M Y', strtotime((string) $item['tgl_produksi']))); ?></td>
                                        <td><strong><?php echo e($item['kode_ternak']); ?></strong><br /><span style="font-size:12px;color:#7c8493;"><?php echo e($item['nama_jenis']); ?></span></td>
                                        <td><?php echo e($item['nama_produksi']); ?><br /><span style="font-size:12px;color:#7c8493;"><?php echo e($item['satuan']); ?></span></td>
                                        <td><?php echo e((string) $item['jumlah_produksi']); ?></td>
                                        <td style="text-align:center;">
                                            <div style="display:flex;gap:6px;justify-content:center;"><a href="update.php?id=<?php echo (int) $item['id_produksi']; ?>" class="action-btn" title="Edit"><i class="bi bi-pencil"></i></a>
                                                <form method="POST" style="margin:0;" onsubmit="return confirm('Hapus catatan ini?');"><input type="hidden" name="delete_id" value="<?php echo (int) $item['id_produksi']; ?>" /><button type="submit" class="action-btn danger" title="Hapus"><i class="bi bi-trash"></i></button></form>
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