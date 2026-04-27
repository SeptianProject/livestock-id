<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$errors = [];
$successMessage = '';
$formData = [
  'nama_petugas' => '',
  'id_jabatan' => '',
  'no_telp' => '',
];

$params = [];
$jabatans = [];

try {
  $jabatanStmt = $pdo->query('SELECT * FROM tb_jabatan ORDER BY nama_jabatan ASC');
  $jabatans = $jabatanStmt->fetchAll();
} catch (Throwable $exception) {
  http_response_code(500);
  echo 'Gagal memuat data jabatan: ' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = trim((string) ($_POST['action'] ?? 'save'));

  foreach ($formData as $key => $defaultValue) {
    $formData[$key] = trim((string) ($_POST[$key] ?? $defaultValue));
  }

  if ($formData['nama_petugas'] === '') {
    $errors[] = 'Nama petugas wajib diisi.';
  } elseif (strlen($formData['nama_petugas']) > 100) {
    $errors[] = 'Nama petugas maksimal 100 karakter.';
  }

  if ($formData['id_jabatan'] === '') {
    $errors[] = 'Jabatan wajib diisi.';
  }

  if ($formData['no_telp'] === '') {
    $errors[] = 'Nomor telepon wajib diisi.';
  } elseif (!preg_match('/^0\d{9,12}$/', $formData['no_telp'])) {
    $errors[] = 'Nomor telepon tidak valid.';
  }


  if ($errors === []) {
    try {
      $insertStmt = $pdo->prepare(
        'INSERT INTO tb_petugas (nama_petugas, id_jabatan, no_telp)
                 VALUES (:nama_petugas, :id_jabatan, :no_telp)'
      );

      $insertStmt->execute([
        'nama_petugas' => $formData['nama_petugas'],
        'id_jabatan' => $formData['id_jabatan'],
        'no_telp' => $formData['no_telp'],
      ]);

      if ($action === 'save_new') {
        $successMessage = 'Data petugas berhasil disimpan. Silakan tambah data baru.';
        $formData = [
          'nama_petugas' => '',
          'id_jabatan' => '',
          'no_telp' => '',
        ];
      } else {
        header('Location: index.php?success=Data petugas berhasil ditambahkan');
        exit;
      }
    } catch (Throwable $exception) {
      $errors[] = 'Gagal menyimpan data: ' . $exception->getMessage();
    }
  }
}

?>

<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Petugas — LivestockID</title>
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
        <div style="display: flex; align-items: center; gap: 8px; flex: 1">
          <a
            href="index.php"
            style="
                color: #7c8493;
                font-size: 13px;
                display: flex;
                align-items: center;
                gap: 4px;
              "><i class="bi bi-chevron-left"></i> Petugas</a>
          <i
            class="bi bi-chevron-right"
            style="font-size: 11px; color: #b0b8c4"></i>
          <span class="topbar-title" style="font-size: 15px">Tambah Petugas</span>
        </div>
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
        <div class="page-header">
          <h1>Tambah Petugas Baru</h1>
          <p>
            Daftarkan akun petugas baru untuk mengakses sistem LivestockID.
          </p>
        </div>

        <form method="POST">
          <div class="form-card">
            <p class="form-section-title">Data Pribadi</p>
            <div
              style="
                  display: grid;
                  grid-template-columns: 1fr 1fr;
                  gap: 16px;
                ">
              <div>
                <label class="form-label" for="nama_petugas">Nama Lengkap <span style="color: #e05252">*</span></label>
                <input
                  type="text"
                  id="nama_petugas"
                  name="nama_petugas"
                  class="form-control-custom"
                  placeholder="Masukkan nama petugas"
                  maxlength="100"
                  required
                  value="<?php echo e($formData['nama_petugas']); ?>"
                  required />
              </div>
              <div>
                <label class="form-label" for="jabatan">Jabatan <span style="color: #e05252">*</span></label>
                <select
                  id="jabatan"
                  name="jabatan"
                  class="form-control-custom"
                  required>
                  <option value="" disabled selected>Pilih jabatan</option>
                  <?php foreach ($jabatans as $index => $jabatan): ?>
                    <option value="">
                      <?php echo e($jabatan['nama_jabatan']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label class="form-label" for="no_telp">No. Telepon <span style="color: #e05252">*</span></label>
                <input
                  type="text"
                  id="no_telp"
                  name="no_telp"
                  class="form-control-custom"
                  placeholder="Contoh: +62 812-3456-7890"
                  required
                  value="<?php echo e($formData['no_telp']); ?>" />
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary-custom">
                <i class="bi bi-check-lg"></i> Simpan Petugas
              </button>
              <button
                type="submit"
                name="action"
                value="save_new"
                class="btn-secondary-custom">
                <i class="bi bi-plus-circle"></i> Simpan &amp; Buat Baru
              </button>
              <a href="index.php" class="btn-secondary-custom"><i class="bi bi-x-lg"></i> Batal</a>
            </div>
          </div>
        </form>
      </main>
    </div>
  </div>
</body>

</html>