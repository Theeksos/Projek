<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$halaman_aktif = "sop";

$kategori_dipilih = $_GET['kategori'] ?? 'semua';

$stmt = $koneksi->query("SELECT DISTINCT kategori FROM tb_sop ORDER BY kategori ASC");
$daftar_kategori = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($kategori_dipilih === 'semua') {
    $stmt = $koneksi->query("SELECT * FROM tb_sop ORDER BY tanggal_update DESC");
} else {
    $stmt = $koneksi->prepare("SELECT * FROM tb_sop WHERE kategori = :kategori ORDER BY tanggal_update DESC");
    $stmt->bindParam(':kategori', $kategori_dipilih);
    $stmt->execute();
}
$daftar_sop = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sop_detail = null;
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $stmt = $koneksi->prepare("SELECT * FROM tb_sop WHERE id_sop = :id");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    $sop_detail = $stmt->fetch(PDO::FETCH_ASSOC);
}

$bulanIndo = [1=>"Jan",2=>"Feb",3=>"Mar",4=>"Apr",5=>"Mei",6=>"Jun",
              7=>"Jul",8=>"Agu",9=>"Sep",10=>"Okt",11=>"Nov",12=>"Des"];
function format_tanggal_pendek($tanggal, $bulanIndo) {
    $ts = strtotime($tanggal);
    return $bulanIndo[(int)date('n', $ts)] . " " . date('Y', $ts);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOP Digital - Dough & Co Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../Assets/css/dashboard.css">
</head>
<body>
<div class="app-layout">

    <?php include "../includes/sidebar.php"; ?>

    <main class="main-content">
        <div class="page-topbar">
            <h4>SOP Digital</h4>
            <div class="topbar-actions">
                <form method="GET" id="formKategori">
                    <select name="kategori" class="select-kategori" onchange="document.getElementById('formKategori').submit()">
                        <option value="semua" <?= $kategori_dipilih === 'semua' ? 'selected' : '' ?>>Semua Kategori</option>
                        <?php foreach ($daftar_kategori as $kat): ?>
                            <option value="<?= htmlspecialchars($kat) ?>" <?= $kategori_dipilih === $kat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <a href="form/form_sop.php" class="btn-export"><i class="bi bi-plus-lg"></i> Tambah SOP</a>
            </div>
        </div>

        <div class="sop-grid">
            <?php foreach ($daftar_sop as $sop): ?>
                <?php $aktif = ($sop_detail && $sop_detail['id_sop'] == $sop['id_sop']); ?>
                <div class="sop-card <?= $aktif ? 'is-active' : '' ?>">
                    <div class="sop-icon-circle"><i class="bi <?= htmlspecialchars($sop['icon']) ?>"></i></div>
                    <div class="sop-title"><?= htmlspecialchars($sop['judul']) ?></div>
                    <div class="sop-desc"><?= htmlspecialchars($sop['deskripsi']) ?></div>
                    <div class="sop-meta">
                        <span>v<?= htmlspecialchars($sop['versi']) ?> · <?= format_tanggal_pendek($sop['tanggal_update'], $bulanIndo) ?></span>
                        <a href="sop.php?id=<?= $sop['id_sop'] ?>&kategori=<?= urlencode($kategori_dipilih) ?>" class="btn-lihat">Lihat</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($daftar_sop)): ?>
                <p class="text-muted">Belum ada SOP untuk kategori ini.</p>
            <?php endif; ?>
        </div>

        <?php if ($sop_detail): ?>
        <div class="panel">
            <div class="sop-detail-header">
                <div class="panel-title" style="margin-bottom:0;">
                    Detail — <?= htmlspecialchars($sop_detail['judul']) ?> v<?= htmlspecialchars($sop_detail['versi']) ?>
                </div>
                <div style="display:flex; gap:8px;">
                    <a href="form_sop.php?id=<?= $sop_detail['id_sop'] ?>" class="btn-outline-pink">
                        <i class="bi bi-pencil"></i> Edit Dokumen
                    </a>
                    <a href="export_sop_pdf.php?id=<?= $sop_detail['id_sop'] ?>" class="btn-outline-pink">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                </div>
            </div>

            <ol class="sop-langkah-list">
                <?php
                $langkah = array_filter(array_map('trim', explode("\n", $sop_detail['langkah_langkah'])));
                foreach ($langkah as $item):
                ?>
                    <li><?= htmlspecialchars($item) ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
