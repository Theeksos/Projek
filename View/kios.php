<?php
session_start();
require_once "../config/database.php"; 

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$halaman_aktif = "kios";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // 1. Query ambil data kios + gabung nama pemilik dari tb_user
    $query_str = "SELECT k.*, u.nama_lengkap AS nama_pemilik 
                  FROM kios k 
                  LEFT JOIN tb_user u ON k.id_mitra = u.id_user";
                  
    if (!empty($search)) {
        $query_str .= " WHERE k.nama_kios LIKE :search OR u.nama_lengkap LIKE :search ORDER BY k.id_kios ASC";
        $stmt = $koneksi->prepare($query_str);
        $search_param = "%" . $search . "%";
        $stmt->bindParam(':search', $search_param);
    } else {
        $query_str .= " ORDER BY k.id_kios ASC";
        $stmt = $koneksi->prepare($query_str);
    }
    
    $stmt->execute();
    $data_kios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Hitung data untuk 4 blok Stat Grid secara dinamis
    // 2. Hitung data untuk 4 blok Stat Grid secara dinamis
    $total_kios = count($data_kios);
    
    // Hitung Kios Buka dan Tutup berdasarkan kolom status
    $kios_buka  = $koneksi->query("SELECT COUNT(*) FROM kios WHERE status = 'buka'")->fetchColumn();
    $kios_tutup = $koneksi->query("SELECT COUNT(*) FROM kios WHERE status = 'tutup'")->fetchColumn();
    
    // Total Pendapatan / Omset
    $total_omset = $koneksi->query("SELECT SUM(pendapatan) FROM kios")->fetchColumn() ?? 0;

} catch (PDOException $e) {
    die("Gagal memuat data kios: " . $e->getMessage());
}

// Inisial untuk user login di pojok kiri bawah (Dummy / Sesuai Session Ryan)
$user_login = $_SESSION['nama_user'] ?? 'Ryan';
$user_role  = $_SESSION['role_user'] ?? 'Mitra';
$inisial_user = strtoupper(substr($user_login, 0, 1));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dough & Co - Manajemen Kios</title>
    <!-- Tetap bawa Bootstrap hanya untuk utilitas form grid & spasi jika dibutuhkan -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../Assets/css/dashboard.css">
    <!-- Tambahan input search manual agar selaras dengan style template baru -->
    <style>
        .input-search-custom {
            border: 1px solid #f0d3e3;
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 0.88rem;
            background-color: #ffffff;
            color: #374151;
            width: 220px;
        }
        .input-search-custom:focus {
            outline: none;
            border-color: #DB2777;
        }
    </style>
</head>
<body>

<div class="app-layout">
    
    <!-- SIDEBAR BARU -->
    <?php include "../includes/sidebar.php"; ?>


    <!-- MAIN CONTENT AREA -->
    <main class="main-content">
        
        <!-- TOPBAR ACTION -->
        <div class="page-topbar">
            <h4>Manajemen Data Kios</h4>
            <div class="topbar-actions">
                <form action="" method="GET" style="display: flex; gap: 8px;">
                    <input type="text" name="search" class="input-search-custom" placeholder="Cari kios atau pemilik..." value="<?= htmlspecialchars($search); ?>">
                </form>
                <a href="form/kios.php" class="btn-export">+ Tambah Kios</a>
            </div>
        </div>

        <!-- 4 COLUMNS STAT GRID REVISI -->
        <div class="stat-grid">
            <div class="stat-card stat-pink">
                <div class="stat-label">TOTAL KIOS</div>
                <div class="stat-value"><?= $total_kios; ?></div>
            </div>
            <div class="stat-card stat-green">
                <div class="stat-label">KIOS BUKA</div>
                <div class="stat-value"><?= $kios_buka; ?></div>
            </div>
            <div class="stat-card stat-orange">
                <div class="stat-label">KIOS TUTUP</div>
                <div class="stat-value"><?= $kios_tutup; ?></div>
            </div>
            <div class="stat-card stat-blue">
                <div class="stat-label">TOTAL PENDAPATAN</div>
                <div class="stat-value">Rp<?= number_format($total_omset, 0, ',', '.'); ?></div>
            </div>
        </div>

        <!-- TABLE PANEL -->
        <div class="panel">
            <div class="panel-title">Daftar Kios Dough & Co</div>
            
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Nama Kios</th>
                        <th>Alamat / Lokasi</th>
                        <th>Pemilik (Mitra)</th>
                        <th>Pendapatan</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_kios)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #9ca3af; padding: 20px;">Belum ada data kios yang tersedia.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data_kios as $row): ?>
                            <tr>
                                <td style="font-weight: 600; color: #1f2937;"><?= htmlspecialchars($row['nama_kios']); ?></td>
                                <td><?= htmlspecialchars($row['lokasi']); ?></td>
                                <td>
                                    <span style="font-weight: 500;">
                                        <?= htmlspecialchars($row['nama_pemilik'] ?? 'Tidak Ada Pemilik'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-up">
                                        Rp<?= number_format($row['pendapatan'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'buka'): ?>
                                        <span class="badge-up">● Buka</span>
                                    <?php else: ?>
                                        <span class="badge-down">● Tutup</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="form/kios.php?id=<?= $row['id_kios']; ?>" class="btn-outline-pink">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>

</body>
</html>