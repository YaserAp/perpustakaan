-- Database Dump Perpustakaan Digital
-- Waktu Ekspor: 2026-07-20 05:50:18
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES ('13', 'wahyuuu', 'wahyu@gmail.com', '$2y$10$sMwsG0i8gZFO3VqZOHASo.eyeAxhCMVq2Xvrz7/z2J97WHLNSrN2W', 'user');
INSERT INTO `users` VALUES ('15', 'saiful', 'saiful@gmail.com', '$2y$10$iIktTNj.s6FEPjy2egiTSem2aSkCUJXrlkYag7FcNQNUXpz8NKtDO', 'admin');

DROP TABLE IF EXISTS `buku`;
CREATE TABLE `buku` (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penulis` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penerbit` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun` int DEFAULT NULL,
  `stok` int DEFAULT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `buku` VALUES ('17', 'bahasa indonesia', 'ir.karyo, S.T, M.T, CITPM', NULL, NULL, '4', 'b indonesia.jpg');
INSERT INTO `buku` VALUES ('18', 'IPA', 'Dr.Ir.Rina Karlina, S.E,M.Si', NULL, NULL, '3', 'ipa.jpg');
INSERT INTO `buku` VALUES ('19', 'IPAS', 'Ir.Suhartanto,S.Ilkom,S.T.M.kom,M.H', NULL, NULL, '1', 'ipas.jpg');
INSERT INTO `buku` VALUES ('20', 'MATEMATIKA', 'Dr.Ir.Nia Khalifah Halmahera,S.Si, M,Si, M.Kom, ', NULL, NULL, '5', 'mtk.jpg');
INSERT INTO `buku` VALUES ('21', 'INFORMATIKA', 'Dr.Ir.Suryana Abas Alhidayah,S.Kom,S.Si,S.Ilkom,M.kom', NULL, NULL, '4', 'if.jpg');
INSERT INTO `buku` VALUES ('22', 'EKONOMI', 'Rehani Susanti Madyaherna,S.E, M.E', NULL, NULL, '10', 'ekonomi.jpg');

DROP TABLE IF EXISTS `peminjaman`;
CREATE TABLE `peminjaman` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `buku_id` int DEFAULT NULL,
  `tanggal_pinjam` date DEFAULT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `buku_id` (`buku_id`),
  CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `peminjaman` VALUES ('22', '13', '22', '2026-07-20', '2026-07-20', 'kembali');
INSERT INTO `peminjaman` VALUES ('23', '13', '19', '2026-07-20', '2026-07-20', 'kembali');

DROP TABLE IF EXISTS `denda`;
CREATE TABLE `denda` (
  `id` int NOT NULL AUTO_INCREMENT,
  `peminjaman_id` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `peminjaman_id` (`peminjaman_id`),
  CONSTRAINT `denda_ibfk_1` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `ulasan`;
CREATE TABLE `ulasan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `buku_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `komentar` text,
  `tanggal` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `ulasan` VALUES ('1', '13', '17', '5', 'Buku Bahasa Indonesia ini sangat bagus dan penjelasannya sangat jelas!', '2026-07-20 12:31:56');
INSERT INTO `ulasan` VALUES ('2', '13', '18', '5', 'Materi IPA Terpadu dijelaskan secara rinci dengan gambar pendukung yang baik.', '2026-07-20 12:31:56');
INSERT INTO `ulasan` VALUES ('3', '13', '20', '4', 'Buku Matematika yang sangat membantu untuk latihan soal-soal harian.', '2026-07-20 12:31:56');

SET FOREIGN_KEY_CHECKS=1;
