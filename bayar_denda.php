<?php
include 'koneksi.php';

if(!isset($_GET['id']) || !isset($_GET['email'])){
    header("Location: admin.php?page=denda");
    exit;
}

$id = $_GET['id'];
$email = $_GET['email'];

// update status
mysqli_query($conn, "
UPDATE denda 
SET status='sudah bayar' 
WHERE id='$id'
");

//  redirect BALIK KE ADMIN ROUTING (INI YANG PENTING)
header("Location: admin.php?page=denda&email=" . urlencode($email));
exit;
?>