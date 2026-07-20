<?php
include 'koneksi.php';

$email = $_GET['email'] ?? '';

if(isset($_POST['reset'])){
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $email = mysqli_real_escape_string($conn, $email);

    mysqli_query($conn, "
    UPDATE users SET password='$password' 
    WHERE email='$email'
    ");

    header("Location: login.php?reset=success");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - LibrarySmart</title>
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
            background: linear-gradient(135deg, var(--success), #059669);
            border-radius: var(--radius-md);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            margin-bottom: 14px;
            box-shadow: 0 0 25px rgba(16, 185, 129, 0.35);
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
            <i class="fa-solid fa-lock-open"></i>
        </div>
        <h2>Buat Password Baru</h2>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">Untuk email: <strong><?= htmlspecialchars($email) ?></strong></p>
    </div>

    <form method="POST">
        <div class="input-group">
            <input type="password" name="password" class="form-control" placeholder="Password Baru" required minlength="4">
            <i class="fa-solid fa-lock"></i>
        </div>

        <button type="submit" name="reset" class="btn btn-success" style="width: 100%; padding: 14px; font-size: 15px;">
            <i class="fa-solid fa-check"></i> Simpan Password Baru
        </button>
    </form>
</div>

</body>
</html>