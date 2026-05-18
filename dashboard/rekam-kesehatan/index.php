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
            $deleteStmt = $pdo->prepare('DELETE FROM tb_kesehatan WHERE id_kesehatan = :id_kesehatan');
            $deleteStmt->execute(['id_kesehatan' => $deleteId]);
            header('Location: index.php?success=Data rekam kesehatan berhasil dihapus');
            exit;
        } catch (Throwable $exception) {
            $errorMessage = 'Gagal menghapus data: ' . $exception->getMessage();
        }
    }
}

$sql = "SELECT 
			k.id_kesehatan,
			k.tgl_pemeriksaan,
			CONCAT('TRN-', LPAD(t.id_ternak, 3, '0')) AS kode_ternak,
			jt.nama_jenis,
			p.nama_petugas,
			td.nama_tindakan,
			k.diagnosa,
			k.berat_badan,
			k.suhu_badan
		FROM tb_kesehatan k
		INNER JOIN tb_ternak t ON t.id_ternak = k.id_ternak
		INNER JOIN tb_jenis_ternak jt ON jt.id_jenis_ternak = t.id_jenis_ternak
		INNER JOIN tb_petugas p ON p.id_petugas = k.id_petugas
		INNER JOIN tb_tindakan td ON td.id_tindakan = k.id_tindakan";

$conditions = [];
$params = [];

if ($q !== '') {
    $conditions[] = "(
		CAST(k.id_kesehatan AS CHAR) LIKE :q OR
		CONCAT('TRN-', LPAD(t.id_ternak, 3, '0')) LIKE :q OR
		jt.nama_jenis LIKE :q OR
		p.nama_petugas LIKE :q OR
		td.nama_tindakan LIKE :q OR
		k.diagnosa LIKE :q
	)";
    $params['q'] = '%' . $q . '%';
}

if ($conditions !== []) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' ORDER BY k.id_kesehatan DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$kesehatanList = $stmt->fetchAll();

$totalStmt = $pdo->query('SELECT COUNT(*) FROM tb_kesehatan');
$totalKesehatan = (int) $totalStmt->fetchColumn();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rekam Kesehatan — LivestockID</title>
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
                    <li class="nav-item" style="margin-left: 20px;"><a href="../rekam-kesehatan/tindakan/index.php" class="nav-link-item"><i class="bi bi-bandaid"></i><span>Tindakan</span></a></li>
                    <li class="nav-item"><a href="../catatan-produksi/index.php" class="nav-link-item"><i class="bi bi-journal-richtext"></i><span>Catatan Produksi</span></a></li>
                </ul>
            </nav>
            <div class="sidebar-footer"><a href="../../auth/login.php" class="nav-link-item"><i class="bi bi-box-arrow-left"></i><span>Keluar</span></a></div>
        </aside>
        <div class="main-area">
            <header class="topbar"><button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button><span class="topbar-title">Rekam Kesehatan</span></header>
            <main class="page-content">
                <div class="list-header">
                    <div>
                        <h2>Rekam Kesehatan</h2>
                        <p style="margin:4px 0 0;font-size:13px;color:#7c8493;">Total <?php echo e((string) $totalKesehatan); ?> catatan pemeriksaan</p>
                    </div><a href="create.php" class="btn-primary-custom"><i class="bi bi-plus-lg"></i> Tambah Catatan</a>
                </div><?php if ($successMessage !== ''): ?><div class="card-panel" style="border-left:4px solid #2f7d32;margin-bottom:16px;color:#1f5f24;"><?php echo e($successMessage); ?></div><?php endif; ?><?php if ($errorMessage !== ''): ?><div class="card-panel" style="border-left:4px solid #e05252;margin-bottom:16px;">
                        <p style="margin:0;font-weight:600;color:#9f1f1f;">Terjadi kesalahan:</p>
                        <p style="margin:4px 0 0;color:#6b2121;"><?php echo e($errorMessage); ?></p>
                    </div><?php endif; ?><div class="card-panel" style="margin-bottom:0">
                    <form method="GET" class="table-toolbar">
                        <div class="search-box"><i class="bi bi-search"></i><input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari ternak, petugas, tindakan, atau diagnosa..." /></div><button type="submit" class="btn-secondary-custom">Cari</button>
                    </form>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Ternak</th>
                                    <th>Petugas</th>
                                    <th>Tindakan</th>
                                    <th>Diagnosa</th>
                                    <th style="text-align:center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody><?php if ($kesehatanList === []): ?><tr>
                                        <td colspan="6" style="text-align:center;padding:20px;color:#7c8493;">Belum ada data rekam kesehatan.</td>
                                    </tr><?php else: ?><?php foreach ($kesehatanList as $item): ?><tr>
                                        <td><?php echo e(date('d M Y', strtotime((string) $item['tgl_pemeriksaan']))); ?></td>
                                        <td><strong><?php echo e($item['kode_ternak']); ?></strong><br /><span style="font-size:12px;color:#7c8493;"><?php echo e($item['nama_jenis']); ?></span></td>
                                        <td><?php echo e($item['nama_petugas']); ?></td>
                                        <td><?php echo e($item['nama_tindakan']); ?></td>
                                        <td><?php echo e($item['diagnosa'] !== null && $item['diagnosa'] !== '' ? $item['diagnosa'] : '-'); ?><br /><span style="font-size:12px;color:#7c8493;"><?php echo e($item['berat_badan'] !== null ? 'BB: ' . $item['berat_badan'] . ' kg' : ''); ?><?php echo e($item['suhu_badan'] !== null ? ($item['berat_badan'] !== null ? ' | ' : '') . 'Suhu: ' . $item['suhu_badan'] . '°C' : ''); ?></span></td>
                                        <td style="text-align:center;">
                                            <div style="display:flex;gap:6px;justify-content:center;"><a href="update.php?id=<?php echo (int) $item['id_kesehatan']; ?>" class="action-btn" title="Edit"><i class="bi bi-pencil"></i></a>
                                                <form method="POST" style="margin:0;" onsubmit="return confirm('Hapus catatan ini?');"><input type="hidden" name="delete_id" value="<?php echo (int) $item['id_kesehatan']; ?>" /><button type="submit" class="action-btn danger" title="Hapus"><i class="bi bi-trash"></i></button></form>
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