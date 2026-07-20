<?php
include 'koneksi.php';

$id = $_GET['id'];

// ambil data peminjaman
$data = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id='$id'");
$d = mysqli_fetch_assoc($data);

$tanggal_kembali = $d['tanggal_kembali'];
$hari_ini = date('Y-m-d');

// hitung keterlambatan
$telat = (strtotime($hari_ini) - strtotime($tanggal_kembali)) / (60*60*24);

if($telat > 0){
    $denda = $telat * 2000; // 2000 per hari

    mysqli_query($conn, "
    INSERT INTO denda (peminjaman_id, jumlah, status)
    VALUES ('$id', '$denda', 'belum bayar')
    ");
}

// update status jadi kembali
mysqli_query($conn, "
UPDATE peminjaman 
SET status='kembali', tanggal_kembali='$hari_ini'
WHERE id='$id'
");

// balikin stok
mysqli_query($conn, "
UPDATE buku 
SET stok = stok + 1 
WHERE id='".$d['buku_id']."'
");

header("Location: dashboard_user.php?page=peminjaman");
?>