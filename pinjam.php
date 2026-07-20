<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['id'];
$buku_id = isset($_POST['buku_id']) ? (int)$_POST['buku_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
$lama    = isset($_POST['lama']) ? (int)$_POST['lama'] : 7;
if ($lama <= 0) $lama = 7;

if ($buku_id > 0) {
    // cek stok buku
    $cek = mysqli_query($conn, "SELECT * FROM buku WHERE id='$buku_id'");
    $data = mysqli_fetch_assoc($cek);

    if ($data && (int)$data['stok'] > 0) {
        $tgl_pinjam = date('Y-m-d');
        $tgl_kembali = date('Y-m-d', strtotime("+$lama days"));

        mysqli_query($conn, "
            INSERT INTO peminjaman (user_id, buku_id, tanggal_pinjam, tanggal_kembali, status) 
            VALUES ('$user_id', '$buku_id', '$tgl_pinjam', '$tgl_kembali', 'dipinjam')
        ");

        mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id='$buku_id'");
    }
}

header("Location: dashboard_user.php?page=peminjaman");
exit;
?>