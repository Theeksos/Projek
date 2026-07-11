<?php

session_start();
require_once "config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: sop.php");
    exit;
}

$id_sop         = trim($_POST['id_sop'] ?? '');
$judul          = trim($_POST['judul'] ?? '');
$kategori       = trim($_POST['kategori'] ?? '');
$deskripsi      = trim($_POST['deskripsi'] ?? '');
$versi          = trim($_POST['versi'] ?? '1.0');
$tanggal_update = trim($_POST['tanggal_update'] ?? date('Y-m-d'));
$icon           = trim($_POST['icon'] ?? 'bi-journal-text');
$langkah        = trim($_POST['langkah_langkah'] ?? '');

if ($judul === '' || $kategori === '' || $deskripsi === '' || $langkah === '') {
    die("Semua field wajib diisi. <a href='javascript:history.back()'>Kembali</a>");
}

try {
    if ($id_sop !== '' && ctype_digit($id_sop)) {
        // Mode EDIT -> UPDATE data yang sudah ada
        $stmt = $koneksi->prepare("
            UPDATE tb_sop
            SET judul = :judul, kategori = :kategori, deskripsi = :deskripsi,
                versi = :versi, tanggal_update = :tanggal_update,
                icon = :icon, langkah_langkah = :langkah
            WHERE id_sop = :id_sop
        ");
        $stmt->bindParam(':id_sop', $id_sop, PDO::PARAM_INT);
    } else {
        // Mode TAMBAH -> INSERT data baru
        $stmt = $koneksi->prepare("
            INSERT INTO tb_sop (judul, kategori, deskripsi, versi, tanggal_update, icon, langkah_langkah, dibuat_oleh)
            VALUES (:judul, :kategori, :deskripsi, :versi, :tanggal_update, :icon, :langkah, :dibuat_oleh)
        ");
        $stmt->bindParam(':dibuat_oleh', $_SESSION['id_user'], PDO::PARAM_INT);
    }

    $stmt->bindParam(':judul', $judul);
    $stmt->bindParam(':kategori', $kategori);
    $stmt->bindParam(':deskripsi', $deskripsi);
    $stmt->bindParam(':versi', $versi);
    $stmt->bindParam(':tanggal_update', $tanggal_update);
    $stmt->bindParam(':icon', $icon);
    $stmt->bindParam(':langkah', $langkah);
    $stmt->execute();

    $id_tujuan = ($id_sop !== '' && ctype_digit($id_sop)) ? $id_sop : $koneksi->lastInsertId();
    header("Location: sop.php?id=" . $id_tujuan);
    exit;

} catch (PDOException $e) {
    die("Gagal menyimpan SOP: " . htmlspecialchars($e->getMessage()));
}
