-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 01, 2026 at 06:28 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_toko`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_detail`
--

CREATE TABLE `tb_detail` (
  `id_detail` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `id_produk` int NOT NULL,
  `jumlah` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_detail`
--

INSERT INTO `tb_detail` (`id_detail`, `id_transaksi`, `id_produk`, `jumlah`) VALUES
(4, 4, 18, 1),
(5, 5, 12, 1),
(6, 6, 1, 1),
(7, 6, 17, 1),
(8, 7, 18, 1);

--
-- Triggers `tb_detail`
--
DELIMITER $$
CREATE TRIGGER `kurangi_stok` AFTER INSERT ON `tb_detail` FOR EACH ROW BEGIN
    UPDATE tb_produk
    SET stok = stok - NEW.jumlah
    WHERE id = NEW.id_produk;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_kategori`
--

CREATE TABLE `tb_kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kategori`
--

INSERT INTO `tb_kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Tea series'),
(2, 'Thai tea series'),
(3, 'Squash series'),
(4, 'Yakult series'),
(5, 'Milky series');

-- --------------------------------------------------------

--
-- Table structure for table `tb_produk`
--

CREATE TABLE `tb_produk` (
  `id` int NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `harga` int NOT NULL,
  `stok` int NOT NULL,
  `poto` text COLLATE utf8mb4_general_ci,
  `id_kategori` int DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_produk`
--

INSERT INTO `tb_produk` (`id`, `nama`, `harga`, `stok`, `poto`, `id_kategori`, `deskripsi`) VALUES
(1, 'Jasmine tea', 3000, 47, 'jasmine.jpeg', 1, 'Seduhan es teh melati murni dengan wangi bunga melati yang khas, rasa sepat tipis, dan manis yang pas.'),
(2, 'Lemon Tea', 6000, 50, 'lemon.jpeg', 1, 'Kombinasi klasik antara teh melati yang pekat dengan kesegaran asam buah lemon asli.'),
(3, 'Mango Tea', 6000, 50, 'mango.jpeg', 1, 'Teh melati yang diberi rasa mangga tropis, menghasilkan rasa manis buah yang legit.'),
(4, 'Grape Tea', 6000, 50, 'grape.jpeg', 1, 'Es teh segar yang berpadu sempurna dengan manisnya sirup buah anggur merah.'),
(5, 'Thai Tea Original', 7000, 30, 'original.jpeg', 2, 'Teh hitam asal Thailand asli beraroma rempah unik yang dicampur susu evaporasi gurih, berwarna jingga cerah.'),
(6, 'Thai Tea Macchiato', 11000, 29, 'macciato.jpeg', 2, 'Berbasis Thai Tea original yang diberikan lapisan krim macchiato gurih bertestur padat dan sedikit asin-manis di bagian atas cup.'),
(7, 'Thai Tea Oreo', 11000, 30, 'oreo.jpeg', 2, 'Manis gurihnya es Thai tea khas Thailand yang makin ramai dengan tambahan taburan remahan biskuit Oreo renyah.'),
(8, 'Thai Tea Choco', 11000, 30, 'choco.jpeg', 2, 'Eksperimen rasa premium yang memadukan pekatnya Thai tea dengan seduhan cokelat hitam manis.'),
(9, 'Grape Squash', 10000, 40, 'grape_s.jpeg', 3, 'Kombinasi menggelitik dari soda dingin berpadu manis pekat buah anggur ungu.'),
(10, 'Lychee Squash', 10000, 40, 'lychee_s.jpeg', 3, 'Kesegaran air soda berkarbonasi kuat yang dipadukan dengan sirup buah leci yang manis harum.'),
(11, 'Orange Squash', 10000, 40, 'orange_s.jpeg', 3, 'Sari buah jeruk manis-asam segar yang diguyur es batu melimpah dan air soda. Sangat pas di cuaca panas.'),
(12, 'Strawberry Squash', 10000, 39, 'stroberi_s.jpeg', 3, 'Paduan air soda dengan sirup buah stroberi merah terang yang menghasilkan kombinasi rasa manis-asam bergas.'),
(14, 'Yakult Strawberry', 10000, 35, 'stoberi_y.jpeg', 4, 'Detail Rasa: Campuran sirup stroberi cair asam-manis yang disiram bersama kebaikan susu fermentasi Yakult segar.'),
(15, 'Yakult Mango', 10000, 40, 'mango_y.jpeg', 4, 'Kombinasi legit buah mangga tropis dengan asam segar Yakult utuh.'),
(16, 'Melon Yakult', 10000, 45, 'melon_y.jpeg', 4, 'Segarnya aroma melon madu manis yang berpadu seimbang dengan kebaikan Yakult.'),
(17, 'Yakult Grape', 10000, 44, 'grape_y.jpeg', 4, 'Rasa manis anggur yang pekat bersatu dengan segarnya rasa kecut khas Yakult.'),
(18, 'Matcha Creamy', 10000, 48, 'matcha_m.jpeg', 5, 'Bubuk teh hijau Jepang (matcha) pilihan berkarakter sedikit pahit alami dikocok gurih bersama susu.'),
(19, 'Bubble Gum Creamy', 10000, 50, 'bubblegum_m.jpeg', 5, 'Minuman susu nostalgia anak-anak dengan warna biru muda estetik dan rasa manis permen karet yang khas.'),
(20, 'Royal Choco Creamy', 10000, 50, 'choco_m.jpeg', 5, 'Olahan susu kental manis berpadu bubuk cokelat hitam double-rich yang rasanya manis-pahit pekat nan mewah.'),
(21, 'Taro Creamy', 10000, 50, 'taro_m.jpeg', 5, 'Olahan rasa talas ungu yang punya keharuman wangi mirip kue panggang manis, dipadukan krimer kental.');

-- --------------------------------------------------------

--
-- Table structure for table `tb_restok`
--

CREATE TABLE `tb_restok` (
  `id_restok` int NOT NULL,
  `id_produk` int DEFAULT NULL,
  `jumlah_restok` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `tb_restok`
--
DELIMITER $$
CREATE TRIGGER `restok` AFTER INSERT ON `tb_restok` FOR EACH ROW BEGIN
    UPDATE tb_produk
    SET stok = stok + NEW.jumlah_restok
    WHERE id_produk = NEW.id_produk;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_transaksi` int NOT NULL,
  `id_pelanggan` int NOT NULL,
  `tanggal` date NOT NULL,
  `total_harga` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_transaksi`, `id_pelanggan`, `tanggal`, `total_harga`) VALUES
(1, 5, '2026-09-24', 3000),
(4, 2, '2026-09-30', 10000),
(5, 4, '2026-09-30', 10000),
(6, 3, '2026-09-30', 13000),
(7, 6, '2026-10-01', 10000);

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id` int NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hp` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('pelanggan','admin') COLLATE utf8mb4_general_ci NOT NULL,
  `poto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id`, `nama`, `email`, `username`, `password`, `hp`, `alamat`, `role`, `poto`) VALUES
(1, 'Widiaa', 'widiarikalestari15@gmail.com', 'widia', '$2y$10$Wxm7F5/X1Bzj3bIzGaWPMOxzsm1KJL7UCvRFt5d9j8mIJ9sLRbmNa', '088994540805', 'Jl. Perjuanagan', 'admin', 'user_1_1790051491.jpg'),
(2, 'Anggun', 'anggundwi15@gmail.com', 'anggun', 'anggun123', ' 088222162461', 'Sekar kemuning', 'pelanggan', ''),
(3, 'bening', NULL, 'bening', 'bening', NULL, NULL, 'pelanggan', ''),
(4, 'salma', NULL, 'salma', 'salma', NULL, NULL, 'pelanggan', ''),
(5, 'lita', 'lita', 'lita', 'lita', NULL, NULL, 'pelanggan', ''),
(6, 'indah', 'indah@gmail.com', 'indah', 'indah', '', 'kalikoa', 'pelanggan', ''),
(7, NULL, '', 'lovi', 'lovi', NULL, NULL, 'pelanggan', ''),
(8, NULL, NULL, 'afni', 'afni', NULL, NULL, 'pelanggan', ''),
(9, NULL, 'pelanggan@gmail.com', 'pelanggan', 'pelanggan', NULL, NULL, 'pelanggan', ''),
(10, NULL, '', 'ajeng', 'ajeng', NULL, NULL, 'pelanggan', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_detail`
--
ALTER TABLE `tb_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_transaksi` (`id_transaksi`),
  ADD KEY `fk_produk` (`id_produk`);

--
-- Indexes for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `tb_produk`
--
ALTER TABLE `tb_produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `tb_restok`
--
ALTER TABLE `tb_restok`
  ADD PRIMARY KEY (`id_restok`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_detail`
--
ALTER TABLE `tb_detail`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_produk`
--
ALTER TABLE `tb_produk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tb_restok`
--
ALTER TABLE `tb_restok`
  MODIFY `id_restok` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_detail`
--
ALTER TABLE `tb_detail`
  ADD CONSTRAINT `fk_produk` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `fk_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
