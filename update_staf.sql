-- =========================================================
-- File: update_staf.sql
-- Fungsi: Tabel untuk fitur Manajemen Staf
-- Jalankan file ini SETELAH database.sql & update_laporan.sql
-- (butuh tb_kios yang sudah dibuat di update_laporan.sql)
-- =========================================================

USE doughco_hub;

CREATE TABLE IF NOT EXISTS tb_staf (
    id_staf INT AUTO_INCREMENT PRIMARY KEY,
    nama_staf VARCHAR(100) NOT NULL,
    id_kios INT NOT NULL,
    shift ENUM('Pagi','Sore') NOT NULL DEFAULT 'Pagi',
    jenis_kelamin ENUM('Laki-laki','Perempuan') NOT NULL,
    status ENUM('Aktif','Non Aktif') NOT NULL DEFAULT 'Aktif',
    tanggal_gabung DATE NOT NULL DEFAULT (CURRENT_DATE),
    FOREIGN KEY (id_kios) REFERENCES tb_kios(id_kios)
);

-- Data contoh staf, tersebar di 5 kios yang sudah ada
INSERT INTO tb_staf (nama_staf, id_kios, shift, jenis_kelamin, status, tanggal_gabung) VALUES
('Ahmad Fauzi',    1, 'Pagi', 'Laki-laki', 'Aktif',     '2025-01-10'),
('Siti Nurhaliza',  1, 'Sore', 'Perempuan', 'Aktif',     '2025-02-14'),
('Rizky Maulana',   2, 'Pagi', 'Laki-laki', 'Aktif',     '2025-01-20'),
('Dewi Lestari',    3, 'Sore', 'Perempuan', 'Aktif',     '2025-03-01'),
('Fajar Ramadhan',  4, 'Pagi', 'Laki-laki', 'Non Aktif', '2024-11-05'),
('Nadya Putri',     5, 'Sore', 'Perempuan', 'Aktif',     '2025-02-01'),
('Bima Aditya',     2, 'Pagi', 'Laki-laki', 'Aktif',     '2025-01-15'),
('Rina Susanti',    1, 'Pagi', 'Perempuan', 'Aktif',     '2025-01-05'),
('Budi Hartono',    2, 'Sore', 'Laki-laki', 'Aktif',     '2025-01-08'),
('Andi Permana',    3, 'Pagi', 'Laki-laki', 'Non Aktif', '2024-12-01'),
('Wulan Sari',      4, 'Sore', 'Perempuan', 'Aktif',     '2025-02-20'),
('Eko Prasetyo',    3, 'Pagi', 'Laki-laki', 'Aktif',     '2025-01-25'),
('Maya Anggraini',  4, 'Sore', 'Perempuan', 'Aktif',     '2025-03-10'),
('Doni Saputra',    5, 'Pagi', 'Laki-laki', 'Non Aktif', '2024-10-15'),
('Lina Marlina',    2, 'Sore', 'Perempuan', 'Aktif',     '2025-02-05');
