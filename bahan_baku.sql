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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
