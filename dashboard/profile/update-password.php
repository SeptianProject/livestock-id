<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    return;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/helpers.php';

$passwordErrors = [];
$passwordSuccess = '';
$passwordUserId = (int) $_SESSION['user_id'];

$passwordUserStmt = $pdo->prepare('SELECT id_user, username, password FROM tb_user WHERE id_user = :id_user LIMIT 1');
$passwordUserStmt->execute(['id_user' => $passwordUserId]);
$passwordUser = $passwordUserStmt->fetch();

if (!$passwordUser) {
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string) ($_POST['password_action'] ?? '') === 'update_password') {
    $currentPassword = (string) ($_POST['current_password'] ?? '');
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($currentPassword === '') {
        $passwordErrors[] = 'Kata sandi lama wajib diisi.';
    }

    if ($newPassword === '') {
        $passwordErrors[] = 'Kata sandi baru wajib diisi.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($newPassword, 'UTF-8') : strlen($newPassword)) < 8) {
        $passwordErrors[] = 'Kata sandi baru minimal 8 karakter.';
    }

    if ($confirmPassword === '') {
        $passwordErrors[] = 'Konfirmasi kata sandi wajib diisi.';
    }

    if ($newPassword !== '' && $confirmPassword !== '' && $newPassword !== $confirmPassword) {
        $passwordErrors[] = 'Konfirmasi kata sandi tidak cocok.';
    }

    if ($passwordErrors === [] && !password_verify($currentPassword, (string) $passwordUser['password'])) {
        $passwordErrors[] = 'Kata sandi lama tidak sesuai.';
    }

    if ($passwordErrors === []) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updatePasswordStmt = $pdo->prepare('UPDATE tb_user SET password = :password WHERE id_user = :id_user');
            $updatePasswordStmt->execute([
                'password' => $hashedPassword,
                'id_user' => $passwordUserId,
            ]);

            $passwordSuccess = 'Kata sandi berhasil diperbarui.';
            $passwordUser['password'] = $hashedPassword;
        } catch (Throwable $exception) {
            error_log('Gagal memperbarui kata sandi user: ' . $exception->getMessage());
            $passwordErrors[] = 'Gagal memperbarui kata sandi. Silakan coba lagi.';
        }
    }
}
?>

<div class="card-panel">
    <div class="card-panel-header" style="margin-bottom: 18px">
        <div>
            <h3>Ubah Kata Sandi</h3>
            <p class="subtitle">Gunakan sandi yang kuat dan unik.</p>
        </div>
    </div>

    <?php if ($passwordErrors !== []): ?>
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 12px; border: 1px solid #fecaca; background: #fef2f2; color: #991b1b; font-size: 13px; line-height: 1.5;">
            <strong style="display: block; margin-bottom: 4px;">Terjadi kesalahan:</strong>
            <ul style="margin: 0; padding-left: 18px;">
                <?php foreach ($passwordErrors as $passwordError): ?>
                    <li><?php echo e($passwordError); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($passwordSuccess !== ''): ?>
        <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 12px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534; font-size: 13px; line-height: 1.5;">
            <?php echo e($passwordSuccess); ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="password_action" value="update_password" />
        <div style="display: flex; flex-direction: column; gap: 12px">
            <div>
                <label class="form-label" for="currentPassword">Kata Sandi Lama</label>
                <input
                    type="password"
                    id="currentPassword"
                    name="current_password"
                    class="form-control-custom"
                    placeholder="Masukkan sandi saat ini"
                    autocomplete="current-password"
                    required />
            </div>
            <div>
                <label class="form-label" for="newPassword">Kata Sandi Baru</label>
                <input
                    type="password"
                    id="newPassword"
                    name="new_password"
                    class="form-control-custom"
                    placeholder="Minimal 8 karakter"
                    minlength="8"
                    autocomplete="new-password"
                    required />
            </div>
            <div>
                <label class="form-label" for="confirmPassword">Konfirmasi Sandi Baru</label>
                <input
                    type="password"
                    id="confirmPassword"
                    name="confirm_password"
                    class="form-control-custom"
                    placeholder="Ulangi sandi baru"
                    autocomplete="new-password"
                    required />
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