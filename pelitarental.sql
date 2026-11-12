-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2026 at 12:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pelitarental`
--

-- --------------------------------------------------------

--
-- Table structure for table `alat`
--

CREATE TABLE `alat` (
  `ID_alat` int(10) UNSIGNED NOT NULL,
  `ID_kategori` int(10) UNSIGNED NOT NULL,
  `nama_alat` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_per_hari` decimal(12,2) NOT NULL,
  `stok_total` int(10) UNSIGNED NOT NULL DEFAULT 0
) ;

--
-- Dumping data for table `alat`
--

INSERT INTO `alat` (`ID_alat`, `ID_kategori`, `nama_alat`, `deskripsi`, `harga_per_hari`, `stok_total`) VALUES
(1, 1, 'Canon EOS 80D', 'DSLR 24MP, cocok untuk foto dan video pemula', 150000.00, 3),
(2, 1, 'Sony A6400', 'Mirrorless APS-C dengan autofocus cepat', 200000.00, 2),
(3, 1, 'Fujifilm X-T20', 'Mirrorless dengan tampilan retro dan hasil warna khas', 180000.00, 2),
(4, 1, 'Nikon D3500', 'DSLR entry level dengan baterai awet', 130000.00, 4),
(5, 1, 'Canon EOS M50', 'Mirrorless kompak dengan layar putar', 160000.00, 2),
(6, 2, 'Canon 50mm f/1.8 STM', 'Lensa prime ringan untuk potret', 50000.00, 4),
(7, 2, 'Sigma 16mm f/1.4', 'Lensa lebar untuk foto malam dan landscape', 90000.00, 2),
(8, 2, 'Tamron 18-200mm', 'Zoom serbaguna untuk perjalanan', 80000.00, 2),
(9, 2, 'Sony 85mm f/1.8', 'Lensa telefoto untuk potret dengan bokeh halus', 100000.00, 1),
(10, 2, 'Nikon 35mm f/1.8G', 'Lensa standar untuk dokumenter dan street photo', 60000.00, 3),
(11, 3, 'Softbox 60x60 cm', 'Softbox untuk cahaya lembut produk dan potret', 40000.00, 3),
(12, 3, 'Lampu LED Godox SL60W', 'Lampu LED continuous 60W dengan suhu warna stabil', 120000.00, 2),
(13, 3, 'Ring Light 18 inch', 'Ring light dengan tripod dan 3 mode warna', 45000.00, 4),
(14, 3, 'Reflector 5-in-1', 'Reflektor lipat untuk memantulkan cahaya', 25000.00, 5),
(15, 3, 'Flash Godox TT600', 'Speedlite eksternal untuk kamera hot shoe', 75000.00, 2),
(16, 4, 'Tripod Velbon Sherpa 235', 'Tripod ringan dengan kepala pan-tilt', 30000.00, 4),
(17, 4, 'Baterai Canon LP-E17', 'Baterai cadangan untuk Canon EOS M-series', 20000.00, 6),
(18, 4, 'Memory Card SanDisk 64GB', 'Kartu memori SDXC kecepatan tinggi', 15000.00, 8),
(19, 4, 'Gimbal DJI OM 5', 'Stabilizer smartphone 3 sumbu', 70000.00, 2),
(20, 4, 'Tas Kamera Ransel', 'Tas kamera dengan ruang laptop 15 inch', 25000.00, 3);

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `ID_booking` int(10) UNSIGNED NOT NULL,
  `Kode_booking` varchar(20) NOT NULL,
  `ID_user` int(10) UNSIGNED NOT NULL,
  `Tanggal_Mulai` date NOT NULL,
  `Tanggal_selesai` date NOT NULL,
  `Durasi` int(10) UNSIGNED NOT NULL,
  `Total_bayar` decimal(14,2) NOT NULL DEFAULT 0.00,
  `Jenis_Jaminan` varchar(100) NOT NULL,
  `Status` enum('menunggu_bayar','dibayar','diambil','dikembalikan','selesai','gagal') NOT NULL DEFAULT 'menunggu_bayar',
  `Batas_Bayar` datetime DEFAULT NULL,
  `Dibayar_at` datetime DEFAULT NULL,
  `Diambil_at` datetime DEFAULT NULL,
  `Dikembalikan_at` datetime DEFAULT NULL,
  `Created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_booking`
--

CREATE TABLE `detail_booking` (
  `ID_detail` int(10) UNSIGNED NOT NULL,
  `ID_booking` int(10) UNSIGNED NOT NULL,
  `ID_alat` int(10) UNSIGNED NOT NULL,
  `Harga_Satuan` decimal(12,2) NOT NULL,
  `Jumlah` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `Subtotal` decimal(14,2) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `ID_kategori` int(10) UNSIGNED NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`ID_kategori`, `nama_kategori`) VALUES
(4, 'Aksesori'),
(1, 'Kamera'),
(2, 'Lensa'),
(3, 'Lighting');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID_user` int(10) UNSIGNED NOT NULL,
  `Nama` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `Role` enum('admin','penyewa') NOT NULL DEFAULT 'penyewa',
  `Created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID_user`, `Nama`, `Email`, `Password`, `no_hp`, `Role`, `Created_at`) VALUES
(1, 'Admin Pelita', 'admin@gmail.com', 'admin123', '081200000001', 'admin', '2026-11-10 18:38:12'),
(2, 'Kaka Niger', 'kaka@gmail.com', 'user123', '081200000002', 'penyewa', '2026-11-10 18:38:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`ID_alat`),
  ADD KEY `idx_alat_kategori` (`ID_kategori`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`ID_booking`),
  ADD UNIQUE KEY `uq_booking_kode` (`Kode_booking`),
  ADD KEY `idx_booking_user` (`ID_user`),
  ADD KEY `idx_booking_tanggal` (`Tanggal_Mulai`,`Tanggal_selesai`),
  ADD KEY `idx_booking_status` (`Status`);

--
-- Indexes for table `detail_booking`
--
ALTER TABLE `detail_booking`
  ADD PRIMARY KEY (`ID_detail`),
  ADD UNIQUE KEY `uq_detail_booking_alat` (`ID_booking`,`ID_alat`),
  ADD KEY `idx_detail_alat` (`ID_alat`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`ID_kategori`),
  ADD UNIQUE KEY `uq_kategori_nama` (`nama_kategori`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID_user`),
  ADD UNIQUE KEY `uq_user_email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alat`
--
ALTER TABLE `alat`
  MODIFY `ID_alat` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `ID_booking` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_booking`
--
ALTER TABLE `detail_booking`
  MODIFY `ID_detail` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `ID_kategori` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `ID_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alat`
--
ALTER TABLE `alat`
  ADD CONSTRAINT `fk_alat_kategori` FOREIGN KEY (`ID_kategori`) REFERENCES `kategori` (`ID_kategori`) ON UPDATE CASCADE;

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`ID_user`) REFERENCES `user` (`ID_user`) ON UPDATE CASCADE;

--
-- Constraints for table `detail_booking`
--
ALTER TABLE `detail_booking`
  ADD CONSTRAINT `fk_detail_alat` FOREIGN KEY (`ID_alat`) REFERENCES `alat` (`ID_alat`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_booking` FOREIGN KEY (`ID_booking`) REFERENCES `booking` (`ID_booking`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
