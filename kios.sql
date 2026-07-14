-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 14, 2026 at 07:24 AM
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kios`
--
ALTER TABLE `kios`
  ADD PRIMARY KEY (`id_kios`),
  ADD KEY `id_mitra` (`id_mitra`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kios`
--
ALTER TABLE `kios`
  MODIFY `id_kios` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kios`
--
ALTER TABLE `kios`
  ADD CONSTRAINT `kios_ibfk_1` FOREIGN KEY (`id_mitra`) REFERENCES `tb_user` (`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
