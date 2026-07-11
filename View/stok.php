<?php
session_start();
require_once "../Logic/database.php"; 

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // 1. Query ambil data sesuai kolom baru tabel bahan_baku
    if (!empty($search)) {
        $query = "SELECT * FROM bahan_baku WHERE nama LIKE :search ORDER BY id ASC";
        $stmt = $koneksi->prepare($query);
        $search_param = "%" . $search . "%";
        $stmt->bindParam(':search', $search_param);
    } else {
        $query = "SELECT * FROM bahan_baku ORDER BY id ASC";
        $stmt = $koneksi->prepare($query);
    }
    
    $stmt->execute();
    $data_stok = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Logika Hitung 4 Bar Summary secara Dinamis
    $total_stok = count($data_stok);
    
    // Aktif: Jumlah masih di atas 100 dan di atas ROP
    $stok_aktif = $koneksi->query("SELECT COUNT(*) FROM bahan_baku WHERE jumlah > 100 AND jumlah > rop")->fetchColumn();
    
    // Kritis: Jumlah sudah menyentuh atau di bawah ROP
    $stok_kritis = $koneksi->query("SELECT COUNT(*) FROM bahan_baku WHERE jumlah <= rop")->fetchColumn();
    
    // Hampir Habis: Jumlah turun setengah (<= 100) tapi masih di atas ROP
    $stok_hampir = $koneksi->query("SELECT COUNT(*) FROM bahan_baku WHERE jumlah <= 100 AND jumlah > rop")->fetchColumn();

} catch (PDOException $e) {
    die("Gagal mengambil data stok: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dough & Co - Stok Bahan Baku</title>
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
                <a class="nav-link-custom active" href="stok.php">Stok</a>
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
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="page-title">Manajemen Bahan Baku</h2>
                <div class="d-flex gap-2">
                    <form action="" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control search-input" placeholder="cari bahan baku..." value="<?= htmlspecialchars($search); ?>">
                    </form>                    
                    <a href="../Logic/form/stok.php" class="btn btn-pink">+ Tambah Bahan Baku</a>
                </div>
            </div>

            <!-- 4 BAR SUMMARY CARDS -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card summary-card card-total-stok">
                        <div class="card-label">Total Stok</div>
                        <div class="card-value"><?= $total_stok; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card card-aktif">
                        <div class="card-label">Aktif</div>
                        <div class="card-value"><?= $stok_aktif; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card card-kritis">
                        <div class="card-label">Stok Kritis</div>
                        <div class="card-value"><?= $stok_kritis; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card card-warning">
                        <div class="card-label">Hampir Habis</div>
                        <div class="card-value"><?= $stok_hampir; ?></div>
                    </div>
                </div>
            </div>

            <!-- TABLE CONTAINER -->
            <div class="table-container">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 25%;">Nama Bahan</th>
                            <th scope="col" style="width: 15%;">Stok</th>
                            <th scope="col" style="width: 15%;">ROP</th>
                            <th scope="col" style="width: 30%;">Status</th>
                            <th scope="col" class="text-center" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data_stok)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data stok bahan baku.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data_stok as $row): 
                                $stok_skrg = (float)$row['jumlah'];
                                $rop_skrg  = (float)$row['rop'];
                                $satuan    = $row['satuan'];
                                
                                // Progress bar berdasarkan kapasitas MAX 200
                                $persen = ($stok_skrg / 200) * 100;
                                if($persen > 100) $persen = 100;
                                if($persen < 0) $persen = 0;

                                if ($stok_skrg <= $rop_skrg) {
                                    $bar_color = "bg-stok-kritis"; // Merah
                                } elseif ($stok_skrg <= 100) {
                                    $bar_color = "bg-stok-warning"; // Kuning
                                } else {
                                    $bar_color = "bg-stok-aman"; // Hijau
                                }
                            ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama']); ?></td>
                                    <td class="fw-semibold"><?= $stok_skrg . ' ' . $satuan; ?></td>
                                    <td class="text-secondary fw-semibold"><?= $rop_skrg . ' ' . $satuan; ?></td>
                                    <td>
                                        <div class="progress w-100">
                                            <div class="progress-bar <?= $bar_color; ?>" role="progressbar" style="width: <?= $persen; ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="../Logic/form/stok.php?id=<?= $row['id']; ?>" class="btn btn-outline-pink">Edit</a>
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