<?php
session_start();
require_once "../config/database.php"; 

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$halaman_aktif = "mitra";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    /* 
       LOGIKA BARU (GROUP_CONCAT):
       Kita mengambil data dari tb_user, lalu men-JOIN tabel kios berdasarkan id_user (pemiliknya).
       Fungsi GROUP_CONCAT akan otomatis menggabungkan nama-nama kios yang dimiliki oleh mitra yang sama
       menjadi satu baris teks dipisahkan oleh tanda koma (misal: "Kios Alun-Alun, Kios Sudirman").
    */
    $base_query = "SELECT u.id_user, u.nama_lengkap, u.role, 
                          GROUP_CONCAT(k.nama_kios SEPARATOR ', ') AS daftar_kios,
                          COUNT(k.id_kios) AS jumlah_kios_mitra
                   FROM tb_user u
                   LEFT JOIN kios k ON u.id_user = k.id_mitra
                   WHERE u.role = 'Mitra'";

    if (!empty($search)) {
        // Karena menggunakan GROUP BY, filter pencarian diletakkan sebelum GROUP BY
        $query_str = $base_query . " AND u.nama_lengkap LIKE :search GROUP BY u.id_user ORDER BY u.id_user DESC";
        $stmt = $koneksi->prepare($query_str);
        $search_param = "%" . $search . "%";
        $stmt->bindParam(':search', $search_param);
    } else {
        $query_str = $base_query . " GROUP BY u.id_user ORDER BY u.id_user DESC";
        $stmt = $koneksi->prepare($query_str);
    }
    
    $stmt->execute();
    $all_mitra = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- LOGIKA HITUNG SUMMARY BOX (STAT GRID) ---
    // 1. Total Mitra Unik
    $total_mitra = count($all_mitra);
    
    // 2. Total Keseluruhan Kios Beroperasi
    $total_kios = $koneksi->query("SELECT COUNT(*) FROM kios")->fetchColumn();
    
    // 3. Status Aktif (Dummy sesuai request)
    $mitra_aktif_dummy = $total_mitra;

} catch (PDOException $e) {
    die("Gagal mengambil data mitra: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dough & Co - Data Mitra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../Assets/css/dashboard.css">
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
        .kios-tag {
            background-color: #fdf2f8;
            color: #db2777;
            border: 1px solid #fbcfe8;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
            margin: 2px;
        }
        .kios-kosong {
            color: #9ca3af;
            font-style: italic;
            font-size: 0.88rem;
        }
    </style>
</head>
<body>

<div class="app-layout">
    
    <?php include "../includes/sidebar.php"; ?>

    <main class="main-content">
        
        <div class="page-topbar">
            <h4>Manajemen Kemitraan Multipos</h4>
            <div class="topbar-actions">
                <form action="" method="GET" style="display: flex; gap: 8px;">
                    <input type="text" name="search" class="input-search-custom" placeholder="Cari nama mitra..." value="<?= htmlspecialchars($search); ?>">
                </form>
            </div>
        </div>

        <div class="stat-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="stat-card stat-pink">
                <div class="stat-label">TOTAL MITRA TERDAFTAR</div>
                <div class="stat-value"><?= $total_mitra; ?></div>
            </div>
            <div class="stat-card stat-green">
                <div class="stat-label">TOTAL KIOS BEROPERASI</div>
                <div class="stat-value"><?= $total_kios; ?></div>
            </div>
            <div class="stat-card stat-orange">
                <div class="stat-label">MITRA STATUS AKTIF</div>
                <div class="stat-value"><?= $mitra_aktif_dummy; ?></div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-title">Daftar Kepemilikan Kios oleh Mitra</div>
            
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>ID User</th>
                        <th>Nama Lengkap Mitra</th>
                        <th style="text-align: center;">Jumlah Kios</th>
                        <th>Kios yang Dikelola</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($all_mitra)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #9ca3af; padding: 20px;">Tidak ada data mitra yang ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($all_mitra as $row): ?>
                            <tr>
                                <td style="color: #6b7280; font-weight: 500;">#USR-<?= $row['id_user']; ?></td>
                                <td style="font-weight: 600; color: #1f2937;"><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td style="text-align: center; font-weight: 600;">
                                    <span class="badge rounded-pill bg-dark" style="font-size: 0.8rem;">
                                        <?= $row['jumlah_kios_mitra']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($row['daftar_kios'])): ?>
                                        <?php 
                                        // Memecah kembali string koma menjadi array agar bisa dibentuk badge kotak-kotak kecil yang rapi
                                        $kios_array = explode(', ', $row['daftar_kios']);
                                        foreach ($kios_array as $kios_nama): 
                                        ?>
                                            <span class="kios-tag"><i class="bi bi-shop me-1"></i><?= htmlspecialchars($kios_nama); ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="kios-kosong">Belum memplot/memiliki kios</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="form/mitra.php?id=<?= $row['id_user']; ?>" class="btn-outline-pink">Edit</a>
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