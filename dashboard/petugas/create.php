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
              <a href="../index.php" class="nav-link-item"
                ><i class="bi bi-speedometer2"></i><span>Overview</span></a
              >
            </li>
            <li class="nav-item">
              <a href="../ternak/index.php" class="nav-link-item"
                ><i class="bi bi-box-seam"></i><span>Ternak</span></a
              >
            </li>
            <li class="nav-item">
              <a href="../kandang/index.php" class="nav-link-item"
                ><i class="bi bi-house-door"></i><span>Kandang</span></a
              >
            </li>
            <li class="nav-item">
              <a href="index.php" class="nav-link-item active"
                ><i class="bi bi-people"></i><span>Petugas</span></a
              >
            </li>
          </ul>
          <p class="nav-section-label">Pencatatan</p>
          <ul style="list-style: none; padding: 0; margin: 0">
            <li class="nav-item">
              <a href="#" class="nav-link-item"
                ><i class="bi bi-heart-pulse"></i
                ><span>Rekam Kesehatan</span></a
              >
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link-item"
                ><i class="bi bi-journal-text"></i
                ><span>Catatan Produksi</span></a
              >
            </li>
          </ul>
        </nav>
        <div class="sidebar-footer">
          <a href="../../auth/login.html" class="nav-link-item"
            ><i class="bi bi-box-arrow-left"></i><span>Keluar</span></a
          >
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
              "
              ><i class="bi bi-chevron-left"></i> Petugas</a
            >
            <i
              class="bi bi-chevron-right"
              style="font-size: 11px; color: #b0b8c4"></i>
            <span class="topbar-title" style="font-size: 15px"
              >Tambah Petugas</span
            >
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
                <span class="name">Admin</span
                ><span class="role">Administrator</span>
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

          <form action="insert.php" method="POST">
            <div class="form-card">
              <p class="form-section-title">Data Pribadi</p>
              <div
                style="
                  display: grid;
                  grid-template-columns: 1fr 1fr;
                  gap: 16px;
                ">
                <div>
                  <label class="form-label" for="namaLengkap"
                    >Nama Lengkap <span style="color: #e05252">*</span></label
                  >
                  <input
                    type="text"
                    id="namaLengkap"
                    name="nama"
                    class="form-control-custom"
                    placeholder="Masukkan nama lengkap"
                    required />
                </div>
                <div>
                  <label class="form-label" for="jabatan"
                    >Jabatan <span style="color: #e05252">*</span></label
                  >
                  <select
                    id="jabatan"
                    name="jabatan"
                    class="form-control-custom"
                    required>
                    <option value="" disabled selected>Pilih jabatan</option>
                    <option>Dokter Hewan</option>
                    <option>Asisten Peternak</option>
                    <option>Petugas Produksi</option>
                    <option>Petugas Lapang</option>
                    <option>Administrator</option>
                  </select>
                </div>
                <div>
                  <label class="form-label" for="noTelepon"
                    >No. Telepon <span style="color: #e05252">*</span></label
                  >
                  <input
                    type="tel"
                    id="noTelepon"
                    name="telepon"
                    class="form-control-custom"
                    placeholder="Contoh: +62 812-3456-7890"
                    required />
                </div>
                <div>
                  <label class="form-label" for="tanggalMasuk"
                    >Tanggal Bergabung
                    <span style="color: #e05252">*</span></label
                  >
                  <input
                    type="date"
                    id="tanggalMasuk"
                    name="tanggal_masuk"
                    class="form-control-custom"
                    required />
                </div>
              </div>

              <p class="form-section-title" style="margin-top: 28px">
                Akun &amp; Akses Sistem
              </p>
              <div
                style="
                  display: grid;
                  grid-template-columns: 1fr 1fr;
                  gap: 16px;
                ">
                <div>
                  <label class="form-label" for="email"
                    >Alamat Email <span style="color: #e05252">*</span></label
                  >
                  <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control-custom"
                    placeholder="petugas@livestock.id"
                    required />
                </div>
                <div>
                  <label class="form-label" for="password"
                    >Kata Sandi <span style="color: #e05252">*</span></label
                  >
                  <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control-custom"
                    placeholder="Minimal 8 karakter"
                    minlength="8"
                    required />
                </div>
                <div>
                  <label class="form-label" for="konfirmasiPassword"
                    >Konfirmasi Sandi
                    <span style="color: #e05252">*</span></label
                  >
                  <input
                    type="password"
                    id="konfirmasiPassword"
                    name="password_confirm"
                    class="form-control-custom"
                    placeholder="Ulangi kata sandi"
                    required />
                </div>
                <div>
                  <label class="form-label" for="statusPetugas">Status</label>
                  <select
                    id="statusPetugas"
                    name="status"
                    class="form-control-custom">
                    <option value="aktif" selected>Aktif</option>
                    <option value="nonaktif">Tidak Aktif</option>
                  </select>
                </div>
              </div>

              <p class="form-section-title" style="margin-top: 28px">
                Catatan Tambahan
              </p>
              <textarea
                name="catatan"
                class="form-control-custom"
                rows="3"
                placeholder="Keterangan tambahan (opsional)..."
                style="resize: vertical"></textarea>

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
                <a href="index.php" class="btn-secondary-custom"
                  ><i class="bi bi-x-lg"></i> Batal</a
                >
              </div>
            </div>
          </form>
        </main>
      </div>
    </div>
  </body>
</html>
