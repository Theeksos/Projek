<?php
session_start();
require_once "../database.php";

$is_edit = false;
$id         = '';
$nama_kios  = '';
$lokasi     = '';
$id_mitra   = '';
$pendapatan = 0;
$status     = 'buka';

try {
    // Ambil data penanggung jawab (mitra) dari tb_user
    $stmt_mitra = $koneksi->query("SELECT id_user, nama_lengkap FROM tb_user WHERE role = 'mitra' ORDER BY nama_lengkap ASC");
    $list_mitra = $stmt_mitra->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data penanggung jawab: " . $e->getMessage());
}

// --- LOGIKA MODE EDIT ---
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $is_edit = true;
    $id      = $_GET['id'];

    try {
        $stmt = $koneksi->prepare("SELECT * FROM kios WHERE id_kios = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            header("Location: kios.php");
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

// --- LOGIKA PROSES SIMPAN ---
if (isset($_POST['simpan'])) {
    $form_id    = $_POST['id'];
    $nama_kios  = trim($_POST['nama_kios']);
    $lokasi     = trim($_POST['lokasi']);
    $id_mitra   = $_POST['id_mitra'];
    $pendapatan = $_POST['pendapatan'];
    $status     = $_POST['status'];

    try {
        if (!empty($form_id)) {
            // Proses Update
            $sql = "UPDATE kios SET nama_kios = :nama_kios, lokasi = :lokasi, id_mitra = :id_mitra, pendapatan = :pendapatan, status = :status WHERE id_kios = :id";
            $stmt = $koneksi->prepare($sql);
            $stmt->bindParam(':id', $form_id);
            $pesan_sukses = "Data kios berhasil diperbarui!";
        } else {
            // Proses Insert New
            $sql = "INSERT INTO kios (nama_kios, lokasi, id_mitra, pendapatan, status) VALUES (:nama_kios, :lokasi, :id_mitra, :pendapatan, :status)";
            $stmt = $koneksi->prepare($sql);
            $pesan_sukses = "Kios baru sukses didaftarkan!";
        }

        $stmt->bindParam(':nama_kios', $nama_kios);
        $stmt->bindParam(':lokasi', $lokasi);
        $stmt->bindParam(':id_mitra', $id_mitra);
        $stmt->bindParam(':pendapatan', $pendapatan);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            echo "<script>alert('$pesan_sukses'); window.location='../../View/kios.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        die("Terjadi kesalahan database: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? "Edit Kios" : "Tambah Kios"; ?> - Dough & Co</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Assets/css/style.css"> 
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-9 col-md-8 col-lg-6">
            <!-- Card Form dengan style melengkung halus putih kontras dengan bg soft pink -->
            <div class="card card-form border-0 shadow-sm p-4 p-sm-5" style="border-radius: 16px !important;">
                <h3 class="fw-bold mb-4 text-center" style="color: var(--text-pink); font-weight: 800;">
                    <?= $is_edit ? "Edit Informasi Kios" : "Tambah Kios Baru"; ?>
                </h3>
                
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?= $id; ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nama Kios</label>
                        <input type="text" name="nama_kios" class="form-control rounded-3" placeholder="Masukkan nama kios..." value="<?= htmlspecialchars($nama_kios); ?>" required style="padding: 0.6rem 1rem;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Lokasi Alamat</label>
                        <textarea name="lokasi" class="form-control rounded-3" rows="3" placeholder="Tulis alamat detail kios..." required style="padding: 0.6rem 1rem;"><?= htmlspecialchars($lokasi); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Penanggung Jawab Kios (Mitra)</label>
                        <select name="id_mitra" class="form-select rounded-3" required style="padding: 0.6rem 1rem;">
                            <option value="">-- Pilih Mitra Terdaftar --</option>
                            <?php foreach ($list_mitra as $mitra): ?>
                                <option value="<?= $mitra['id_user']; ?>" <?= ($id_mitra == $mitra['id_user']) ? 'selected' : ''; ?>><?= htmlspecialchars($mitra['nama_lengkap']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-secondary">Pendapatan (Rp)</label>
                            <input type="number" name="pendapatan" class="form-control rounded-3" value="<?= $pendapatan; ?>" min="0" required style="padding: 0.6rem 1rem;">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-secondary">Status Operasional</label>
                            <select name="status" class="form-select rounded-3" required style="padding: 0.6rem 1rem;">
                                <option value="buka" <?= ($status == 'buka') ? 'selected' : ''; ?>>Buka</option>
                                <option value="tutup" <?= ($status == 'tutup') ? 'selected' : ''; ?>>Tutup</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 pt-2">
                        <div class="col-6">
                            <a href="../../View/kios.php" class="btn btn-batal w-100 rounded-3 fw-semibold py-2">Batal</a>
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