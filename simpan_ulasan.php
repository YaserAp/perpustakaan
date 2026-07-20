<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['id'];
$buku_id = isset($_POST['buku_id']) ? (int)$_POST['buku_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
$komentar = isset($_POST['komentar']) ? mysqli_real_escape_string($conn, trim($_POST['komentar'])) : '';

if ($buku_id > 0 && $rating >= 1 && $rating <= 5) {
    mysqli_query($conn, "
        INSERT INTO ulasan (user_id, buku_id, rating, komentar) 
        VALUES ('$user_id', '$buku_id', '$rating', '$komentar')
    ");
}

header("Location: dashboard_user.php?page=buku");
exit;
