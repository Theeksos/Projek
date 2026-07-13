<?php
/**
 * File: staf.php
 * Fungsi: Halaman Manajemen Staf (Presentation + Logic Layer)
 * Menampilkan ringkasan jumlah staf, pencarian, dan tabel data staf
 * per kios. Sesuai halaman "3. HALAMAN STAF" pada makalah.
 */

session_start();
require_once "config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$halaman_aktif = "staf";

// =========================================================
// 1. Kata kunci pencarian (dari kotak "Cari staf...")
// =========================================================
$cari = trim($_GET['cari'] ?? '');

// =========================================================
// 2. Query 4 kartu ringkasan
// =========================================================
$total_staf = $koneksi->query("SELECT COUNT(*) FROM tb_staf")->fetchColumn();
$staf_aktif = $koneksi->query("SELECT COUNT(*) FROM tb_staf WHERE status = 'Aktif'")->fetchColumn();
$staf_non_aktif = $koneksi->query("SELECT COUNT(*) FROM tb_staf WHERE status = 'Non Aktif'")->fetchColumn();
$total_kios = $koneksi->query("SELECT COUNT(*) FROM tb_kios")->fetchColumn();

// =========================================================
// 3. Query tabel staf (JOIN ke tb_kios untuk dapat nama kios)
//    + filter pencarian kalau ada kata kunci
// =========================================================
if ($cari !== '') {
    $stmt = $koneksi->prepare("
        SELECT s.*, k.nama_kios
        FROM tb_staf s
        JOIN tb_kios k ON k.id_kios = s.id_kios
        WHERE s.nama_staf LIKE :cari OR k.nama_kios LIKE :cari
        ORDER BY s.nama_staf ASC
    ");
    $kata_cari = "%$cari%";
    $stmt->bindParam(':cari', $kata_cari);
    $stmt->execute();
} else {
    $stmt = $koneksi->query("
        SELECT s.*, k.nama_kios
        FROM tb_staf s
        JOIN tb_kios k ON k.id_kios = s.id_kios
        ORDER BY s.nama_staf ASC
    ");
}
$daftar_staf = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Staf - Dough & Co Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
<div class="app-layout">

    <?php include "includes/sidebar.php"; ?>

    <main class="main-content">
        <div class="page-topbar">
            <h4>Manajemen Staf</h4>
            <div class="topbar-actions">
                <form method="GET" class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="cari" placeholder="Cari staf..." value="<?= htmlspecialchars($cari) ?>">
                </form>
                <a href="form_staf.php" class="btn-export"><i class="bi bi-plus-lg"></i> Tambah Staf</a>
            </div>
        </div>

        <!-- 4 Kartu ringkasan -->
        <div class="stat-grid">
            <div class="stat-card stat-pink">
                <div class="stat-label">Total Staf</div>
                <div class="stat-value"><?= number_format($total_staf, 0, ',', '.') ?></div>
            </div>
            <div class="stat-card stat-green">
                <div class="stat-label">Aktif</div>
                <div class="stat-value"><?= number_format($staf_aktif, 0, ',', '.') ?></div>
            </div>
            <div class="stat-card stat-orange">
                <div class="stat-label">Non Aktif</div>
                <div class="stat-value"><?= number_format($staf_non_aktif, 0, ',', '.') ?></div>
            </div>
            <div class="stat-card stat-blue">
                <div class="stat-label">Kios</div>
                <div class="stat-value"><?= number_format($total_kios, 0, ',', '.') ?></div>
            </div>
        </div>

        <!-- Tabel data staf -->
        <div class="panel">
            <div class="panel-title">Daftar Staf</div>
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Nama Staf</th>
                        <th>Kios</th>
                        <th>Shift</th>
                        <th>Jenis Kelamin</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftar_staf)): ?>
                        <tr><td colspan="6" class="text-muted">Tidak ada staf yang cocok dengan pencarian "<?= htmlspecialchars($cari) ?>".</td></tr>
                    <?php endif; ?>

                    <?php foreach ($daftar_staf as $staf): ?>
                    <tr>
                        <td><?= htmlspecialchars($staf['nama_staf']) ?></td>
                        <td><?= htmlspecialchars($staf['nama_kios']) ?></td>
                        <td><?= htmlspecialchars($staf['shift']) ?></td>
                        <td><?= htmlspecialchars($staf['jenis_kelamin']) ?></td>
                        <td>
                            <?php $kelasBadge = $staf['status'] === 'Aktif' ? 'status-aktif' : 'status-nonaktif'; ?>
                            <span class="status-badge <?= $kelasBadge ?>"><?= htmlspecialchars($staf['status']) ?></span>
                        </td>
                        <td><a href="form_staf.php?id=<?= $staf['id_staf'] ?>" class="btn-edit-kecil">Edit</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
