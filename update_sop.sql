
USE doughco_hub;

CREATE TABLE IF NOT EXISTS tb_sop (
    id_sop INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    kategori VARCHAR(50) NOT NULL,      
    deskripsi VARCHAR(255) NOT NULL,
    versi VARCHAR(10) NOT NULL DEFAULT '1.0',
    tanggal_update DATE NOT NULL,
    icon VARCHAR(50) NOT NULL DEFAULT 'bi-journal-text', 
    langkah_langkah TEXT NOT NULL,      
    dibuat_oleh INT,                     
    FOREIGN KEY (dibuat_oleh) REFERENCES tb_user(id_user)
);

INSERT INTO tb_sop (judul, kategori, deskripsi, versi, tanggal_update, icon, langkah_langkah) VALUES
('SOP Adonan', 'Produksi', 'Prosedur ragi premium', '2.1', '2026-05-10', 'bi-book-fill',
'Siapkan 500gr tepung terigu, 15gr ragi instan, 10gr gula, 5gr garam, 300ml susu hangat, 50gr mentega.
Campur tepung, ragi, dan gula. Tuang susu hangat bertahap sambil diuleni 8-10 menit hingga elastis.
Masukkan mentega, uleni kembali 5 menit. Tutup, istirahatkan 60 menit di suhu ruang.
Kempiskan adonan, bentuk sesuai varian. Fermentasi kedua 30 menit. Goreng di 170°C selama 2-3 menit per sisi.'),

('SOP Penggorengan', 'Produksi', 'Suhu & waktu standar', '1.4', '2026-04-15', 'bi-fire',
'Panaskan minyak hingga mencapai suhu 170°C, gunakan termometer dapur untuk memastikan akurat.
Masukkan adonan yang sudah difermentasi kedua, jangan menumpuk terlalu banyak dalam satu waktu.
Goreng 2-3 menit per sisi hingga warna keemasan merata, balik sekali saja.
Angkat dan tiriskan di atas rak kawat, bukan tisu, agar tidak lembab.'),

('SOP Topping', 'Produksi', 'Standar plating produk', '1.2', '2026-04-05', 'bi-palette-fill',
'Pastikan produk sudah dingin (suhu ruang) sebelum diberi topping agar topping tidak meleleh.
Gunakan takaran topping sesuai standar resep per varian, timbang jika perlu.
Ratakan topping dengan spatula/piping bag sesuai pola yang ditentukan tiap varian.
Simpan di etalase pendingin jika tidak langsung dijual dalam 2 jam.'),

('SOP Pelayanan', 'Pelayanan', 'Standar layanan tamu', '1.0', '2026-03-20', 'bi-emoji-smile-fill',
'Sambut pelanggan dengan senyum dan salam dalam 5 detik pertama sejak masuk kios.
Tawarkan bantuan memilih produk, sebutkan promo yang sedang berlangsung jika ada.
Konfirmasi pesanan sebelum diproses ke kasir, ulangi detail pesanan pada pelanggan.
Ucapkan terima kasih dan harapan kunjungan kembali saat pelanggan meninggalkan kios.'),

('SOP Kebersihan', 'Kebersihan', 'Checklist harian toko', '1.1', '2026-03-08', 'bi-clipboard-check-fill',
'Bersihkan etalase dan meja kasir setiap pagi sebelum kios dibuka.
Cuci peralatan produksi (loyang, spatula, mixer) segera setelah dipakai, jangan menumpuk.
Buang sampah organik minimal 2 kali sehari agar tidak mengundang serangga.
Pel lantai area produksi dan area pelanggan setiap penutupan kios.'),

('SOP Input Bahan', 'Gudang', 'Cara input stok bahan', '1.0', '2026-02-14', 'bi-box-seam-fill',
'Cek kondisi fisik bahan baku yang baru datang (kemasan, tanggal kadaluarsa) sebelum diterima.
Timbang/hitung jumlah bahan, cocokkan dengan surat jalan/nota dari supplier.
Input jumlah bahan ke sistem melalui menu Stok, sertakan tanggal terima dan supplier.
Simpan bahan sesuai kategori (kering, dingin, beku) di lokasi gudang yang sesuai.');
