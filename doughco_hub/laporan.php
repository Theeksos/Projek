<?php

session_start();
require_once "config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$halaman_aktif = "laporan";

$periode = $_GET['periode'] ?? date("Y-m"); 
if (!preg_match('/^\d{4}-\d{2}$/', $periode)) {
    $periode = date("Y-m");
}

$periode_lalu = date("Y-m", strtotime($periode . "-01 -1 month"));

$opsi_periode = [];
for ($i = 0; $i < 6; $i++) {
    $bulanKe = date("Y-m", strtotime(date("Y-m") . "-01 -$i month"));
    $opsi_periode[$bulanKe] = strftime_id($bulanKe);
}

function strftime_id($ym) {
    $bulanIndo = [
        1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",
        7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
    ];
    [$y, $m] = explode("-", $ym);
    return $bulanIndo[(int)$m] . " " . $y;
}

$stmt = $koneksi->prepare("SELECT COALESCE(SUM(total_pendapatan),0) AS total
                            FROM tb_transaksi WHERE DATE_FORMAT(tanggal, '%Y-%m') = :periode");
$stmt->bindParam(':periode', $periode);
$stmt->execute();
$pendapatan_bulan_ini = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $koneksi->prepare("SELECT COALESCE(SUM(jumlah_transaksi),0) AS total
                            FROM tb_transaksi WHERE DATE_FORMAT(tanggal, '%Y-%m') = :periode");
$stmt->bindParam(':periode', $periode);
$stmt->execute();
$total_transaksi = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $koneksi->prepare("SELECT k.nama_kios, SUM(t.total_pendapatan) AS total
                            FROM tb_transaksi t
                            JOIN tb_kios k ON k.id_kios = t.id_kios
                            WHERE DATE_FORMAT(t.tanggal, '%Y-%m') = :periode
                            GROUP BY t.id_kios ORDER BY total DESC LIMIT 1");
$stmt->bindParam(':periode', $periode);
$stmt->execute();
$kios_terbaik = $stmt->fetch(PDO::FETCH_ASSOC);
$nama_kios_terbaik = $kios_terbaik['nama_kios'] ?? '-';

$stmt = $koneksi->prepare("SELECT COUNT(DISTINCT tanggal) AS jumlah_hari
                            FROM tb_transaksi WHERE DATE_FORMAT(tanggal, '%Y-%m') = :periode");
$stmt->bindParam(':periode', $periode);
$stmt->execute();
$jumlah_hari_aktif = (int) $stmt->fetch(PDO::FETCH_ASSOC)['jumlah_hari'];
$rata_rata_harian = $jumlah_hari_aktif > 0 ? round($pendapatan_bulan_ini / $jumlah_hari_aktif) : 0;

$stmt = $koneksi->prepare("SELECT DAY(tanggal) AS hari, SUM(total_pendapatan) AS total
                            FROM tb_transaksi
                            WHERE DATE_FORMAT(tanggal, '%Y-%m') = :periode
                            GROUP BY DAY(tanggal) ORDER BY hari ASC");
$stmt->bindParam(':periode', $periode);
$stmt->execute();
$data_grafik = $stmt->fetchAll(PDO::FETCH_ASSOC);

$label_hari = [];
$nilai_harian = [];
foreach ($data_grafik as $row) {
    $label_hari[] = (int) $row['hari'];
    $nilai_harian[] = (float) $row['total'];
}

$stmt = $koneksi->prepare("
    SELECT
        k.nama_kios,
        COALESCE(SUM(CASE WHEN DATE_FORMAT(t.tanggal,'%Y-%m') = :periode THEN t.total_pendapatan END), 0) AS pendapatan_ini,
        COALESCE(SUM(CASE WHEN DATE_FORMAT(t.tanggal,'%Y-%m') = :periode THEN t.jumlah_transaksi END), 0) AS transaksi_ini,
        COALESCE(SUM(CASE WHEN DATE_FORMAT(t.tanggal,'%Y-%m') = :periode_lalu THEN t.total_pendapatan END), 0) AS pendapatan_lalu,
        COUNT(DISTINCT CASE WHEN DATE_FORMAT(t.tanggal,'%Y-%m') = :periode THEN t.tanggal END) AS hari_aktif
    FROM tb_kios k
    LEFT JOIN tb_transaksi t ON t.id_kios = k.id_kios
    GROUP BY k.id_kios, k.nama_kios
    ORDER BY pendapatan_ini DESC
");
$stmt->bindParam(':periode', $periode);
$stmt->bindParam(':periode_lalu', $periode_lalu);
$stmt->execute();
$tabel_kios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan & Analitik - Dough & Co Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
<div class="app-layout">

    <?php include "includes/sidebar.php"; ?>

    <main class="main-content">
        <div class="page-topbar">
            <h4>Laporan & Analitik</h4>
            <div class="topbar-actions">
                <form method="GET" id="formPeriode">
                    <select name="periode" class="select-periode" onchange="document.getElementById('formPeriode').submit()">
                        <?php foreach ($opsi_periode as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $value === $periode ? 'selected' : '' ?>>
                                Periode: <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <a href="export_laporan_pdf.php?periode=<?= $periode ?>" class="btn-export">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-card stat-pink">
                <div class="stat-label">Pendapatan Bulan Ini</div>
                <div class="stat-value">Rp<?= number_format($pendapatan_bulan_ini, 0, ',', '.') ?></div>
            </div>
            <div class="stat-card stat-blue">
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value"><?= number_format($total_transaksi, 0, ',', '.') ?></div>
            </div>
            <div class="stat-card stat-green">
                <div class="stat-label">Kios Terbaik</div>
                <div class="stat-value"><?= htmlspecialchars($nama_kios_terbaik) ?></div>
            </div>
            <div class="stat-card stat-orange">
                <div class="stat-label">Rata-rata Omset/Hari</div>
                <div class="stat-value">Rp<?= number_format($rata_rata_harian, 0, ',', '.') ?></div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-title">Tren Pendapatan Harian (<?= strftime_id($periode) ?>)</div>
            <canvas id="chartTren" height="90"></canvas>
        </div>

        <div class="panel">
            <div class="panel-title">Perbandingan Antar Kios</div>
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kios</th>
                        <th>Pendapatan</th>
                        <th>Transaksi</th>
                        <th>Rata-rata/Hari</th>
                        <th>vs Lalu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($tabel_kios as $row):
                        $rata2 = $row['hari_aktif'] > 0 ? $row['pendapatan_ini'] / $row['hari_aktif'] : 0;

                        if ($row['pendapatan_lalu'] > 0) {
                            $persen = (($row['pendapatan_ini'] - $row['pendapatan_lalu']) / $row['pendapatan_lalu']) * 100;
                        } else {
                            $persen = $row['pendapatan_ini'] > 0 ? 100 : 0;
                        }
                        $naik = $persen >= 0;
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_kios']) ?></td>
                        <td>Rp<?= number_format($row['pendapatan_ini'], 0, ',', '.') ?></td>
                        <td><?= number_format($row['transaksi_ini'], 0, ',', '.') ?></td>
                        <td>Rp<?= number_format($rata2, 0, ',', '.') ?></td>
                        <td class="<?= $naik ? 'badge-up' : 'badge-down' ?>">
                            <?= $naik ? '▲' : '▼' ?> <?= number_format(abs($persen), 1) ?>%
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const labelHari  = <?= json_encode($label_hari) ?>;
    const nilaiHarian = <?= json_encode($nilai_harian) ?>;

    new Chart(document.getElementById('chartTren'), {
        type: 'bar',
        data: {
            labels: labelHari,
            datasets: [{
                label: 'Pendapatan Harian',
                data: nilaiHarian,
                backgroundColor: '#f472b6',
                borderRadius: 4,
                maxBarThickness: 18
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    ticks: {
                        callback: (v) => 'Rp' + (v/1000).toLocaleString('id-ID') + 'rb'
                    }
                }
            }
        }
    });
</script>
</body>
</html>
