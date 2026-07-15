<?php
session_start();
require_once "../config/database.php"; 

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$halaman_aktif = "produk";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_hapus = $_GET['id'];
    
    try {
        $sql_foto = "SELECT foto FROM produk WHERE id_produk = :id";
        $stmt_foto = $koneksi->prepare($sql_foto);
        $stmt_foto->bindParam(':id', $id_hapus);
        $stmt_foto->execute();
        $file_foto = $stmt_foto->fetchColumn();

        if ($file_foto && $file_foto !== 'default-product.png') {
            $path_foto = "../Assets/images/produk/" . $file_foto;
            if (file_exists($path_foto)) {
                unlink($path_foto);
            }
        }

        $sql_delete = "DELETE FROM produk WHERE id_produk = :id";
        $stmt_delete = $koneksi->prepare($sql_delete);
        $stmt_delete->bindParam(':id', $id_hapus);
        $stmt_delete->execute();

        echo "<script>alert('Produk berhasil dihapus!'); window.location='produk.php';</script>";
        exit;
    } catch (PDOException $e) {
        die("Gagal menghapus produk: " . $e->getMessage());
    }
}

try {
    if (!empty($search)) {
        $sql = "SELECT * FROM produk WHERE nama LIKE :search OR kategori LIKE :search ORDER BY id_produk DESC";
        $stmt = $koneksi->prepare($sql);
        $search_param = "%" . $search . "%";
        $stmt->bindParam(':search', $search_param);
    } else {
        $sql = "SELECT * FROM produk ORDER BY id_produk DESC";
        $stmt = $koneksi->prepare($sql);
    }
    $stmt->execute();
    $all_produk = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_produk = count($all_produk);

    $stat_kritis = $koneksi->query("SELECT COUNT(*) FROM produk WHERE stok <= rop")->fetchColumn();

    $stat_hampir = $koneksi->query("SELECT COUNT(*) FROM produk WHERE stok > rop AND stok <= (rop * 1.5)")->fetchColumn();

    $stat_aman = $koneksi->query("SELECT COUNT(*) FROM produk WHERE stok > (rop * 1.5)")->fetchColumn();

} catch (PDOException $e) {
    die("Gagal memuat data produk: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dough & Co - Manajemen Produk</title>
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
            width: 240px;
        }
        .input-search-custom:focus {
            outline: none;
            border-color: #DB2777;
        }
        .img-produk-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #f6e0ec;
        }
        .badge-status-aman {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-status-hampir {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-status-kritis {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="app-layout">
    
    <?php include "../includes/sidebar.php"; ?>

    <!-- MAIN CONTENT AREA -->
    <main class="main-content">
        
        <!-- TOPBAR ACTION -->
        <div class="page-topbar">
            <h4>Manajemen Menu & Produk</h4>
            <div class="topbar-actions">
                <form action="" method="GET" style="display: flex; gap: 8px;">
                    <input type="text" name="search" class="input-search-custom" placeholder="Cari menu atau kategori..." value="<?= htmlspecialchars($search); ?>">
                </form>
                <a href="form/produk.php" class="btn-export">+ Tambah Produk</a>
            </div>
        </div>

        <!-- 4 COLUMNS STAT GRID -->
        <div class="stat-grid" style="grid-template-columns: repeat(4, 1fr); gap: 16px;">
            <div class="stat-card stat-pink">
                <div class="stat-label">TOTAL PRODUK</div>
                <div class="stat-value"><?= $total_produk; ?></div>
            </div>
            <div class="stat-card stat-green">
                <div class="stat-label">STATUS AMAN</div>
                <div class="stat-value"><?= $stat_aman; ?></div>
            </div>
            <div class="stat-card stat-orange">
                <div class="stat-label">HAMPIR HABIS</div>
                <div class="stat-value"><?= $stat_hampir; ?></div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #fef2f2 0%, #ffe4e4 100%); border: 1px solid #fecaca;">
                <div class="stat-label" style="color: #dc2626;">STATUS KRITIS</div>
                <div class="stat-value" style="color: #dc2626;"><?= $stat_kritis; ?></div>
            </div>
        </div>

        <!-- TABLE PANEL -->
        <div class="panel">
            <div class="panel-title">Daftar Menu Dough & Co</div>
            
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">Foto</th>
                        <th>Nama Produk & Detail</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th style="text-align: center;">Stok</th>
                        <th style="text-align: center;">ROP</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center; width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($all_produk)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #9ca3af; padding: 30px;">Belum ada data produk yang terdaftar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($all_produk as $row): ?>
                            <tr>
                                <td style="text-align: center;">
                                    <img src="../Assets/images/produk/<?= htmlspecialchars($row['foto']); ?>" 
                                         class="img-produk-thumbnail" 
                                         alt="<?= htmlspecialchars($row['nama']); ?>"
                                         onerror="this.onerror=null; this.src='../Assets/images/default-product.png';">
                                </td>

                                <td>
                                    <div style="font-weight: 600; color: #1f2937; font-size: 0.95rem;">
                                        <?= htmlspecialchars($row['nama']); ?>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #6b7280; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 2px;">
                                        <?= htmlspecialchars($row['keterangan'] ?? 'Tidak ada keterangan.'); ?>
                                    </div>
                                </td>

                                <td style="font-weight: 500; color: #4b5563;">
                                    <span class="badge bg-light text-dark" style="border: 1px solid #e5e7eb; font-size: 0.78rem;">
                                        <?= htmlspecialchars($row['kategori']); ?>
                                    </span>
                                </td>

                                <td style="font-weight: 600; color: #DB2777;">
                                    Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                </td>

                                <td style="text-align: center; font-weight: 600; color: #374151;">
                                    <?= number_format($row['stok'], 0, ',', '.'); ?>
                                </td>

                                <td style="text-align: center; font-weight: 500; color: #9ca3af;">
                                    <?= number_format($row['rop'], 0, ',', '.'); ?>
                                </td>

                                <td style="text-align: center;">
                                    <?php 
                                    if ($row['stok'] <= $row['rop']) {
                                        echo '<span class="badge-status-kritis">Kritis</span>';
                                    } elseif ($row['stok'] <= ($row['rop'] * 1.5)) {
                                        echo '<span class="badge-status-hampir">Hampir Habis</span>';
                                    } else {
                                        echo '<span class="badge-status-aman">Aman</span>';
                                    }
                                    ?>
                                </td>

                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center;">
                                        <a href="form/produk.php?id=<?= $row['id_produk']; ?>" class="btn-outline-pink" style="padding: 4px 10px; font-size: 0.8rem;">Edit</a>
                                        <a href="produk.php?action=delete&id=<?= $row['id_produk']; ?>" 
                                           class="btn-lihat" 
                                           style="padding: 4px 10px; font-size: 0.8rem; background-color: #fef2f2; border-color: #fecaca; color: #dc2626; text-decoration: none;"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus produk <?= htmlspecialchars($row['nama']); ?>?');">
                                           Hapus
                                        </a>
                                    </div>
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