<?php

session_start();
require_once "config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: staf.php");
    exit;
}

$id_staf        = trim($_POST['id_staf'] ?? '');
$nama_staf      = trim($_POST['nama_staf'] ?? '');
$id_kios        = trim($_POST['id_kios'] ?? '');
$shift          = trim($_POST['shift'] ?? 'Pagi');
$jenis_kelamin  = trim($_POST['jenis_kelamin'] ?? 'Laki-laki');
$status         = trim($_POST['status'] ?? 'Aktif');
$tanggal_gabung = trim($_POST['tanggal_gabung'] ?? date('Y-m-d'));

if ($nama_staf === '' || $id_kios === '' || !ctype_digit($id_kios)) {
    die("Nama staf dan Kios wajib diisi dengan benar. <a href='javascript:history.back()'>Kembali</a>");
}

try {
    if ($id_staf !== '' && ctype_digit($id_staf)) {
        $stmt = $koneksi->prepare("
            UPDATE tb_staf
            SET nama_staf = :nama_staf, id_kios = :id_kios, shift = :shift,
                jenis_kelamin = :jenis_kelamin, status = :status, tanggal_gabung = :tanggal_gabung
            WHERE id_staf = :id_staf
        ");
        $stmt->bindParam(':id_staf', $id_staf, PDO::PARAM_INT);
    } else {
        $stmt = $koneksi->prepare("
            INSERT INTO tb_staf (nama_staf, id_kios, shift, jenis_kelamin, status, tanggal_gabung)
            VALUES (:nama_staf, :id_kios, :shift, :jenis_kelamin, :status, :tanggal_gabung)
        ");
    }

    $stmt->bindParam(':nama_staf', $nama_staf);
    $stmt->bindParam(':id_kios', $id_kios, PDO::PARAM_INT);
    $stmt->bindParam(':shift', $shift);
    $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':tanggal_gabung', $tanggal_gabung);
    $stmt->execute();

    header("Location: staf.php");
    exit;

} catch (PDOException $e) {
    die("Gagal menyimpan data staf: " . htmlspecialchars($e->getMessage()));
}
