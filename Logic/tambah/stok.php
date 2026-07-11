<?php
require_once '../database.php';

$is_edit = false;
$id      = '';
$nama    = '';
$jumlah  = '';
$satuan  = '';
$rop     = '';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $is_edit = true;
    $id      = $_GET['id'];
    $judul_halaman = "Edit Bahan Baku";

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
        die("Error mengambil data: " . $e->getMessage());
    }
}

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
            $pesan_sukses = "Data berhasil diperbarui!";
        } else {
            $sql = "INSERT INTO bahan_baku (nama, jumlah, satuan, rop) VALUES (:nama, :jumlah, :satuan, :rop)";
            $stmt = $koneksi->prepare($sql);
            $pesan_sukses = "Data baru berhasil ditambahkan!";
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
        die("Terjadi kesalahan sistem: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? "Edit Bahan Baku" : "Tambah Bahan Baku"; ?> - Dough & Co</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Assets/css/style.css"> 
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-9 col-md-7 col-lg-5">
            
            <div class="card card-form border-0 shadow-sm p-4 p-sm-5">
                
                <h3 class="text-center fw-bold mb-4 text-dark">
                    <?php echo $is_edit ? "Edit Bahan Baku" : "Tambah Bahan Baku"; ?>
                </h3>
                
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div class="mb-3">
                        <label class="form-label form-label-pink fw-semibold">Nama Bahan Baku</label>
                        <input type="text" name="nama" class="form-control form-control-lg rounded-3" value="<?php echo htmlspecialchars($nama); ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label form-label-pink fw-semibold">Jumlah Stok</label>
                            <input type="number" name="jumlah" class="form-control form-control-lg rounded-3" step="any" value="<?php echo $jumlah; ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label form-label-pink fw-semibold">Satuan</label>
                            <select name="satuan" class="form-select form-select-lg rounded-3" required>
                                <option value="">Pilih...</option>
                                <option value="KG" <?php echo ($satuan == 'KG') ? 'selected' : ''; ?>>KG</option>
                                <option value="Gram" <?php echo ($satuan == 'Gram') ? 'selected' : ''; ?>>Gram</option>
                                <option value="Liter" <?php echo ($satuan == 'Liter') ? 'selected' : ''; ?>>Liter</option>
                                <option value="Pcs" <?php echo ($satuan == 'Pcs') ? 'selected' : ''; ?>>Pcs</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label form-label-pink fw-semibold">Batas ROP</label>
                        <input type="number" name="rop" class="form-control form-control-lg rounded-3" step="any" value="<?php echo $rop; ?>" required>
                    </div>

                    <div class="row g-2 pt-2">
                        <div class="col-6">
                            <a href="../../View/stok.php" class="btn btn-batal btn-lg w-100 rounded-3 fw-semibold">Batal</a>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="simpan" class="btn btn-pink btn-lg w-100 rounded-3 fw-semibold">
                                <?php echo $is_edit ? "Perbarui Data" : "Simpan Data"; ?>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>