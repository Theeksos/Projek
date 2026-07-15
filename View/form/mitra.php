<?php
session_start();
require_once "../../config/database.php"; 

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$halaman_aktif = "mitra";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../mitra.php");
    exit;
}

$id_user = $_GET['id'];
$nama_user = '';

try {
    $stmt = $koneksi->prepare("SELECT id_user, nama_lengkap FROM tb_user WHERE id_user = :id AND role = 'Mitra' LIMIT 1");
    $stmt->bindParam(':id', $id_user);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo "<script>alert('Data mitra tidak ditemukan!'); window.location='../mitra.php';</script>";
        exit;
    }
    $nama_user = $data['nama_lengkap'];
} catch (PDOException $e) {
    die("Error mengambil data mitra: " . $e->getMessage());
}

try {
    $stmt_kios = $koneksi->query("SELECT id_kios, nama_kios, id_mitra FROM kios ORDER BY nama_kios ASC");
    $list_kios = $stmt_kios->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data kios: " . $e->getMessage());
}

if (isset($_POST['simpan'])) {
    $nama_user_input = trim($_POST['nama_user']);
    $kios_terpilih   = isset($_POST['kios_plot']) ? $_POST['kios_plot'] : [];

    try {
        $koneksi->beginTransaction(); 

        $sql_update_user = "UPDATE tb_user SET nama_lengkap = :nama WHERE id_user = :id AND role = 'Mitra'";
        $stmt_user = $koneksi->prepare($sql_update_user);
        $stmt_user->bindParam(':nama', $nama_user_input);
        $stmt_user->bindParam(':id', $id_user);
        $stmt_user->execute();
        
        if (!empty($kios_terpilih)) {
            foreach ($kios_terpilih as $id_kios_form) {
                $sql_plot = "UPDATE kios SET id_mitra = :id_mitra WHERE id_kios = :id_kios";
                $stmt_plot = $koneksi->prepare($sql_plot);
                $stmt_plot->bindParam(':id_mitra', $id_user);
                $stmt_plot->bindParam(':id_kios', $id_kios_form);
                $stmt_plot->execute();
            }
        }

        $koneksi->commit(); 
        echo "<script>alert('Data nama mitra dan kepemilikan kios berhasil diperbarui!'); window.location='../mitra.php';</script>";
        exit;

    } catch (PDOException $e) {
        $koneksi->rollBack(); 
        die("Gagal memperbarui data kemitraan: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kemitraan - Dough & Co</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../Assets/css/dashboard.css">
    
    <style>
        
    </style>
</head>
<body>

<div class="app-layout">
    <?php include "../../includes/sidebar.php"; ?>

    <main class="main-content">
        <div class="page-topbar">
            <div style="display: flex; align-items: center; gap: 14px;">
                <a href="../mitra.php" class="btn-lihat" style="padding: 6px 12px; text-decoration: none;">← Kembali</a>
                <h4>Atur Kepemilikan & Kios Mitra</h4>
            </div>
        </div>

        <div class="panel form-container-custom">
            <div class="panel-title" style="margin-bottom: 18px; border-bottom: 1px solid #f6e0ec; padding-bottom: 8px;">
                Form Perubahan Nama & Plotting Kios
            </div>
            
            <form action="" method="POST">
                <div class="form-group-custom">
                    <label class="label-custom">Nama Lengkap Mitra</label>
                    <input type="text" name="nama_user" class="input-custom" placeholder="Tulis nama lengkap..." value="<?= htmlspecialchars($nama_user); ?>" required>
                </div>

                <div class="form-group-custom">
                    <label class="label-custom">Tambahkan Plot Kios Baru</label>
                    <div class="kios-selection-box">
                        <?php if (empty($list_kios)): ?>
                            <div style="color: #9ca3af; font-size: 0.88rem; font-style: italic; text-align: center; padding-top: 20px;">
                                Belum ada data kios terdaftar di sistem.
                            </div>
                        <?php else: ?>
                            <?php foreach ($list_kios as $kios): 
                                $is_checked = ($kios['id_mitra'] == $id_user) ? 'checked' : '';
                                
                                $info_pemilik_lain = "";
                                if (!empty($kios['id_mitra']) && $kios['id_mitra'] != $id_user) {
                                    $info_pemilik_lain = " (Dipegang Mitra ID #" . $kios['id_mitra'] . ")";
                                }
                            ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="kios_plot[]" value="<?= $kios['id_kios']; ?>" id="kios_<?= $kios['id_kios']; ?>" <?= $is_checked; ?>>
                                    <label for="kios_<?= $kios['id_kios']; ?>">
                                        <i class="bi bi-shop me-2" style="color: #db2777;"></i>
                                        <?= htmlspecialchars($kios['nama_kios']); ?>
                                        <span style="font-size: 0.75rem; color: #9ca3af; font-weight: normal; font-style: italic;">
                                            <?= $info_pemilik_lain; ?>
                                        </span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions-custom">
                    <a href="../mitra.php" class="btn-cancel-custom">Batal</a>
                    <button type="submit" name="simpan" class="btn-export">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>