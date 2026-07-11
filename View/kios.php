<?php
session_start();
require_once "../Logic/database.php"; 

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $base_query = "SELECT k.*, u.nama_lengkap AS nama_mitra, 
                   COUNT(s.id_staff) AS jumlah_staff 
                   FROM kios k
                   LEFT JOIN tb_user u ON k.id_mitra = u.id_user
                   LEFT JOIN staff s ON k.id_kios = s.id_kios";

    if (!empty($search)) {
        $query = $base_query . " WHERE k.nama_kios LIKE :search GROUP BY k.id_kios ORDER BY k.id_kios ASC";
        $stmt = $koneksi->prepare($query);
        $search_param = "%" . $search . "%";
        $stmt->bindParam(':search', $search_param);
    } else {
        $query = $base_query . " GROUP BY k.id_kios ORDER BY k.id_kios ASC";
        $stmt = $koneksi->prepare($query);
    }
    
    $stmt->execute();
    $data_kios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hitung Summary
    $total_kios = count($data_kios);
    $total_buka = $koneksi->query("SELECT COUNT(*) FROM kios WHERE status = 'buka'")->fetchColumn();
    $total_omset = $koneksi->query("SELECT SUM(pendapatan) FROM kios")->fetchColumn() ?: 0;

} catch (PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dough & Co - Kios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/css/style.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar">
            <div class="brand-title">Dough & Co</div>
            <nav class="nav flex-column">
                <a class="nav-link-custom" href="#">Dashboard</a>
                <a class="nav-link-custom active" href="kios.php">Kios</a>
                <a class="nav-link-custom" href="#">Produk</a>
                <a class="nav-link-custom" href="stok.php">Stok</a>
                <a class="nav-link-custom" href="staff.php">Staff</a>
                <a class="nav-link-custom" href="#">Laporan</a>
                <a class="nav-link-custom" href="#">Transaksi</a>
                <hr style="border-color: #FF3377; margin: 1rem 0;">
                <a class="nav-link-custom" href="#">Mitra</a>
                <a class="nav-link-custom" href="#">SOP</a>
                <button class="btn btn-logout">Logout</button>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-10 main-content">
            
            <!-- HEADER SEARCH & BUTTON -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="page-title">Manajemen Data Kios</h2>
                <div class="d-flex gap-2">
                    <form action="" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control search-input" placeholder="cari nama kios..." value="<?= htmlspecialchars($search); ?>">
                    </form>                    
                    <a href="../Logic/form/kios.php" class="btn btn-pink">+ Tambah Kios Baru</a>
                </div>
            </div>

            <!-- CARDS (5 Kolom Mini Sesuai Layout Figma) -->
            <div class="row g-3 mb-4">
                <div class="col">
                    <div class="card summary-card card-total-stok">
                        <div class="card-label">Total Kios</div>
                        <div class="card-value"><?= $total_kios; ?></div>
                    </div>
                </div>
                <div class="col">
                    <div class="card summary-card card-aktif">
                        <div class="card-label">Kios Buka</div>
                        <div class="card-value"><?= $total_buka; ?></div>
                    </div>
                </div>
                <div class="col">
                    <div class="card summary-card card-kritis">
                        <div class="card-label">Kios Tutup</div>
                        <div class="card-value"><?= $total_kios - $total_buka; ?></div>
                    </div>
                </div>
                <div class="col">
                    <div class="card summary-card card-warning">
                        <div class="card-label">Total Omset</div>
                        <div class="card-value" style="font-size: 1.8rem; font-weight:800;">Rp <?= number_format($total_omset, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>

            <!-- TABLE CONTAINER -->
            <div class="table-container">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Nama Kios</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Mitra Penanggung Jawab</th>
                            <th scope="col">Total Pendapatan</th>
                            <th scope="col" class="text-center">Jumlah Staff</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data_kios)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Tidak ada data kios ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data_kios as $row): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_kios']); ?></td>
                                    <td><?= htmlspecialchars($row['lokasi']); ?></td>
                                    <td><span class="fw-semibold text-secondary"><?= htmlspecialchars($row['nama_mitra'] ?? 'Belum Ditunjuk'); ?></span></td>
                                    <td class="text-success fw-bold">Rp <?= number_format($row['pendapatan'], 0, ',', '.'); ?></td>
                                    <td class="text-center fw-bold"><?= $row['jumlah_staff']; ?> Orang</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill px-3 py-2 <?= $row['status'] == 'buka' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?= ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="../Logic/form/kios.php?id=<?= $row['id_kios']; ?>" class="btn btn-outline-pink">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

</body>
</html>