-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Jan 2024 pada 14.45
-- Versi server: 10.4.22-MariaDB
-- Versi PHP: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `polihir`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(5, 'tahir', '$2y$10$cue0dx.NnWSmU.LCeRHgdetEhjS8VnSgw10XsIWlyyyNNDlVRWOGS'),
(6, 'wiyan', '$2y$10$gn51Yfu0M9gBSdPd5dq9LO39N.dRujgH9wm1i6OZbgzEAJZmgqJi.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `daftar_poli`
--

CREATE TABLE `daftar_poli` (
  `id` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `keluhan` text NOT NULL,
  `no_antrian` int(11) NOT NULL,
  `status_periksa` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `daftar_poli`
--

INSERT INTO `daftar_poli` (`id`, `id_pasien`, `id_jadwal`, `keluhan`, `no_antrian`, `status_periksa`) VALUES
(15, 4, 1, 'saya sakit cacar', 1, '1'),
(19, 7, 1, 'sakit gigi', 2, '1'),
(20, 3, 4, 'sakit batuk, leher pegal', 1, '1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_periksa`
--

CREATE TABLE `detail_periksa` (
  `id` int(11) NOT NULL,
  `id_periksa` int(11) NOT NULL,
  `id_obat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `detail_periksa`
--

INSERT INTO `detail_periksa` (`id`, `id_periksa`, `id_obat`) VALUES
(22, 10, 13),
(31, 15, 14),
(32, 15, 15),
(33, 16, 14),
(34, 16, 15);

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokter`
--

CREATE TABLE `dokter` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `no_hp` varchar(50) NOT NULL,
  `id_poli` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `dokter`
--

INSERT INTO `dokter` (`id`, `nama`, `password`, `alamat`, `no_hp`, `id_poli`) VALUES
(1, 'nunu', '$2y$10$euw8.UjUmt2nSBf4XtUkWeY5e8kD/s/0JLFBHovEcEjU0NcX9b2TW', 'Jawa Timur', '0812345678', 1),
(2, 'jajan', '$2y$10$heUmNVyCSCMjxiiIyqigN.2trVUBX9YoW6hZb0iEL1teC.MTL.TL6', 'Sumatra', '08987654321', 2),
(5, 'tahirwiyan', '$2y$10$ub7.LWsk5506PQMiD/S.nOyWSE/tgU8AbHVYcniMEiChTFw0b18cW', 'tahir', '081236417727', 3),
(6, 'wiyan', '$2y$10$.dSonvekHw1l0z4c7FSi7OBHgmwntrDIgppceky5mByy8cbCRwxwu', 'Toko, Kec. Penawangan, Kabupaten Grobogan, Jawa Tengah 58161', '0812361637', 4),
(7, 'priyambodo', '$2y$10$sphfDEPaWGxDjZ.JeFdov.DKmKpmykWru/bnDZngkD6qI6KXVKNES', 'Semarang', '081234125637', 3),
(8, 'prisna', '$2y$10$v.dlxT98LGdbMrv7V7Wvvun.1/lo8..Y0ifLv5SKQzLLWmFGDamqG', 'Semarang', '08364527359', 4),
(9, 'zaenab', '$2y$10$hkqcRfJgDWGzPFlaBUWeQu8ZwGosCZ3zU/Z0vgYxcHJoy8r6OUdfC', 'tluko', '0902849172', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_periksa`
--

CREATE TABLE `jadwal_periksa` (
  `id` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `jadwal_periksa`
--

INSERT INTO `jadwal_periksa` (`id`, `id_dokter`, `hari`, `jam_mulai`, `jam_selesai`) VALUES
(1, 1, 'Senin', '07:00:00', '12:00:00'),
(2, 2, 'Selasa', '10:00:00', '18:00:00'),
(3, 5, 'Rabu', '15:00:00', '20:00:00'),
(4, 6, 'Kamis', '10:00:00', '19:00:00'),
(7, 1, 'Jumat', '20:00:00', '21:00:00'),
(9, 7, 'Jumat', '20:00:00', '22:00:00'),
(10, 9, 'Sabtu', '12:00:00', '18:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `obat`
--

CREATE TABLE `obat` (
  `id` int(11) NOT NULL,
  `nama_obat` varchar(100) NOT NULL,
  `kemasan` varchar(35) NOT NULL,
  `harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `obat`
--

INSERT INTO `obat` (`id`, `nama_obat`, `kemasan`, `harga`) VALUES
(13, 'Abacavir', '50ml', 90000),
(14, 'Oskadon', '20ml', 30000),
(15, 'Cataflam', '20ml', 45000),
(16, 'Duphaston', '10ml', 350000),
(17, 'Basiliximab', '50ml', 50000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pasien`
--

CREATE TABLE `pasien` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `no_ktp` varchar(255) NOT NULL,
  `no_hp` varchar(50) NOT NULL,
  `no_rm` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pasien`
--

INSERT INTO `pasien` (`id`, `nama`, `password`, `alamat`, `no_ktp`, `no_hp`, `no_rm`) VALUES
(2, 'rudi', '$2a$12$NDvJAP44I6KPgv8mnK7freJqd3DOQzjsoELlJbas/jdiP2RH.EV4y', 'jambi', '553377996677', '0833333', '202305-001'),
(3, 'yatmi', '$2a$12$h4zOaLFdQWuPa/L5NdSvq./b8LD0gmpOuLcasPgyYKnapDci7zjMK', 'Solo', '998822334455', '08452345647', '202305-002'),
(4, 'heri', '$2a$12$yvYnpMgtOBAY9NnAoBqnau/5XO6zpysPaYmZ6lxVxDF27ZBLBYoH6', 'Purwordadi', '8877123561234', '0877665234', '202305-003'),
(5, 'emba', '$2a$12$P4ir8EbcoNHEGGfICxv88eDoZX3Cg8on01fTZYBRyR6LXM85xvOeu', 'Toko, Kec. Penawangan, Kabupaten Grobogan, Jawa Tengah 58161', '83537485834', '08123412346', '202312-001'),
(6, 'embu', '$2a$12$rgv9BajvW7jZEf.vdEfRe.DH0VnBygRW.ygQYpQtYQHSsTt/Or0.C', 'Toko, Kec. Penawangan, Kabupaten Grobogan, Jawa Tengah 58161', '83537485834', '08123412346', '202312-002'),
(7, 'syifa', '$2y$10$Quw1hsGDcKIeHKrPqEVCrec/4Foxgdqnb5i/q6xORtfY/2qCavaBC', 'Semarang', '937917329411234', '08999111884', '202312-003'),
(8, 'sauqiya', '$2y$10$0paZMW1KRokpb6oz6WXvgehFcdZDL3gJ1eXR5h383z2JdKMUUM2s.', 'Bandung', '318468174713418148', '0846668881236', '202312-004'),
(9, 'jujun', '$2y$10$9EfzuU9jgdPEhiHfeh4bxOAD28GJxupBxnBiug3RumS3caJBnjda.', 'Kalimantan', '878285823476580', '0888812321', '202312-005'),
(10, 'panji', '$2y$10$KwE.3fW7mHfXhWruZDdhCeUy.pJNEXPeNC3rN0e4a7XUECD8oOYxK', 'jL. Jenderal Ahamd Yani, Mekar Sari, Kec. Balikpapan Tengah', '912347912374819', '0881267712', '202312-006'),
(13, 'yuni', '$2y$10$bXtvHJhS6GxucV/R3ct1C.NlYBG/AmKMrMSn3qItmHFudq5xKtori', 'Purwokerto, rt 1', '893134751373451', '088777665546', '202401-007');

-- --------------------------------------------------------

--
-- Struktur dari tabel `periksa`
--

CREATE TABLE `periksa` (
  `id` int(11) NOT NULL,
  `id_daftar_poli` int(11) NOT NULL,
  `tgl_periksa` datetime NOT NULL,
  `catatan` text NOT NULL,
  `biaya_periksa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `periksa`
--

INSERT INTO `periksa` (`id`, `id_daftar_poli`, `tgl_periksa`, `catatan`, `biaya_periksa`) VALUES
(10, 15, '2024-01-04 13:46:00', 'dimimun 2x sehari ya', 270000),
(15, 19, '2024-01-05 19:57:00', 'Minum obat 3x sehari ya', 225000),
(16, 20, '2024-01-05 20:09:00', 'Istirahat yang cukup minum obatnya 3x sehari', 225000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `poli`
--

CREATE TABLE `poli` (
  `id` int(11) NOT NULL,
  `nama_poli` varchar(25) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `poli`
--

INSERT INTO `poli` (`id`, `nama_poli`, `keterangan`) VALUES
(1, 'Poli Umum', 'Penyakit Umum'),
(2, 'Poli Anak', 'Mengenai Anak'),
(3, 'Poli Kandungan', 'Menangani Kandungan'),
(4, 'Poli Lansia', 'Menangani Lansia');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `daftar_poli`
--
ALTER TABLE `daftar_poli`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `id_jadwal` (`id_jadwal`);

--
-- Indeks untuk tabel `detail_periksa`
--
ALTER TABLE `detail_periksa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detail_periksa_obat` (`id_obat`),
  ADD KEY `id_periksa` (`id_periksa`);

--
-- Indeks untuk tabel `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_poli` (`id_poli`);

--
-- Indeks untuk tabel `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indeks untuk tabel `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `periksa`
--
ALTER TABLE `periksa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_daftar_poli` (`id_daftar_poli`);

--
-- Indeks untuk tabel `poli`
--
ALTER TABLE `poli`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `daftar_poli`
--
ALTER TABLE `daftar_poli`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `detail_periksa`
--
ALTER TABLE `detail_periksa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT untuk tabel `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `obat`
--
ALTER TABLE `obat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `periksa`
--
ALTER TABLE `periksa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `poli`
--
ALTER TABLE `poli`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `daftar_poli`
--
ALTER TABLE `daftar_poli`
  ADD CONSTRAINT `daftar_poli_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `daftar_poli_ibfk_2` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_periksa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_periksa`
--
ALTER TABLE `detail_periksa`
  ADD CONSTRAINT `detail_periksa_ibfk_1` FOREIGN KEY (`id_periksa`) REFERENCES `periksa` (`id`),
  ADD CONSTRAINT `fk_detail_periksa_obat` FOREIGN KEY (`id_obat`) REFERENCES `obat` (`id`);

--
-- Ketidakleluasaan untuk tabel `dokter`
--
ALTER TABLE `dokter`
  ADD CONSTRAINT `dokter_ibfk_1` FOREIGN KEY (`id_poli`) REFERENCES `poli` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  ADD CONSTRAINT `jadwal_periksa_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `periksa`
--
ALTER TABLE `periksa`
  ADD CONSTRAINT `periksa_ibfk_1` FOREIGN KEY (`id_daftar_poli`) REFERENCES `daftar_poli` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
