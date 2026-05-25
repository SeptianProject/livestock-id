<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    return;
}

$accountName = isset($_SESSION['username']) && is_string($_SESSION['username']) && $_SESSION['username'] !== '' ? $_SESSION['username'] : 'Admin';
$accountRole = isset($_SESSION['role']) && is_string($_SESSION['role']) && $_SESSION['role'] !== '' ? $_SESSION['role'] : 'admin';

$accountRoleLabelMap = [
    'admin' => 'Administrator',
    'dokter' => 'Dokter',
    'petugas_lapang' => 'Petugas Lapang',
    'petugas_produksi' => 'Petugas Produksi',
];

$accountRoleLabel = $accountRoleLabelMap[$accountRole] ?? ucfirst(str_replace('_', ' ', $accountRole));
$accountData = isset($profileUser) && is_array($profileUser) ? $profileUser : [];
$accountEmail = '';
$accountFullName = '';

if (function_exists('profileResolveValue')) {
    $accountEmail = profileResolveValue($accountData, ['email', 'email_user']);
    $accountFullName = profileResolveValue($accountData, ['nama_lengkap', 'full_name', 'nama_user', 'nama']);
}

$accountUsername = (string) ($accountData['username'] ?? $accountName);
?>

<div class="card-panel">
    <div class="card-panel-header" style="margin-bottom: 16px">
        <h3>Informasi Akun</h3>
    </div>
    <div style="display: flex; flex-direction: column; gap: 12px">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f5f7f9; border-radius: 10px;">
            <span style="font-size: 12px; color: #7c8493">Status Akun</span>
            <span class="status-badge aktif">Aktif</span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f5f7f9; border-radius: 10px;">
            <span style="font-size: 12px; color: #7c8493">Tingkat Akses</span>
            <span style="font-size: 12px; font-weight: 600; color: #96ca50"><i class="bi bi-shield-check"></i> <?php echo e($accountRoleLabel); ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f5f7f9; border-radius: 10px;">
            <span style="font-size: 12px; color: #7c8493">Username</span>
            <span style="font-size: 12px; font-weight: 500; color: #3d4658"><?php echo e($accountUsername); ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f5f7f9; border-radius: 10px;">
            <span style="font-size: 12px; color: #7c8493">Nama Lengkap</span>
            <span style="font-size: 12px; font-weight: 500; color: #3d4658"><?php echo e($accountFullName !== '' ? $accountFullName : 'Belum diisi'); ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f5f7f9; border-radius: 10px;">
            <span style="font-size: 12px; color: #7c8493">Email</span>
            <span style="font-size: 12px; font-weight: 500; color: #3d4658"><?php echo e($accountEmail !== '' ? $accountEmail : 'Belum diisi'); ?></span>
        </div>
    </div>
</div>