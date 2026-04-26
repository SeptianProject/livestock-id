<?php

declare(strict_types=1);

session_start();

if (isset($_SESSION['name']) && $_SESSION['name'] !== '') {
    header('Location: ../dashboard/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['nama_lengkap'] ?? ''));
    $password = trim((string) ($_POST['kata_sandi'] ?? ''));

    if ($name === '' || $password === '') {
        $error = 'Nama lengkap dan kata sandi wajib diisi.';
    } else {
        $_SESSION['name'] = $name;
        header('Location: ../dashboard/index.php');
        exit;
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

<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-container">
            <div class="row g-0">
                <div class="col-md-6 login-form-section">
                    <div class="brand-logo">
                        <span style="color: #2e7d32">Livestock</span><span style="color: #4caf50">ID</span>
                    </div>

                    <h2>Masuk</h2>
                    <p class="subtitle">Selamat datang kembali! Silakan masuk untuk melanjutkan.</p>

                    <?php if ($error !== ''): ?>
                        <div style="margin-bottom:12px;padding:10px 12px;border-radius:10px;background:#fef2f2;color:#991b1b;font-size:13px;">
                            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="post">
                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label>Kata Sandi</label>
                            <input type="password" name="kata_sandi" class="form-control" />
                            <a href="#" class="forgot-password">Lupa Kata Sandi?</a>
                        </div>

                        <button type="submit" class="btn btn-masuk w-100">Masuk</button>
                    </form>

                    <p class="divider-text">Belum punya akun?</p>
                    <p class="footer-text"><a href="register.php" style="color: #69b35a">Daftar sekarang</a></p>
                </div>

                <div class="col-md-6 d-none d-md-block">
                    <div class="image-section"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>