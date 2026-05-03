<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$successMessage = '';
$errorMessage = '';
$q = trim((string) ($_GET['q'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deleteId = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);

    if ($deleteId) {
        try {
            $deleteStmt = $pdo->prepare('DELETE FROM tb_ternak WHERE id_ternak = :id_ternak');
            $deleteStmt->execute(['id_ternak' => $deleteId]);

            header('Location: index.php?success=Data ternak berhasil dihapus');
            exit;
        } catch (Throwable $exception) {
            $errorMessage = 'Gagal menghapus data: ' . $exception->getMessage();
        }
    }
}

$successMessage = trim((string) ($_GET['success'] ?? ''));

$sql = "SELECT 
            t.id_ternak,
            CONCAT('TRN-', LPAD(t.id_ternak, 3, '0')) AS kode_ternak,
            jt.nama_jenis,
            t.tgl_lahir,
            t.jenis_kelamin,
            k.nama_kandang,
            k.lokasi
        FROM tb_ternak t
        INNER JOIN tb_jenis_ternak jt ON jt.id_jenis_ternak = t.id_jenis_ternak
        INNER JOIN tb_kandang k ON k.id_kandang = t.id_kandang";

$conditions = [];
$params = [];

if ($q !== '') {
    $conditions[] = "(
        CAST(t.id_ternak AS CHAR) LIKE :q OR
        jt.nama_jenis LIKE :q OR
        k.nama_kandang LIKE :q OR
        k.lokasi LIKE :q OR
        t.jenis_kelamin LIKE :q OR
        t.tgl_lahir LIKE :q
    )";
    $params['q'] = '%' . $q . '%';
}

if ($conditions !== []) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' ORDER BY t.id_ternak DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ternakList = $stmt->fetchAll();

$totalStmt = $pdo->query('SELECT COUNT(*) FROM tb_ternak');
$totalTernak = (int) $totalStmt->fetchColumn();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ternak — LivestockID</title>
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
                <p class="nav-section-label">Pencatatan</p>
                <ul style="list-style: none; padding: 0; margin: 0">
                    <li class="nav-item"><a href="../rekam-kesehatan/index.php" class="nav-link-item"><i class="bi bi-heart-pulse"></i><span>Rekam Kesehatan</span></a></li>
                    <li class="nav-item"><a href="../catatan-produksi/index.php" class="nav-link-item"><i class="bi bi-journal-text"></i><span>Catatan Produksi</span></a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../../auth/login.php" class="nav-link-item"><i class="bi bi-box-arrow-left"></i><span>Keluar</span></a>
            </div>
        </aside>

        <div class="main-area">
            <header class="topbar">
                <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button>
                <span class="topbar-title">Ternak</span>
                <div class="topbar-actions">
                    <a href="../profile/index.php" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                        <div class="topbar-user-info"><span class="name">Admin</span><span class="role">Administrator</span></div>
                        <div class="topbar-avatar">AS</div>
                    </a>
                </div>
            </header>

            <main class="page-content">
                <div class="list-header">
                    <div>
                        <h2>Daftar Ternak</h2>
                        <p style="margin: 4px 0 0; font-size: 13px; color: #7c8493">Total <?php echo e((string) $totalTernak); ?> ternak terdaftar</p>
                    </div>
                    <a href="create.php" class="btn-primary-custom"><i class="bi bi-plus-lg"></i> Tambah Ternak</a>
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
                    <form method="GET" class="table-toolbar">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari kode, jenis, kandang, atau tanggal..." />
                        </div>
                        <button type="submit" class="btn-secondary-custom">Cari</button>
                    </form>

                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID Ternak</th>
                                    <th>Jenis Ternak</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Kandang</th>
                                    <th style="text-align:center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($ternakList === []): ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center;padding:20px;color:#7c8493;">Belum ada data ternak.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($ternakList as $ternak): ?>
                                        <tr>
                                            <td><strong><?php echo e($ternak['kode_ternak']); ?></strong></td>
                                            <td><?php echo e($ternak['nama_jenis']); ?></td>
                                            <td><?php echo e(ucfirst($ternak['jenis_kelamin'])); ?></td>
                                            <td><?php echo e(date('d M Y', strtotime((string) $ternak['tgl_lahir']))); ?></td>
                                            <td><?php echo e($ternak['nama_kandang']); ?></td>
                                            <td style="text-align:center;">
                                                <div style="display:flex;gap:6px;justify-content:center;">
                                                    <a class="action-btn" title="Edit" href="update.php?id=<?php echo (int) $ternak['id_ternak']; ?>"><i class="bi bi-pencil"></i></a>
                                                    <form method="POST" onsubmit="return confirm('Hapus data ternak ini?');" style="margin:0;">
                                                        <input type="hidden" name="delete_id" value="<?php echo (int) $ternak['id_ternak']; ?>" />
                                                        <button class="action-btn danger" title="Hapus" type="submit"><i class="bi bi-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>