<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

$email    = trim(mysqli_real_escape_string($conn, $_POST['email'] ?? ''));
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    header("Location: login.php?error=invalid");
    exit;
}

$query  = "SELECT * FROM users WHERE LOWER(email)='" . strtolower($email) . "'";
$result = mysqli_query($conn, $query);
$data   = mysqli_fetch_assoc($result);

if ($data && (password_verify($password, $data['password']) || $password === $data['password'])) {
    // Auto re-hash if plain text matched
    if ($password === $data['password']) {
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        $user_id  = $data['id'];
        mysqli_query($conn, "UPDATE users SET password='$new_hash' WHERE id='$user_id'");
    }

    $_SESSION['email'] = $data['email'];
    $_SESSION['nama']  = $data['nama'];
    $_SESSION['role']  = $data['role'];
    $_SESSION['id']    = $data['id'];

    if ($data['role'] == 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: dashboard_user.php");
    }
    exit;
} else {
    header("Location: login.php?error=invalid");
    exit;
}
?>
