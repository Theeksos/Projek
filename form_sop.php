<?php
/**
 * File: form_sop.php
 * Fungsi: Form untuk menambah SOP baru ATAU mengedit SOP yang sudah ada.
 * Kalau ada parameter ?id=.. di URL -> mode EDIT (form terisi otomatis).
 * Kalau tidak ada -> mode TAMBAH (form kosong).
 */

session_start();
require_once "config/database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$halaman_aktif = "sop";
$mode_edit = false;
$data = [
    'id_sop' => '', 'judul' => '', 'kategori' => '', 'deskripsi' => '',
    'versi' => '1.0', 'tanggal_update' => date('Y-m-d'),
    'icon' => 'bi-journal-text', 'langkah_langkah' => ''
];

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $stmt = $koneksi->prepare("SELECT * FROM tb_sop WHERE id_sop = :id");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    $hasil = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($hasil) {
        $data = $hasil;
        $mode_edit = true;
    }
}

// Daftar pilihan ikon yang tersedia (Bootstrap Icons)
$pilihan_icon = [
    'bi-book-fill' => 'Buku (Adonan/Resep)',
    'bi-fire' => 'Api (Penggorengan/Panggang)',
    'bi-palette-fill' => 'Palet (Topping/Dekorasi)',
    'bi-emoji-smile-fill' => 'Senyum (Pelayanan)',
    'bi-clipboard-check-fill' => 'Checklist (Kebersihan)',
    'bi-box-seam-fill' => 'Kotak (Gudang/Stok)',
    'bi-journal-text' => 'Dokumen (Umum)',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $mode_edit ? 'Edit SOP' : 'Tambah SOP' ?> - Dough & Co Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
<div class="app-layout">

    <?php include "includes/sidebar.php"; ?>

    <main class="main-content">
        <div class="page-topbar">
            <h4><?= $mode_edit ? 'Edit SOP' : 'Tambah SOP Baru' ?></h4>
            <a href="sop.php" class="btn-outline-pink"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>

        <div class="panel" style="max-width:640px;">
            <form action="proses_sop.php" method="POST" id="formSop">
                <input type="hidden" name="id_sop" value="<?= htmlspecialchars($data['id_sop']) ?>">

                <div class="mb-3">
                    <label class="form-label">Judul SOP</label>
                    <input type="text" name="judul" class="form-control" required
                           value="<?= htmlspecialchars($data['judul']) ?>" placeholder="Contoh: SOP Adonan">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="kategori" class="form-control" required
                               value="<?= htmlspecialchars($data['kategori']) ?>" placeholder="Contoh: Produksi">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Versi</label>
                        <input type="text" name="versi" class="form-control" required
                               value="<?= htmlspecialchars($data['versi']) ?>" placeholder="1.0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal_update" class="form-control" required
                               value="<?= htmlspecialchars($data['tanggal_update']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi Singkat</label>
                    <input type="text" name="deskripsi" class="form-control" required
                           value="<?= htmlspecialchars($data['deskripsi']) ?>" placeholder="Contoh: Prosedur ragi premium">
                </div>

                <div class="mb-3">
                    <label class="form-label">Ikon</label>
                    <select name="icon" class="form-select">
                        <?php foreach ($pilihan_icon as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $data['icon'] === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Langkah-langkah</label>
                    <textarea name="langkah_langkah" class="form-control" rows="6" required
                              placeholder="Tulis 1 langkah per baris. Contoh:&#10;Siapkan bahan...&#10;Campur semua bahan...&#10;Panggang selama 20 menit..."><?= htmlspecialchars($data['langkah_langkah']) ?></textarea>
                    <div class="form-text">Setiap baris baru akan otomatis jadi nomor urut langkah di halaman detail.</div>
                </div>

                <button type="submit" class="btn-export">
                    <i class="bi bi-check-lg"></i> <?= $mode_edit ? 'Simpan Perubahan' : 'Simpan SOP' ?>
                </button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
