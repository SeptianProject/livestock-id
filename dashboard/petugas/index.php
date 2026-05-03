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
            no_telp
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
                            Total 18 petugas terdaftar
                        </p>
                    </div>
                    <a href="create.php" class="btn-primary-custom"><i class="bi bi-plus-lg"></i> Tambah Petugas</a>
                </div>

                <div class="card-panel" style="margin-bottom: 0">
                    <div class="table-toolbar">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input
                                type="text"
                                placeholder="Cari nama atau email petugas..." />
                        </div>
                        <select class="filter-select">
                            <option value="">Semua Jabatan</option>
                            <option>Dokter Hewan</option>
                            <option>Asisten Peternak</option>
                            <option>Petugas Produksi</option>
                            <option>Petugas Lapang</option>
                        </select>
                        <select class="filter-select">
                            <option value="">Semua Status</option>
                            <option>Aktif</option>
                            <option>Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Petugas</th>
                                    <th>Jabatan</th>
                                    <th>No. Telepon</th>
                                    <th>Email</th>
                                    <th>Bergabung</th>
                                    <th>Status</th>
                                    <th style="text-align: center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div
                                            style="display: flex; align-items: center; gap: 10px">
                                            <div
                                                class="petugas-avatar"
                                                style="width: 34px; height: 34px; font-size: 12px">
                                                BS
                                            </div>
                                            <div>
                                                <p
                                                    style="
                              margin: 0;
                              font-size: 13px;
                              font-weight: 600;
                              color: #000005;
                            ">
                                                    Budi Santoso
                                                </p>
                                                <p style="margin: 0; font-size: 11px; color: #7c8493">
                                                    #PTG-001
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Dokter Hewan</td>
                                    <td>+62 812-3456-7890</td>
                                    <td>budi.s@livestock.id</td>
                                    <td>Jan 2023</td>
                                    <td><span class="status-badge aktif">Aktif</span></td>
                                    <td style="text-align: center">
                                        <div
                                            style="
                          display: flex;
                          gap: 6px;
                          justify-content: center;
                        ">
                                            <button class="action-btn" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="action-btn danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div
                                            style="display: flex; align-items: center; gap: 10px">
                                            <div
                                                class="petugas-avatar"
                                                style="width: 34px; height: 34px; font-size: 12px">
                                                RW
                                            </div>
                                            <div>
                                                <p
                                                    style="
                              margin: 0;
                              font-size: 13px;
                              font-weight: 600;
                              color: #000005;
                            ">
                                                    Rina Wulandari
                                                </p>
                                                <p style="margin: 0; font-size: 11px; color: #7c8493">
                                                    #PTG-002
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Asisten Peternak</td>
                                    <td>+62 813-9876-5432</td>
                                    <td>rina.w@livestock.id</td>
                                    <td>Mar 2023</td>
                                    <td><span class="status-badge aktif">Aktif</span></td>
                                    <td style="text-align: center">
                                        <div
                                            style="
                          display: flex;
                          gap: 6px;
                          justify-content: center;
                        ">
                                            <button class="action-btn" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="action-btn danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div
                                            style="display: flex; align-items: center; gap: 10px">
                                            <div
                                                class="petugas-avatar"
                                                style="width: 34px; height: 34px; font-size: 12px">
                                                AF
                                            </div>
                                            <div>
                                                <p
                                                    style="
                              margin: 0;
                              font-size: 13px;
                              font-weight: 600;
                              color: #000005;
                            ">
                                                    Ahmad Fauzi
                                                </p>
                                                <p style="margin: 0; font-size: 11px; color: #7c8493">
                                                    #PTG-003
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Petugas Produksi</td>
                                    <td>+62 857-1234-5678</td>
                                    <td>ahmad.f@livestock.id</td>
                                    <td>Jun 2023</td>
                                    <td><span class="status-badge aktif">Aktif</span></td>
                                    <td style="text-align: center">
                                        <div
                                            style="
                          display: flex;
                          gap: 6px;
                          justify-content: center;
                        ">
                                            <button class="action-btn" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="action-btn danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div
                                            style="display: flex; align-items: center; gap: 10px">
                                            <div
                                                class="petugas-avatar"
                                                style="width: 34px; height: 34px; font-size: 12px">
                                                SN
                                            </div>
                                            <div>
                                                <p
                                                    style="
                              margin: 0;
                              font-size: 13px;
                              font-weight: 600;
                              color: #000005;
                            ">
                                                    Sari Ningrum
                                                </p>
                                                <p style="margin: 0; font-size: 11px; color: #7c8493">
                                                    #PTG-004
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Petugas Lapang</td>
                                    <td>+62 878-5678-9012</td>
                                    <td>sari.n@livestock.id</td>
                                    <td>Sep 2023</td>
                                    <td>
                                        <span class="status-badge nonaktif">Tidak Aktif</span>
                                    </td>
                                    <td style="text-align: center">
                                        <div
                                            style="
                          display: flex;
                          gap: 6px;
                          justify-content: center;
                        ">
                                            <button class="action-btn" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="action-btn danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="table-footer">
                            <span>Menampilkan 1–4 dari 18 data</span>
                            <div class="pagination-btns">
                                <button class="page-btn">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button class="page-btn active">1</button>
                                <button class="page-btn">2</button>
                                <button class="page-btn">3</button>
                                <button class="page-btn">4</button>
                                <button class="page-btn">5</button>
                                <button class="page-btn">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>