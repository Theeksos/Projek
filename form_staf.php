<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$halaman_aktif = "staf";
$mode_edit = false;
$data = [
    'id_staf' => '', 'nama_staf' => '', 'id_kios' => '', 'shift' => 'Pagi',
    'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'tanggal_gabung' => date('Y-m-d')
];

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $stmt = $koneksi->prepare("SELECT * FROM tb_staf WHERE id_staf = :id");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    $hasil = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($hasil) {
        $data = $hasil;
        $mode_edit = true;
    }
}

$daftar_kios = $koneksi->query("SELECT id_kios, nama_kios FROM tb_kios ORDER BY nama_kios ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $mode_edit ? 'Edit Staf' : 'Tambah Staf' ?> - Dough & Co Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
<div class="app-layout">

    <?php include "includes/sidebar.php"; ?>

    <main class="main-content">
        <div class="page-topbar">
            <h4><?= $mode_edit ? 'Edit Staf' : 'Tambah Staf Baru' ?></h4>
            <a href="staf.php" class="btn-outline-pink"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>

        <div class="panel" style="max-width:560px;">
            <form action="proses_staf.php" method="POST">
                <input type="hidden" name="id_staf" value="<?= htmlspecialchars($data['id_staf']) ?>">

                <div class="mb-3">
                    <label class="form-label">Nama Staf</label>
                    <input type="text" name="nama_staf" class="form-control" required
                           value="<?= htmlspecialchars($data['nama_staf']) ?>" placeholder="Contoh: Ahmad Fauzi">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kios</label>
                        <select name="id_kios" class="form-select" required>
                            <option value="">-- Pilih Kios --</option>
                            <?php foreach ($daftar_kios as $kios): ?>
                                <option value="<?= $kios['id_kios'] ?>" <?= (string)$data['id_kios'] === (string)$kios['id_kios'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kios['nama_kios']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Shift</label>
                        <select name="shift" class="form-select">
                            <option value="Pagi" <?= $data['shift'] === 'Pagi' ? 'selected' : '' ?>>Pagi</option>
                            <option value="Sore" <?= $data['shift'] === 'Sore' ? 'selected' : '' ?>>Sore</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select">
                            <option value="Laki-laki" <?= $data['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $data['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Aktif" <?= $data['status'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Non Aktif" <?= $data['status'] === 'Non Aktif' ? 'selected' : '' ?>>Non Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Gabung</label>
                    <input type="date" name="tanggal_gabung" class="form-control" required
                           value="<?= htmlspecialchars($data['tanggal_gabung']) ?>">
                </div>

                <button type="submit" class="btn-export">
                    <i class="bi bi-check-lg"></i> <?= $mode_edit ? 'Simpan Perubahan' : 'Simpan Staf' ?>
                </button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
