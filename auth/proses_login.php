<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    header("Location: login.php?error=invalid");
    exit;
}

$clean_email = mysqli_real_escape_string($conn, strtolower($email));
$query  = "SELECT * FROM users WHERE LOWER(email)='$clean_email'";
$result = mysqli_query($conn, $query);
$data   = mysqli_fetch_assoc($result);

// If table has 0 users, auto-seed default admin and user
if (!$data) {
    $count_query = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users");
    if ($count_query) {
        $count_data = mysqli_fetch_assoc($count_query);
        if (($count_data['cnt'] ?? 0) == 0) {
            $pass_admin = password_hash('123123', PASSWORD_DEFAULT);
            $pass_user  = password_hash('890890', PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('saiful', 'saiful@gmail.com', '$pass_admin', 'admin')");
            mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('wahyuuu', 'wahyu@gmail.com', '$pass_user', 'user')");
            
            // Re-fetch user after auto-seeding
            $result = mysqli_query($conn, $query);
            $data   = mysqli_fetch_assoc($result);
        }
    }
}

$is_valid_password = false;
if ($data) {
    if (password_verify($password, $data['password'])) {
        $is_valid_password = true;
    } elseif ($password === $data['password'] || md5($password) === $data['password']) {
        $is_valid_password = true;
        // Auto re-hash plain text / legacy MD5 to Bcrypt
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        $user_id  = $data['id'];
        mysqli_query($conn, "UPDATE users SET password='$new_hash' WHERE id='$user_id'");
    }
}

if ($is_valid_password) {
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
