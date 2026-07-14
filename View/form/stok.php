<?php
session_start();
require_once "../../config/database.php"; 

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$halaman_aktif = "stok";

$is_edit = false;
$id   = '';
$nama = '';
$jumlah     = 0;
$satuan     = 'Gram';
$rop        = 0;

// --- 1. LOGIKA DETEKSI MODE EDIT ---
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $is_edit = true;
    $id = $_GET['id'];

    try {
        $stmt = $koneksi->prepare("SELECT * FROM bahan_baku WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            header("Location: ../stok.php");
            exit;
        }

        $nama = $data['nama'];
        $jumlah     = $data['jumlah'];
        $satuan     = $data['satuan'];
        $rop        = $data['rop'];
    } catch (PDOException $e) {
        die("Error mengambil data lama: " . $e->getMessage());
    }
}

// --- 2. LOGIKA PROSES SIMPAN (INSERT / UPDATE) ---
if (isset($_POST['simpan'])) {
    $form_id = $_POST['id'];
    $nama    = trim($_POST['nama']);
    $jumlah        = $_POST['jumlah'];
    $satuan        = $_POST['satuan'];
    $rop           = $_POST['rop'];

    try {
        if (!empty($form_id)) {
            // Jalur Update
            $sql = "UPDATE bahan_baku SET nama = :nama, jumlah = :jumlah, satuan = :satuan, rop = :rop WHERE id = :id";
            $stmt = $koneksi->prepare($sql);
            $stmt->bindParam(':id', $form_id);
            $pesan_sukses = "Data bahan baku berhasil diperbarui!";
        } else {
            // Jalur Insert
            $sql = "INSERT INTO bahan_baku (nama, jumlah, satuan, rop) VALUES (:nama, :jumlah, :satuan, :rop)";
            $stmt = $koneksi->prepare($sql);
            $pesan_sukses = "Bahan baku baru berhasil ditambahkan!";
        }

        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':jumlah', $jumlah);
        $stmt->bindParam(':satuan', $satuan);
        $stmt->bindParam(':rop', $rop);

        if ($stmt->execute()) {
            echo "<script>alert('$pesan_sukses'); window.location='../stok.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        die("Gagal memproses data stok: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? "Edit Bahan Baku" : "Tambah Bahan Baku"; ?> - Dough & Co</title>
    <!-- Aturan Path CSS: Mundur 2 Kali dari Folder form/ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../Assets/css/dashboard.css">
    
    <!-- CSS Tambahan Khusus Elemen Form (Terpisah) -->
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
    
    <!-- Include Sidebar Mundur 2 Kali -->
    <?php include "../../includes/sidebar.php"; ?>

    <!-- Main Content Sisi Kanan -->
    <main class="main-content">
        
        <!-- Topbar dengan Tombol Kembali di Samping Judul -->
        <div class="page-topbar">
            <div style="display: flex; align-items: center; gap: 14px;">
                <a href="../stok.php" class="btn-lihat" style="padding: 6px 12px; text-decoration: none;">← Kembali</a>
                <h4><?= $is_edit ? "Ubah Stok Bahan Baku" : "Tambah Inventaris Bahan"; ?></h4>
            </div>
        </div>

        <!-- Panel Form Dashboard Layout -->
        <div class="panel form-container-custom">
            <div class="panel-title" style="margin-bottom: 18px; border-bottom: 1px solid #f6e0ec; padding-bottom: 8px;">
                Formulir Logistik Bahan Baku
            </div>
            
            <form action="" method="POST">
                <!-- Hidden input ID untuk mode edit -->
                <input type="hidden" name="id" value="<?= $id; ?>">

                <!-- Nama Bahan Baku -->
                <div class="form-group-custom">
                    <label class="label-custom">Nama Bahan Baku</label>
                    <input type="text" name="nama" class="input-custom" placeholder="Contoh: Tepung Terigu Cakra Kembar..." value="<?= htmlspecialchars($nama); ?>" required>
                </div>

                <!-- Jumlah Stok Awal / Sekarang -->
                <div class="form-group-custom">
                    <label class="label-custom">Jumlah Stok Saat Ini</label>
                    <input type="number" name="jumlah" class="input-custom" min="0" value="<?= $jumlah; ?>" required>
                </div>

                <!-- Satuan Baku -->
                <div class="form-group-custom">
                    <label class="label-custom">Satuan Pengukuran</label>
                    <select name="satuan" class="input-custom" required>
                        <option value="Gram" <?= ($satuan == 'Gram') ? 'selected' : ''; ?>>Gram (g)</option>
                        <option value="KG" <?= ($satuan == 'Kilogram') ? 'selected' : ''; ?>>Kilogram (kg)</option>
                        <option value="Liter" <?= ($satuan == 'Liter') ? 'selected' : ''; ?>>Liter (L)</option>
                        <option value="Pcs" <?= ($satuan == 'Pcs') ? 'selected' : ''; ?>>Pcs / Butir</option>
                    </select>
                </div>

                <!-- Batas Reorder Point (ROP) -->
                <div class="form-group-custom">
                    <label class="label-custom">Batas Minimum Kritis (ROP)</label>
                    <input type="number" name="rop" class="input-custom" min="0" value="<?= $rop; ?>" required>
                    <small style="color: #9ca3af; font-size: 0.75rem; display: block; margin-top: 4px;">
                        *Sistem akan menandai status "Kritis" secara otomatis jika jumlah stok sama atau di bawah angka ini.
                    </small>
                </div>

                <!-- Tombol Submit Form -->
                <div class="form-actions-custom">
                    <a href="../stok.php" class="btn-cancel-custom">Batal</a>
                    <button type="submit" name="simpan" class="btn-export">
                        <?= $is_edit ? "Perbarui Stok" : "Simpan Bahan"; ?>
                    </button>
                </div>

            </form>
        </div>

    </main>
</div>

</body>
</html>