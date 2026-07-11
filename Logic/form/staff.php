<?php
session_start();
require_once "../database.php"; 

$is_edit = false;
$id            = '';
$nama_staff    = '';
$id_kios       = ''; 
$shift         = '';
$jenis_kelamin = '';
$status        = 'active'; 

try {
    // Ambil data pilihan kios langsung dari tabel kios untuk drop-down
    $stmt_kios = $koneksi->query("SELECT id_kios, nama_kios FROM kios ORDER BY nama_kios ASC");
    $list_kios = $stmt_kios->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data pilihan kios: " . $e->getMessage());
}

// --- LOGIKA DETEKSI MODE EDIT ---
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $is_edit = true;
    $id      = $_GET['id'];

    try {
        $stmt = $koneksi->prepare("SELECT * FROM staff WHERE id_staff = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            header("Location: staff.php");
            exit;
        }

        $nama_staff    = $data['nama_staff'];
        $id_kios       = $data['id_kios']; 
        $shift         = $data['shift'];
        $jenis_kelamin = $data['jenis_kelamin'];
        $status        = $data['status'];
    } catch (PDOException $e) {
        die("Error mengambil data lama staff: " . $e->getMessage());
    }
}

// --- LOGIKA PROSES SIMPAN (INSERT / UPDATE) ---
if (isset($_POST['simpan'])) {
    $form_id       = $_POST['id']; 
    $nama_staff    = trim($_POST['nama_staff']);
    $form_id_kios  = $_POST['id_kios']; 
    $shift         = $_POST['shift'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $status        = $_POST['status'];

    try {
        if (!empty($form_id)) {
            // Jalur Update Data Lama
            $sql = "UPDATE staff SET 
                        nama_staff = :nama_staff, 
                        id_kios = :id_kios, 
                        shift = :shift, 
                        jenis_kelamin = :jenis_kelamin, 
                        status = :status 
                    WHERE id_staff = :id";
            $stmt = $koneksi->prepare($sql);
            $stmt->bindParam(':id', $form_id);
            $pesan_sukses = "Data staff berhasil diperbarui!";
        } else {
            // Jalur Tambah Data Baru
            $sql = "INSERT INTO staff (nama_staff, id_kios, shift, jenis_kelamin, status) 
                    VALUES (:nama_staff, :id_kios, :shift, :jenis_kelamin, :status)";
            $stmt = $koneksi->prepare($sql);
            $pesan_sukses = "Staff baru berhasil didaftarkan!";
        }

        $stmt->bindParam(':nama_staff', $nama_staff);
        $stmt->bindParam(':id_kios', $form_id_kios);
        $stmt->bindParam(':shift', $shift);
        $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            echo "<script>alert('$pesan_sukses'); window.location='../../View/staff.php';</script>";
            exit;
        }

    } catch (PDOException $e) {
        die("Terjadi kesalahan sistem saat menyimpan: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? "Edit Staff" : "Tambah Staff"; ?> - Dough & Co</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Assets/css/style.css"> 
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-9 col-md-8 col-lg-6">
            
            <div class="card card-form border-0 shadow-sm p-4 p-sm-5" style="border-radius: 16px !important;">
                
                <h3 class="fw-bold mb-4 text-center" style="color: var(--text-pink); font-weight: 800;">
                    <?= $is_edit ? "Edit Data Staff" : "Tambah Staff Baru"; ?>
                </h3>
                
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?= $id; ?>">

                    <!-- Nama Staff -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nama Lengkap Staff</label>
                        <input type="text" name="nama_staff" class="form-control rounded-3" placeholder="Masukkan nama lengkap..." value="<?= htmlspecialchars($nama_staff); ?>" required style="padding: 0.6rem 1rem;">
                    </div>

                    <!-- Dropdown Pilihan Kios Dinamis -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Kios Tempat Tugas</label>
                        <select name="id_kios" class="form-select rounded-3" required style="padding: 0.6rem 1rem;">
                            <option value="">-- Pilih Kios Penugasan --</option>
                            <?php foreach ($list_kios as $kios): ?>
                                <option value="<?= $kios['id_kios']; ?>" <?= ($id_kios == $kios['id_kios']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($kios['nama_kios']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Row Grid Shift & Jenis Kelamin -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-secondary">Shift Kerja</label>
                            <select name="shift" class="form-select rounded-3" required style="padding: 0.6rem 1rem;">
                                <option value="">Pilih Shift...</option>
                                <option value="Pagi" <?= ($shift == 'Pagi') ? 'selected' : ''; ?>>Pagi</option>
                                <option value="Siang" <?= ($shift == 'Siang') ? 'selected' : ''; ?>>Siang</option>
                                <option value="Malam" <?= ($shift == 'Malam') ? 'selected' : ''; ?>>Malam</option>
                            </select>
                        </div>
                        
                        <div class="col-6">
                            <label class="form-label fw-bold small text-secondary">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select rounded-3" required style="padding: 0.6rem 1rem;">
                                <option value="">Pilih...</option>
                                <option value="Laki-laki" <?= ($jenis_kelamin == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="Perempuan" <?= ($jenis_kelamin == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pilihan Status Kerja -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-secondary">Status Kerja</label>
                        <select name="status" class="form-select rounded-3" required style="padding: 0.6rem 1rem;">
                            <option value="active" <?= ($status == 'active') ? 'selected' : ''; ?>>Active (Aktif Berdinas)</option>
                            <option value="nonactive" <?= ($status == 'nonactive') ? 'selected' : ''; ?>>Non-active (Libur/Off)</option>
                        </select>
                    </div>

                    <!-- Tombol Navigasi -->
                    <div class="row g-2 pt-2">
                        <div class="col-6">
                            <a href="../../View/staff.php" class="btn btn-batal w-100 rounded-3 fw-semibold py-2">Batal</a>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="simpan" class="btn btn-pink w-100 rounded-3 fw-semibold py-2">
                                <?= $is_edit ? "Perbarui" : "Simpan"; ?>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>