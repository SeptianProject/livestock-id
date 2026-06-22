<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
    header('Location: ../dashboard/index.php');
    exit;
}   

$error = '';

function attemptLogin(PDO $pdo, string $username, string $password): array|false
{
    $stmt = $pdo->prepare(
        'SELECT u.id_user, u.username, u.password, u.role,
                p.id_petugas, p.nama_petugas, p.id_jabatan, j.nama_jabatan
         FROM tb_user u
         LEFT JOIN tb_petugas p ON p.id_user = u.id_user
         LEFT JOIN tb_jabatan j ON j.id_jabatan = p.id_jabatan
         WHERE u.username = :username
         LIMIT 1'
    );
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, (string) $user['password'])) {    
        return false;
    }

    return $user;
}

function setLoginSession(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['user_id']      = (int) $user['id_user'];
    $_SESSION['username']     = (string) $user['username'];
    $_SESSION['role']         = (string) $user['role'];
    $_SESSION['petugas_id']   = $user['id_petugas']    !== null ? (int)    $user['id_petugas']    : null;
    $_SESSION['petugas_name'] = $user['nama_petugas']  !== null ? (string) $user['nama_petugas']  : null;
    $_SESSION['jabatan_id']   = $user['id_jabatan']    !== null ? (int)    $user['id_jabatan']    : null;
    $_SESSION['jabatan_name'] = $user['nama_jabatan']  !== null ? (string) $user['nama_jabatan']  : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $user = attemptLogin($pdo, $username, $password);

        if ($user === false) {
            $error = 'Username atau password salah.';
        } else {
            setLoginSession($user);
            header('Location: ../dashboard/index.php');
            exit;
        }
    }
}
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Masuk - LivestockID</title>
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

                    <h2 class="auth-title">Masuk</h2>
                    <p class="auth-subtitle">Selamat datang kembali! Silakan masuk untuk melanjutkan.</p>

                    <?php if ($error !== ''): ?>
                        <div class="auth-message auth-message--error">
                            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="post" novalidate>
                        <div class="auth-field">
                            <label for="username" class="auth-label">Username</label>
                            <input type="text" id="username" name="username" class="form-control auth-control" value="<?php echo isset($username) ? htmlspecialchars($username, ENT_QUOTES, 'UTF-8') : ''; ?>" />
                        </div>
                        <div class="auth-field">
                            <label for="password" class="auth-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control auth-control" />
                            <a href="#" class="forgot-password">Lupa Password?</a>
                        </div>

                        <button type="submit" class="btn-masuk btn-auth mt-2">Masuk</button>
                    </form>

                    <p class="auth-divider">Belum punya akun?</p>
                    <p class="auth-footer"><a href="register.php" class="auth-link">Daftar sekarang</a></p>
                </div>

                <div class="col-md-6 d-none d-md-block">
                    <div class="auth-visual-section">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>