<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
    header('Location: ../dashboard/index.php');
    exit;
}

$error = '';
$success = '';
const ALLOWED_ROLES = ['admin', 'dokter', 'petugas_lapang', 'petugas_produksi'];

function validateRegisterInput(
    string $username,
    string $password,
    string $role,
    string $confirmPassword
): string {
    if ($username === '' || $password === '' || $role === ''  || $confirmPassword === '') {
        return 'Semua kolom wajib diisi.';
    }
    if (strlen($username) < 4) {
        return 'Username minimal 4 karakter.';
    }
    if (strlen($password) < 6) {
        return 'Kata sandi minimal 6 karakter.';
    }
    if (!in_array($role, ALLOWED_ROLES, true)) {
        return 'Role tidak valid.';
    }
    if ($password !== $confirmPassword) {
        return 'Kata sandi tidak cocok.';
    }

    return '';
}

function registerUser(PDO $pdo, string $username,  string $password, string $role): int | false
{
    $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM tb_user WHERE username = :username');
    $checkStmt->execute(['username' => $username]);

    if ((int) $checkStmt->fetchColumn() > 0) {
        return false;
    }

    $insertStmt = $pdo->prepare(
        'INSERT INTO tb_user (username, password, role) VALUES (:username, :password, :role)'
    );
    $insertStmt->execute([
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role'     => $role,
    ]);

    return (int) $pdo->lastInsertId();
}

function setRegisterSession(int $userId, string $username, string $role): void
{
    session_regenerate_id(true);

    $_SESSION['user_id']      = $userId;
    $_SESSION['username']     = $username;
    $_SESSION['role']         = $role;
    $_SESSION['petugas_id']   = null;
    $_SESSION['petugas_name'] = null;
    $_SESSION['jabatan_id']   = null;
    $_SESSION['jabatan_name'] = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $role = trim((string) ($_POST['role'] ?? 'petugas_lapang'));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    $error = validateRegisterInput($username, $password, $role, $confirmPassword);

    if ($error === '') {
        try {
            $userId = registerUser($pdo, $username, $password, $role);

            if ($userId === false) {
                $error = 'Username sudah digunakan.';
            } else {
                setRegisterSession($userId, $username, $role);
                $success = 'Registrasi berhasil! Anda akan diarahkan ke dashboard.';
                header('Refresh: 2; URL=../dashboard/index.php');
            }
        } catch (PDOException $e) {
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
                $error = 'Username sudah digunakan.';
            } else {
                throw $e;
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar - LivestockID</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>

<body class="auth-page">
    <div class="container py-4">
        <div class="auth-shell">
            <div class="row g-0">
                <div class="col-md-6 auth-form-section">
                    <div class="auth-brand">
                        <span class="auth-brand-accent">Livestock</span><span>ID</span>
                    </div>

                    <h2 class="auth-title">Daftar Akun</h2>
                    <p class="auth-subtitle">Buat akun untuk mengakses sistem peternakan.</p>

                    <?php if ($error !== ''): ?>
                        <div class="auth-message auth-message--error">
                            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success !== ''): ?>
                        <div class="auth-message auth-message--success">
                            <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="post" novalidate>
                        <div class="auth-field">
                            <label for="username" class="auth-label">Username</label>
                            <input type="text" id="username" name="username" class="form-control auth-control" minlength="4" required value="<?php echo isset($username) ? htmlspecialchars($username, ENT_QUOTES, 'UTF-8') : ''; ?>" />
                        </div>

                        <div class="auth-field">
                            <label for="role" class="auth-label">Role</label>
                            <select id="role" name="role" class="form-control auth-control" required>
                                <option value="petugas_lapang" <?php echo isset($role) && $role === 'petugas_lapang' ? 'selected' : ''; ?>>Petugas Lapang</option>
                                <option value="petugas_produksi" <?php echo isset($role) && $role === 'petugas_produksi' ? 'selected' : ''; ?>>Petugas Produksi</option>
                                <option value="dokter" <?php echo isset($role) && $role === 'dokter' ? 'selected' : ''; ?>>Dokter</option>
                                <option value="admin" <?php echo isset($role) && $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>

                        <div class="auth-grid auth-grid--2">
                            <div class="auth-field">
                                <label for="password" class="auth-label">Kata Sandi</label>
                                <div class="auth-password-field">
                                    <input type="password" id="password" name="password" class="form-control auth-control" required />
                                    <span class="auth-password-toggle" onclick="togglePassword('password')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <div class="auth-field">
                                <label for="confirm_password" class="auth-label">Konfirmasi Kata Sandi</label>
                                <div class="auth-password-field">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control auth-control" required />
                                    <span class="auth-password-toggle" onclick="togglePassword('confirm_password')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-daftar btn-auth mt-3">Daftar</button>
                    </form>

                    <p class="auth-divider">Sudah punya akun?</p>
                    <p class="auth-footer"><a href="login.php" class="auth-link">Masuk di sini</a></p>
                </div>

                <div class="col-md-6 d-none d-md-block">
                    <div class="auth-visual-section">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
        }
    </script>
</body>

</html>