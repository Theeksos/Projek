<?php
session_start();
require_once "../../config/database.php";

// Definisikan variabel awal agar aman
$id_produk    = '';
$nama         = '';
$keterangan   = '';
$kategori     = '';
$harga        = '';
$stok         = '';
$rop          = '';
$status       = 'aman';
$foto_lama    = 'default-product.png';
$is_update    = false;

// --- 1. DETEKSI MODE: JIKA ADA PARAMETER ID, MAKA MODE UPDATE ---
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_produk = $_GET['id'];
    $is_update = true;

    try {
        $sql = "SELECT * FROM produk WHERE id_produk = :id";
        $stmt = $koneksi->prepare($sql);
        $stmt->bindParam(':id', $id_produk);
        $stmt->execute();
        $produk = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produk) {
            $nama       = $produk['nama'];
            $keterangan = $produk['keterangan'];
            $kategori   = $produk['kategori'];
            $harga      = $produk['harga'];
            $stok       = $produk['stok'];
            $rop        = $produk['rop'];
            $status     = $produk['status'];
            $foto_lama  = $produk['foto'];
        } else {
            echo "<script>alert('Produk tidak ditemukan!'); window.location='../produk.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        die("Error database: " . $e->getMessage());
    }
}

// --- 2. LOGIKA PROSES SIMPAN (TAMBAH / UPDATE) ---
if (isset($_POST['simpan'])) {
    $nama       = trim($_POST['nama']);
    $keterangan = trim($_POST['keterangan']);
    $kategori   = trim($_POST['kategori']);
    $harga      = floatval($_POST['harga']);
    $stok       = intval($_POST['stok']);
    $rop        = intval($_POST['rop']);
    
    // Logika otomatisasi status berdasarkan ROP & Stok
    // Stok <= ROP -> Kritis
    // Stok <= ROP + (ROP * 50%) -> Hampir Habis (misal batas aman toleransi 1.5 kali ROP)
    // Selebihnya -> Aman
    if ($stok <= $rop) {
        $status = 'kritis';
    } elseif ($stok <= ($rop * 1.5)) {
        $status = 'hampir habis';
    } else {
        $status = 'aman';
    }

    $nama_file_foto = $foto_lama; // default gunakan foto lama/bawaan

    // Proses Upload Foto jika ada file baru yang diunggah
    if (isset($_FILES['foto']['name']) && !empty($_FILES['foto']['name'])) {
        $file_name = $_FILES['foto']['name'];
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $ekstensi_boleh = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $ekstensi_boleh)) {
            // Beri nama unik agar tidak bentrok
            $nama_file_foto = time() . '_' . preg_replace("/[^a-zA-Z0-9]/", "_", $nama) . '.' . $file_ext;
            $target_dir     = "../../Assets/images/produk/";
            
            // Buat folder jika belum ada
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($file_tmp, $target_dir . $nama_file_foto)) {
                // Hapus foto lama dari penyimpanan jika bukan gambar default
                if ($is_update && $foto_lama !== 'default-product.png' && file_exists($target_dir . $foto_lama)) {
                    unlink($target_dir . $foto_lama);
                }
            } else {
                echo "<script>alert('Gagal mengunggah foto baru. Menggunakan foto sebelumnya.');</script>";
                $nama_file_foto = $foto_lama;
            }
        } else {
            echo "<script>alert('Format foto tidak didukung (gunakan JPG, JPEG, PNG, WEBP).');</script>";
        }
    }

    try {
        if ($is_update) {
            // Query UPDATE
            $sql_query = "UPDATE produk SET 
                            nama = :nama, 
                            keterangan = :keterangan, 
                            kategori = :kategori, 
                            harga = :harga, 
                            stok = :stok, 
                            rop = :rop, 
                            status = :status, 
                            foto = :foto 
                          WHERE id_produk = :id";
            $stmt = $koneksi->prepare($sql_query);
            $stmt->bindParam(':id', $id_produk);
        } else {
            // Query INSERT
            $sql_query = "INSERT INTO produk (nama, keterangan, kategori, harga, stok, rop, status, foto) 
                          VALUES (:nama, :keterangan, :kategori, :harga, :stok, :rop, :status, :foto)";
            $stmt = $koneksi->prepare($sql_query);
        }

        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':keterangan', $keterangan);
        $stmt->bindParam(':kategori', $kategori);
        $stmt->bindParam(':harga', $harga);
        $stmt->bindParam(':stok', $stok);
        $stmt->bindParam(':rop', $rop);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':foto', $nama_file_foto);

        $stmt->execute();

        $pesan = $is_update ? "Data produk berhasil diperbarui!" : "Produk baru berhasil ditambahkan!";
        echo "<script>alert('$pesan'); window.location='../produk.php';</script>";
        exit;

    } catch (PDOException $e) {
        die("Proses simpan gagal: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dough & Co - <?= $is_update ? 'Edit' : 'Tambah'; ?> Produk</title>
    <!-- Aturan Link Aset untuk Halaman di sub-folder Form -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../Assets/css/dashboard.css">
    <style>
        .form-label-custom {
            font-weight: 600;
            color: #374151;
            font-size: 0.88rem;
            margin-bottom: 6px;
        }
        .form-input-custom, .form-select-custom {
            border: 1px solid #f0d3e3;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.9rem;
            width: 100%;
            background-color: #ffffff;
            transition: border-color 0.2s;
        }
        .form-input-custom:focus, .form-select-custom:focus {
            outline: none;
            border-color: #DB2777;
        }
        .img-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px dashed #f0d3e3;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="app-layout">
    
    <!-- Include Sidebar dari Root Folder -->
    <?php include "../../includes/sidebar.php"; ?>

    <!-- MAIN CONTENT AREA -->
    <main class="main-content">
        
        <!-- TOPBAR ACTION -->
        <div class="page-topbar">
            <h4><?= $is_update ? 'Edit Data' : 'Tambah Baru'; ?> Produk</h4>
            <!-- Back Button menggunakan jalur relatif yang sudah disesuaikan -->
            <a href="../produk.php" class="btn-lihat" style="text-decoration: none;">Kembali</a>
        </div>

        <!-- FORM PANEL -->
        <div class="panel" style="max-width: 800px; margin: 0 auto;">
            <div class="panel-title"><?= $is_update ? 'Form Perubahan Produk' : 'Isi Detail Menu Baru'; ?></div>
            
            <form action="" method="POST" enctype="multipart/form-data" class="mt-3">
                <div class="row g-4">
                    
                    <!-- Kiri: Unggah Foto -->
                    <div class="col-md-4 text-center">
                        <div class="form-label-custom">Foto Produk</div>
                        <div class="d-flex flex-column align-items-center mt-2">
                            <img id="previewImg" src="../../Assets/images/produk/<?= $foto_lama; ?>" 
                                 class="img-preview" 
                                 alt="Preview"
                                 onerror="this.onerror=null; this.src='../../Assets/images/default-product.png';">
                            
                            <label for="fotoInput" class="btn-outline-pink" style="cursor: pointer; padding: 6px 12px; font-size: 0.8rem;">
                                Pilih Gambar
                            </label>
                            <input type="file" id="fotoInput" name="foto" style="display: none;" accept="image/*" onchange="previewFile()">
                            <small class="text-muted mt-2" style="font-size: 0.72rem;">Format: JPG, PNG, WEBP</small>
                        </div>
                    </div>

                    <!-- Kanan: Form Inputs -->
                    <div class="col-md-8">
                        <div class="row g-3">
                            <!-- Nama Produk -->
                            <div class="col-12">
                                <label class="form-label-custom">Nama Produk *</label>
                                <input type="text" name="nama" class="form-input-custom" placeholder="Contoh: Croissant Almond" value="<?= htmlspecialchars($nama); ?>" required>
                            </div>

                            <!-- Keterangan -->
                            <div class="col-12">
                                <label class="form-label-custom">Keterangan / Deskripsi</label>
                                <textarea name="keterangan" rows="3" class="form-input-custom" placeholder="Tulis deskripsi singkat produk..."><?= htmlspecialchars($keterangan); ?></textarea>
                            </div>

                            <!-- Kategori -->
                            <div class="col-md-6">
                                <label class="form-label-custom">Kategori *</label>
                                <select name="kategori" class="form-select-custom" required>
                                    <option value="" disabled <?= empty($kategori) ? 'selected' : ''; ?>>Pilih Kategori</option>
                                    <option value="Pastry" <?= $kategori == 'Pastry' ? 'selected' : ''; ?>>Pastry</option>
                                    <option value="Bread" <?= $kategori == 'Bread' ? 'selected' : ''; ?>>Bread</option>
                                    <option value="Cake" <?= $kategori == 'Cake' ? 'selected' : ''; ?>>Cake</option>
                                    <option value="Beverage" <?= $kategori == 'Beverage' ? 'selected' : ''; ?>>Beverage</option>
                                    <option value="Cookies" <?= $kategori == 'Cookies' ? 'selected' : ''; ?>>Cookies</option>
                                </select>
                            </div>

                            <!-- Harga -->
                            <div class="col-md-6">
                                <label class="form-label-custom">Harga Jual (Rp) *</label>
                                <input type="number" min="0" name="harga" class="form-input-custom" placeholder="Contoh: 25000" value="<?= htmlspecialchars($harga); ?>" required>
                            </div>

                            <!-- Stok -->
                            <div class="col-md-6">
                                <label class="form-label-custom">Stok Awal *</label>
                                <input type="number" min="0" name="stok" class="form-input-custom" placeholder="0" value="<?= htmlspecialchars($stok); ?>" required>
                            </div>

                            <!-- ROP -->
                            <div class="col-md-6">
                                <label class="form-label-custom">Reorder Point (ROP) *</label>
                                <input type="number" min="0" name="rop" class="form-input-custom" placeholder="Batas minimal sisa stok" value="<?= htmlspecialchars($rop); ?>" required>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="../produk.php" class="btn-lihat" style="text-decoration: none; padding: 10px 20px;">Batal</a>
                            <button type="submit" name="simpan" class="btn-export" style="padding: 10px 20px;">
                                <?= $is_update ? 'Simpan Perubahan' : 'Tambah Produk'; ?>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>

    </main>
</div>

<!-- Script Pratonton (Preview) Gambar Secara Real-Time -->
<script>
function previewFile() {
    const preview = document.getElementById('previewImg');
    const file = document.getElementById('fotoInput').files[0];
    const reader = new FileReader();

    reader.addEventListener("load", function () {
        preview.src = reader.result;
    }, false);

    if (file) {
        reader.readAsDataURL(file);
    }
}
</script>

</body>
</html>