-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2025 at 12:18 PM
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
-- Database: `best_schedule`
--

-- --------------------------------------------------------

--
-- Table structure for table `reminder`
--

CREATE TABLE `reminder` (
  `id_reminder` int(11) NOT NULL,
  `id_schedule` int(11) DEFAULT NULL,
  `tanggal_kirim` date NOT NULL,
  `pesan` text DEFAULT NULL,
  `status` enum('tertunda','terkirim') DEFAULT 'tertunda'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reminder`
--

INSERT INTO `reminder` (`id_reminder`, `id_schedule`, `tanggal_kirim`, `pesan`, `status`) VALUES
(8, 3, '2025-11-10', 'Pengingat: Aktivitas \'rabes plasa\' akan dilaksanakan pada 2025-11-13', 'terkirim'),
(10, 4, '2025-11-17', 'Pengingat: Aktivitas \'ratek koorlap plasa\' akan dilaksanakan pada 2025-11-20', 'tertunda'),
(12, 5, '2025-11-08', 'Pengingat: Aktivitas \'TP 7 DPBO\' akan dilaksanakan pada 2025-11-11', 'tertunda'),
(19, 13, '2025-11-12', 'Pengingat: Aktivitas \'Meeting Project\' akan dilaksanakan pada 2025-11-15', 'tertunda'),
(21, 14, '2025-11-03', 'Pengingat: Aktivitas \'Training PHP\' akan dilaksanakan pada 2025-11-06', 'tertunda'),
(23, 15, '2025-11-10', 'Pengingat: Aktivitas \'Presentasi Akhir\' akan dilaksanakan pada 2025-11-13', 'tertunda');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id_schedule` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama_aktivitas` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal` date NOT NULL,
  `status` enum('belum dimulai','berlangsung','selesai') DEFAULT 'belum dimulai',
  `file_tambahan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id_schedule`, `id_user`, `nama_aktivitas`, `deskripsi`, `tanggal`, `status`, `file_tambahan`) VALUES
(3, 2, 'rabes plasa', 'di gmeet ', '2025-11-13', 'belum dimulai', 'https://www.figma.com/design/WaIlfqAkqVwxyIHOIys3mw/Web-mancing?node-id=0-1&t=53ILoT7q79ilbaHn-1'),
(4, 2, 'ratek koorlap plasa', 'sama kang alfian', '2025-11-20', 'belum dimulai', NULL),
(5, 2, 'TP 7 DPBO', 'ngerjain web tema bebas, dl nya hari ini omg', '2025-11-11', 'berlangsung', NULL),
(13, 3, 'Meeting Project', 'Diskusi progress project', '2025-11-15', 'belum dimulai', NULL),
(14, 4, 'Training PHP', 'Belajar PHP dasar', '2025-11-06', 'selesai', NULL),
(15, 5, 'Presentasi Akhir', 'Presentasi tugas akhir', '2025-11-13', 'berlangsung', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `instansi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `instansi`) VALUES
(2, 'hawa', 'hawadwiafina@upi.edu', 'student'),
(3, 'Karina', 'karina@example.com', 'Universitas A'),
(4, 'Budi Santoso', 'budi@example.com', 'Perusahaan B'),
(5, 'Citra Lestari', 'citra@example.com', 'Sekolah C');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reminder`
--
ALTER TABLE `reminder`
  ADD PRIMARY KEY (`id_reminder`),
  ADD KEY `id_schedule` (`id_schedule`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id_schedule`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reminder`
--
ALTER TABLE `reminder`
  MODIFY `id_reminder` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id_schedule` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reminder`
--
ALTER TABLE `reminder`
  ADD CONSTRAINT `reminder_ibfk_1` FOREIGN KEY (`id_schedule`) REFERENCES `schedule` (`id_schedule`);

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
