<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tambah Ternak — LivestockID</title>
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
              <a href="../index.html" class="nav-link-item"
                ><i class="bi bi-speedometer2"></i><span>Overview</span></a
              >
            </li>
            <li class="nav-item">
              <a href="index.html" class="nav-link-item active"
                ><i class="bi bi-box-seam"></i><span>Ternak</span></a
              >
            </li>
            <li class="nav-item">
              <a href="../kandang/index.html" class="nav-link-item"
                ><i class="bi bi-house-door"></i><span>Kandang</span></a
              >
            </li>
            <li class="nav-item">
              <a href="../petugas/index.html" class="nav-link-item"
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
              href="index.html"
              style="
                color: #7c8493;
                font-size: 13px;
                display: flex;
                align-items: center;
                gap: 4px;
              "
              ><i class="bi bi-chevron-left"></i> Ternak</a
            >
            <i
              class="bi bi-chevron-right"
              style="font-size: 11px; color: #b0b8c4"></i>
            <span class="topbar-title" style="font-size: 15px"
              >Tambah Ternak</span
            >
          </div>
          <div class="topbar-actions">
            <div class="topbar-notif">
              <i class="bi bi-bell"></i><span class="notif-badge"></span>
            </div>
            <a
              href="../profile/index.html"
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
            <h1>Tambah Ternak Baru</h1>
            <p>
              Isi formulir di bawah untuk mendaftarkan ternak baru ke sistem.
            </p>
          </div>

          <form action="insert.php" method="POST">
            <div class="form-card">
              <p class="form-section-title">Informasi Dasar</p>
              <div
                style="
                  display: grid;
                  grid-template-columns: 1fr 1fr;
                  gap: 16px;
                ">
                <div>
                  <label class="form-label" for="idTernak"
                    >ID Ternak <span style="color: #e05252">*</span></label
                  >
                  <input
                    type="text"
                    id="idTernak"
                    name="id_ternak"
                    class="form-control-custom"
                    placeholder="Contoh: TRN-249"
                    required />
                </div>
                <div>
                  <label class="form-label" for="jenisTernak"
                    >Jenis Ternak <span style="color: #e05252">*</span></label
                  >
                  <select
                    id="jenisTernak"
                    name="jenis_ternak"
                    class="form-control-custom"
                    required>
                    <option value="" disabled selected>
                      Pilih jenis ternak
                    </option>
                    <option>Sapi Perah</option>
                    <option>Sapi Potong</option>
                    <option>Kambing PE</option>
                    <option>Domba Merino</option>
                  </select>
                </div>
                <div>
                  <label class="form-label" for="jenisKelamin"
                    >Jenis Kelamin <span style="color: #e05252">*</span></label
                  >
                  <select
                    id="jenisKelamin"
                    name="jenis_kelamin"
                    class="form-control-custom"
                    required>
                    <option value="" disabled selected>
                      Pilih jenis kelamin
                    </option>
                    <option value="jantan">Jantan</option>
                    <option value="betina">Betina</option>
                  </select>
                </div>
                <div>
                  <label class="form-label" for="tanggalLahir"
                    >Tanggal Lahir <span style="color: #e05252">*</span></label
                  >
                  <input
                    type="date"
                    id="tanggalLahir"
                    name="tanggal_lahir"
                    class="form-control-custom"
                    required />
                </div>
                <div>
                  <label class="form-label" for="beratBadan"
                    >Berat Badan (kg)</label
                  >
                  <input
                    type="number"
                    id="beratBadan"
                    name="berat_badan"
                    class="form-control-custom"
                    placeholder="Contoh: 350"
                    min="0" />
                </div>
                <div>
                  <label class="form-label" for="warna"
                    >Warna / Ciri Fisik</label
                  >
                  <input
                    type="text"
                    id="warna"
                    name="warna"
                    class="form-control-custom"
                    placeholder="Contoh: Coklat, berbintik putih" />
                </div>
              </div>

              <p class="form-section-title" style="margin-top: 28px">
                Penempatan
              </p>
              <div
                style="
                  display: grid;
                  grid-template-columns: 1fr 1fr;
                  gap: 16px;
                ">
                <div>
                  <label class="form-label" for="idKandang"
                    >Kandang <span style="color: #e05252">*</span></label
                  >
                  <select
                    id="idKandang"
                    name="id_kandang"
                    class="form-control-custom"
                    required>
                    <option value="" disabled selected>Pilih kandang</option>
                    <option value="KDG-01">KDG-01 (Sapi Perah A)</option>
                    <option value="KDG-02">KDG-02 (Sapi Potong B)</option>
                    <option value="KDG-03">KDG-03 (Kambing & Domba)</option>
                  </select>
                </div>
                <div>
                  <label class="form-label" for="statusKesehatan"
                    >Status Kesehatan Awal</label
                  >
                  <select
                    id="statusKesehatan"
                    name="status_kesehatan"
                    class="form-control-custom">
                    <option value="sehat" selected>Sehat</option>
                    <option value="observasi">Observasi</option>
                    <option value="sakit">Sakit</option>
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
                  <i class="bi bi-check-lg"></i> Simpan Ternak
                </button>
                <button
                  type="submit"
                  name="action"
                  value="save_new"
                  class="btn-secondary-custom">
                  <i class="bi bi-plus-circle"></i> Simpan & Buat Baru
                </button>
                <a href="index.html" class="btn-secondary-custom"
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
