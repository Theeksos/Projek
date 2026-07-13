<?php


session_start();
require_once "config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("SOP tidak ditemukan.");
}

$stmt = $koneksi->prepare("SELECT * FROM tb_sop WHERE id_sop = :id");
$stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$sop = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sop) {
    die("SOP tidak ditemukan.");
}

$langkah = array_filter(array_map('trim', explode("\n", $sop['langkah_langkah'])));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak SOP - <?= htmlspecialchars($sop['judul']) ?></title>
<style>
    body { font-family: Arial, sans-serif; padding: 30px; color: #1f2937; }
    h2 { color: #DB2777; margin-bottom: 4px; }
    .meta { color: #6b7280; font-size: 0.9rem; margin-bottom: 20px; }
    ol { padding-left: 20px; }
    ol li { margin-bottom: 10px; line-height: 1.5; }
    .btn-print { margin-top: 24px; padding: 10px 18px; background: #DB2777; color: #fff; border: none; border-radius: 8px; cursor: pointer; }
    @media print { .btn-print { display: none; } }
</style>
</head>
<body>
    <h2><?= htmlspecialchars($sop['judul']) ?></h2>
    <div class="meta">
        Kategori: <?= htmlspecialchars($sop['kategori']) ?> ·
        Versi <?= htmlspecialchars($sop['versi']) ?> ·
        Update: <?= date('d F Y', strtotime($sop['tanggal_update'])) ?>
    </div>
    <p><?= htmlspecialchars($sop['deskripsi']) ?></p>

    <ol>
        <?php foreach ($langkah as $item): ?>
            <li><?= htmlspecialchars($item) ?></li>
        <?php endforeach; ?>
    </ol>

    <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>
</body>
</html>
