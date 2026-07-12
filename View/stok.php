<?php
session_start();
require_once "../config/database.php"; 

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // 1. Query langsung ke tabel bahan_baku tanpa relasi ke tabel lain
    $query_str = "SELECT * FROM bahan_baku";
    
    if (!empty($search)) {
        $query_str .= " WHERE nama LIKE :search ORDER BY id DESC";
        $stmt = $koneksi->prepare($query_str);
        $search_param = "%" . $search . "%";
        $stmt->bindParam(':search', $search_param);
    } else {
        $query_str .= " ORDER BY id DESC";
        $stmt = $koneksi->prepare($query_str);
    }
    
    $stmt->execute();
    $all_stok = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Hitung ringkasan data berdasarkan nilai ROP secara dinamis
    $total_jenis = count($all_stok);
    
    // Stok kritis jika jumlah kurang dari atau sama dengan nilai ROP ($jumlah \le ROP$)
    $stok_kritis = $koneksi->query("SELECT COUNT(*) FROM bahan_baku WHERE jumlah <= rop")->fetchColumn();
    
    // Stok aman jika jumlah berada di atas nilai ROP ($jumlah > ROP$)
    $stok_aman = $koneksi->query("SELECT COUNT(*) FROM bahan_baku WHERE jumlah > rop")->fetchColumn();

} catch (PDOException $e) {
    die("Gagal mengambil data bahan baku: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dough & Co - Stok Bahan Baku</title>
    <!-- Aturan Link Aset untuk View Utama -->
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
    </style>
</head>
<body>

<div class="app-layout">
    
    <!-- Include Sidebar Utama -->
    <?php include "../includes/sidebar.php"; ?>

    <!-- MAIN CONTENT AREA -->
    <main class="main-content">
        
        <!-- TOPBAR ACTION -->
        <div class="page-topbar">
            <h4>Manajemen Stok Bahan Baku</h4>
            <div class="topbar-actions">
                <form action="" method="GET" style="display: flex; gap: 8px;">
                    <input type="text" name="search" class="input-search-custom" placeholder="Cari nama bahan..." value="<?= htmlspecialchars($search); ?>">
                </form>
                <a href="form/stok.php" class="btn-export">+ Tambah Bahan</a>
            </div>
        </div>

        <!-- 3 COLUMNS STAT GRID REVISI -->
        <div class="stat-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="stat-card stat-pink">
                <div class="stat-label">JENIS STOK (VARIAN)</div>
                <div class="stat-value"><?= $total_jenis; ?></div>
            </div>
            <div class="stat-card stat-green">
                <div class="stat-label">STOK AMAN (> ROP)</div>
                <div class="stat-value"><?= $stok_aman; ?></div>
            </div>
            <div class="stat-card stat-orange">
                <div class="stat-label">STOK KRITIS (<= ROP)</div>
                <div class="stat-value"><?= $stok_kritis; ?></div>
            </div>
        </div>

        <!-- TABLE PANEL -->
        <div class="panel">
            <div class="panel-title">Daftar Inventaris Bahan Baku</div>
            
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Nama Bahan Baku</th>
                        <th>Jumlah Stok</th>
                        <th>Satuan</th>
                        <th>Batas ROP</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($all_stok)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #9ca3af; padding: 20px;">Tidak ada data bahan baku ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($all_stok as $row): ?>
                            <tr>
                                <td style="font-weight: 600; color: #1f2937;"><?= htmlspecialchars($row['nama']); ?></td>
                                <td style="font-weight: 600;"><?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
                                <td><?= htmlspecialchars($row['satuan'] ?? 'Gram'); ?></td>
                                <td style="color: #6b7280; font-weight: 500;"><?= number_format($row['rop'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php if ($row['jumlah'] <= $row['rop']): ?>
                                        <span class="badge-down"><i class="bi bi-exclamation-triangle-fill"></i> Kritis</span>
                                    <?php else: ?>
                                        <span class="badge-up"><i class="bi bi-check-circle-fill"></i> Aman</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="form/stok.php?id=<?= $row['id']; ?>" class="btn-outline-pink">Edit</a>
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