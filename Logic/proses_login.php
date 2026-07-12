<?php

session_start();
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../View/login.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = "Username dan Password wajib diisi.";
    header("Location: ../View/login.php");
    exit;
}

try {
    $stmt = $koneksi->prepare("SELECT * FROM tb_user WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['id_user']   = $user['id_user'];
        $_SESSION['nama']      = $user['nama_lengkap'];
        $_SESSION['role']      = $user['role']; // owner / mitra / staff

        switch ($user['role']) {
            case 'owner':
                header("Location: ../View/dashboard_owner.php");
                break;
            case 'mitra':
                header("Location: ../View/dashboard_mitra.php");
                break;
            case 'staff':
                header("Location: ../View/dashboard_staff.php");
                break;
            default:
                header("Location: ../View/login.php");
        }
        exit;

    } else {
        $_SESSION['login_error'] = "Username atau password salah.";
        header("Location: ../View/login.php");
        exit;
    }

} catch (PDOException $e) {
    $_SESSION['login_error'] = "Terjadi kesalahan sistem. Coba lagi nanti.";
    header("Location: ../View/login.php");
    exit;
}
