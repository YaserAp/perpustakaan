<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran - Perpustakaan Digital</title>
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
            max-width: 400px;
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
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <h2>Buat Akun Baru</h2>
        <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">Daftar sebagai anggota Perpustakaan Digital</p>
    </div>

    <form id="formRegister">
        <div class="input-group">
            <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
            <i class="fa-regular fa-user"></i>
        </div>

        <div class="input-group">
            <input type="email" name="email" class="form-control" placeholder="Alamat Email" required>
            <i class="fa-regular fa-envelope"></i>
        </div>

        <div class="input-group">
            <input type="password" name="password" class="form-control" placeholder="Kata Sandi" required>
            <i class="fa-solid fa-lock"></i>
        </div>

        <div class="form-group">
            <label class="form-label">Tipe Akun:</label>
            <select name="role" class="form-control" required style="background-color: var(--bg-card);">
                <option value="user">Anggota Perpustakaan</option>
                <option value="admin">Petugas Administrator</option>
            </select>
        </div>

        <button type="submit" id="btnRegister" class="btn btn-primary" style="width: 100%; padding: 10px; font-size: 14px; margin-top: 6px;">
            Daftar Akun
        </button>
    </form>

    <div class="auth-footer">
        Sudah memiliki akun? <a href="login.php">Masuk</a>
    </div>
</div>

<script src="assets/js/main.js"></script>
<script>
const form = document.getElementById("formRegister");
const btn = document.getElementById("btnRegister");

form.addEventListener("submit", function(e){
    e.preventDefault();
    let formData = new FormData(this);

    btn.innerText = "Memproses...";
    btn.disabled = true;

    fetch("proses_register.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        data = data.trim();
        if(data === "success"){
            showToast("<i class='fa-solid fa-circle-check'></i> Pendaftaran berhasil! Silakan login.", "success");
            form.reset();
            setTimeout(() => { window.location.href = "login.php"; }, 1200);
        } else if(data === "email_exist"){
            showToast("<i class='fa-solid fa-triangle-exclamation'></i> Email sudah terdaftar!", "warning");
        } else {
            showToast("<i class='fa-solid fa-circle-xmark'></i> Pendaftaran gagal, coba lagi.", "danger");
        }
        btn.innerText = "Daftar Akun";
        btn.disabled = false;
    });
});
</script>
<script src="assets/js/main.js"></script>
</body>
</html>