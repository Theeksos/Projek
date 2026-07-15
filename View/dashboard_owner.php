<?php
session_start();

// 1. Proteksi Halaman: Hanya Owner/Admin yang boleh masuk
if (!isset($_SESSION['role']) || (strtolower($_SESSION['role']) !== 'owner' && strtolower($_SESSION['role']) !== 'admin')) {
    header("Location: ../login.php"); 
    exit;
}

require_once "../config/database.php"; 

// Tentukan menu aktif untuk sidebar
$halaman_aktif = "dashboard";

try {
    // --- 2. AMBIL DATA STATISTIK UTAMA (Use Case: Akses Laporan & Kelola Mitra) ---
    // A. Total Pendapatan Seluruh Kios (Simulasi / Jika ada tabel transaksi)
    // $total_pendapatan = $koneksi->query("SELECT SUM(total_harga) FROM transaksi")->fetchColumn() ?? 0;
    $total_pendapatan = 24500000; // Representasi visual awal rupiah

    // B. Total Mitra Aktif (Use Case: Kelola Data Mitra)
    $total_mitra = $koneksi->query("SELECT COUNT(*) FROM tb_user WHERE role = 'Mitra'")->fetchColumn() ?? 0;

    // C. Total Produk Terdaftar (Use Case: Kelola Data Produk)
    $total_produk = $koneksi->query("SELECT COUNT(*) FROM produk")->fetchColumn() ?? 0;

    // D. Total SOP Digital Terbit (Use Case: Kelola & Akses SOP)
    $total_sop = $koneksi->query("SELECT COUNT(*) FROM tb_sop")->fetchColumn() ?? 0;


    // --- 3. QUERY DAFTAR MITRA TERBARU (Use Case: Kelola Data Mitra) ---
    // Mengambil daftar user dengan role Mitra
    $stmt_mitra = $koneksi->query("
        SELECT id_user, nama_lengkap
        FROM tb_user 
        WHERE role = 'Mitra' 
        LIMIT 4
    ");
    $list_mitra = $stmt_mitra->fetchAll(PDO::FETCH_ASSOC);


    // --- 4. QUERY PRODUK DENGAN STOK MENIPIS (Use Case: Kelola Data Produk) ---
    // Membantu owner memantau produk yang butuh perhatian khusus
    $stmt_produk_kritis = $koneksi->query("
        SELECT nama, stok, rop 
        FROM produk 
        WHERE stok <= rop 
        LIMIT 4
    ");
    $produk_kritis = $stmt_produk_kritis->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Gagal memuat data dashboard owner: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dough & Co - Owner Dashboard</title>
    <!-- Link Aset Global -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../Assets/css/dashboard.css">
</head>
<body>

<div class="app-layout">
    
    <!-- Include Sidebar Terfilter -->
    <?php include "../includes/sidebar.php"; ?>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        
        <!-- TOPBAR -->
        <div class="page-topbar">
            <div>
                <h4>Selamat Datang, Owner! 👑</h4>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Panel kendali utama operasional dan kemitraan Dough & Co.</p>
            </div>
            <div class="topbar-actions">
                <span class="badge bg-light text-dark p-2" style="border: 1px solid #f0d3e3;">
                    <i class="bi bi-calendar3 me-2" style="color: #DB2777;"></i> <?= date('d M Y'); ?>
                </span>
            </div>
        </div>

        <!-- 4 STAT CARDS (Menggunakan Grid bawaan CSS global) -->
        <div class="stat-grid" style="grid-template-columns: repeat(4, 1fr); gap: 16px;">
            <!-- Stat 1: Pendapatan Global (Use Case: Laporan) -->
            <div class="stat-card stat-pink">
                <div class="stat-label">OMSET SELURUH KIOS</div>
                <div class="stat-value" style="font-size: 1.4rem;">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></div>
            </div>
            <!-- Stat 2: Total Mitra (Use Case: Kelola Mitra) -->
            <div class="stat-card" style="background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); border: 1px solid #ddd6fe;">
                <div class="stat-label" style="color: #7c3aed;">MITRA BERGABUNG</div>
                <div class="stat-value" style="color: #7c3aed;"><?= $total_mitra; ?> <span style="font-size: 0.9rem; font-weight: normal;">Partner</span></div>
            </div>
            <!-- Stat 3: Total Produk (Use Case: Kelola Produk) -->
            <div class="stat-card stat-orange">
                <div class="stat-label">VARIAN PRODUK</div>
                <div class="stat-value"><?= $total_produk; ?> <span style="font-size: 0.9rem; font-weight: normal; color: #d97706;">Menu</span></div>
            </div>
            <!-- Stat 4: Total SOP (Use Case: Kelola SOP) -->
            <div class="stat-card" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0;">
                <div class="stat-label" style="color: #16a34a;">SOP DIGITAL</div>
                <div class="stat-value" style="color: #16a34a;"><?= $total_sop; ?> <span style="font-size: 0.9rem; font-weight: normal;">Dokumen</span></div>
            </div>
        </div>

        <!-- TWO COLUMN LAYOUT -->
        <div class="row g-4 mt-2">
            
            <!-- Kiri: Kelola Data Mitra (Use Case: Kelola Data Mitra) -->
            <div class="col-lg-7">
                <div class="panel h-100">
                    <div class="panel-header-custom">
                        <div class="panel-title" style="color: #7c3aed;">
                            <i class="bi bi-people-fill me-2"></i>Manajemen Kemitraan
                        </div>
                        <a href="mitra.php" class="btn-lihat-all-purple">Kelola Mitra</a>
                    </div>
                    <p class="text-muted" style="font-size: 0.8rem; margin-top: 4px;">Daftar mitra aktif pemilik hak waralaba gerai/kios Dough & Co.</p>
                    
                    <table class="table-custom mt-3">
                        <thead>
                            <tr>
                                <th>Nama Mitra</th>
                                <th style="text-align: center;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($list_mitra)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada mitra terdaftar.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($list_mitra as $mitra): ?>
                                    <tr>
                                        <td style="font-weight: 600; color: #1f2937;">
                                            <i class="bi bi-person-badge-fill me-2 text-purple"></i><?= htmlspecialchars($mitra['nama_lengkap']); ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="mitra.php" class="btn bg-light text-dark px-2 py-1" style="font-size: 0.72rem; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none;">Detail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kanan: Monitoring Stok Produk & SOP (Use Case: Kelola Produk & SOP) -->
            <div class="col-lg-5">
                <div class="panel h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="panel-header-custom">
                            <div class="panel-title" style="color: #ea580c;">
                                <i class="bi bi-box-seam-fill me-2"></i>Pantau Stok Menu
                            </div>
                            <a href="produk.php" class="btn-lihat-all-orange">Kelola Produk</a>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 0.8rem; margin-top: 4px;">Menu dengan sisa stok kritis yang butuh dorongan suplai produksi.</p>

                        <div class="owner-alert-list">
                            <?php if (empty($produk_kritis)): ?>
                                <div class="text-center py-4 text-muted" style="font-size: 0.8rem;">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i> Semua stok menu aman terdistribusi.
                                </div>
                            <?php else: ?>
                                <?php foreach ($produk_kritis as $prod): ?>
                                    <div class="alert-item-mini">
                                        <span class="product-name-txt"><?= htmlspecialchars($prod['nama']); ?></span>
                                        <span class="badge bg-danger-subtle text-danger" style="font-size: 0.72rem; font-weight: 600;">Sisa: <?= $prod['stok']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Shortcut Use Case: Kelola & Akses SOP Digital -->
                    <div class="sop-shortcut-owner mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div style="font-weight: 600; color: #1e293b; font-size: 0.88rem;">SOP Digital</div>
                                <div style="font-size: 0.75rem; color: #64748b;">Kelola standar baku Dough & Co.</div>
                            </div>
                            <a href="sop.php" class="btn-lihat-all">Buka & Edit SOP <i class="bi bi-arrow-right-short"></i></a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>
</div>

</body>
</html>