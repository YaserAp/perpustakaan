<?php
session_start();
include 'koneksi.php';

$user = $_SESSION['id'];
$buku = $_POST['buku_id'];
$lama = $_POST['lama'];

// tanggal kembali otomatis
$tanggal_kembali = date('Y-m-d', strtotime("+$lama days"));

mysqli_query($conn, "INSERT INTO peminjaman 
(user_id, buku_id, tanggal_pinjam, tanggal_kembali, status) 
VALUES ('$user','$buku',NOW(),'$tanggal_kembali','dipinjam')");

mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id='$buku'");

header("Location: dashboard_user.php?page=peminjaman");
?>