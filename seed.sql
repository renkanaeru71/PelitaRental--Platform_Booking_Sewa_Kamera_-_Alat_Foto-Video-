-- Skrip Seed Ulang Bersih Database pelitarental
-- Digunakan untuk mereset data ke kondisi awal pengujian

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Perlebar Kode_booking agar memuat format PR-ymd-His-ID
ALTER TABLE `booking` MODIFY `Kode_booking` varchar(35) NOT NULL;

-- 2. Kosongkan tabel transaksi dan master
TRUNCATE TABLE `detail_booking`;
TRUNCATE TABLE `booking`;
TRUNCATE TABLE `alat`;
TRUNCATE TABLE `kategori`;
TRUNCATE TABLE `user`;

SET FOREIGN_KEY_CHECKS = 1;

-- 3. Isi Kategori
INSERT INTO `kategori` (`ID_kategori`, `nama_kategori`) VALUES
(1, 'Kamera'),
(2, 'Lensa'),
(3, 'Lighting'),
(4, 'Aksesori');

-- 4. Isi Alat (stok_total murni sesuai master, tidak pernah diubah oleh transaksi booking)
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

-- 5. Isi User dengan password ter-hash
-- Admin: admin@gmail.com / Pelita2026
-- Penyewa: penyewa@gmail.com / Penyewa2026
INSERT INTO `user` (`ID_user`, `Nama`, `Email`, `Password`, `no_hp`, `Role`, `Created_at`) VALUES
(1, 'Admin Pelita', 'admin@gmail.com', '$2y$10$YUTQhbfwU5qkpuDqIVwpd.2dnmlOvUvK9xvTk5HER606qt52CwbGe', '081200000001', 'admin', NOW()),
(2, 'Kaka Penyewa', 'penyewa@gmail.com', '$2y$10$WVIvV.6t88ouF7uqHnqwb.spF30aLDfy3dljwpp6vebMT7xQCIej2', '081200000002', 'penyewa', NOW());
