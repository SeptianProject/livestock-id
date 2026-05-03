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
$q = trim((string) ($_GET['q'] ?? ''));

$sql = "SELECT 
            id_ternak,
            CONCAT('T', LPAD(id_ternak, 3, '0')) AS kode_ternak,
            id_jenis_ternak,
            tgl_lahir,
            jenis_kelamin,
            id_kandang
        FROM tb_ternak";

$conditions = [];
$params = [];

if ($q !== '') {
    $conditions[] = "(
        CAST(id_ternak AS CHAR) LIKE :q OR 
        CONCAT('T', LPAD(id_ternak, 3, '0')) LIKE :q OR 
        jenis_kelamin LIKE :q OR 
        tgl_lahir LIKE :q
    )";
    $params['q'] = '%' . $q . '%';
}

if ($conditions !== []) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

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
            <a href="index.php" class="nav-link-item active"><i class="bi bi-box-seam"></i><span>Ternak</span></a>
          </li>
          <li class="nav-item">
            <a href="../kandang/index.php" class="nav-link-item"><i class="bi bi-house-door"></i><span>Kandang</span></a>
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
        <span class="topbar-title">Ternak</span>
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
            <h2>Daftar Ternak</h2>
            <p style="margin: 4px 0 0; font-size: 13px; color: #7c8493">
              Total 248 ekor ternak terdaftar
            </p>
          </div>
          <a href="create.php" class="btn-primary-custom"><i class="bi bi-plus-lg"></i> Tambah Ternak</a>
        </div>

        <div class="card-panel" style="margin-bottom: 0">
          <div class="table-toolbar">
            <div class="search-box">
              <i class="bi bi-search"></i>
              <input type="text" placeholder="Cari ID atau nama ternak..." />
            </div>
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST") {
              $id_ternak =
                $_POST["id_ternak"];
              $jenis_ternak = $_POST["jenis_ternak"];
              $jenis_kelamin = $_POST["jenis_kelamin"];
              $tanggal_lahir =
                $_POST["tanggal_lahir"];
              $berat_badan = $_POST["berat_badan"];
              $warna = $_POST["warna"];
              $id_kandang = $_POST["id_kandang"];
              $status_kandang = $_POST["status_kandang"];
              $catatan =
                $_POST["catatan"];
            } ?>

            <select class="filter-select">
              <option value="">Semua Jenis</option>
              <option>Sapi Perah</option>
              <option>Sapi Potong</option>
              <option>Kambing PE</option>
              <option>Domba Merino</option>
            </select>
            <select class="filter-select">
              <option value="">Semua Status</option>
              <option>Sehat</option>
              <option>Sakit</option>
              <option>Observasi</option>
            </select>
            <select class="filter-select">
              <option value="">Semua Kandang</option>
              <option>KDG-01</option>
              <option>KDG-02</option>
              <option>KDG-03</option>
            </select>
          </div>

          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID Ternak</th>
                  <th>Jenis Ternak</th>
                  <th>Jenis Kelamin</th>
                  <th>Tanggal Lahir</th>
                  <th>Kandang</th>
                  <th>Status Kesehatan</th>
                  <th style="text-align: center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>#TRN-042</strong></td>
                  <td>Sapi Perah</td>
                  <td>
                    <span
                      style="
                          display: inline-flex;
                          align-items: center;
                          gap: 5px;
                        "><i
                        class="bi bi-gender-female"
                        style="color: #ec4899"></i>
                      Betina</span>
                  </td>
                  <td>12 Jan 2022</td>
                  <td>KDG-01</td>
                  <td>
                    <span class="status-badge observasi">Observasi</span>
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
                <tr>
                  <td><strong>#TRN-117</strong></td>
                  <td>Kambing PE</td>
                  <td>
                    <span
                      style="
                          display: inline-flex;
                          align-items: center;
                          gap: 5px;
                        "><i
                        class="bi bi-gender-male"
                        style="color: #3b82f6"></i>
                      Jantan</span>
                  </td>
                  <td>03 Jun 2023</td>
                  <td>KDG-02</td>
                  <td><span class="status-badge sehat">Sehat</span></td>
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
                  <td><strong>#TRN-205</strong></td>
                  <td>Sapi Perah</td>
                  <td>
                    <span
                      style="
                          display: inline-flex;
                          align-items: center;
                          gap: 5px;
                        "><i
                        class="bi bi-gender-female"
                        style="color: #ec4899"></i>
                      Betina</span>
                  </td>
                  <td>07 Sep 2021</td>
                  <td>KDG-01</td>
                  <td><span class="status-badge sakit">Sakit</span></td>
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
                  <td><strong>#TRN-089</strong></td>
                  <td>Domba Merino</td>
                  <td>
                    <span
                      style="
                          display: inline-flex;
                          align-items: center;
                          gap: 5px;
                        "><i
                        class="bi bi-gender-male"
                        style="color: #3b82f6"></i>
                      Jantan</span>
                  </td>
                  <td>22 Feb 2023</td>
                  <td>KDG-03</td>
                  <td><span class="status-badge sehat">Sehat</span></td>
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
                  <td><strong>#TRN-033</strong></td>
                  <td>Sapi Potong</td>
                  <td>
                    <span
                      style="
                          display: inline-flex;
                          align-items: center;
                          gap: 5px;
                        "><i
                        class="bi bi-gender-male"
                        style="color: #3b82f6"></i>
                      Jantan</span>
                  </td>
                  <td>15 Nov 2020</td>
                  <td>KDG-02</td>
                  <td>
                    <span class="status-badge observasi">Observasi</span>
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
              <span>Menampilkan 1–5 dari 248 data</span>
              <div class="pagination-btns">
                <button class="page-btn">
                  <i class="bi bi-chevron-left"></i>
                </button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn">...</button>
                <button class="page-btn">50</button>
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