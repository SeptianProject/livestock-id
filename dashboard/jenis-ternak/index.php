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
            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM tb_ternak WHERE id_jenis_ternak = :id_jenis_ternak');
            $checkStmt->execute(['id_jenis_ternak' => $deleteId]);
            if ((int) $checkStmt->fetchColumn() > 0) {
                $errorMessage = 'Jenis ternak tidak bisa dihapus karena masih dipakai oleh data ternak.';
            } else {
                $deleteStmt = $pdo->prepare('DELETE FROM tb_jenis_ternak WHERE id_jenis_ternak = :id_jenis_ternak');
                $deleteStmt->execute(['id_jenis_ternak' => $deleteId]);
                header('Location: index.php?success=Data jenis ternak berhasil dihapus');
                exit;
            }
        } catch (Throwable $exception) {
            $errorMessage = 'Gagal menghapus data: ' . $exception->getMessage();
        }
    }
}

$successMessage = trim((string) ($_GET['success'] ?? ''));

$sql = 'SELECT id_jenis_ternak, nama_jenis FROM tb_jenis_ternak';
$params = [];
if ($q !== '') {
    $sql .= ' WHERE (CAST(id_jenis_ternak AS CHAR) LIKE :q OR nama_jenis LIKE :q)';
    $params['q'] = '%' . $q . '%';
}
$sql .= ' ORDER BY nama_jenis ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jenisTernakList = $stmt->fetchAll();

$totalStmt = $pdo->query('SELECT COUNT(*) FROM tb_jenis_ternak');
$totalJenisTernak = (int) $totalStmt->fetchColumn();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Jenis Ternak — LivestockID</title>
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
                    <li class="nav-item"><a href="../ternak/index.php" class="nav-link-item"><i class="bi bi-box-seam"></i><span>Ternak</span></a></li>
                    <li class="nav-item"><a href="../kandang/index.php" class="nav-link-item"><i class="bi bi-house-door"></i><span>Kandang</span></a></li>
                    <li class="nav-item"><a href="../petugas/index.php" class="nav-link-item"><i class="bi bi-people"></i><span>Petugas</span></a></li>
                </ul>
                <p class="nav-section-label">Pengaturan</p>
                <ul style="list-style: none; padding: 0; margin: 0">
                    <li class="nav-item"><a href="index.php" class="nav-link-item active"><i class="bi bi-tags"></i><span>Jenis Ternak</span></a></li>
                </ul>
            </nav>
        </aside>

        <div class="main-area">
            <header class="topbar">
                <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button>
                <span class="topbar-title">Jenis Ternak</span>
            </header>

            <main class="page-content">
                <div class="list-header">
                    <div>
                        <h2>Daftar Jenis Ternak</h2>
                        <p style="margin: 4px 0 0; font-size: 13px; color: #7c8493">Total <?php echo e((string) $totalJenisTernak); ?> jenis ternak terdaftar</p>
                    </div>
                    <a href="create.php" class="btn-primary-custom"><i class="bi bi-plus-lg"></i> Tambah Jenis</a>
                </div>

                <?php if ($successMessage !== ''): ?>
                    <div class="card-panel" style="border-left: 4px solid #2f7d32; margin-bottom: 16px; color: #1f5f24;">
                        <i class="bi bi-check-circle" style="margin-right: 8px;"></i> <?php echo e($successMessage); ?>
                    </div>
                <?php endif; ?>

                <?php if ($errorMessage !== ''): ?>
                    <div class="card-panel" style="border-left: 4px solid #e05252; margin-bottom: 16px;">
                        <p style="margin:0;font-weight:600;color:#9f1f1f;">Terjadi kesalahan:</p>
                        <p style="margin:4px 0 0;color:#6b2121;"><?php echo e($errorMessage); ?></p>
                    </div>
                <?php endif; ?>

                <div class="card-panel" style="margin-bottom: 0">
                    <form method="GET" class="table-toolbar">
                        <div class="search-box"><i class="bi bi-search"></i><input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari nama jenis ternak..." /></div>
                        <button type="submit" class="btn-secondary-custom">Cari</button>
                    </form>

                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Jenis Ternak</th>
                                    <th style="text-align:center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($jenisTernakList === []): ?>
                                    <tr>
                                        <td colspan="2" style="text-align:center;padding:20px;color:#7c8493;">Belum ada data jenis ternak.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($jenisTernakList as $jenis): ?>
                                        <tr>
                                            <td>
                                                <div style="display:flex;align-items:center;gap:10px;">
                                                    <div class="petugas-avatar" style="width:34px;height:34px;font-size:12px;background:#e8eef7;color:#4f46e5;">JT</div>
                                                    <div>
                                                        <p style="margin:0;font-size:13px;font-weight:600;color:#000005;"><?php echo e($jenis['nama_jenis']); ?></p>
                                                        <p style="margin:0;font-size:11px;color:#7c8493;">#JT-<?php echo e(str_pad((string) $jenis['id_jenis_ternak'], 3, '0', STR_PAD_LEFT)); ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="text-align:center;">
                                                <div style="display:flex;gap:6px;justify-content:center;">
                                                    <a href="update.php?id=<?php echo (int) $jenis['id_jenis_ternak']; ?>" class="action-btn" title="Edit"><i class="bi bi-pencil"></i></a>
                                                    <form method="POST" style="margin:0;" onsubmit="return confirm('Hapus jenis ternak ini?');">
                                                        <input type="hidden" name="delete_id" value="<?php echo (int) $jenis['id_jenis_ternak']; ?>" />
                                                        <button type="submit" class="action-btn danger" title="Hapus"><i class="bi bi-trash"></i></button>
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