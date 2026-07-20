<?php
session_start();
include 'koneksi.php';

if($_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

if(isset($_POST['submit'])){
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $stok = (int)$_POST['stok'];

    $nama_file = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $folder = "assets/img/";

    if(!empty($nama_file)){
        $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_file = "buku_" . time() . "." . $ext;
        move_uploaded_file($tmp, $folder.$nama_file);
    } else {
        $nama_file = "default.jpg";
    }

    mysqli_query($conn, "
    INSERT INTO buku (judul, penulis, stok, gambar)
    VALUES ('$judul','$penulis','$stok','$nama_file')
    ");

    header("Location: admin.php?page=buku");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - LibrarySmart</title>
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
            max-width: 500px;
            padding: 32px;
            box-shadow: var(--shadow-lg);
        }
    </style>
</head>
<body>

<div class="form-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="font-size: 22px;"><i class="fa-solid fa-plus-circle" style="color: var(--primary);"></i> Tambah Buku Baru</h3>
        <a href="admin.php?page=buku" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label class="form-label">Judul Buku</label>
            <input type="text" name="judul" class="form-control" placeholder="Contoh: Pemrograman Web Modern" required>
        </div>

        <div class="form-group">
            <label class="form-label">Penulis / Pengarang</label>
            <input type="text" name="penulis" class="form-control" placeholder="Contoh: Dr. Ir. Ahmad Subagyo" required>
        </div>

        <div class="form-group">
            <label class="form-label">Jumlah Stok</label>
            <input type="number" name="stok" class="form-control" placeholder="Masukkan jumlah eksemplar" min="1" required>
        </div>

        <div class="form-group">
            <label class="form-label">Cover Buku (Opsional)</label>
            <input type="file" name="gambar" class="form-control" accept="image/*">
        </div>

        <button type="submit" name="submit" class="btn btn-primary" style="width: 100%; padding: 14px; margin-top: 10px;">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Buku Baru
        </button>
    </form>
</div>

</body>
</html>