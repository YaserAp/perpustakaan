<?php
session_start();
include 'koneksi.php';

if($_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: admin.php?page=buku");
    exit;
}

$id = $_GET['id'];

// Ambil data buku
$data = mysqli_query($conn, "SELECT * FROM buku WHERE id='$id'");
$b = mysqli_fetch_assoc($data);

if(!$b){
    header("Location: admin.php?page=buku");
    exit;
}

if(isset($_POST['update'])){
    $judul   = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $stok    = (int)$_POST['stok'];

    $nama_file = $_FILES['gambar']['name'];
    $tmp       = $_FILES['gambar']['tmp_name'];
    $folder    = "assets/img/";

    if(!empty($nama_file)){
        $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_file_baru = "buku_" . time() . "." . $ext;

        if($b['gambar'] != 'default.jpg' && file_exists($folder.$b['gambar'])){
            unlink($folder.$b['gambar']);
        }

        move_uploaded_file($tmp, $folder.$nama_file_baru);

        mysqli_query($conn, "
        UPDATE buku SET
            judul='$judul',
            penulis='$penulis',
            stok='$stok',
            gambar='$nama_file_baru'
        WHERE id='$id'
        ");
    } else {
        mysqli_query($conn, "
        UPDATE buku SET
            judul='$judul',
            penulis='$penulis',
            stok='$stok'
        WHERE id='$id'
        ");
    }

    header("Location: admin.php?page=buku");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Perpustakaan Digital</title>
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
            max-width: 520px;
            padding: 32px;
            box-shadow: var(--shadow-lg);
        }
    </style>
</head>
<body>

<div class="form-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="font-size: 22px;"><i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i> Edit Data Buku</h3>
        <a href="admin.php?page=buku" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <img src="assets/img/<?= htmlspecialchars($b['gambar'] ?: 'default.jpg') ?>" 
             style="width: 90px; height: 120px; object-fit: cover; border-radius: var(--radius-md); box-shadow: var(--shadow-md);"
             onerror="this.src='assets/img/default.jpg'">
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label class="form-label">Judul Buku</label>
            <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($b['judul']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Penulis / Pengarang</label>
            <input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($b['penulis']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Jumlah Stok</label>
            <input type="number" name="stok" class="form-control" value="<?= $b['stok'] ?>" min="0" required>
        </div>

        <div class="form-group">
            <label class="form-label">Ganti Cover (Biarkan kosong jika tidak diganti)</label>
            <input type="file" name="gambar" class="form-control" accept="image/*">
        </div>

        <button type="submit" name="update" class="btn btn-primary" style="width: 100%; padding: 14px; margin-top: 10px;">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
        </button>
    </form>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>