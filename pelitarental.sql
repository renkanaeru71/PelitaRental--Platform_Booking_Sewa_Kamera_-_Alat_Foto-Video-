-- Database pelitarental - dump bersih untuk PelitaRental
-- Import file ini lewat phpMyAdmin kalau database perlu dibuat ulang dari nol.
-- Akun contoh:
--   admin    : admin@gmail.com    / Pelita2026
--   penyewa  : penyewa@gmail.com  / Penyewa2026
-- Semua password tersimpan sebagai hash password_hash().

CREATE DATABASE IF NOT EXISTS `pelitarental` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pelitarental`;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `alat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alat` (
  `ID_alat` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID_kategori` int(10) unsigned NOT NULL,
  `nama_alat` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_per_hari` decimal(12,2) NOT NULL,
  `stok_total` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID_alat`),
  KEY `idx_alat_kategori` (`ID_kategori`),
  CONSTRAINT `fk_alat_kategori` FOREIGN KEY (`ID_kategori`) REFERENCES `kategori` (`ID_kategori`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `alat` DISABLE KEYS */;
INSERT INTO `alat` VALUES (1,1,'Canon EOS 80D','DSLR 24MP, cocok untuk foto dan video pemula',150000.00,3),(2,1,'Sony A6400','Mirrorless APS-C dengan autofocus cepat',200000.00,2),(3,1,'Fujifilm X-T20','Mirrorless dengan tampilan retro dan hasil warna khas',180000.00,2),(4,1,'Nikon D3500','DSLR entry level dengan baterai awet',130000.00,4),(5,1,'Canon EOS M50','Mirrorless kompak dengan layar putar',160000.00,2),(6,2,'Canon 50mm f/1.8 STM','Lensa prime ringan untuk potret',50000.00,4),(7,2,'Sigma 16mm f/1.4','Lensa lebar untuk foto malam dan landscape',90000.00,2),(8,2,'Tamron 18-200mm','Zoom serbaguna untuk perjalanan',80000.00,2),(9,2,'Sony 85mm f/1.8','Lensa telefoto untuk potret dengan bokeh halus',100000.00,1),(10,2,'Nikon 35mm f/1.8G','Lensa standar untuk dokumenter dan street photo',60000.00,3),(11,3,'Softbox 60x60 cm','Softbox untuk cahaya lembut produk dan potret',40000.00,3),(12,3,'Lampu LED Godox SL60W','Lampu LED continuous 60W dengan suhu warna stabil',120000.00,2),(13,3,'Ring Light 18 inch','Ring light dengan tripod dan 3 mode warna',45000.00,4),(14,3,'Reflector 5-in-1','Reflektor lipat untuk memantulkan cahaya',25000.00,5),(15,3,'Flash Godox TT600','Speedlite eksternal untuk kamera hot shoe',75000.00,2),(16,4,'Tripod Velbon Sherpa 235','Tripod ringan dengan kepala pan-tilt',30000.00,4),(17,4,'Baterai Canon LP-E17','Baterai cadangan untuk Canon EOS M-series',20000.00,6),(18,4,'Memory Card SanDisk 64GB','Kartu memori SDXC kecepatan tinggi',15000.00,8),(19,4,'Gimbal DJI OM 5','Stabilizer smartphone 3 sumbu',70000.00,2),(20,4,'Tas Kamera Ransel','Tas kamera dengan ruang laptop 15 inch',25000.00,3);
/*!40000 ALTER TABLE `alat` ENABLE KEYS */;
DROP TABLE IF EXISTS `booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking` (
  `ID_booking` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Kode_booking` varchar(35) NOT NULL,
  `ID_user` int(10) unsigned NOT NULL,
  `Tanggal_Mulai` date NOT NULL,
  `Tanggal_selesai` date NOT NULL,
  `Durasi` int(10) unsigned NOT NULL,
  `Total_bayar` decimal(14,2) NOT NULL DEFAULT 0.00,
  `Jenis_Jaminan` varchar(100) NOT NULL,
  `Status` enum('menunggu_bayar','dibayar','diambil','dikembalikan','selesai','gagal') NOT NULL DEFAULT 'menunggu_bayar',
  `Batas_Bayar` datetime DEFAULT NULL,
  `Dibayar_at` datetime DEFAULT NULL,
  `Diambil_at` datetime DEFAULT NULL,
  `Dikembalikan_at` datetime DEFAULT NULL,
  `Created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID_booking`),
  UNIQUE KEY `uq_booking_kode` (`Kode_booking`),
  KEY `idx_booking_user` (`ID_user`),
  KEY `idx_booking_tanggal` (`Tanggal_Mulai`,`Tanggal_selesai`),
  KEY `idx_booking_status` (`Status`),
  CONSTRAINT `fk_booking_user` FOREIGN KEY (`ID_user`) REFERENCES `user` (`ID_user`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `booking` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking` ENABLE KEYS */;
DROP TABLE IF EXISTS `detail_booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail_booking` (
  `ID_detail` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID_booking` int(10) unsigned NOT NULL,
  `ID_alat` int(10) unsigned NOT NULL,
  `Harga_Satuan` decimal(12,2) NOT NULL,
  `Jumlah` int(10) unsigned NOT NULL DEFAULT 1,
  `Subtotal` decimal(14,2) NOT NULL,
  PRIMARY KEY (`ID_detail`),
  UNIQUE KEY `uq_detail_booking_alat` (`ID_booking`,`ID_alat`),
  KEY `idx_detail_alat` (`ID_alat`),
  CONSTRAINT `fk_detail_alat` FOREIGN KEY (`ID_alat`) REFERENCES `alat` (`ID_alat`) ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_booking` FOREIGN KEY (`ID_booking`) REFERENCES `booking` (`ID_booking`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `detail_booking` DISABLE KEYS */;
/*!40000 ALTER TABLE `detail_booking` ENABLE KEYS */;
DROP TABLE IF EXISTS `kategori`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kategori` (
  `ID_kategori` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_kategori`),
  UNIQUE KEY `uq_kategori_nama` (`nama_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `kategori` DISABLE KEYS */;
INSERT INTO `kategori` VALUES (4,'Aksesori'),(1,'Kamera'),(2,'Lensa'),(3,'Lighting');
/*!40000 ALTER TABLE `kategori` ENABLE KEYS */;
DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `ID_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Nama` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `Role` enum('admin','penyewa') NOT NULL DEFAULT 'penyewa',
  `Created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID_user`),
  UNIQUE KEY `uq_user_email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Admin Pelita','admin@gmail.com','$2y$10$YUTQhbfwU5qkpuDqIVwpd.2dnmlOvUvK9xvTk5HER606qt52CwbGe','081200000001','admin','2026-11-15 04:35:26'),(2,'Kaka Penyewa','penyewa@gmail.com','$2y$10$WVIvV.6t88ouF7uqHnqwb.spF30aLDfy3dljwpp6vebMT7xQCIej2','081200000002','penyewa','2026-11-15 04:35:26');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

