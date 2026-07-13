<?php
session_start();
require_once "../../config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$halaman_aktif = "kios";

$is_edit = false;
$id_kios    = '';
$nama_kios  = '';
$lokasi     = '';
$id_mitra   = '';
$pendapatan = 0;
$status     = 'buka';

// --- 1. AMBIL DATA MITRA UNTUK DROPDOWN (REVISI) ---
try {
    // Menambahkan alias 'u.' pada kolom status agar menunjuk langsung ke tb_user
    $stmt_mitra = $koneksi->query("SELECT id_user, nama_lengkap FROM tb_user WHERE role = 'mitra' ORDER BY nama_lengkap ASC");
    $list_mitra = $stmt_mitra->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data mitra: " . $e->getMessage());
}

// --- 2. LOGIKA DETEKSI MODE EDIT ---
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $is_edit = true;
    $id_kios = $_GET['id'];

    try {
        $stmt = $koneksi->prepare("SELECT * FROM kios WHERE id_kios = :id LIMIT 1");
        $stmt->bindParam(':id', $id_kios);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            header("Location: ../kios.php");
            exit;
        }

        $nama_kios  = $data['nama_kios'];
        $lokasi     = $data['lokasi'];
        $id_mitra   = $data['id_mitra'];
        $pendapatan = $data['pendapatan'];
        $status     = $data['status'];
    } catch (PDOException $e) {
        die("Error mengambil data lama: " . $e->getMessage());
    }
}

// --- 3. LOGIKA PROSES SIMPAN (INSERT / UPDATE) ---
if (isset($_POST['simpan'])) {
    $form_id_kios = $_POST['id_kios'];
    $nama_kios    = trim($_POST['nama_kios']);
    $lokasi       = trim($_POST['lokasi']);
    $id_mitra     = $_POST['id_mitra'];
    $pendapatan   = $_POST['pendapatan'];
    $status       = $_POST['status'];

    try {
        if (!empty($form_id_kios)) {
            // Jalur Update
            $sql = "UPDATE kios SET nama_kios = :nama, lokasi = :lokasi, id_mitra = :id_mitra, pendapatan = :pendapatan, status = :status WHERE id_kios = :id";
            $stmt = $koneksi->prepare($sql);
            $stmt->bindParam(':id', $form_id_kios);
            $pesan_sukses = "Data kios berhasil diperbarui!";
        } else {
            // Jalur Insert
            $sql = "INSERT INTO kios (nama_kios, lokasi, id_mitra, pendapatan, status) VALUES (:nama, :lokasi, :id_mitra, :pendapatan, :status)";
            $stmt = $koneksi->prepare($sql);
            $pesan_sukses = "Kios baru berhasil ditambahkan!";
        }

        $stmt->bindParam(':nama', $nama_kios);
        $stmt->bindParam(':lokasi', $lokasi);
        $stmt->bindParam(':id_mitra', $id_mitra);
        $stmt->bindParam(':pendapatan', $pendapatan);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            echo "<script>alert('$pesan_sukses'); window.location='../kios.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        die("Gagal memproses data kios: " . $e->getMessage());
    }
}

// Info User di Sidebar
$user_login = $_SESSION['nama_user'] ?? 'Ryan';
$user_role  = $_SESSION['role_user'] ?? 'Mitra';
$inisial_user = strtoupper(substr($user_login, 0, 1));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? "Edit Kios" : "Tambah Kios"; ?> - Dough & Co</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../Assets/css/dashboard.css">
    
    <!-- TERPISAH: Tambahan CSS khusus halaman form agar sesuai dengan tema dashboard baru -->
    <style>
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-label-custom {
            display: block;
            font-size: 0.82rem;
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
            transition: border-color 0.15s ease;
        }

        .input-custom:focus {
            outline: none;
            border-color: #DB2777;
            box-shadow: 0 0 0 3px rgba(219, 39, 119, 0.1);
        }

        textarea.input-custom {
            resize: vertical;
            min-height: 80px;
        }

        .form-action-row {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 26px;
        }

        .btn-back {
            background-color: #f3f4f6;
            color: #4b5563;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.15s ease;
        }

        .btn-back:hover {
            background-color: #e5e7eb;
        }

        .max-width-container {
            max-width: 600px;
        }
    </style>
</head>
<body>

<div class="app-layout">
    
    <!-- SIDEBAR -->
    <?php include "../../includes/sidebar.php"; ?>


    <!-- MAIN CONTENT AREA -->
    <main class="main-content">
        
        <!-- TOPBAR -->
        <div class="page-topbar">
            <h4><?= $is_edit ? "Ubah Informasi Kios" : "Registrasi Kios Baru"; ?></h4>
        </div>

        <!-- FORM PANEL -->
        <div class="panel max-width-container">
            <div class="panel-title" style="margin-bottom: 20px; border-bottom: 1px solid #f6e0ec; padding-bottom: 10px;">
                Isi Data Kios dengan Benar
            </div>
            
            <form action="" method="POST">
                <!-- Hidden ID untuk mode edit -->
                <input type="hidden" name="id_kios" value="<?= $id_kios; ?>">

                <!-- Nama Kios -->
                <div class="form-group">
                    <label class="form-label-custom">Nama Kios</label>
                    <input type="text" name="nama_kios" class="input-custom" placeholder="Contoh: Dough & Co - Kios Margonda" value="<?= htmlspecialchars($nama_kios); ?>" required>
                </div>

                <!-- Alamat Kios -->
                <div class="form-group">
                    <label class="form-label-custom">Alamat Lengkap / Lokasi</label>
                    <textarea name="lokasi" class="input-custom" placeholder="Tuliskan alamat jalan, nomor, Kios..." required><?= htmlspecialchars($lokasi); ?></textarea>
                </div>

                <!-- Dropdown Pemilik (Mitra) -->
                <div class="form-group">
                    <label class="form-label-custom">Pemilik Kios (Mitra)</label>
                    <select name="id_mitra" class="input-custom" required>
                        <option value="">-- Pilih Mitra Penanggung Jawab --</option>
                        <?php foreach ($list_mitra as $mitra): ?>
                            <option value="<?= $mitra['id_user']; ?>" <?= ($id_mitra == $mitra['id_user']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($mitra['nama_lengkap']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Pendapatan Kios -->
                <div class="form-group">
                    <label class="form-label-custom">Omset / Pendapatan Awal (Rp)</label>
                    <input type="number" name="pendapatan" class="input-custom" min="0" value="<?= $pendapatan; ?>" required>
                </div>

                <!-- Status Operasional Kios -->
                <div class="form-group">
                    <label class="form-label-custom">Status Operasional</label>
                    <select name="status" class="input-custom" required>
                        <option value="buka" <?= ($status == 'buka') ? 'selected' : ''; ?>>Buka (Aktif Beroperasi)</option>
                        <option value="tutup" <?= ($status == 'tutup') ? 'selected' : ''; ?>>Tutup (Libur / Non-Aktif)</option>
                    </select>
                </div>

                <!-- Tombol Aksi -->
                <div class="form-action-row">
                    <a href="../kios.php" class="btn-back">Batal</a>
                    <button type="submit" name="simpan" class="btn-export">
                        <?= $is_edit ? "Perbarui Kios" : "Simpan Kios"; ?>
                    </button>
                </div>

            </form>
        </div>

    </main>
</div>

</body>
</html>