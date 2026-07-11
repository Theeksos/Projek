<?php
session_start();
require_once "../Logic/database.php"; 

// 1. Ambil kata kunci pencarian jika ada
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Query dasar menggabungkan tabel staff dengan tabel kios (Tanpa prefix tb_)
    $base_query = "SELECT s.*, k.nama_kios 
                   FROM staff s
                   LEFT JOIN kios k ON s.id_kios = k.id_kios";

    // 2. Query data staff (bisa disaring lewat input search)
    if (!empty($search)) {
        $query = $base_query . " WHERE s.nama_staff LIKE :search ORDER BY s.id_staff DESC";
        $stmt = $koneksi->prepare($query);
        $search_param = "%" . $search . "%";
        $stmt->bindParam(':search', $search_param);
    } else {
        $query = $base_query . " ORDER BY s.id_staff DESC";
        $stmt = $koneksi->prepare($query);
    }
    
    $stmt->execute();
    $all_staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Hitung Data Summary Staff secara dinamis
    $total_staff = count($all_staff);
    
    $q_open = $koneksi->query("SELECT COUNT(*) FROM staff WHERE status = 'active'");
    $staff_active = $q_open->fetchColumn();
    
    $q_close = $koneksi->query("SELECT COUNT(*) FROM staff WHERE status = 'nonactive'");
    $staff_nonactive = $q_close->fetchColumn();

} catch (PDOException $e) {
    die("Gagal mengambil data staff: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dough & Co - Staff</title>
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
                <a class="nav-link-custom" href="kios.php">Kios</a>
                <a class="nav-link-custom" href="#">Produk</a>
                <a class="nav-link-custom" href="stok.php">Stok</a>
                <a class="nav-link-custom active" href="staff.php">Staff</a>
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
                <h2 class="page-title">Manajemen Data Staff</h2>
                <div class="d-flex gap-2">
                    <form action="" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control search-input" placeholder="cari nama staff..." value="<?= htmlspecialchars($search); ?>">
                    </form>                    
                    <a href="../Logic/form/staff.php" class="btn btn-pink">+ Tambah Staff Baru</a>
                </div>
            </div>

            <!-- SUMMARY CARDS -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card summary-card card-total-stok">
                        <div class="card-label">Total Keseluruhan Staff</div>
                        <div class="card-value"><?= $total_staff; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card summary-card card-aktif">
                        <div class="card-label">Staff Aktif (Dinas)</div>
                        <div class="card-value"><?= $staff_active; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card summary-card card-kritis">
                        <div class="card-label">Staff Libur / Off</div>
                        <div class="card-value"><?= $staff_nonactive; ?></div>
                    </div>
                </div>
            </div>

            <!-- TABLE CONTAINER -->
            <div class="table-container">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Nama Staff</th>
                            <th scope="col">Kios Penugasan</th>
                            <th scope="col">Shift Kerja</th>
                            <th scope="col">Jenis Kelamin</th>
                            <th scope="col" class="text-center">Status Kerja</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($all_staff)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Tidak ada data staff ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($all_staff as $row): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_staff']); ?></td>
                                    <td>
                                        <span class="fw-semibold text-secondary">
                                            <?= htmlspecialchars($row['nama_kios'] ?? 'Belum Diplot Kios'); ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['shift']); ?></td>
                                    <td><?= htmlspecialchars($row['jenis_kelamin']); ?></td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill px-3 py-2 <?= $row['status'] == 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?= $row['status'] == 'active' ? 'Active' : 'Off'; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="../Logic/form/staff.php?id=<?= $row['id_staff']; ?>" class="btn btn-outline-pink">Edit</a>
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