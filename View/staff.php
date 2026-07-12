<?php
session_start();
require_once "../config/database.php"; 

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Query dasar menggabungkan tabel staff dengan tabel kios
    $base_query = "SELECT s.*, k.nama_kios 
                   FROM staff s
                   LEFT JOIN kios k ON s.id_kios = k.id_kios";

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

    // Hitung Ringkasan Data Staff
    $total_staff = count($all_staff);
    
    $staff_active = $koneksi->query("SELECT COUNT(*) FROM staff WHERE status = 'active'")->fetchColumn();
    $staff_nonactive = $koneksi->query("SELECT COUNT(*) FROM staff WHERE status = 'nonactive'")->fetchColumn();

} catch (PDOException $e) {
    die("Gagal mengambil data staff: " . $e->getMessage());
}

// Data info user di pojok kiri bawah
$user_login = $_SESSION['nama_user'] ?? 'Ryan';
$user_role  = $_SESSION['role_user'] ?? 'Mitra';
$inisial_user = strtoupper(substr($user_login, 0, 1));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dough & Co - Data Staff</title>
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
        .badge-active { color: #16a34a; font-weight: 600; }
        .badge-off { color: #dc2626; font-weight: 600; }
    </style>
</head>
<body>

<div class="app-layout">
    
    <!-- SIDEBAR -->
    <?php include "../includes/sidebar.php"; ?>


    <!-- MAIN CONTENT AREA -->
    <main class="main-content">
        
        <!-- TOPBAR ACTION -->
        <div class="page-topbar">
            <h4>Manajemen Data Staff</h4>
            <div class="topbar-actions">
                <form action="" method="GET" style="display: flex; gap: 8px;">
                    <input type="text" name="search" class="input-search-custom" placeholder="Cari nama staff..." value="<?= htmlspecialchars($search); ?>">
                </form>
                <a href="form/staff.php" class="btn-export">+ Tambah Staff</a>
            </div>
        </div>

        <!-- 3 COLUMNS STAT GRID -->
        <div class="stat-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="stat-card stat-pink">
                <div class="stat-label">TOTAL KESELURUHAN STAFF</div>
                <div class="stat-value"><?= $total_staff; ?></div>
            </div>
            <div class="stat-card stat-green">
                <div class="stat-label">STAFF AKTIF (DINAS)</div>
                <div class="stat-value"><?= $staff_active; ?></div>
            </div>
            <div class="stat-card stat-orange">
                <div class="stat-label">STAFF LIBUR / OFF</div>
                <div class="stat-value"><?= $staff_nonactive; ?></div>
            </div>
        </div>

        <!-- TABLE PANEL -->
        <div class="panel">
            <div class="panel-title">Daftar Aktif Staff Kios</div>
            
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Nama Staff</th>
                        <th>Kios Penugasan</th>
                        <th>Shift Kerja</th>
                        <th>Jenis Kelamin</th>
                        <th>Status Kerja</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($all_staff)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #9ca3af; padding: 20px;">Tidak ada data staff ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($all_staff as $row): ?>
                            <tr>
                                <td style="font-weight: 600; color: #1f2937;"><?= htmlspecialchars($row['nama_staff']); ?></td>
                                <td>
                                    <span style="font-weight: 500; color: #6b7280;">
                                        <?= htmlspecialchars($row['nama_kios'] ?? 'Belum Diplot Kios'); ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['shift']); ?></td>
                                <td><?= htmlspecialchars($row['jenis_kelamin']); ?></td>
                                <td>
                                    <?php if ($row['status'] == 'active'): ?>
                                        <span class="badge-active">● Active</span>
                                    <?php else: ?>
                                        <span class="badge-off">● Off</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="form/staff.php?id=<?= $row['id_staff']; ?>" class="btn-outline-pink">Edit</a>
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