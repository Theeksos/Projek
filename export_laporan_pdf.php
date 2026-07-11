<?php
/**
 * File: export_laporan_pdf.php
 * Fungsi: Menyediakan versi "siap cetak" dari laporan, supaya bisa
 * disimpan sebagai PDF lewat fitur Print bawaan browser
 * (Ctrl+P -> Save as PDF). Ini pendekatan paling sederhana untuk
 * PHP Native tanpa perlu install library PDF tambahan.
 */

session_start();
require_once "config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$periode = $_GET['periode'] ?? date("Y-m");
if (!preg_match('/^\d{4}-\d{2}$/', $periode)) {
    $periode = date("Y-m");
}

$bulanIndo = [
    1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",
    7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
];
[$y, $m] = explode("-", $periode);
$label_periode = $bulanIndo[(int)$m] . " " . $y;

$stmt = $koneksi->prepare("
    SELECT k.nama_kios,
           COALESCE(SUM(t.total_pendapatan),0) AS pendapatan,
           COALESCE(SUM(t.jumlah_transaksi),0) AS transaksi
    FROM tb_kios k
    LEFT JOIN tb_transaksi t ON t.id_kios = k.id_kios AND DATE_FORMAT(t.tanggal,'%Y-%m') = :periode
    GROUP BY k.id_kios, k.nama_kios
    ORDER BY pendapatan DESC
");
$stmt->bindParam(':periode', $periode);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_pendapatan = array_sum(array_column($data, 'pendapatan'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak Laporan - <?= $label_periode ?></title>
<style>
    body { font-family: Arial, sans-serif; padding: 30px; color: #1f2937; }
    h2 { color: #DB2777; margin-bottom: 4px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; font-size: 0.9rem; }
    th { background-color: #fce7f3; color: #9d174d; }
    .total-row td { font-weight: bold; background-color: #fdf2f8; }
    .btn-print { margin-top: 20px; padding: 10px 18px; background: #DB2777; color: #fff; border: none; border-radius: 8px; cursor: pointer; }
    @media print { .btn-print { display: none; } }
</style>
</head>
<body>
    <h2>Laporan Pendapatan — Dough & Co Hub</h2>
    <p>Periode: <strong><?= $label_periode ?></strong></p>

    <table>
        <thead>
            <tr><th>Nama Kios</th><th>Pendapatan</th><th>Jumlah Transaksi</th></tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_kios']) ?></td>
                <td>Rp<?= number_format($row['pendapatan'], 0, ',', '.') ?></td>
                <td><?= number_format($row['transaksi'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td>Total</td>
                <td>Rp<?= number_format($total_pendapatan, 0, ',', '.') ?></td>
                <td>-</td>
            </tr>
        </tbody>
    </table>

    <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>
</body>
</html>
