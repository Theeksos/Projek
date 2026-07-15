-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 15, 2026 at 01:33 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `doughco_hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `bahan_baku`
--

CREATE TABLE `bahan_baku` (
  `id` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jumlah` decimal(10,2) NOT NULL DEFAULT '0.00',
  `satuan` enum('KG','Gram','Liter','Pcs') NOT NULL,
  `rop` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bahan_baku`
--

INSERT INTO `bahan_baku` (`id`, `nama`, `jumlah`, `satuan`, `rop`, `created_at`) VALUES
(1, 'Tepung Terigu', '120.00', 'KG', '20.00', '2026-07-11 07:44:12'),
(2, 'dodo', '20.50', 'Gram', '10.00', '2026-07-11 08:21:50'),
(3, 'jdaidwa', '1212.00', 'KG', '2121.00', '2026-07-11 08:27:51'),
(4, 'jdaidwa', '1212.00', 'KG', '2121.00', '2026-07-11 08:28:41'),
(5, 'jdaidwa', '1212.00', 'KG', '2121.00', '2026-07-11 08:36:04'),
(6, 'ajdiawia', '123.00', 'KG', '123.00', '2026-07-11 08:36:42'),
(7, 'baba', '201.00', 'Gram', '123.00', '2026-07-11 08:39:32'),
(8, 'sadas', '12.00', 'KG', '2.00', '2026-07-11 08:41:08'),
(9, 'waqiwiod', '20.00', 'KG', '10.00', '2026-07-11 10:19:51'),
(10, 'Ragi', '100.00', 'Liter', '45.00', '2026-07-11 10:27:53'),
(11, 'Gula', '120.00', 'Pcs', '50.00', '2026-07-11 13:54:36');

-- --------------------------------------------------------

--
-- Table structure for table `kios`
--

CREATE TABLE `kios` (
  `id_kios` int NOT NULL,
  `nama_kios` varchar(100) NOT NULL,
  `lokasi` text NOT NULL,
  `id_mitra` int NOT NULL,
  `pendapatan` int DEFAULT '0',
  `status` enum('buka','tutup') DEFAULT 'buka'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kios`
--

INSERT INTO `kios` (`id_kios`, `nama_kios`, `lokasi`, `id_mitra`, `pendapatan`, `status`) VALUES
(1, 'Depok 2', 'Margonda, Depok', 2, 720000, 'tutup'),
(2, 'Jakarta 1', 'Sudirman, Jakarta', 2, 610000, 'tutup');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int NOT NULL,
  `foto` varchar(255) DEFAULT 'default-product.png',
  `nama` varchar(150) NOT NULL,
  `keterangan` text,
  `kategori` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stok` int NOT NULL DEFAULT '0',
  `rop` int NOT NULL DEFAULT '0',
  `status` enum('aman','hampir habis','kritis') NOT NULL DEFAULT 'aman',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `foto`, `nama`, `keterangan`, `kategori`, `harga`, `stok`, `rop`, `status`, `created_at`, `updated_at`) VALUES
(2, '1784016851_Choco_Croissant_Large.jpeg', 'Choco Croissant Large', 'Croissant dengan isian cokelat berukuran besar', 'Bread', '30000.00', 34, 25, 'hampir habis', '2026-07-14 08:14:11', '2026-07-14 08:23:48'),
(3, '1784017241_Strawberry_Donut.jpeg', 'Strawberry Donut', 'Donat lembut rasa strawberry', 'Pastry', '20000.00', 100, 30, 'aman', '2026-07-14 08:20:41', '2026-07-14 08:20:41'),
(4, '1784017282_Blueberry_Muffin.jpeg', 'Blueberry Muffin', 'Muffin rasa blueberry', 'Beverage', '35000.00', 30, 50, 'kritis', '2026-07-14 08:21:22', '2026-07-14 08:21:22');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id_staff` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `nama_staff` varchar(100) NOT NULL,
  `id_kios` int NOT NULL,
  `shift` varchar(50) NOT NULL,
  `jenis_kelamin` varchar(20) NOT NULL,
  `status` enum('active','nonactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'nonactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id_staff`, `id_user`, `nama_staff`, `id_kios`, `shift`, `jenis_kelamin`, `status`) VALUES
(1, 3, 'Agus', 2, 'Pagi', 'Laki-laki', 'active'),
(2, 4, 'Wawan', 2, 'Siang', 'Laki-laki', 'nonactive');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kios`
--

CREATE TABLE `tb_kios` (
  `id_kios` int NOT NULL,
  `nama_kios` varchar(100) NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `status` enum('aktif','libur') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_kios`
--

INSERT INTO `tb_kios` (`id_kios`, `nama_kios`, `lokasi`, `status`) VALUES
(1, 'Depok 2', 'Margonda, Depok', 'aktif'),
(2, 'Jakarta 1', 'Sudirman, Jakarta', 'aktif'),
(3, 'Bandung 1', 'Dago, Bandung', 'aktif'),
(4, 'Bogor 1', 'Cibinong, Bogor', 'aktif'),
(5, 'Tangerang 1', 'BSD, Tangerang', 'libur');

-- --------------------------------------------------------

--
-- Table structure for table `tb_sop`
--

CREATE TABLE `tb_sop` (
  `id_sop` int NOT NULL,
  `judul` varchar(150) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `versi` varchar(10) NOT NULL DEFAULT '1.0',
  `tanggal_update` date NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'bi-journal-text',
  `langkah_langkah` text NOT NULL,
  `dibuat_oleh` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_sop`
--

INSERT INTO `tb_sop` (`id_sop`, `judul`, `kategori`, `deskripsi`, `versi`, `tanggal_update`, `icon`, `langkah_langkah`, `dibuat_oleh`) VALUES
(1, 'SOP Adonan', 'Produksi', 'Prosedur ragi premium', '2.1', '2026-05-10', 'bi-book-fill', 'Siapkan 500gr tepung terigu, 15gr ragi instan, 10gr gula, 5gr garam, 300ml susu hangat, 50gr mentega.\r\nCampur tepung, ragi, dan gula. Tuang susu hangat bertahap sambil diuleni 8-10 menit hingga elastis.\r\nMasukkan mentega, uleni kembali 5 menit. Tutup, istirahatkan 60 menit di suhu ruang.\r\nKempiskan adonan, bentuk sesuai varian. Fermentasi kedua 30 menit. Goreng di 170°C selama 2-3 menit per sisi.', NULL),
(2, 'SOP Penggorengan', 'Produksi', 'Suhu & waktu standar', '1.4', '2026-04-15', 'bi-fire', 'Panaskan minyak hingga mencapai suhu 170°C, gunakan termometer dapur untuk memastikan akurat.\r\nMasukkan adonan yang sudah difermentasi kedua, jangan menumpuk terlalu banyak dalam satu waktu.\r\nGoreng 2-3 menit per sisi hingga warna keemasan merata, balik sekali saja.\r\nAngkat dan tiriskan di atas rak kawat, bukan tisu, agar tidak lembab.', NULL),
(3, 'SOP Topping', 'Produksi', 'Standar plating produk', '1.2', '2026-04-05', 'bi-palette-fill', 'Pastikan produk sudah dingin (suhu ruang) sebelum diberi topping agar topping tidak meleleh.\r\nGunakan takaran topping sesuai standar resep per varian, timbang jika perlu.\r\nRatakan topping dengan spatula/piping bag sesuai pola yang ditentukan tiap varian.\r\nSimpan di etalase pendingin jika tidak langsung dijual dalam 2 jam.', NULL),
(4, 'SOP Pelayanan', 'Pelayanan', 'Standar layanan tamu', '1.0', '2026-03-20', 'bi-emoji-smile-fill', 'Sambut pelanggan dengan senyum dan salam dalam 5 detik pertama sejak masuk kios.\r\nTawarkan bantuan memilih produk, sebutkan promo yang sedang berlangsung jika ada.\r\nKonfirmasi pesanan sebelum diproses ke kasir, ulangi detail pesanan pada pelanggan.\r\nUcapkan terima kasih dan harapan kunjungan kembali saat pelanggan meninggalkan kios.', NULL),
(5, 'SOP Kebersihan', 'Kebersihan', 'Checklist harian toko', '1.1', '2026-03-08', 'bi-clipboard-check-fill', 'Bersihkan etalase dan meja kasir setiap pagi sebelum kios dibuka.\r\nCuci peralatan produksi (loyang, spatula, mixer) segera setelah dipakai, jangan menumpuk.\r\nBuang sampah organik minimal 2 kali sehari agar tidak mengundang serangga.\r\nPel lantai area produksi dan area pelanggan setiap penutupan kios.', NULL),
(6, 'SOP Input Bahan', 'Gudang', 'Cara input stok bahan', '1.0', '2026-02-14', 'bi-box-seam-fill', 'Cek kondisi fisik bahan baku yang baru datang (kemasan, tanggal kadaluarsa) sebelum diterima.\r\nTimbang/hitung jumlah bahan, cocokkan dengan surat jalan/nota dari supplier.\r\nInput jumlah bahan ke sistem melalui menu Stok, sertakan tanggal terima dan supplier.\r\nSimpan bahan sesuai kategori (kering, dingin, beku) di lokasi gudang yang sesuai.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_transaksi` int NOT NULL,
  `id_kios` int NOT NULL,
  `tanggal` date NOT NULL,
  `total_pendapatan` decimal(12,0) NOT NULL,
  `jumlah_transaksi` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_transaksi`, `id_kios`, `tanggal`, `total_pendapatan`, `jumlah_transaksi`) VALUES
(1, 1, '2026-07-01', '248600', 37),
(2, 1, '2026-07-02', '303600', 25),
(3, 1, '2026-07-03', '233200', 42),
(4, 1, '2026-07-04', '156200', 37),
(5, 1, '2026-07-05', '220000', 41),
(6, 1, '2026-07-06', '255200', 34),
(7, 1, '2026-07-08', '176000', 37),
(8, 1, '2026-07-09', '253000', 28),
(9, 1, '2026-07-10', '246400', 41),
(10, 1, '2026-07-11', '178200', 17),
(11, 1, '2026-07-12', '292600', 31),
(12, 1, '2026-07-13', '259600', 25),
(13, 1, '2026-07-14', '215600', 39),
(14, 1, '2026-07-15', '220000', 30),
(15, 1, '2026-07-16', '308000', 29),
(16, 1, '2026-07-18', '297000', 45),
(17, 1, '2026-07-19', '305800', 39),
(18, 1, '2026-07-20', '198000', 32),
(19, 1, '2026-07-21', '275000', 45),
(20, 1, '2026-07-22', '187000', 21),
(21, 1, '2026-07-23', '191400', 20),
(22, 1, '2026-07-24', '215600', 20),
(23, 1, '2026-07-25', '222200', 37),
(24, 1, '2026-07-26', '246400', 18),
(25, 1, '2026-07-27', '244200', 32),
(26, 1, '2026-07-28', '242000', 43),
(27, 1, '2026-07-29', '268400', 18),
(28, 1, '2026-07-30', '176000', 38),
(29, 1, '2026-07-31', '184800', 22),
(30, 1, '2026-06-01', '169400', 21),
(31, 1, '2026-06-02', '283800', 22),
(32, 1, '2026-06-03', '213400', 21),
(33, 1, '2026-06-04', '264000', 28),
(34, 1, '2026-06-05', '268400', 36),
(35, 1, '2026-06-06', '184800', 39),
(36, 1, '2026-06-07', '178200', 22),
(37, 1, '2026-06-08', '294800', 24),
(38, 1, '2026-06-09', '156200', 38),
(39, 1, '2026-06-10', '279400', 33),
(40, 1, '2026-06-11', '266200', 22),
(41, 1, '2026-06-12', '261800', 38),
(42, 1, '2026-06-13', '171600', 30),
(43, 1, '2026-06-14', '228800', 32),
(44, 1, '2026-06-15', '217800', 40),
(45, 1, '2026-06-16', '167200', 27),
(46, 1, '2026-06-17', '195800', 27),
(47, 1, '2026-06-18', '187000', 15),
(48, 1, '2026-06-19', '226600', 34),
(49, 1, '2026-06-20', '277200', 23),
(50, 1, '2026-06-21', '209000', 28),
(51, 1, '2026-06-22', '154000', 34),
(52, 1, '2026-06-23', '171600', 17),
(53, 1, '2026-06-24', '259600', 32),
(54, 1, '2026-06-25', '154000', 38),
(55, 1, '2026-06-26', '213400', 19),
(56, 1, '2026-06-27', '270600', 37),
(57, 1, '2026-06-28', '165000', 23),
(58, 1, '2026-06-29', '305800', 39),
(59, 2, '2026-07-01', '270000', 25),
(60, 2, '2026-07-02', '236000', 32),
(61, 2, '2026-07-03', '278000', 37),
(62, 2, '2026-07-04', '272000', 41),
(63, 2, '2026-07-05', '272000', 26),
(64, 2, '2026-07-06', '146000', 23),
(65, 2, '2026-07-07', '192000', 26),
(66, 2, '2026-07-08', '238000', 32),
(67, 2, '2026-07-10', '154000', 27),
(68, 2, '2026-07-11', '176000', 39),
(69, 2, '2026-07-12', '224000', 15),
(70, 2, '2026-07-13', '182000', 33),
(71, 2, '2026-07-14', '186000', 16),
(72, 2, '2026-07-15', '214000', 34),
(73, 2, '2026-07-16', '276000', 37),
(74, 2, '2026-07-18', '238000', 22),
(75, 2, '2026-07-19', '256000', 17),
(76, 2, '2026-07-21', '224000', 19),
(77, 2, '2026-07-22', '160000', 25),
(78, 2, '2026-07-23', '270000', 28),
(79, 2, '2026-07-24', '256000', 27),
(80, 2, '2026-07-25', '188000', 24),
(81, 2, '2026-07-26', '268000', 18),
(82, 2, '2026-07-28', '256000', 26),
(83, 2, '2026-07-29', '232000', 22),
(84, 2, '2026-07-30', '246000', 17),
(85, 2, '2026-07-31', '208000', 27),
(86, 2, '2026-06-01', '256000', 23),
(87, 2, '2026-06-02', '156000', 28),
(88, 2, '2026-06-03', '142000', 44),
(89, 2, '2026-06-04', '200000', 21),
(90, 2, '2026-06-05', '188000', 22),
(91, 2, '2026-06-06', '174000', 15),
(92, 2, '2026-06-07', '184000', 43),
(93, 2, '2026-06-08', '180000', 24),
(94, 2, '2026-06-09', '174000', 38),
(95, 2, '2026-06-10', '148000', 17),
(96, 2, '2026-06-11', '210000', 26),
(97, 2, '2026-06-12', '204000', 36),
(98, 2, '2026-06-13', '268000', 21),
(99, 2, '2026-06-14', '160000', 24),
(100, 2, '2026-06-15', '252000', 39),
(101, 2, '2026-06-16', '264000', 37),
(102, 2, '2026-06-17', '270000', 37),
(103, 2, '2026-06-18', '168000', 41),
(104, 2, '2026-06-19', '144000', 40),
(105, 2, '2026-06-20', '222000', 42),
(106, 2, '2026-06-21', '182000', 18),
(107, 2, '2026-06-22', '278000', 39),
(108, 2, '2026-06-23', '260000', 29),
(109, 2, '2026-06-24', '178000', 37),
(110, 2, '2026-06-25', '194000', 27),
(111, 2, '2026-06-26', '252000', 25),
(112, 2, '2026-06-28', '206000', 42),
(113, 2, '2026-06-29', '244000', 36),
(114, 2, '2026-06-30', '226000', 19),
(115, 3, '2026-07-01', '151200', 42),
(116, 3, '2026-07-02', '172200', 36),
(117, 3, '2026-07-03', '172200', 28),
(118, 3, '2026-07-04', '279300', 31),
(119, 3, '2026-07-05', '222600', 21),
(120, 3, '2026-07-06', '199500', 41),
(121, 3, '2026-07-07', '294000', 41),
(122, 3, '2026-07-08', '239400', 42),
(123, 3, '2026-07-09', '195300', 33),
(124, 3, '2026-07-10', '170100', 28),
(125, 3, '2026-07-11', '277200', 20),
(126, 3, '2026-07-12', '186900', 19),
(127, 3, '2026-07-13', '228900', 38),
(128, 3, '2026-07-14', '224700', 19),
(129, 3, '2026-07-15', '285600', 45),
(130, 3, '2026-07-16', '233100', 30),
(131, 3, '2026-07-17', '205800', 39),
(132, 3, '2026-07-18', '176400', 33),
(133, 3, '2026-07-19', '241500', 23),
(134, 3, '2026-07-20', '237300', 18),
(135, 3, '2026-07-21', '189000', 43),
(136, 3, '2026-07-22', '210000', 22),
(137, 3, '2026-07-23', '260400', 22),
(138, 3, '2026-07-24', '260400', 23),
(139, 3, '2026-07-25', '174300', 30),
(140, 3, '2026-07-26', '165900', 16),
(141, 3, '2026-07-27', '189000', 44),
(142, 3, '2026-07-28', '178500', 35),
(143, 3, '2026-07-29', '151200', 42),
(144, 3, '2026-07-30', '254100', 25),
(145, 3, '2026-07-31', '161700', 23),
(146, 3, '2026-06-01', '193200', 42),
(147, 3, '2026-06-02', '186900', 16),
(148, 3, '2026-06-05', '184800', 32),
(149, 3, '2026-06-06', '195300', 30),
(150, 3, '2026-06-07', '239400', 40),
(151, 3, '2026-06-08', '256200', 18),
(152, 3, '2026-06-09', '218400', 36),
(153, 3, '2026-06-10', '291900', 21),
(154, 3, '2026-06-11', '199500', 27),
(155, 3, '2026-06-12', '241500', 45),
(156, 3, '2026-06-13', '275100', 44),
(157, 3, '2026-06-14', '186900', 41),
(158, 3, '2026-06-15', '182700', 17),
(159, 3, '2026-06-16', '283500', 41),
(160, 3, '2026-06-17', '264600', 44),
(161, 3, '2026-06-18', '222600', 43),
(162, 3, '2026-06-19', '281400', 28),
(163, 3, '2026-06-20', '224700', 20),
(164, 3, '2026-06-21', '224700', 33),
(165, 3, '2026-06-22', '210000', 26),
(166, 3, '2026-06-23', '165900', 32),
(167, 3, '2026-06-24', '254100', 15),
(168, 3, '2026-06-25', '174300', 26),
(169, 3, '2026-06-26', '182700', 39),
(170, 3, '2026-06-27', '165900', 20),
(171, 3, '2026-06-28', '289800', 15),
(172, 3, '2026-06-29', '178500', 41),
(173, 3, '2026-06-30', '235200', 35),
(174, 4, '2026-07-01', '152600', 23),
(175, 4, '2026-07-02', '105000', 26),
(176, 4, '2026-07-03', '165200', 28),
(177, 4, '2026-07-04', '110600', 18),
(178, 4, '2026-07-05', '189000', 18),
(179, 4, '2026-07-06', '176400', 38),
(180, 4, '2026-07-07', '137200', 30),
(181, 4, '2026-07-08', '187600', 23),
(182, 4, '2026-07-10', '98000', 17),
(183, 4, '2026-07-12', '148400', 25),
(184, 4, '2026-07-13', '186200', 17),
(185, 4, '2026-07-14', '193200', 42),
(186, 4, '2026-07-15', '119000', 16),
(187, 4, '2026-07-16', '126000', 42),
(188, 4, '2026-07-17', '154000', 19),
(189, 4, '2026-07-19', '166600', 30),
(190, 4, '2026-07-20', '99400', 33),
(191, 4, '2026-07-21', '173600', 40),
(192, 4, '2026-07-22', '173600', 20),
(193, 4, '2026-07-23', '177800', 40),
(194, 4, '2026-07-24', '154000', 16),
(195, 4, '2026-07-25', '168000', 20),
(196, 4, '2026-07-26', '113400', 30),
(197, 4, '2026-07-27', '170800', 27),
(198, 4, '2026-07-28', '166600', 43),
(199, 4, '2026-07-29', '105000', 15),
(200, 4, '2026-07-30', '194600', 19),
(201, 4, '2026-07-31', '117600', 35),
(202, 4, '2026-06-01', '141400', 21),
(203, 4, '2026-06-02', '165200', 18),
(204, 4, '2026-06-03', '159600', 16),
(205, 4, '2026-06-04', '140000', 44),
(206, 4, '2026-06-05', '179200', 17),
(207, 4, '2026-06-06', '151200', 38),
(208, 4, '2026-06-07', '183400', 38),
(209, 4, '2026-06-08', '177800', 26),
(210, 4, '2026-06-10', '190400', 31),
(211, 4, '2026-06-11', '189000', 44),
(212, 4, '2026-06-13', '161000', 16),
(213, 4, '2026-06-14', '99400', 37),
(214, 4, '2026-06-15', '121800', 20),
(215, 4, '2026-06-16', '168000', 19),
(216, 4, '2026-06-17', '121800', 38),
(217, 4, '2026-06-18', '172200', 17),
(218, 4, '2026-06-19', '106400', 33),
(219, 4, '2026-06-20', '121800', 28),
(220, 4, '2026-06-21', '194600', 40),
(221, 4, '2026-06-22', '161000', 19),
(222, 4, '2026-06-23', '105000', 15),
(223, 4, '2026-06-24', '191800', 30),
(224, 4, '2026-06-25', '119000', 29),
(225, 4, '2026-06-26', '134400', 30),
(226, 4, '2026-06-27', '158200', 42),
(227, 4, '2026-06-28', '159600', 23),
(228, 4, '2026-06-29', '182000', 20),
(229, 4, '2026-06-30', '130200', 36);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('owner','mitra','staff') NOT NULL DEFAULT 'staff',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'owner1', '$2y$10$DtnnBewmVCz7CTaVZ7DZ9.o.1elFWcrJo.LhQ4oNaOH.An7xNp8um', 'Jova (Owner)', 'owner', '2026-07-11 10:38:31'),
(2, 'mitra1', '$2y$10$DtnnBewmVCz7CTaVZ7DZ9.o.1elFWcrJo.LhQ4oNaOH.An7xNp8um', 'Ryan (Mitra)', 'mitra', '2026-07-11 10:38:31'),
(3, 'staff1', '$2y$10$DtnnBewmVCz7CTaVZ7DZ9.o.1elFWcrJo.LhQ4oNaOH.An7xNp8um', 'Staf Kios A', 'staff', '2026-07-11 10:38:31'),
(4, 'staff2', '$2y$10$DtnnBewmVCz7CTaVZ7DZ9.o.1elFWcrJo.LhQ4oNaOH.An7xNp8um', 'Staff 2', 'staff', '2026-07-15 01:08:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kios`
--
ALTER TABLE `kios`
  ADD PRIMARY KEY (`id_kios`),
  ADD KEY `id_mitra` (`id_mitra`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id_staff`),
  ADD KEY `fk_staff_kios` (`id_kios`),
  ADD KEY `fk_staff_user` (`id_user`);

--
-- Indexes for table `tb_kios`
--
ALTER TABLE `tb_kios`
  ADD PRIMARY KEY (`id_kios`);

--
-- Indexes for table `tb_sop`
--
ALTER TABLE `tb_sop`
  ADD PRIMARY KEY (`id_sop`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_kios` (`id_kios`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `kios`
--
ALTER TABLE `kios`
  MODIFY `id_kios` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id_staff` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_kios`
--
ALTER TABLE `tb_kios`
  MODIFY `id_kios` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_sop`
--
ALTER TABLE `tb_sop`
  MODIFY `id_sop` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kios`
--
ALTER TABLE `kios`
  ADD CONSTRAINT `kios_ibfk_1` FOREIGN KEY (`id_mitra`) REFERENCES `tb_user` (`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_staff_kios` FOREIGN KEY (`id_kios`) REFERENCES `kios` (`id_kios`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_staff_user` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`);

--
-- Constraints for table `tb_sop`
--
ALTER TABLE `tb_sop`
  ADD CONSTRAINT `tb_sop_ibfk_1` FOREIGN KEY (`dibuat_oleh`) REFERENCES `tb_user` (`id_user`);

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `tb_transaksi_ibfk_1` FOREIGN KEY (`id_kios`) REFERENCES `tb_kios` (`id_kios`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
