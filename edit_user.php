<?php
session_start();
include 'koneksi.php';

if($_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: admin.php?page=user");
    exit;
}

$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
$u = mysqli_fetch_assoc($data);

if(!$u){
    header("Location: admin.php?page=user");
    exit;
}

if(isset($_POST['update'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    mysqli_query($conn, "
    UPDATE users SET
        nama='$nama',
        email='$email',
        role='$role'
    WHERE id='$id'
    ");

    header("Location: admin.php?page=user");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota - Perpustakaan Digital</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 460px;
            padding: 32px;
            box-shadow: var(--shadow-lg);
        }
    </style>
</head>
<body>

<div class="form-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="font-size: 22px;"><i class="fa-solid fa-user-pen" style="color: var(--primary);"></i> Edit Data Anggota</h3>
        <a href="admin.php?page=user" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <form method="POST">
        <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($u['nama']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Alamat Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($u['email']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Role Pengguna</label>
            <select name="role" class="form-control" style="background: var(--bg-main);">
                <option value="user" <?= ($u['role'] ?? 'user') == 'user' ? 'selected' : '' ?>>User / Anggota</option>
                <option value="admin" <?= ($u['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Administrator</option>
            </select>
        </div>

        <button type="submit" name="update" class="btn btn-primary" style="width: 100%; padding: 14px; margin-top: 10px;">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
        </button>
    </form>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>