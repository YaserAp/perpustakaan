<?php
require_once __DIR__ . '/../config/koneksi.php';

$nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
$email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = mysqli_real_escape_string($conn, $_POST['role'] ?? 'user');

// Cek ketersediaan email
$cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
if(mysqli_num_rows($cek) > 0){
    echo "email_exist";
    exit;
}

// Secure Bcrypt password hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hashed_password', '$role')";

if (mysqli_query($conn, $query)) {
    echo "success";
} else {
    echo "error";
}
?>
