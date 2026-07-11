<?php
<<<<<<< HEAD
// File: proses_login.php
// Fungsi: Logic Layer -> memproses autentikasi & otorisasi (RBAC)
// Alur ini mengikuti Activity Diagram "Login" pada makalah:
// 1. Ambil input -> 2. Validasi ke database -> 3. Cek role -> 4. Redirect
=======
>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4

session_start();
require_once "config/database.php";

<<<<<<< HEAD
// Pastikan form dikirim dengan method POST
=======
>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

<<<<<<< HEAD
// Ambil data dari form (gunakan trim untuk buang spasi)
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validasi dasar di server (jangan cuma percaya validasi JS di browser)
=======
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
    // Cari user berdasarkan username (pakai prepared statement -> aman dari SQL Injection)
=======
>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4
    $stmt = $koneksi->prepare("SELECT * FROM tb_user WHERE username = :username LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

<<<<<<< HEAD
    // Cek apakah user ditemukan DAN password cocok dengan hash di database
    if ($user && password_verify($password, $user['password'])) {

        // Login berhasil -> simpan data penting ke session
=======
    if ($user && password_verify($password, $user['password'])) {

>>>>>>> 062a1b73ba1e9441c6e8aa42e9f05ef9d199fae4
        $_SESSION['id_user']   = $user['id_user'];
        $_SESSION['nama']      = $user['nama_lengkap'];
        $_SESSION['role']      = $user['role']; // owner / mitra / staff

<<<<<<< HEAD
        // RBAC: arahkan ke dashboard sesuai role (sesuai makalah)
=======
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
        // Username tidak ada ATAU password salah
=======
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
