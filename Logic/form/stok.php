<?php
session_start();
require_once "../database.php";

$is_edit = false;
$id      = '';
$nama    = '';
$jumlah  = 0;
$satuan  = 'KG';
$rop     = 0;

// --- LOGIKA DETEKSI MODE EDIT ---
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $is_edit = true;
    $id      = $_GET['id'];

    try {
        $stmt = $koneksi->prepare("SELECT * FROM bahan_baku WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            header("Location: ../../View/stok.php");
            exit;
        }

        $nama   = $data['nama'];
        $jumlah = $data['jumlah'];
        $satuan = $data['satuan'];
        $rop    = $data['rop'];
    } catch (PDOException $e) {
        die("Error mengambil data lama: " . $e->getMessage());
    }
}

// --- LOGIKA PROSES SIMPAN ---
if (isset($_POST['simpan'])) {
    $form_id = $_POST['id'];
    $nama    = trim($_POST['nama']);
    $jumlah  = $_POST['jumlah'];
    $satuan  = $_POST['satuan'];
    $rop     = $_POST['rop'];

    try {
        if (!empty($form_id)) {
            $sql = "UPDATE bahan_baku SET nama = :nama, jumlah = :jumlah, satuan = :satuan, rop = :rop WHERE id = :id";
            $stmt = $koneksi->prepare($sql);
            $stmt->bindParam(':id', $form_id);
            $pesan_sukses = "Stok bahan baku sukses diperbarui!";
        } else {
            $sql = "INSERT INTO bahan_baku (nama, jumlah, satuan, rop) VALUES (:nama, :jumlah, :satuan, :rop)";
            $stmt = $koneksi->prepare($sql);
            $pesan_sukses = "Bahan baku baru berhasil ditambahkan!";
        }

        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':jumlah', $jumlah);
        $stmt->bindParam(':satuan', $satuan);
        $stmt->bindParam(':rop', $rop);

        if ($stmt->execute()) {
            echo "<script>alert('$pesan_sukses'); window.location='../../View/stok.php';</script>";
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
    <title><?= $is_edit ? "Edit Stok" : "Tambah Stok"; ?> - Dough & Co</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Assets/css/style.css"> 
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-9 col-md-8 col-lg-6">
            <div class="card card-form border-0 shadow-sm p-4 p-sm-5" style="border-radius: 16px !important;">
                <h3 class="fw-bold mb-4 text-center" style="color: var(--text-pink); font-weight: 800;">
                    <?= $is_edit ? "Edit Stok Bahan" : "Tambah Bahan Baku"; ?>
                </h3>
                
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?= $id; ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nama Bahan Baku</label>
                        <input type="text" name="nama" class="form-control rounded-3" placeholder="Contoh: Tepung Terigu, Gula, dll..." value="<?= htmlspecialchars($nama); ?>" required style="padding: 0.6rem 1rem;">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-8">
                            <label class="form-label fw-bold small text-secondary">Jumlah Volume Stok</label>
                            <input type="number" step="0.01" name="jumlah" class="form-control rounded-3" placeholder="0.00" value="<?= $jumlah; ?>" min="0" required style="padding: 0.6rem 1rem;">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold small text-secondary">Satuan</label>
                            <select name="satuan" class="form-select rounded-3" required style="padding: 0.6rem 1rem;">
                                <option value="KG" <?= ($satuan == 'KG') ? 'selected' : ''; ?>>KG</option>
                                <option value="Gram" <?= ($satuan == 'Gram') ? 'selected' : ''; ?>>Gram</option>
                                <option value="Liter" <?= ($satuan == 'Liter') ? 'selected' : ''; ?>>Liter</option>
                                <option value="Pcs" <?= ($satuan == 'Pcs') ? 'selected' : ''; ?>>Pcs</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-secondary">Reorder Point (ROP)</label>
                        <input type="number" step="0.01" name="rop" class="form-control rounded-3" placeholder="Batas minimal untuk order kembali..." value="<?= $rop; ?>" min="0" required style="padding: 0.6rem 1rem;">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Jika jumlah stok menyentuh atau berada di bawah nilai ini, sistem akan menandainya sebagai 'Kritis' (Merah).</div>
                    </div>

                    <div class="row g-2 pt-2">
                        <div class="col-6">
                            <a href="../../View/stok.php" class="btn btn-batal w-100 rounded-3 fw-semibold py-2">Batal</a>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="simpan" class="btn btn-pink w-100 rounded-3 fw-semibold py-2"><?= $is_edit ? "Perbarui" : "Simpan"; ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>