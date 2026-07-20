<?php
include 'koneksi.php';

if(!isset($_GET['id'])){
    header("Location: admin.php?page=buku");
    exit;
}

$id = $_GET['id'];

//  ambil data buku (buat hapus gambar nanti)
$get = mysqli_query($conn, "SELECT * FROM buku WHERE id='$id'");
$buku = mysqli_fetch_assoc($get);

// ========================
// HAPUS RELASI DULU
// ========================

// hapus denda yang terkait peminjaman buku ini
mysqli_query($conn, "
DELETE denda FROM denda
JOIN peminjaman ON peminjaman.id = denda.peminjaman_id
WHERE peminjaman.buku_id='$id'
");

// hapus peminjaman
mysqli_query($conn, "DELETE FROM peminjaman WHERE buku_id='$id'");

// ========================
// HAPUS BUKU
// ========================
mysqli_query($conn, "DELETE FROM buku WHERE id='$id'");

// ========================
// HAPUS GAMBAR (OPSIONAL)
// ========================
if($buku && $buku['gambar'] != 'default.jpg'){
    $path = "assets/img/".$buku['gambar'];
    if(file_exists($path)){
        unlink($path);
    }
}

// ========================
// REDIRECT BALIK
// ========================
header("Location: admin.php?page=buku");
exit;
?>