<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit;
}

$profileName = isset($_SESSION['username']) && is_string($_SESSION['username']) && $_SESSION['username'] !== '' ? $_SESSION['username'] : 'Admin';
$profileRole = isset($_SESSION['role']) && is_string($_SESSION['role']) && $_SESSION['role'] !== '' ? $_SESSION['role'] : 'admin';

$profileRoleLabelMap = [
    'admin' => 'Administrator',
    'dokter' => 'Dokter',
    'petugas_lapang' => 'Petugas Lapang',
    'petugas_produksi' => 'Petugas Produksi',
];

$profileRoleLabel = $profileRoleLabelMap[$profileRole] ?? ucfirst(str_replace('_', ' ', $profileRole));
$profileAvatarInitials = strtoupper(substr($profileName, 0, 2));
?>
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
                            <span class="name"><?php echo htmlspecialchars($profileName, ENT_QUOTES, 'UTF-8'); ?></span><span class="role"><?php echo htmlspecialchars($profileRoleLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="topbar-avatar"><?php echo htmlspecialchars($profileAvatarInitials, ENT_QUOTES, 'UTF-8'); ?></div>
                    </a>
                </div>
            </header>

            <main class="page-content">
                <!-- Profile Header Card -->
                <div class="profile-header-card">
                    <div class="profile-avatar-lg" id="avatarDisplay"><?php echo htmlspecialchars($profileAvatarInitials, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="profile-header-info">
                        <h2><?php echo htmlspecialchars($profileName, ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p>
                            <i class="bi bi-shield-check" style="margin-right: 6px"></i><?php echo htmlspecialchars($profileRoleLabel, ENT_QUOTES, 'UTF-8'); ?> &nbsp;|&nbsp;
                            <i class="bi bi-envelope" style="margin-right: 6px"></i><?php echo htmlspecialchars((string) ($_SESSION['email'] ?? 'admin@livestock.id'), ENT_QUOTES, 'UTF-8'); ?>
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
                    <?php include __DIR__ . '/update-profile.php'; ?>

                    <!-- Right Column -->
                    <div style="display: flex; flex-direction: column; gap: 20px">
                        <!-- Change Password -->
                        <?php include __DIR__ . '/update-password.php'; ?>

                        <!-- Account Info -->
                        <?php include __DIR__ . '/account-information.php'; ?>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (!file) return;
            const display = document.getElementById("avatarDisplay");
            const imageUrl = URL.createObjectURL(file);
            display.innerHTML = `<img src="${imageUrl}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
        }
    </script>
</body>

</html>