<?php
<<<<<<< HEAD
>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4

session_start();
require_once "config/database.php";

<<<<<<< HEAD
>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

<<<<<<< HEAD
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4
if ($username === '' || $password === '') {
    $_SESSION['login_error'] = "Username dan Password wajib diisi.";
    header("Location: login.php");
    exit;
}

try {
<<<<<<< HEAD
>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4
    $stmt = $koneksi->prepare("SELECT * FROM tb_user WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

<<<<<<< HEAD
    if ($user && password_verify($password, $user['password'])) {

    if ($user && password_verify($password, $user['password'])) {

>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4
        $_SESSION['id_user']   = $user['id_user'];
        $_SESSION['nama']      = $user['nama_lengkap'];
        $_SESSION['role']      = $user['role']; // owner / mitra / staff

<<<<<<< HEAD
>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4
        switch ($user['role']) {
            case 'owner':
                header("Location: dashboard_owner.php");
                break;
            case 'mitra':
                header("Location: dashboard_mitra.php");
                break;
            case 'staff':
                header("Location: dashboard_staff.php");
                break;
            default:
                header("Location: login.php");
        }
        exit;

    } else {
<<<<<<< HEAD
>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4
        $_SESSION['login_error'] = "Username atau password salah.";
        header("Location: login.php");
        exit;
    }

} catch (PDOException $e) {
    $_SESSION['login_error'] = "Terjadi kesalahan sistem. Coba lagi nanti.";
    header("Location: login.php");
    exit;
}
