<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'mitra') {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php"; 

$halaman_aktif = "dashboard";

try {
    $total_kios = $koneksi->query("SELECT COUNT(*) FROM kios")->fetchColumn();

    $total_staf = $koneksi->query("SELECT COUNT(*) FROM tb_user WHERE role = 'Staff' OR role = 'Kasir'")->fetchColumn();

    $produk_kritis = $koneksi->query("SELECT COUNT(*) FROM produk WHERE stok <= rop")->fetchColumn();

    $total_aset = $koneksi->query("SELECT SUM(stok * harga) FROM produk")->fetchColumn() ?? 0;


    $stmt_kios = $koneksi->query("
        SELECT k.id_kios, k.nama_kios, u.nama_lengkap as nama_mitra 
        FROM kios k
        LEFT JOIN tb_user u ON k.id_mitra = u.id_user
        LIMIT 5
    ");
    $ringkasan_kios = $stmt_kios->fetchAll(PDO::FETCH_ASSOC);


    $stmt_alert = $koneksi->query("
        SELECT nama, stok, rop, kategori 
        FROM produk 
        WHERE stok <= rop 
        ORDER BY stok ASC 
        LIMIT 5
    ");
    $alert_produk = $stmt_alert->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Gagal memuat data dashboard: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dough & Co - Owner Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../Assets/css/dashboard.css">
</head>
<body>

<div class="app-layout">
    
    <?php include "../includes/sidebar.php"; ?>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        
        <!-- TOPBAR -->
        <div class="page-topbar">
            <div>
                <h4>Selamat Datang, <?= htmlspecialchars($_SESSION['nama'] ?? 'Owner'); ?>! 👋</h4>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Berikut ringkasan operasional Dough & Co hari ini.</p>
            </div>
            <div class="topbar-actions">
                <span class="badge bg-light text-dark p-2" style="border: 1px solid #f0d3e3;">
                    <i class="bi bi-calendar3 me-2" style="color: #DB2777;"></i> <?= date('d M Y'); ?>
                </span>
            </div>
        </div>

        <div class="stat-grid" style="grid-template-columns: repeat(4, 1fr); gap: 16px;">
            <div class="stat-card stat-pink">
                <div class="stat-label">ESTIMASI NILAI PRODUK</div>
                <div class="stat-value" style="font-size: 1.5rem;">Rp <?= number_format($total_aset, 0, ',', '.'); ?></div>
            </div>
            <div class="stat-card stat-green">
                <div class="stat-label">TOTAL KIOS MITRA</div>
                <div class="stat-value"><?= $total_kios; ?> <span style="font-size: 0.9rem; font-weight: normal; color: #059669;">Kios</span></div>
            </div>
            <div class="stat-card stat-orange">
                <div class="stat-label">PRODUK KRITIS</div>
                <div class="stat-value"><?= $produk_kritis; ?> <span style="font-size: 0.9rem; font-weight: normal; color: #d97706;">Menu</span></div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #f3e8ff 0%, #fae8ff 100%); border: 1px solid #e9d5ff;">
                <div class="stat-label" style="color: #7c3aed;">TOTAL STAF</div>
                <div class="stat-value" style="color: #7c3aed;"><?= $total_staf; ?> <span style="font-size: 0.9rem; font-weight: normal;">Orang</span></div>
            </div>
        </div>

        <!-- TWO COLUMN LAYOUT -->
        <div class="row g-4 mt-2">
            
            <div class="col-lg-7">
                <div class="panel h-100">
                    <div class="panel-header-custom">
                        <div class="panel-title">Daftar Kios Dough & Co</div>
                        <a href="kios.php" class="btn-lihat-all">Lihat Semua Kios</a>
                    </div>
                    
                    <table class="table-custom mt-3">
                        <thead>
                            <tr>
                                <th>Nama Kios</th>
                                <th>Penanggung Jawab (Mitra)</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ringkasan_kios)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada kios terdaftar.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ringkasan_kios as $kios): ?>
                                    <tr>
                                        <td style="font-weight: 600; color: #1f2937;">
                                            <i class="bi bi-shop me-2 text-pink"></i><?= htmlspecialchars($kios['nama_kios']); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark" style="border: 1px solid #e5e7eb;">
                                                <?= htmlspecialchars($kios['nama_mitra'] ?? 'Belum ada mitra'); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="kios.php" class="btn-outline-pink" style="padding: 4px 10px; font-size: 0.78rem; text-decoration: none;">Detail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="panel h-100">
                    <div class="panel-title text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Peringatan Restock Produk
                    </div>
                    <p class="text-muted" style="font-size: 0.8rem; margin-top: 4px;">Daftar menu yang stoknya sudah menyentuh atau berada di bawah nilai ROP.</p>
                    
                    <div class="alert-list-container mt-3">
                        <?php if (empty($alert_produk)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-check2-circle text-success" style="font-size: 2.5rem;"></i>
                                <div class="mt-2" style="font-size: 0.88rem; font-weight: 600;">Semua Stok Aman!</div>
                                <div style="font-size: 0.78rem;">Belum ada produk yang menyentuh batas kritis.</div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($alert_produk as $alert): ?>
                                <div class="alert-item-custom">
                                    <div class="alert-icon-box">
                                        <i class="bi bi-box-seam" style="color: #dc2626;"></i>
                                    </div>
                                    <div class="alert-info-box">
                                        <div class="alert-product-name"><?= htmlspecialchars($alert['nama']); ?></div>
                                        <div class="alert-product-meta">Kategori: <?= htmlspecialchars($alert['kategori']); ?></div>
                                    </div>
                                    <div class="alert-qty-box">
                                        <span class="badge-status-kritis" style="font-size: 0.7rem;">Stok: <?= $alert['stok']; ?></span>
                                        <div style="font-size: 0.68rem; color: #9ca3af; text-align: right; margin-top: 2px;">ROP: <?= $alert['rop']; ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-end mt-3">
                                <a href="produk.php" class="btn-lihat-all text-danger" style="border-color: #fecaca;">Kelola Stok Produk</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

    </main>
</div>

</body>
</html>
