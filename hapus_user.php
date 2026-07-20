<?php
include 'koneksi.php';

if(!isset($_GET['id'])){
    header("Location: admin.php?page=user");
    exit;
}

$id = $_GET['id'];

// ==========================
// HAPUS DENDA TERKAIT USER
// ==========================
mysqli_query($conn, "
DELETE denda FROM denda
JOIN peminjaman ON peminjaman.id = denda.peminjaman_id
WHERE peminjaman.user_id='$id'
");

// ==========================
// HAPUS PEMINJAMAN USER
// ==========================
mysqli_query($conn, "
DELETE FROM peminjaman WHERE user_id='$id'
");

// ==========================
// HAPUS USER
// ==========================
mysqli_query($conn, "
DELETE FROM users WHERE id='$id'
");

// ==========================
// REDIRECT KE ADMIN PANEL
// ==========================
header("Location: admin.php?page=user");
exit;
?>