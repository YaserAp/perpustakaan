<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$buku_id = $_GET['id'];

// cek stok dulu
$cek = mysqli_query($conn, "SELECT * FROM buku WHERE id='$buku_id'");
$data = mysqli_fetch_assoc($cek);

if($data['stok'] > 0){
    $tgl_pinjam = date('Y-m-d');
    $tgl_kembali = date('Y-m-d', strtotime("+7 days"));

    mysqli_query($conn, "INSERT INTO peminjaman 
    (user_id, buku_id, tanggal_pinjam, tanggal_kembali, status) 
    VALUES ('$user_id','$buku_id','$tgl_pinjam','$tgl_kembali','dipinjam')");

    mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id='$buku_id'");

    header("Location: dashboard_user.php?page=peminjaman");
    exit;

} else {
    echo "Stok habis bro!";
}
?>