<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
  header('Location: ../auth/login.php');
  exit;
}

$name = isset($_SESSION['username']) && is_string($_SESSION['username']) && $_SESSION['username'] !== '' ? $_SESSION['username'] : 'Admin';
$role = isset($_SESSION['role']) && is_string($_SESSION['role']) && $_SESSION['role'] !== '' ? $_SESSION['role'] : 'admin';

$roleLabelMap = [
  'admin' => 'Administrator',
  'dokter' => 'Dokter',
  'petugas_lapang' => 'Petugas Lapang',
  'petugas_produksi' => 'Petugas Produksi',
];

$roleLabel = $roleLabelMap[$role] ?? ucfirst(str_replace('_', ' ', $role));
$avatarInitials = strtoupper(substr($name, 0, 2));

?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Overview — LivestockID</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div class="layout">
    <!-- ===== SIDEBAR ===== -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- ===== MAIN AREA ===== -->
    <div class="main-area">
      <!-- TOPBAR -->
      <header class="topbar">
        <button
          class="sidebar-toggle"
          id="sidebarToggle"
          onclick="toggleSidebar()">
          <i class="bi bi-list"></i>
        </button>
        <span class="topbar-title">Overview</span>
        <div class="topbar-actions">
          <div class="topbar-notif" title="Notifikasi">
            <i class="bi bi-bell"></i>
            <span class="notif-badge"></span>
          </div>
          <a
            href="profile/index.php"
            style="
                display: flex;
                align-items: center;
                gap: 10px;
                text-decoration: none;
              ">
            <div class="topbar-user-info">
              <span class="name"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></span>
              <span class="role"><?php echo htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="topbar-avatar" title="Lihat profil"><?php echo htmlspecialchars($avatarInitials, ENT_QUOTES, 'UTF-8'); ?></div>
          </a>
        </div>
      </header>

      <!-- PAGE CONTENT -->
      <main class="page-content">
        <!-- Page Header -->
        <div class="page-header">
          <h1>Selamat Datang, <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?> 👋</h1>
          <p>
            Pantau dan kelola seluruh aktivitas peternakan Anda dari satu
            tempat.
          </p>
        </div>

        <!-- ─── STAT CARDS ─── -->
        <div class="stat-grid">
          <!-- Total Ternak -->
          <div class="stat-card">
            <div class="stat-icon green">
              <i class="bi bi-box-seam-fill"></i>
            </div>
            <div class="stat-info">
              <p class="label">Total Ternak</p>
              <p class="value">248</p>
              <span class="badge-trend up"><i class="bi bi-arrow-up-short"></i>+12 bulan ini</span>
            </div>
          </div>
          <!-- Kapasitas Kandang -->
          <div class="stat-card">
            <div class="stat-icon blue">
              <i class="bi bi-house-door-fill"></i>
            </div>
            <div class="stat-info">
              <p class="label">Kapasitas Kandang</p>
              <p class="value">82%</p>
              <span class="badge-trend neutral"><i class="bi bi-dash"></i>248 / 300 slot</span>
            </div>
          </div>
          <!-- Produksi Hari Ini -->
          <div class="stat-card">
            <div class="stat-icon orange">
              <i class="bi bi-droplet-fill"></i>
            </div>
            <div class="stat-info">
              <p class="label">Produksi Hari Ini</p>
              <p class="value">1.240 L</p>
              <span class="badge-trend up"><i class="bi bi-arrow-up-short"></i>+5% dari kemarin</span>
            </div>
          </div>
          <!-- Dalam Penanganan -->
          <div class="stat-card">
            <div class="stat-icon red">
              <i class="bi bi-bandaid-fill"></i>
            </div>
            <div class="stat-info">
              <p class="label">Dalam Penanganan</p>
              <p class="value">7</p>
              <span class="badge-trend down"><i class="bi bi-arrow-down-short"></i>-2 dari kemarin</span>
            </div>
          </div>
        </div>

        <!-- ─── CHARTS ─── -->
        <div class="chart-grid">
          <!-- Tren Produksi -->
          <div class="card-panel">
            <div class="card-panel-header">
              <div>
                <h3>Tren Produksi</h3>
                <p class="subtitle">Fluktuasi jumlah produksi harian</p>
              </div>
              <div class="period-tabs">
                <button
                  class="period-tab active"
                  onclick="switchPeriod(this, '7')">
                  7 Hari
                </button>
                <button class="period-tab" onclick="switchPeriod(this, '30')">
                  30 Hari
                </button>
              </div>
            </div>
            <div class="chart-container">
              <canvas id="produksiChart" height="220"></canvas>
            </div>
          </div>

          <!-- Komposisi Ternak -->
          <div class="card-panel">
            <div class="card-panel-header">
              <div>
                <h3>Komposisi Ternak</h3>
                <p class="subtitle">Populasi per jenis ternak</p>
              </div>
            </div>
            <div
              class="chart-container"
              style="
                  display: flex;
                  flex-direction: column;
                  align-items: center;
                ">
              <canvas id="ternakChart" height="200" width="200"></canvas>
              <div
                id="ternakLegend"
                style="margin-top: 16px; width: 100%"></div>
            </div>
          </div>
        </div>

        <!-- ─── ACTIVITY ─── -->
        <div class="activity-grid">
          <!-- Laporan Kesehatan -->
          <div class="card-panel">
            <div class="card-panel-header">
              <div>
                <h3>Laporan Kesehatan Terbaru</h3>
                <p class="subtitle">5 pemeriksaan terakhir</p>
              </div>
              <a
                href="#"
                class="btn-outline-custom"
                style="font-size: 12px; padding: 6px 12px">
                <i class="bi bi-arrow-right"></i> Lihat Semua
              </a>
            </div>
            <div style="overflow-x: auto">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>ID Ternak</th>
                    <th>Jenis</th>
                    <th>Diagnosa</th>
                    <th>Petugas</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><strong>#TRN-042</strong></td>
                    <td>Sapi Perah</td>
                    <td>Mastitis ringan</td>
                    <td>Budi S.</td>
                    <td>09 Mar 2026</td>
                    <td>
                      <span class="status-badge observasi">Observasi</span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>#TRN-117</strong></td>
                    <td>Kambing PE</td>
                    <td>Sehat</td>
                    <td>Rina W.</td>
                    <td>09 Mar 2026</td>
                    <td><span class="status-badge sehat">Sehat</span></td>
                  </tr>
                  <tr>
                    <td><strong>#TRN-205</strong></td>
                    <td>Sapi Perah</td>
                    <td>Demam</td>
                    <td>Ahmad F.</td>
                    <td>08 Mar 2026</td>
                    <td><span class="status-badge sakit">Sakit</span></td>
                  </tr>
                  <tr>
                    <td><strong>#TRN-089</strong></td>
                    <td>Domba Merino</td>
                    <td>Sehat</td>
                    <td>Sari N.</td>
                    <td>08 Mar 2026</td>
                    <td><span class="status-badge sehat">Sehat</span></td>
                  </tr>
                  <tr>
                    <td><strong>#TRN-033</strong></td>
                    <td>Sapi Potong</td>
                    <td>Luka ringan</td>
                    <td>Budi S.</td>
                    <td>07 Mar 2026</td>
                    <td>
                      <span class="status-badge observasi">Observasi</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Petugas Aktif -->
          <div class="card-panel">
            <div class="card-panel-header">
              <div>
                <h3>Petugas Aktif</h3>
                <p class="subtitle">Melakukan tindakan hari ini</p>
              </div>
            </div>
            <div>
              <div class="petugas-item">
                <div class="petugas-avatar">BS</div>
                <div class="petugas-meta">
                  <p class="petugas-name">Budi Santoso</p>
                  <p class="petugas-role">Dokter Hewan</p>
                </div>
                <span class="status-badge aktif petugas-action">Aktif</span>
              </div>
              <div class="petugas-item">
                <div class="petugas-avatar">RW</div>
                <div class="petugas-meta">
                  <p class="petugas-name">Rina Wulandari</p>
                  <p class="petugas-role">Asisten Peternak</p>
                </div>
                <span class="status-badge aktif petugas-action">Aktif</span>
              </div>
              <div class="petugas-item">
                <div class="petugas-avatar">AF</div>
                <div class="petugas-meta">
                  <p class="petugas-name">Ahmad Fauzi</p>
                  <p class="petugas-role">Petugas Produksi</p>
                </div>
                <span class="status-badge aktif petugas-action">Aktif</span>
              </div>
              <div class="petugas-item">
                <div class="petugas-avatar">SN</div>
                <div class="petugas-meta">
                  <p class="petugas-name">Sari Ningrum</p>
                  <p class="petugas-role">Petugas Lapang</p>
                </div>
                <span class="status-badge nonaktif petugas-action">Istirahat</span>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
  <script src="script.js"></script>
</body>

</html>