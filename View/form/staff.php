<?php
session_start();
require_once "../../config/database.php"; 

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$halaman_aktif = "staf";

$is_edit = false;
$id_staff      = '';
$nama_staff    = '';
$id_kios       = '';
$shift         = 'Pagi';
$jenis_kelamin = 'Laki-laki';
$status        = 'active';

try {
    $stmt_kios = $koneksi->query("SELECT id_kios, nama_kios FROM kios ORDER BY nama_kios ASC");
    $list_kios = $stmt_kios->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data kios: " . $e->getMessage());
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $is_edit = true;
    $id_staff = $_GET['id'];

    try {
        $stmt = $koneksi->prepare("SELECT * FROM staff WHERE id_staff = :id LIMIT 1");
        $stmt->bindParam(':id', $id_staff);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            header("Location: ../staff.php");
            exit;
        }

        $nama_staff    = $data['nama_staff'];
        $id_kios       = $data['id_kios'];
        $shift         = $data['shift'];
        $jenis_kelamin = $data['jenis_kelamin'];
        $status        = $data['status'];
    } catch (PDOException $e) {
        die("Error mengambil data lama: " . $e->getMessage());
    }
}

if (isset($_POST['simpan'])) {
    $form_id_staff   = $_POST['id_staff'];
    $nama_staff      = trim($_POST['nama_staff']);
    $id_kios         = !empty($_POST['id_kios']) ? $_POST['id_kios'] : null;
    $shift           = $_POST['shift'];
    $jenis_kelamin   = $_POST['jenis_kelamin'];
    $status          = $_POST['status'];

    try {
        if (!empty($form_id_staff)) {
            $sql = "UPDATE staff SET nama_staff = :nama, id_kios = :id_kios, shift = :shift, jenis_kelamin = :jk, status = :status WHERE id_staff = :id";
            $stmt = $koneksi->prepare($sql);
            $stmt->bindParam(':id', $form_id_staff);
            $pesan_sukses = "Data staff berhasil diperbarui!";
        } else {
            $sql = "INSERT INTO staff (nama_staff, id_kios, shift, jenis_kelamin, status) VALUES (:nama, :id_kios, :shift, :jk, :status)";
            $stmt = $koneksi->prepare($sql);
            $pesan_sukses = "Staff baru berhasil ditambahkan!";
        }

        $stmt->bindParam(':nama', $nama_staff);
        $stmt->bindParam(':id_kios', $id_kios, $id_kios === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(':shift', $shift);
        $stmt->bindParam(':jk', $jenis_kelamin);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            echo "<script>alert('$pesan_sukses'); window.location='../staff.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        die("Gagal memproses data staff: " . $e->getMessage());
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../Assets/css/dashboard.css">
    
    <style>
        .form-container-custom {
            max-width: 600px;
        }
        .form-group-custom {
            margin-bottom: 16px;
        }
        .label-custom {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #DB2777;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .input-custom {
            width: 100%;
            border: 1px solid #f0d3e3;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.9rem;
            background-color: #ffffff;
            color: #374151;
            font-family: inherit;
        }
        .input-custom:focus {
            outline: none;
            border-color: #DB2777;
            box-shadow: 0 0 0 3px rgba(219, 39, 119, 0.1);
        }
        .form-actions-custom {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 24px;
        }
        .btn-cancel-custom {
            background-color: #f3f4f6;
            color: #4b5563;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-cancel-custom:hover {
            background-color: #e5e7eb;
        }
    </style>
</head>
<body>

<div class="app-layout">
    
    <?php include "../../includes/sidebar.php"; ?>

    <!-- Main Content Sisi Kanan -->
    <main class="main-content">
        
        <!-- Topbar -->
        <div class="page-topbar">
            <div style="display: flex; align-items: center; gap: 14px;">
                <a href="../staff.php" class="btn-lihat" style="padding: 6px 12px;">← Kembali</a>
                <h4><?= $is_edit ? "Modifikasi Data Staff" : "Registrasi Staff Kios"; ?></h4>
            </div>
        </div>

        <!-- Panel -->
        <div class="panel form-container-custom">
            <div class="panel-title" style="margin-bottom: 18px; border-bottom: 1px solid #f6e0ec; padding-bottom: 8px;">
                Formulir Informasi Data Staff
            </div>
            
            <form action="" method="POST">
                <input type="hidden" name="id_staff" value="<?= $id_staff; ?>">

                <div class="form-group-custom">
                    <label class="label-custom">Nama Lengkap Staff</label>
                    <input type="text" name="nama_staff" class="input-custom" placeholder="Tulis nama lengkap karyawan..." value="<?= htmlspecialchars($nama_staff); ?>" required>
                </div>

                <div class="form-group-custom">
                    <label class="label-custom">Kios Tempat Penugasan</label>
                    <select name="id_kios" class="input-custom">
                        <option value="">-- Belum Diplot / Cadangan Kios --</option>
                        <?php foreach ($list_kios as $kios): ?>
                            <option value="<?= $kios['id_kios']; ?>" <?= ($id_kios == $kios['id_kios']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($kios['nama_kios']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label class="label-custom">Shift Kerja</label>
                    <select name="shift" class="input-custom" required>
                        <option value="Pagi" <?= ($shift == 'Pagi') ? 'selected' : ''; ?>>Shift Pagi</option>
                        <option value="Siang" <?= ($shift == 'Siang') ? 'selected' : ''; ?>>Shift Siang</option>
                        <option value="Malam" <?= ($shift == 'Malam') ? 'selected' : ''; ?>>Shift Malam</option>
                        <option value="Full Time" <?= ($shift == 'Full Time') ? 'selected' : ''; ?>>Full Time</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label class="label-custom">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="input-custom" required>
                        <option value="Laki-laki" <?= ($jenis_kelamin == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="Perempuan" <?= ($jenis_kelamin == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label class="label-custom">Status Keaktifan Kerja</label>
                    <select name="status" class="input-custom" required>
                        <option value="active" <?= ($status == 'active') ? 'selected' : ''; ?>>Active (Dinas Aktif)</option>
                        <option value="nonactive" <?= ($status == 'nonactive') ? 'selected' : ''; ?>>Off (Libur / Cuti)</option>
                    </select>
                </div>

                <div class="form-actions-custom">
                    <a href="../staff.php" class="btn-cancel-custom">Batal</a>
                    <button type="submit" name="simpan" class="btn-export">
                        <?= $is_edit ? "Perbarui Data" : "Simpan Staff"; ?>
                    </button>
                </div>

            </form>
        </div>

    </main>
</div>

</body>
</html>