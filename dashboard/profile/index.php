<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil — LivestockID</title>
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
            <a href="../petugas/index.php" class="nav-link-item"><i class="bi bi-people"></i><span>Petugas</span></a>
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
        <span class="topbar-title">Profil Saya</span>
        <div class="topbar-actions">
          <div class="topbar-notif">
            <i class="bi bi-bell"></i><span class="notif-badge"></span>
          </div>
          <a
            href="index.php"
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
        <!-- Profile Header Card -->
        <div class="profile-header-card">
          <div class="profile-avatar-lg" id="avatarDisplay">AS</div>
          <div class="profile-header-info">
            <h2>Admin Sistem</h2>
            <p>
              <i class="bi bi-shield-check" style="margin-right: 6px"></i>Administrator &nbsp;|&nbsp;
              <i class="bi bi-envelope" style="margin-right: 6px"></i>admin@livestock.id
            </p>
            <p style="margin-top: 8px; opacity: 0.7; font-size: 12px">
              <i class="bi bi-calendar3" style="margin-right: 6px"></i>Bergabung sejak Januari 2024
            </p>
          </div>
          <div style="margin-left: auto">
            <label
              for="avatarInput"
              style="
                  display: inline-flex;
                  align-items: center;
                  gap: 8px;
                  background: rgba(255, 255, 255, 0.2);
                  border: 1px solid rgba(255, 255, 255, 0.35);
                  color: #fff;
                  padding: 9px 16px;
                  border-radius: 10px;
                  cursor: pointer;
                  font-size: 13px;
                  font-weight: 500;
                  transition: background 0.2s;
                "
              onmouseover="this.style.background = 'rgba(255,255,255,.3)'"
              onmouseout="this.style.background = 'rgba(255,255,255,.2)'">
              <i class="bi bi-camera"></i> Ganti Foto
            </label>
            <input
              type="file"
              id="avatarInput"
              accept="image/*"
              style="display: none"
              onchange="previewAvatar(event)" />
          </div>
        </div>

        <div class="profile-grid">
          <!-- Edit Profile Form -->
          <div class="card-panel">
            <div class="card-panel-header" style="margin-bottom: 20px">
              <div>
                <h3>Edit Informasi Profil</h3>
                <p class="subtitle">
                  Perbarui data diri dan informasi akun Anda.
                </p>
              </div>
            </div>

            <form action="" method="POST">
              <p class="form-section-title">Data Pribadi</p>
              <div
                style="
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 14px;
                  ">
                <div>
                  <label class="form-label" for="namaDepan">Nama Depan</label>
                  <input
                    type="text"
                    id="namaDepan"
                    name="nama_depan"
                    class="form-control-custom"
                    value="Admin" />
                </div>
                <div>
                  <label class="form-label" for="namaBelakang">Nama Belakang</label>
                  <input
                    type="text"
                    id="namaBelakang"
                    name="nama_belakang"
                    class="form-control-custom"
                    value="Sistem" />
                </div>
                <div>
                  <label class="form-label" for="noHP">No. Telepon</label>
                  <input
                    type="tel"
                    id="noHP"
                    name="telepon"
                    class="form-control-custom"
                    value="+62 812-0000-0000" />
                </div>
                <div>
                  <label class="form-label" for="jabatanProfil">Jabatan</label>
                  <input
                    type="text"
                    id="jabatanProfil"
                    name="jabatan"
                    class="form-control-custom"
                    value="Administrator"
                    readonly
                    style="background: #f5f7f9; cursor: not-allowed" />
                </div>
                <div style="grid-column: 1/-1">
                  <label class="form-label" for="emailProfil">Alamat Email</label>
                  <input
                    type="email"
                    id="emailProfil"
                    name="email"
                    class="form-control-custom"
                    value="admin@livestock.id" />
                </div>
                <div style="grid-column: 1/-1">
                  <label class="form-label" for="alamat">Alamat</label>
                  <textarea
                    id="alamat"
                    name="alamat"
                    class="form-control-custom"
                    rows="2"
                    style="resize: vertical">
Jl. Peternakan No. 1, Boyolali, Jawa Tengah</textarea>
                </div>
              </div>

              <p class="form-section-title" style="margin-top: 24px">
                Biografi
              </p>
              <textarea
                name="bio"
                class="form-control-custom"
                rows="3"
                style="resize: vertical"
                placeholder="Ceritakan sedikit tentang Anda...">
Administrator sistem LivestockID yang bertanggung jawab atas pengelolaan data ternak, kandang, dan petugas.</textarea>

              <div class="form-actions" style="margin-top: 20px">
                <button type="submit" class="btn-primary-custom">
                  <i class="bi bi-check-lg"></i> Simpan Perubahan
                </button>
                <button type="reset" class="btn-secondary-custom">
                  <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
              </div>
            </form>
          </div>

          <!-- Right Column -->
          <div style="display: flex; flex-direction: column; gap: 20px">
            <!-- Change Password -->
            <div class="card-panel">
              <div class="card-panel-header" style="margin-bottom: 18px">
                <div>
                  <h3>Ubah Kata Sandi</h3>
                  <p class="subtitle">Gunakan sandi yang kuat dan unik.</p>
                </div>
              </div>
              <form action="" method="POST">
                <div style="display: flex; flex-direction: column; gap: 12px">
                  <div>
                    <label class="form-label" for="sandiLama">Kata Sandi Lama</label>
                    <input
                      type="password"
                      id="sandiLama"
                      name="sandi_lama"
                      class="form-control-custom"
                      placeholder="Masukkan sandi saat ini" />
                  </div>
                  <div>
                    <label class="form-label" for="sandiBaru">Kata Sandi Baru</label>
                    <input
                      type="password"
                      id="sandiBaru"
                      name="sandi_baru"
                      class="form-control-custom"
                      placeholder="Minimal 8 karakter"
                      minlength="8" />
                  </div>
                  <div>
                    <label class="form-label" for="konfirmasiSandi">Konfirmasi Sandi Baru</label>
                    <input
                      type="password"
                      id="konfirmasiSandi"
                      name="sandi_konfirmasi"
                      class="form-control-custom"
                      placeholder="Ulangi sandi baru" />
                  </div>
                </div>
                <div style="margin-top: 16px">
                  <button
                    type="submit"
                    class="btn-primary-custom"
                    style="width: 100%">
                    <i class="bi bi-lock"></i> Perbarui Sandi
                  </button>
                </div>
              </form>
            </div>

            <!-- Account Info -->
            <div class="card-panel">
              <div class="card-panel-header" style="margin-bottom: 16px">
                <h3>Informasi Akun</h3>
              </div>
              <div style="display: flex; flex-direction: column; gap: 12px">
                <div
                  style="
                      display: flex;
                      justify-content: space-between;
                      align-items: center;
                      padding: 10px 14px;
                      background: #f5f7f9;
                      border-radius: 10px;
                    ">
                  <span style="font-size: 12px; color: #7c8493">Status Akun</span>
                  <span class="status-badge aktif">Aktif</span>
                </div>
                <div
                  style="
                      display: flex;
                      justify-content: space-between;
                      align-items: center;
                      padding: 10px 14px;
                      background: #f5f7f9;
                      border-radius: 10px;
                    ">
                  <span style="font-size: 12px; color: #7c8493">Tingkat Akses</span>
                  <span
                    style="font-size: 12px; font-weight: 600; color: #96ca50"><i class="bi bi-shield-check"></i> Administrator</span>
                </div>
                <div
                  style="
                      display: flex;
                      justify-content: space-between;
                      align-items: center;
                      padding: 10px 14px;
                      background: #f5f7f9;
                      border-radius: 10px;
                    ">
                  <span style="font-size: 12px; color: #7c8493">Login Terakhir</span>
                  <span
                    style="font-size: 12px; font-weight: 500; color: #3d4658">09 Mar 2026, 08:32</span>
                </div>
                <div
                  style="
                      display: flex;
                      justify-content: space-between;
                      align-items: center;
                      padding: 10px 14px;
                      background: #f5f7f9;
                      border-radius: 10px;
                    ">
                  <span style="font-size: 12px; color: #7c8493">Bergabung Sejak</span>
                  <span
                    style="font-size: 12px; font-weight: 500; color: #3d4658">15 Jan 2024</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    function previewAvatar(event) {
      const file = event.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (e) => {
        const display = document.getElementById("avatarDisplay");
        display.innerHTML = `<img src="${e.target.result}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
      };
      reader.readAsDataURL(file);
    }
  </script>
</body>

</html>