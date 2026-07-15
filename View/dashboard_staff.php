<?php
session_start();

// 1. Proteksi Halaman: Hanya Staff yang boleh masuk
if (!isset($_SESSION['role']) || (strtolower($_SESSION['role']) !== 'staf' && strtolower($_SESSION['role']) !== 'staff')) {
    header("Location: ../login.php"); 
    exit;
}

require_once "../config/database.php"; 

// Tentukan menu aktif untuk sidebar
$halaman_aktif = "dashboard";

// Ambil ID Staff dari Session (yang disimpan sebagai id_user saat login)
$id_staff = $_SESSION['id_user'] ?? 0;
$nama_staff = $_SESSION['nama'] ?? 'Staff';

try {
    // --- 2. AMBIL INFO KIOS TEMPAT STAFF BEKERJA (RELASI SATU ARAH) ---
    // Mencari info kios dari tabel staff join ke kios lewat id_kios
    $query_kios = "SELECT k.id_kios, k.nama_kios 
                   FROM staff s
                   INNER JOIN kios k ON s.id_kios = k.id_kios 
                   WHERE s.id_staff = :id_staff 
                   LIMIT 1";
    $stmt_kios = $koneksi->prepare($query_kios);
    $stmt_kios->bindParam(':id_staff', $id_staff);
    $stmt_kios->execute();
    $kios_info = $stmt_kios->fetch(PDO::FETCH_ASSOC);

    $id_kios = $kios_info['id_kios'] ?? null;
    $nama_kios = $kios_info['nama_kios'] ?? 'Kios (Belum Ditugaskan)';

    // --- 3. AMBIL DATA STATISTIK UNTUK STAFF ---
    // A. Total Stok Produk Kios (Mengambil data global produk atau silakan sesuaikan jika ada tabel stok per kios)
    $total_stok = $koneksi->query("SELECT SUM(stok) FROM produk")->fetchColumn() ?? 0;

    // B. Produk dengan Stok Kritis (Stok <= ROP)
    $produk_kritis = $koneksi->query("SELECT COUNT(*) FROM produk WHERE stok <= rop")->fetchColumn() ?? 0;

    // C. Jumlah SOP Kerja Aktif
    $total_sop = $koneksi->query("SELECT COUNT(*) FROM tb_sop")->fetchColumn() ?? 0;


    // --- 4. QUERY DAFTAR PRODUK KRITIS (UNTUK SEGERA DI-RESTOCK) ---
    $stmt_kritis = $koneksi->query("
        SELECT nama, stok, rop, kategori 
        FROM produk 
        WHERE stok <= rop 
        ORDER BY stok ASC 
        LIMIT 4
    ");
    $list_kritis = $stmt_kritis->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Gagal memuat data dashboard staff: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dough & Co - Staff Dashboard</title>
    <!-- Link Aset -->
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
                <h4>Semangat Kerja, <?= htmlspecialchars($nama_staff); ?>! 🥖</h4>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Unit Kerja: <strong><?= htmlspecialchars($nama_kios); ?></strong></p>
            </div>
            <div class="topbar-actions">
                <span class="badge bg-light text-dark p-2" style="border: 1px solid #f0d3e3;">
                    <i class="bi bi-calendar3 me-2" style="color: #DB2777;"></i> <?= date('d M Y'); ?>
                </span>
            </div>
        </div>

        <!-- 3 STAT CARDS -->
        <div class="stat-grid" style="grid-template-columns: repeat(3, 1fr); gap: 16px;">
            <!-- Stat 1: Total Stok Global/Kios -->
            <div class="stat-card stat-pink">
                <div class="stat-label">TOTAL STOK PRODUK</div>
                <div class="stat-value"><?= number_format($total_stok, 0, ',', '.'); ?> <span style="font-size: 0.9rem; font-weight: normal;">Pcs</span></div>
            </div>
            <!-- Stat 2: Stok Kritis -->
            <div class="stat-card stat-orange">
                <div class="stat-label">STOK MENIPIS (KRITIS)</div>
                <div class="stat-value"><?= $produk_kritis; ?> <span style="font-size: 0.9rem; font-weight: normal; color: #d97706;">Menu</span></div>
            </div>
            <!-- Stat 3: Dokumen SOP -->
            <div class="stat-card" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%); border: 1px solid #bae6fd;">
                <div class="stat-label" style="color: #0369a1;">SOP & PANDUAN KERJA</div>
                <div class="stat-value" style="color: #0369a1;"><?= $total_sop; ?> <span style="font-size: 0.9rem; font-weight: normal;">Dokumen</span></div>
            </div>
        </div>

        <!-- TWO COLUMN LAYOUT -->
        <div class="row g-4 mt-2">
            
            <!-- Kiri: Peringatan Stok & Tombol Aksi Cepat (Kelola Stok & Transaksi) -->
            <div class="col-lg-7">
                <div class="panel mb-4">
                    <div class="panel-header-custom">
                        <div class="panel-title" style="color: #e25c05;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Peringatan Stok Kritis
                        </div>
                        <a href="stok.php" class="btn-lihat-all-orange">Kelola Stok</a>
                    </div>
                    <p class="text-muted mb-3" style="font-size: 0.8rem; margin-top: 4px;">Daftar menu di bawah ini sudah menyentuh atau berada di bawah batas ROP.</p>

                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th style="text-align: center;">Sisa Stok</th>
                                <th style="text-align: center;">Batas ROP</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($list_kritis)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Hebat! Semua stok produk aman.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($list_kritis as $row): ?>
                                    <tr>
                                        <td style="font-weight: 600; color: #1f2937;"><?= htmlspecialchars($row['nama']); ?></td>
                                        <td style="text-align: center; color: #dc2626; font-weight: bold;"><?= $row['stok']; ?></td>
                                        <td style="text-align: center; color: #9ca3af;"><?= $row['rop']; ?></td>
                                        <td style="text-align: center;">
                                            <span class="badge-status-kritis">Kritis</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Menu Pintasan Transaksi Kasir (Use Case: Input Transaksi) -->
                <div class="panel">
                    <div class="panel-title" style="color: #DB2777;">
                        <i class="bi bi-cart-plus-fill me-2"></i>Kasir Penjualan Kios
                    </div>
                    <p class="text-muted" style="font-size: 0.8rem; margin-top: 4px;">Gunakan tombol di bawah ini untuk mencatat transaksi pelanggan baru secara langsung.</p>
                    <div class="mt-3">
                        <a href="transaksi.php" class="btn-input-transaksi">
                            <i class="bi bi-calculator me-2"></i> Mulai Input Transaksi Baru
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kanan: Akses Cepat SOP Digital (Use Case: Akses SOP Digital) -->
            <div class="col-lg-5">
                <div class="panel h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="panel-header-custom">
                            <div class="panel-title" style="color: #0369a1;">
                                <i class="bi bi-journal-bookmark-fill me-2"></i>Akses SOP Digital
                            </div>
                            <a href="sop.php" class="btn-lihat-all-blue">Lihat Semua</a>
                        </div>
                        <p class="text-muted" style="font-size: 0.8rem; margin-top: 4px;">Bacalah panduan kerja standar untuk menjamin kualitas produk Dough & Co.</p>

                        <div class="sop-shortcut-list mt-3">
                            <div class="sop-card-shortcut">
                                <div class="sop-icon-box">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div class="sop-text-box">
                                    <div class="sop-title">Prosedur Buka Kios</div>
                                    <div class="sop-desc">Persiapan kebersihan, display roti, dan persiapan mesin kasir.</div>
                                </div>
                            </div>

                            <div class="sop-card-shortcut">
                                <div class="sop-icon-box">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div class="sop-text-box">
                                    <div class="sop-title">Standar Higienitas Produk</div>
                                    <div class="sop-desc">Aturan penanganan produk, penggunaan sarung tangan, & penjagaan suhu etalase.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sop-footer-box mt-4 pt-3 border-top text-center text-muted" style="font-size: 0.75rem;">
                        Butuh bantuan operasional? Segera hubungi Mitra/Owner kamu.
                    </div>
                </div>
            </div>

        </div>

    </main>
</div>

</body>
</html>

