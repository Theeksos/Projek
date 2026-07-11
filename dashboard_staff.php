<?php
// File: dashboard_staff.php
// Halaman ini hanya boleh diakses jika sudah login DAN role-nya sesuai
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Staff - Dough & Co Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5" style="background:#FDF2F8;">
    <div class="container">
        <h2 class="mb-3" style="color:#DB2777;">Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?> 👋</h2>
        <p class="text-muted">Kamu login sebagai: <strong><?= ucfirst($_SESSION['role']) ?></strong></p>
        <p>Ini halaman placeholder dashboard staff. Silakan dikembangkan sesuai fitur di makalah (Monitoring, Stok, dsb).</p>
        <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
</body>
</html>
