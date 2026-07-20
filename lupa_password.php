<?php
include 'koneksi.php';

if(isset($_POST['cek'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($cek) > 0){
        header("Location: reset_password.php?email=" . urlencode($email));
        exit;
    } else {
        $error = "Alamat email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - LibrarySmart</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: radial-gradient(circle at top right, rgba(79, 70, 229, 0.15), transparent 40%),
                        radial-gradient(circle at bottom left, rgba(6, 182, 212, 0.15), transparent 40%),
                        var(--bg-main);
            padding: 20px;
        }

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 420px;
            padding: 40px 32px;
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(16px);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .auth-logo {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--warning), #d97706);
            border-radius: var(--radius-md);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            margin-bottom: 14px;
            box-shadow: 0 0 25px rgba(245, 158, 11, 0.35);
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
        }

        .input-group .form-control {
            padding-left: 48px;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="fa-solid fa-key"></i>
        </div>
        <h2>Lupa Kata Sandi</h2>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">Masukkan email Anda untuk verifikasi reset password</p>
    </div>

    <?php if(isset($error)): ?>
        <div class="badge badge-danger" style="width: 100%; padding: 12px; margin-bottom: 18px; justify-content: center;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <input type="email" name="email" class="form-control" placeholder="Email terdaftar..." required>
            <i class="fa-regular fa-envelope"></i>
        </div>

        <button type="submit" name="cek" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 15px;">
            <i class="fa-solid fa-arrow-right"></i> Lanjutkan Reset
        </button>
    </form>

    <div style="margin-top: 24px; text-align: center; font-size: 14px;">
        <a href="login.php" style="color: var(--text-muted); font-weight: 500;"><i class="fa-solid fa-arrow-left"></i> Kembali ke Login</a>
    </div>
</div>

</body>
</html>