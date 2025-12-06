-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2025 at 06:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pustaka_xxx`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `penulis` varchar(100) NOT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `stok` int(11) DEFAULT 1,
  `gambar` varchar(255) DEFAULT 'default_cover.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `judul`, `penulis`, `kategori`, `deskripsi`, `stok`, `gambar`, `created_at`) VALUES
(1, 'Tentang Kamu', 'Tere Liye', 'Fiksi, Novel, Misteri', 'Novel ini mengikuti perjalanan Zaman Zulkarnaen, seorang pengacara muda yang ditugaskan menyelidiki warisan besar milik Sri Ningsih, seorang perempuan tua yang meninggal tanpa ahli waris jelas. Melalui rangkaian penelusuran dokumen, catatan, dan kesaksian, Zaman mengungkap kisah hidup Sri Ningsih yang penuh perjuangan, pengorbanan, dan nilai kemanusiaan. Tentang Kamu menghadirkan narasi mendalam mengenai identitas, keadilan, serta makna kehidupan melalui alur misteri yang terungkap secara bertahap.', 12, '6933bb589a5d1.jpg', '2025-11-23 07:09:49'),
(2, 'Artificial Intelligence: A Modern Approach-Third ed.', 'Stuart J. Russell & Peter Norvig', 'Teknologi, Artificial Intelligence', 'Buku paling terkenal dalam bidang AI. Membahas dasar-dasar kecerdasan buatan, pencarian, machine learning, knowledge representation, dan robotics. Banyak digunakan sebagai buku textbook universitas.', 10, '6933b8ffc84db.jpg', '2025-12-06 05:02:55'),
(3, 'Algorithms + Data Structures = Programs', 'Niklaus Wirth', 'Ilmu Komputer, Algoritma', 'Buku klasik yang mengajarkan dasar-dasar pemrograman menggunakan pendekatan algoritma dan struktur data. Sangat cocok untuk mahasiswa yang ingin memahami fundamental komputasi.', 12, '6933ba1febe6c.jpg', '2025-12-06 05:07:43'),
(4, 'Laut Bercerita', 'Leila S. Chudori', 'Fiksi, Sejarah', 'Novel yang menggambarkan perjuangan aktivis di era Orde Baru. Menyentuh, emosional, dan penuh nilai kemanusiaan.', 12, '6933bb1e3581c.jpg', '2025-12-06 05:11:58'),
(5, 'Hujan', 'Tere Liye', 'Novel, Persahabatan, Perjuangan', 'Novel ini mengisahkan perjalanan Lail, seorang gadis yang harus melalui masa-masa sulit setelah kehilangan kedua orang tuanya akibat sebuah bencana besar. Dengan latar dunia masa depan yang dipenuhi teknologi modern, cerita ini menyoroti proses pemulihan trauma, arti persahabatan, serta upaya Lail dalam menemukan kembali harapan dan arah hidupnya. Hujan menghadirkan tema tentang ketabahan, pertumbuhan pribadi, dan pilihan hidup dalam menghadapi perubahan.', 15, '6933bc51752f1.jpg', '2025-12-06 05:17:05');

-- --------------------------------------------------------

--
-- Table structure for table `borrows`
--

CREATE TABLE `borrows` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `tanggal_pinjam` date DEFAULT curdate(),
  `durasi_hari` int(11) NOT NULL,
  `status` enum('Pending','Disetujui','Ditolak','Dikembalikan') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrows`
--

INSERT INTO `borrows` (`id`, `user_id`, `book_id`, `tanggal_pinjam`, `durasi_hari`, `status`) VALUES
(1, 3, 1, '2025-11-23', 3, 'Dikembalikan'),
(2, 4, 1, '2025-11-23', 3, 'Dikembalikan'),
(3, 4, 1, '2025-11-23', 3, 'Disetujui'),
(4, 4, 1, '2025-11-23', 14, 'Dikembalikan'),
(5, 6, 1, '2025-11-30', 7, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `isi_berita` text NOT NULL,
  `tanggal` date DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `judul`, `isi_berita`, `tanggal`, `created_at`) VALUES
(2, 'Website Resmi Perpustakaan Kini Resmi Diluncurkan!', 'Perpustakaan dengan bangga memperkenalkan website resmi terbaru yang hadir dengan desain lebih modern dan ramah pengguna. Mulai dari katalog buku, berita, layanan digitalkini dapat diakses hanya dengan sekali klik. Selamat datang di pengalaman membaca yang lebih mudah! 🎉', '2025-11-23', '2025-12-06 04:42:27'),
(3, 'Ruang Baca Utama Kini Hadir dengan Tampilan Baru', 'Setelah proses renovasi, ruang baca utama kini tampil lebih nyaman dan estetik. Fasilitas tambahan meliputi pencahayaan yang lebih hangat, kursi ergonomis, area baca santai, serta stop kontak di setiap meja. Ayo datang dan rasakan suasana baru yang lebih produktif! ✨', '2025-11-25', '2025-12-06 04:42:27'),
(4, 'Pendaftaran Kartu Anggota Online Telah Dibuka', 'Pendaftaran anggota perpustakaan kini dapat dilakukan langsung melalui website. Prosesnya cepat, praktis, dan langsung aktif setelah verifikasi. Cocok untuk mahasiswa, pelajar, maupun masyarakat umum. 💳⚡', '2025-11-28', '2025-12-06 04:42:27'),
(5, 'Agenda Literasi Desember: Bulan Kreativitas & Membaca', 'Sepanjang bulan Desember, perpustakaan mengadakan rangkaian kegiatan literasi seperti workshop menulis cerpen, bedah buku mingguan, kelas membaca anak, dan pameran karya komunitas seni. Semua kegiatan gratis dan terbuka untuk umum! Untuk informasi waktu dan tempat pelaksanaannya akan segera diumumkan 🎤📚', '2025-12-01', '2025-12-06 04:42:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','anggota') DEFAULT 'anggota',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin', '$2y$10$YourHashedPasswordHere', 'admin', '2025-11-23 06:23:29'),
(2, 'anjir buset', 'abuse', '$2y$10$u3NdmyXLXL6peUI6DKIw3OqgY3AhhXzcc9T2hCh.mCqombaMwrFLK', 'anggota', '2025-11-23 06:48:39'),
(3, 'superadmin', 'superadmin', '$2y$10$GAsV2l71hYjsiZz46XgtUuK6/sRv6ILfzj2mYDOLXcbC6R0J1im9K', 'admin', '2025-11-23 07:06:55'),
(4, 'Poetr', 'poetry', '$2y$10$WnD8t/FqUpxVuTc8S2jEF.saqZ.fUwGRz/U0gmTITL4mzLnLBNQbS', 'anggota', '2025-11-23 08:10:02'),
(5, 'liyan', 'liyann', '$2y$10$UvTPeXByodwmo7.GCez/R.QP3rIddiTa8j4lULJb0qudjwPhRcYmC', 'anggota', '2025-11-23 11:47:51'),
(6, 'Fiyanni', 'fiyanni', '$2y$10$84q1nr06kCtVlKyAtB99XuNFSAjY/S7uMwTK0Srw/ftGLQy/5ILKm', 'anggota', '2025-11-23 11:48:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrows`
--
ALTER TABLE `borrows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `borrows`
--
ALTER TABLE `borrows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrows`
--
ALTER TABLE `borrows`
  ADD CONSTRAINT `borrows_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `borrows_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
