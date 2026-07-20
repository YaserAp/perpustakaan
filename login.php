<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Perpustakaan Digital</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: var(--bg-main);
            padding: 20px;
        }

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 380px;
            padding: 32px 28px;
            box-shadow: var(--shadow-md);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-logo {
            width: 44px;
            height: 44px;
            background: var(--primary);
            border-radius: var(--radius-md);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            margin-bottom: 12px;
        }

        .input-group {
            position: relative;
            margin-bottom: 16px;
        }

        .input-group i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            font-size: 14px;
        }

        .input-group .form-control {
            padding-left: 38px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            cursor: pointer;
            left: auto !important;
        }

        .auth-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--primary);
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="fa-solid fa-book-open"></i>
        </div>
        <h2>Masuk ke Perpustakaan</h2>
        <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">Perpustakaan Digital</p>
    </div>

    <form action="proses_login.php" method="POST">
        <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
            <div style="background: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger); padding: 10px 14px; border-radius: var(--radius-md); font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Email atau kata sandi tidak cocok. Silakan coba lagi!</span>
            </div>
        <?php endif; ?>
        <div class="input-group">
            <input type="email" name="email" class="form-control" placeholder="Alamat Email" required>
            <i class="fa-regular fa-envelope"></i>
        </div>

        <div class="input-group">
            <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Kata Sandi" required>
            <i class="fa-solid fa-lock"></i>
            <i class="fa-regular fa-eye toggle-password" id="togglePassword"></i>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-muted); cursor: pointer;">
                <input type="checkbox" style="accent-color: var(--primary);"> Ingat Saya
            </label>
            <a href="lupa_password.php" style="font-size: 13px; color: var(--primary); font-weight: 500;">Lupa Password?</a>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px; font-size: 14px;">
            Masuk
        </button>
    </form>

    <div class="auth-footer">
        Belum punya akun? <a href="register.php">Daftar sekarang</a>
    </div>
</div>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
</script>
<script src="assets/js/main.js"></script>
</body>
</html>