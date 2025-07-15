-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Jul 2025 pada 10.25
-- Versi server: 10.1.38-MariaDB
-- Versi PHP: 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e-skkm`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `arsip_eksternal`
--

CREATE TABLE `arsip_eksternal` (
  `id_arsip_eksternal` int(10) NOT NULL,
  `id_berkas_eksternal` int(10) NOT NULL,
  `id_ormawa` int(11) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `id_kemahasiswaan` int(10) NOT NULL,
  `nama_peserta` varchar(100) NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `nomor_sertifikat_eksternal` varchar(100) NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `tanggal_dikeluarkan` date NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `tahun_arsip` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `arsip_eksternal`
--

INSERT INTO `arsip_eksternal` (`id_arsip_eksternal`, `id_berkas_eksternal`, `id_ormawa`, `nama_kegiatan`, `id_kemahasiswaan`, `nama_peserta`, `tanggal_kegiatan`, `nomor_sertifikat_eksternal`, `tanggal_pengajuan`, `tanggal_dikeluarkan`, `keterangan`, `tahun_arsip`) VALUES
(1, 1, 7, 'ABS', 23, 'LPBA', '2025-05-10', '1/Srtf.eks/KMHS/V/2025', '2025-05-11', '2025-05-12', 'sponsor', 2026),
(2, 2, 7, 'ABS', 23, 'Politeknik Nasional', '2025-05-10', '2/Srtf.eks/KMHS/V/2025', '2025-05-11', '2025-05-12', 'sponsor', 2025),
(3, 3, 7, 'ABS', 23, 'Maxim', '2025-05-10', '3/Srtf.eks/KMHS/V/2025', '2025-05-11', '2025-05-12', 'sponsor', 2025),
(4, 4, 7, 'ABS', 23, 'Goelagoelagolf', '2025-05-10', '4/Srtf.eks/KMHS/V/2025', '2025-05-11', '2025-05-12', 'sponsor', 2025),
(5, 5, 7, 'ABS', 23, 'Artlistics Printing', '2025-05-10', '5/Srtf.eks/KMHS/V/2025', '2025-05-11', '2025-05-12', 'sponsor', 2025),
(6, 6, 7, 'ABS', 23, 'Dapure Jamu', '2025-05-10', '6/Srtf.eks/KMHS/V/2025', '2025-05-11', '2025-05-12', 'sponsor', 2025),
(7, 7, 7, 'ABS', 23, 'AMK Tech', '2025-05-10', '7/Srtf.eks/KMHS/V/2025', '2025-05-11', '2025-05-12', 'sponsor', 2025),
(8, 8, 7, 'ABS', 23, 'Keenan Garden', '2025-05-10', '8/Srtf.eks/KMHS/V/2025', '2025-05-11', '2025-05-12', 'sponsor', 2025),
(9, 9, 7, 'ABS', 23, 'LPBA', '2025-05-10', '9/Srtf.eks/KMHS/V/2025', '2025-05-11', '2025-05-12', 'sponsor', 2025),
(10, 1, 7, 'ABS', 0, 'LPBA', '2025-05-10', '', '2025-05-12', '0000-00-00', 'sponsor', 2025),
(11, 2, 7, 'ABS', 0, 'Politeknik Nasional', '2025-05-10', '', '2025-05-12', '0000-00-00', 'sponsor', 2025),
(12, 3, 7, 'ABS', 0, 'Maxim', '2025-05-10', '', '2025-05-12', '0000-00-00', 'sponsor', 2025),
(13, 4, 7, 'ABS', 0, 'Goelagoelagolf', '2025-05-10', '', '2025-05-12', '0000-00-00', 'sponsor', 2025),
(14, 5, 7, 'ABS', 0, 'Artlistics Printing', '2025-05-10', '', '2025-05-12', '0000-00-00', 'sponsor', 2025),
(15, 6, 7, 'ABS', 0, 'Dapure Jamu', '2025-05-10', '', '2025-05-12', '0000-00-00', 'sponsor', 2025),
(16, 7, 7, 'ABS', 0, 'AMK Tech', '2025-05-10', '', '2025-05-12', '0000-00-00', 'sponsor', 2025),
(17, 8, 7, 'ABS', 0, 'Keenan Garden', '2025-05-10', '', '2025-05-12', '0000-00-00', 'sponsor', 2025),
(18, 9, 7, 'ABS', 0, 'LPBA', '2025-05-10', '', '2025-05-12', '0000-00-00', 'sponsor', 2025),
(25, 1, 7, 'ABS', 0, 'LPBA', '2025-05-10', '', '2025-05-15', '0000-00-00', 'sponsor', 2025),
(26, 2, 7, 'ABS', 0, 'Politeknik Nasional', '2025-05-10', '', '2025-05-15', '0000-00-00', 'sponsor', 2025),
(27, 3, 7, 'ABS', 0, 'Maxim', '2025-05-10', '', '2025-05-15', '0000-00-00', 'sponsor', 2025),
(28, 4, 7, 'ABS', 0, 'Goelagoelagolf', '2025-05-10', '', '2025-05-15', '0000-00-00', 'sponsor', 2025),
(29, 5, 7, 'ABS', 0, 'Artlistics Printing', '2025-05-10', '', '2025-05-15', '0000-00-00', 'sponsor', 2025),
(30, 6, 7, 'ABS', 0, 'Dapure Jamu', '2025-05-10', '', '2025-05-15', '0000-00-00', 'sponsor', 2025),
(31, 7, 7, 'ABS', 0, 'AMK Tech', '2025-05-10', '', '2025-05-15', '0000-00-00', 'sponsor', 2025),
(32, 8, 7, 'ABS', 0, 'Keenan Garden', '2025-05-10', '', '2025-05-15', '0000-00-00', 'sponsor', 2025),
(33, 9, 7, 'ABS', 0, 'LPBA', '2025-05-10', '', '2025-05-15', '0000-00-00', 'sponsor', 2025),
(34, 1, 7, 'ABS', 23, 'LPBA', '2025-05-10', '1/Srtf.eks/KMHS/V/2025', '2025-05-15', '2025-05-16', 'sponsor', 2025),
(35, 2, 7, 'ABS', 23, 'Politeknik Nasional', '2025-05-10', '2/Srtf.eks/KMHS/V/2025', '2025-05-15', '2025-05-16', 'sponsor', 2025),
(36, 3, 7, 'ABS', 23, 'Maxim', '2025-05-10', '3/Srtf.eks/KMHS/V/2025', '2025-05-15', '2025-05-16', 'sponsor', 2025),
(37, 4, 7, 'ABS', 23, 'Goelagoelagolf', '2025-05-10', '4/Srtf.eks/KMHS/V/2025', '2025-05-15', '2025-05-16', 'sponsor', 2025),
(38, 5, 7, 'ABS', 23, 'Artlistics Printing', '2025-05-10', '5/Srtf.eks/KMHS/V/2025', '2025-05-15', '2025-05-16', 'sponsor', 2025),
(39, 6, 7, 'ABS', 23, 'Dapure Jamu', '2025-05-10', '6/Srtf.eks/KMHS/V/2025', '2025-05-15', '2025-05-16', 'sponsor', 2025),
(40, 7, 7, 'ABS', 23, 'AMK Tech', '2025-05-10', '7/Srtf.eks/KMHS/V/2025', '2025-05-15', '2025-05-16', 'sponsor', 2025),
(41, 8, 7, 'ABS', 23, 'Keenan Garden', '2025-05-10', '8/Srtf.eks/KMHS/V/2025', '2025-05-15', '2025-05-16', 'sponsor', 2025),
(42, 9, 7, 'ABS', 23, 'LPBA', '2025-05-10', '9/Srtf.eks/KMHS/V/2025', '2025-05-15', '2025-05-16', 'sponsor', 2025),
(43, 10, 6, 'Pemira', 0, 'LPBA', '2025-05-10', '', '2025-05-19', '0000-00-00', 'sponsor', 2025),
(44, 11, 6, 'Pemira', 0, 'Politeknik Nasional', '2025-05-10', '', '2025-05-19', '0000-00-00', 'sponsor', 2025),
(45, 12, 6, 'Pemira', 0, 'Maxim', '2025-05-10', '', '2025-05-19', '0000-00-00', 'sponsor', 2025),
(46, 13, 6, 'Pemira', 0, 'Goelagoelagolf', '2025-05-10', '', '2025-05-19', '0000-00-00', 'sponsor', 2025),
(47, 14, 6, 'Pemira', 0, 'Artlistics Printing', '2025-05-10', '', '2025-05-19', '0000-00-00', 'sponsor', 2025),
(48, 15, 6, 'Pemira', 0, 'LPBA', '2025-05-10', '', '2025-06-12', '0000-00-00', 'sponsor', 2025),
(49, 16, 6, 'Pemira', 0, 'Politeknik Nasional', '2025-05-10', '', '2025-06-12', '0000-00-00', 'sponsor', 2025),
(50, 17, 6, 'Pemira', 0, 'Maxim', '2025-05-10', '', '2025-06-12', '0000-00-00', 'sponsor', 2025),
(51, 18, 6, 'Pemira', 0, 'Goelagoelagolf', '2025-05-10', '', '2025-06-12', '0000-00-00', 'sponsor', 2025),
(52, 19, 6, 'Pemira', 0, 'Artlistics Printing', '2025-05-10', '', '2025-06-12', '0000-00-00', 'sponsor', 2025);

-- --------------------------------------------------------

--
-- Struktur dari tabel `arsip_piagam`
--

CREATE TABLE `arsip_piagam` (
  `id_arsip_piagam` int(10) NOT NULL,
  `id_berkas_piagam` int(10) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `id_ormawa` int(10) NOT NULL,
  `id_kemahasiswaan` int(10) NOT NULL,
  `nama_penerima` varchar(100) NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `tanggal_dikeluarkan` date NOT NULL,
  `nomor_sertifikat_piagam` varchar(100) NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `tahun_arsip` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `arsip_piagam`
--

INSERT INTO `arsip_piagam` (`id_arsip_piagam`, `id_berkas_piagam`, `nama_kegiatan`, `id_ormawa`, `id_kemahasiswaan`, `nama_penerima`, `tanggal_kegiatan`, `tanggal_pengajuan`, `tanggal_dikeluarkan`, `nomor_sertifikat_piagam`, `keterangan`, `tahun_arsip`) VALUES
(1, 1, 'ABS', 7, 0, 'AKHMAD RIZKI PRAYOGA', '2025-05-10', '2025-05-12', '0000-00-00', '', 'Juara 1', 2026),
(2, 2, 'ABS', 7, 0, 'DEWA PUTU AGIE ADITYA', '2025-05-10', '2025-05-12', '0000-00-00', '', 'Juara 1', 2025),
(3, 3, 'ABS', 7, 0, 'I Nyoman Adi Pratama Putra, S.Kom', '2025-05-10', '2025-05-12', '0000-00-00', '', 'Juara 1', 2025),
(4, 4, 'ABS', 7, 0, 'Gusti Diptha Pranasta Wisma', '2025-05-10', '2025-05-12', '0000-00-00', '', 'juara 2', 2025),
(5, 5, 'ABS', 7, 0, 'GDE SASTRAWANGSA, S.T., M.T.', '2025-05-10', '2025-05-12', '0000-00-00', '', 'juara 2', 2025),
(6, 6, 'ABS', 7, 0, 'I PUTU SUNDIKA, S.T., MT.', '2025-05-10', '2025-05-12', '0000-00-00', '', 'Juara 1', 2025),
(7, 7, 'ABS', 7, 0, 'Bill Elim', '2025-05-10', '2025-05-12', '0000-00-00', '', 'Juara 1', 2025);

-- --------------------------------------------------------

--
-- Struktur dari tabel `arsip_skkm`
--

CREATE TABLE `arsip_skkm` (
  `id_arsip` int(11) NOT NULL,
  `id_berkas_internal` int(11) DEFAULT NULL,
  `id_ormawa` int(11) NOT NULL,
  `nim` int(20) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `partisipasi` varchar(100) NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `id_bem` int(11) NOT NULL,
  `kategori_kegiatan` varchar(100) NOT NULL,
  `tingkat` varchar(100) NOT NULL,
  `poin_skkm` int(11) NOT NULL,
  `id_kemahasiswaan` int(11) NOT NULL,
  `nomor_sertifikat_internal` varchar(100) NOT NULL,
  `tanggal_dikeluarkan` date NOT NULL,
  `tahun_arsip` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `arsip_skkm`
--

INSERT INTO `arsip_skkm` (`id_arsip`, `id_berkas_internal`, `id_ormawa`, `nim`, `nama_kegiatan`, `partisipasi`, `tanggal_kegiatan`, `tanggal_pengajuan`, `id_bem`, `kategori_kegiatan`, `tingkat`, `poin_skkm`, `id_kemahasiswaan`, `nomor_sertifikat_internal`, `tanggal_dikeluarkan`, `tahun_arsip`) VALUES
(1, 1, 6, 200040045, 'PEMIRA 2025', 'Panitia', '2025-06-09', '2025-06-09', 29, 'Bidang Organisasi & Sosial', 'PT', 2, 23, '1/Srtf/KMHS/VI/2025', '2025-06-10', 2025),
(2, 2, 6, 200040060, 'PEMIRA 2025', 'Panitia', '2025-06-09', '2025-06-09', 29, 'Bidang Organisasi & Sosial', 'PT', 2, 23, '2/Srtf/KMHS/VI/2025', '2025-06-11', 2025),
(3, 3, 6, 200040061, 'PEMIRA 2025', 'Panitia', '2025-06-09', '2025-06-09', 29, 'Bidang Organisasi & Sosial', 'PT', 2, 23, '3/Srtf/KMHS/VI/2025', '2025-06-12', 2025),
(4, 4, 6, 190040028, 'PEMIRA 2025', 'Panitia', '2025-06-09', '2025-06-09', 29, 'Bidang Organisasi & Sosial', 'PT', 2, 23, '4/Srtf/KMHS/VI/2025', '2025-06-13', 2025),
(5, 5, 6, 190040014, 'PEMIRA 2025', 'Panitia', '2025-06-09', '2025-06-09', 29, 'Bidang Organisasi & Sosial', 'PT', 2, 23, '5/Srtf/KMHS/VI/2025', '2025-06-14', 2025);

-- --------------------------------------------------------

--
-- Struktur dari tabel `berkas_eksternal`
--

CREATE TABLE `berkas_eksternal` (
  `id_berkas_eksternal` int(10) NOT NULL,
  `id_ormawa` int(11) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `id_kemahasiswaan` int(10) NOT NULL,
  `nama_peserta` varchar(100) NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `nomor_sertifikat_eksternal` varchar(100) NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `tanggal_dikeluarkan` date NOT NULL,
  `keterangan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `berkas_eksternal`
--

INSERT INTO `berkas_eksternal` (`id_berkas_eksternal`, `id_ormawa`, `nama_kegiatan`, `id_kemahasiswaan`, `nama_peserta`, `tanggal_kegiatan`, `nomor_sertifikat_eksternal`, `tanggal_pengajuan`, `tanggal_dikeluarkan`, `keterangan`) VALUES
(1, 6, 'Pemira', 0, 'LPBA', '2025-05-10', '1/Srtf.eks/KMHS/VI/2025', '2025-06-15', '2025-06-17', 'sponsor'),
(2, 6, 'Pemira', 0, 'Politeknik Nasional', '2025-05-10', '2/Srtf.eks/KMHS/VI/2025', '2025-06-15', '2025-06-17', 'sponsor'),
(3, 6, 'Pemira', 0, 'Maxim', '2025-05-10', '3/Srtf.eks/KMHS/VI/2025', '2025-06-15', '2025-06-17', 'sponsor'),
(4, 6, 'Pemira', 0, 'Goelagoelagolf', '2025-05-10', '4/Srtf.eks/KMHS/VI/2025', '2025-06-15', '2025-06-17', 'sponsor'),
(5, 6, 'Pemira', 0, 'Artlistics Printing', '2025-05-10', '5/Srtf.eks/KMHS/VI/2025', '2025-06-15', '2025-06-17', 'sponsor');

-- --------------------------------------------------------

--
-- Struktur dari tabel `berkas_internal`
--

CREATE TABLE `berkas_internal` (
  `id_berkas_internal` int(10) NOT NULL,
  `id_ormawa` int(10) NOT NULL,
  `nim` int(20) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `partisipasi` varchar(100) NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `id_bem` int(10) NOT NULL,
  `kategori_kegiatan` varchar(100) NOT NULL,
  `jenis_kegiatan` varchar(100) NOT NULL,
  `tingkat` varchar(100) NOT NULL,
  `poin_skkm` int(10) NOT NULL,
  `id_kemahasiswaan` int(10) NOT NULL,
  `nomor_sertifikat_internal` varchar(100) NOT NULL,
  `tanggal_dikeluarkan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `berkas_internal`
--

INSERT INTO `berkas_internal` (`id_berkas_internal`, `id_ormawa`, `nim`, `nama_kegiatan`, `partisipasi`, `tanggal_kegiatan`, `tanggal_pengajuan`, `id_bem`, `kategori_kegiatan`, `jenis_kegiatan`, `tingkat`, `poin_skkm`, `id_kemahasiswaan`, `nomor_sertifikat_internal`, `tanggal_dikeluarkan`) VALUES
(1, 6, 200040045, 'PEMIRA 2025', 'Panitia', '2025-06-09', '2025-06-15', 0, '', '', '', 0, 0, '', '0000-00-00'),
(2, 6, 200040060, 'PEMIRA 2025', 'Panitia', '2025-06-09', '2025-06-15', 0, '', '', '', 0, 0, '', '0000-00-00'),
(3, 6, 200040061, 'PEMIRA 2025', 'Panitia', '2025-06-09', '2025-06-15', 0, '', '', '', 0, 0, '', '0000-00-00'),
(4, 6, 190040028, 'PEMIRA 2025', 'Panitia', '2025-06-09', '2025-06-15', 0, '', '', '', 0, 0, '', '0000-00-00'),
(5, 6, 190040014, 'PEMIRA 2025', 'Panitia', '2025-06-09', '2025-06-15', 0, '', '', '', 0, 0, '', '0000-00-00'),
(6, 6, 200040045, 'AUDIT ORMAWA', 'Panitia', '2025-06-10', '2025-06-15', 6, 'Bidang Organisasi & Sosial', 'Kepanitiaan - Anggota', 'P.T.', 3, 0, '6/srtf/KMHS/VI/2025', '2025-06-26'),
(7, 6, 200040060, 'AUDIT ORMAWA', 'Panitia', '2025-06-10', '2025-06-15', 6, 'Bidang Organisasi & Sosial', 'Kepanitiaan - Anggota', 'P.T.', 3, 0, '7/srtf/KMHS/VI/2025', '2025-06-26'),
(8, 6, 200040061, 'AUDIT ORMAWA', 'Panitia', '2025-06-10', '2025-06-15', 6, 'Bidang Organisasi & Sosial', 'Kepanitiaan - Anggota', 'P.T.', 3, 0, '8/srtf/KMHS/VI/2025', '2025-06-26'),
(9, 6, 190040028, 'AUDIT ORMAWA', 'Panitia', '2025-06-10', '2025-06-15', 6, 'Bidang Organisasi & Sosial', 'Kepanitiaan - Anggota', 'P.T.', 3, 0, '9/srtf/KMHS/VI/2025', '2025-06-26'),
(10, 6, 190040014, 'AUDIT ORMAWA', 'Panitia', '2025-06-10', '2025-06-15', 6, 'Bidang Organisasi & Sosial', 'Kepanitiaan - Anggota', 'P.T.', 3, 0, '10/srtf/KMHS/VI/2025', '2025-06-26'),
(11, 4, 200040045, 'Seminar Nasional', 'Panitia', '2025-06-09', '2025-06-15', 0, '', '', '', 0, 0, '', '0000-00-00'),
(12, 4, 200040060, 'Seminar Nasional', 'Panitia', '2025-06-09', '2025-06-15', 0, '', '', '', 0, 0, '', '0000-00-00'),
(13, 4, 200040061, 'Seminar Nasional', 'Panitia', '2025-06-09', '2025-06-15', 0, '', '', '', 0, 0, '', '0000-00-00'),
(14, 4, 190040028, 'Seminar Nasional', 'Panitia', '2025-06-09', '2025-06-15', 0, '', '', '', 0, 0, '', '0000-00-00'),
(15, 4, 190040014, 'Seminar Nasional', 'Panitia', '2025-06-09', '2025-06-15', 0, '', '', '', 0, 0, '', '0000-00-00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `berkas_piagam`
--

CREATE TABLE `berkas_piagam` (
  `id_berkas_piagam` int(10) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `id_ormawa` int(10) NOT NULL,
  `id_kemahasiswaan` int(10) NOT NULL,
  `nama_penerima` varchar(100) NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `tanggal_dikeluarkan` date NOT NULL,
  `nomor_sertifikat_piagam` varchar(100) NOT NULL,
  `keterangan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `berkas_piagam`
--

INSERT INTO `berkas_piagam` (`id_berkas_piagam`, `nama_kegiatan`, `id_ormawa`, `id_kemahasiswaan`, `nama_penerima`, `tanggal_kegiatan`, `tanggal_pengajuan`, `tanggal_dikeluarkan`, `nomor_sertifikat_piagam`, `keterangan`) VALUES
(1, 'ABS', 7, 23, 'AKHMAD RIZKI PRAYOGA', '2025-05-10', '2025-05-12', '2025-06-18', '1/Piagam/KMHS/VI/2025', 'Juara 1'),
(2, 'ABS', 7, 23, 'DEWA PUTU AGIE ADITYA', '2025-05-10', '2025-05-12', '2025-06-18', '2/Piagam/KMHS/VI/2025', 'Juara 1'),
(3, 'ABS', 7, 23, 'I Nyoman Adi Pratama Putra, S.Kom', '2025-05-10', '2025-05-12', '2025-06-18', '3/Piagam/KMHS/VI/2025', 'Juara 1'),
(4, 'ABS', 7, 23, 'Gusti Diptha Pranasta Wisma', '2025-05-10', '2025-05-12', '2025-06-18', '4/Piagam/KMHS/VI/2025', 'juara 2'),
(5, 'ABS', 7, 23, 'GDE SASTRAWANGSA, S.T., M.T.', '2025-05-10', '2025-05-12', '2025-06-18', '5/Piagam/KMHS/VI/2025', 'juara 2'),
(6, 'ABS', 7, 23, 'I PUTU SUNDIKA, S.T., MT.', '2025-05-10', '2025-05-12', '2025-06-18', '6/Piagam/KMHS/VI/2025', 'Juara 1'),
(7, 'ABS', 7, 23, 'Bill Elim', '2025-05-10', '2025-05-12', '2025-06-18', '7/Piagam/KMHS/VI/2025', 'Juara 1'),
(8, 'Pemira', 6, 23, 'AKHMAD RIZKI PRAYOGA', '2025-05-10', '2025-05-19', '2025-06-18', '8/Piagam/KMHS/VI/2025', 'Pembicara'),
(9, 'Pemira', 6, 23, 'DEWA PUTU AGIE ADITYA', '2025-05-10', '2025-05-19', '2025-06-18', '9/Piagam/KMHS/VI/2025', 'Pembicara'),
(10, 'Pemira', 6, 23, 'I Nyoman Adi Pratama Putra, S.Kom', '2025-05-10', '2025-05-19', '2025-06-18', '10/Piagam/KMHS/VI/2025', 'Pembicara'),
(11, 'Pemira', 6, 23, 'Gusti Diptha Pranasta Wisma', '2025-05-10', '2025-05-19', '2025-06-18', '11/Piagam/KMHS/VI/2025', 'Pembicara'),
(12, 'Pemira', 6, 23, 'GDE SASTRAWANGSA, S.T., M.T.', '2025-05-10', '2025-05-19', '2025-06-18', '12/Piagam/KMHS/VI/2025', 'Pembicara'),
(13, 'Pemira', 6, 0, 'AKHMAD RIZKI PRAYOGA', '2025-05-10', '2025-06-12', '2025-06-18', '13/Piagam/KMHS/VI/2025', 'Pembicara'),
(14, 'Pemira', 6, 0, 'DEWA PUTU AGIE ADITYA', '2025-05-10', '2025-06-12', '2025-06-18', '14/Piagam/KMHS/VI/2025', 'Pembicara'),
(15, 'Pemira', 6, 0, 'I Nyoman Adi Pratama Putra, S.Kom', '2025-05-10', '2025-06-12', '2025-06-18', '15/Piagam/KMHS/VI/2025', 'Pembicara'),
(16, 'Pemira', 6, 0, 'Gusti Diptha Pranasta Wisma', '2025-05-10', '2025-06-12', '2025-06-18', '16/Piagam/KMHS/VI/2025', 'Pembicara'),
(17, 'Pemira', 6, 0, 'GDE SASTRAWANGSA, S.T., M.T.', '2025-05-10', '2025-06-12', '2025-06-18', '17/Piagam/KMHS/VI/2025', 'Pembicara');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_kegiatan`
--

CREATE TABLE `jenis_kegiatan` (
  `jenis_kegiatan` varchar(100) NOT NULL,
  `kategori_kegiatan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `jenis_kegiatan`
--

INSERT INTO `jenis_kegiatan` (`jenis_kegiatan`, `kategori_kegiatan`) VALUES
('Konfrensi - Penulis Utama', 'Bidang Akademik & Ilmiah'),
('Konfrensi - Penyaji', 'Bidang Akademik & Ilmiah'),
('Konfrensi - Moderator', 'Bidang Akademik & Ilmiah'),
('Penelitian/Karya Tulis - Perorangan', 'Bidang Akademik & Ilmiah'),
('Penelitian/Karya Tulis - Kelompok - Ketua', 'Bidang Akademik & Ilmiah'),
('Penelitian/Karya Tulis - Kelompok - Anggota', 'Bidang Akademik & Ilmiah'),
('Seminar/Workshop/Talk show - Peserta', 'Bidang Akademik & Ilmiah'),
('Kuliah Industri - Peserta', 'Bidang Akademik & Ilmiah'),
('Kepanitiaan - Ketua', 'Bidang Akademik & Ilmiah'),
('Kepanitiaan - Anggota', 'Bidang Akademik & Ilmiah'),
('Lomba - Juara 1', 'Bidang Minat Bakat Seni & Olahraga'),
('Lomba - Juara 2', 'Bidang Minat Bakat Seni & Olahraga'),
('Lomba - Juara 3', 'Bidang Minat Bakat Seni & Olahraga'),
('Lomba - Harapan/Favorit', 'Bidang Minat Bakat Seni & Olahraga'),
('Lomba - Peserta', 'Bidang Minat Bakat Seni & Olahraga'),
('Festival/Pentas Seni/Musik/Olahraga - Peserta', 'Bidang Minat Bakat Seni & Olahraga'),
('Festival/Pentas Seni/Musik/Olahraga - Juri', 'Bidang Minat Bakat Seni & Olahraga'),
('Festival/Pentas Seni/Musik/Olahraga - Pengisi Acara', 'Bidang Minat Bakat Seni & Olahraga'),
('Festival/Pentas Seni/Musik/Olahraga - Partisipasi', 'Bidang Minat Bakat Seni & Olahraga'),
('Kepanitiaan - Ketua', 'Bidang Minat Bakat Seni & Olahraga'),
('Kepanitiaan - Anggota', 'Bidang Minat Bakat Seni & Olahraga'),
('BEM/BALMA - Ketua', 'Bidang Organisasi & Sosial'),
('BEM/BALMA - Wakil', 'Bidang Organisasi & Sosial'),
('BEM/BALMA - Sekretaris', 'Bidang Organisasi & Sosial'),
('BEM/BALMA - Bendahara', 'Bidang Organisasi & Sosial'),
('BEM/BALMA - Koordinator', 'Bidang Organisasi & Sosial'),
('BEM/BALMA - Anggota', 'Bidang Organisasi & Sosial'),
('HIMAPRODI/HIMAS/PKM/UKM - Ketua', 'Bidang Organisasi & Sosial'),
('HIMAPRODI/HIMAS/PKM/UKM - Wakil', 'Bidang Organisasi & Sosial'),
('HIMAPRODI/HIMAS/PKM/UKM - Sekretaris', 'Bidang Organisasi & Sosial'),
('Bendahara', 'Bidang Organisasi & Sosial'),
('Koordinator', 'Bidang Organisasi & Sosial'),
('Anggota', 'Bidang Organisasi & Sosial'),
('PM/BAKSOS - Peserta', 'Bidang Organisasi & Sosial'),
('Kepanitiaan - Ketua', 'Bidang Organisasi & Sosial'),
('Kepanitiaan - Anggota', 'Bidang Organisasi & Sosial'),
('Peserta - G.M.T.I', 'Kegiatan Wajib'),
('Peserta - Jalan Santai', 'Kegiatan Wajib'),
('Peserta - Pentas Musik', 'Kegiatan Wajib');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_skkm`
--

CREATE TABLE `pengajuan_skkm` (
  `id_pengajuan` int(10) NOT NULL,
  `nim` int(20) NOT NULL,
  `id_kemahasiswaan` int(10) NOT NULL,
  `id_bem` int(10) NOT NULL,
  `file_bukti` varchar(100) NOT NULL,
  `bukti_keikutsertaan` varchar(255) DEFAULT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `kategori_kegiatan` varchar(100) NOT NULL,
  `tingkat` varchar(100) NOT NULL,
  `partisipasi` varchar(100) NOT NULL,
  `poin_skkm` int(8) NOT NULL,
  `status_verifikasi_bem` enum('Pending','Valid','Invalid','') NOT NULL,
  `tanggal_verifikasi_bem` date NOT NULL,
  `catatan_bem` varchar(100) NOT NULL,
  `status_verifikasi_kemahasiswaan` enum('Pending','Valid','Invalid','') NOT NULL,
  `tanggal_verifikasi_kemahasiswaan` date NOT NULL,
  `catatan_kemahasiswaan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `pengajuan_skkm`
--

INSERT INTO `pengajuan_skkm` (`id_pengajuan`, `nim`, `id_kemahasiswaan`, `id_bem`, `file_bukti`, `bukti_keikutsertaan`, `nama_kegiatan`, `kategori_kegiatan`, `tingkat`, `partisipasi`, `poin_skkm`, `status_verifikasi_bem`, `tanggal_verifikasi_bem`, `catatan_bem`, `status_verifikasi_kemahasiswaan`, `tanggal_verifikasi_kemahasiswaan`, `catatan_kemahasiswaan`) VALUES
(4, 210030012, 23, 21, 'bukti_67ee4b73ee699.jpg', NULL, 'Seminar Nasional 2024', 'Bidang Organisasi & Sosial', 'PT', 'Peserta', 3, 'Valid', '2025-04-03', 'Sesuai', 'Valid', '2025-04-29', 'sesuai'),
(8, 190040014, 23, 29, 'bukti_67f887803f3db.png', NULL, 'Fastekkno 2024', 'Bidang Minat Bakat Seni & Olahraga', 'Perguruan Tinggi', 'Anggota', 2, 'Valid', '2025-04-11', 'Sesuai', 'Valid', '2025-04-11', 'sesuai'),
(9, 210030012, 23, 29, 'bukti_6839697a39f08.png', NULL, 'Fastekkno 2024', 'Bidang Akademik & Ilmiah', 'Perguruan Tinggi', 'Panitia', 3, 'Valid', '2025-06-12', 'Sesuai', 'Valid', '2025-06-12', 'sesuai'),
(10, 190040014, 23, 29, 'bukti_68466ff891891.png', NULL, 'Fastekkno 2024', 'Bidang Akademik & Ilmiah', 'Perguruan Tinggi', 'Panitia', 3, 'Valid', '2025-06-09', 'Sesuai', 'Valid', '2025-06-09', 'sesuai'),
(11, 190040014, 0, 29, 'bukti_684a3c647e0f9.png', NULL, 'Donor darah 2024', 'Bidang Akademik & Ilmiah', 'P.T.', 'Kepanitiaan - Ketua', 4, 'Pending', '2025-06-16', 'Sesuai', 'Pending', '0000-00-00', ''),
(13, 210030012, 23, 29, 'bukti_684ebb65c99c2.jpg', 'keikutsertaan_684ec25cde701.pdf', 'Seminar Nasional 2024', 'Bidang Akademik & Ilmiah', 'Nas.', 'Seminar/Workshop/Talk show - Peserta', 3, 'Valid', '2025-06-15', 'Sesuai', 'Pending', '2025-06-17', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tabel_ketentuan_skkm`
--

CREATE TABLE `tabel_ketentuan_skkm` (
  `kategori_kegiatan` varchar(100) NOT NULL,
  `minimal_poin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tabel_ketentuan_skkm`
--

INSERT INTO `tabel_ketentuan_skkm` (`kategori_kegiatan`, `minimal_poin`) VALUES
('Bidang Akademik & Ilmiah', 16),
('Bidang Minat Bakat Seni & Olahraga', 12),
('Bidang Organisasi & Sosial', 12),
('Kegiatan Wajib', 12);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tingkat`
--

CREATE TABLE `tingkat` (
  `nama_tingkat` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tingkat`
--

INSERT INTO `tingkat` (`nama_tingkat`) VALUES
('P.T.'),
('Reg.'),
('Nas.'),
('Inter.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(10) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(15) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` enum('Mahasiswa','BEM','Kemahasiswaan','Administrator','Ormawa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `username`, `password`, `role`) VALUES
(19, 'Agung Tisna Dwi Putra', 'gusdwikun34@yahoo.com', 'Agung', '1234', 'Mahasiswa'),
(20, 'Agung Tisna Dwi', '210030012@stikom-bali.ac.id', 'Tisna', '1235', 'Mahasiswa'),
(23, 'Ida Ayu Mirah Cahya Dewi,S.Kom.,M.Kom', 'dayu123123@gmail.com', 'dayu', '697846734', 'Kemahasiswaan'),
(24, 'Arsa', 'arsa@gmail.com', 'arsa', '42690285', 'Kemahasiswaan'),
(25, 'administrator', 'Admin@gmail.com', 'admin', '12345', 'Administrator'),
(26, 'PUTU WAHYU ARYSTA PUTRA', 'Putuwahyu@gmail.com', 'putu', '123456', 'Mahasiswa'),
(27, 'ANAK AGUNG MADE AGUS TISNA DWI PUTRA', 'Anak@gmail.com', 'agus', '123', 'Mahasiswa'),
(28, 'I Gede Sandi Mahendra', 'Sandi@gmail.com', 'Sandi', '123', 'Mahasiswa'),
(29, 'Adam', 'adam123@gmail.com', 'adam', '1234', 'BEM'),
(32, 'I Putu Kevin Theo Surya Pratama', 'Kevin123@gmail.com', 'theo', '123', 'Mahasiswa'),
(33, 'Ni Luh Divyani Krisnadewi', 'Divyani@gmail.com', 'divy', '123', 'Mahasiswa'),
(34, 'Pande Komang Ayu Yunia putri', 'Ayu@gmail.com', 'yunia', '123', 'Mahasiswa'),
(35, 'Maudy Vania Anisya', 'Anisya@gmail.com', 'anisya', '123', 'Mahasiswa'),
(36, 'Alce Theresia Ndok', 'Ndok@gmail.com', 'theresia', '123', 'Mahasiswa'),
(37, 'Gung Krisnawan', 'progress@gmail.com', 'progress', '123', 'Ormawa'),
(38, 'Arya Wira', 'KMHD@gmail.com', 'kmhd', '123', 'Ormawa'),
(39, 'Pram', 'jcos@gmail.com', 'jcos', '123', 'Ormawa'),
(40, 'Arya dwipayasa', 'DPM@gmail.com', 'dpm', '123', 'Ormawa'),
(41, 'Candra', 'dos@gmail.com', 'dos', '123', 'Ormawa'),
(43, 'Adam', 'bem@gmail.com', 'bem', '12345', 'Ormawa'),
(44, 'Nama Ketua SI', 'si@gmail.com', 'himasi', '123', 'Ormawa'),
(45, 'Ketua Hima TI', 'ti@gmail.com', 'himati', '123', 'Ormawa'),
(46, 'Nama Ketua SK', 'sk@gmail.com', 'himask', '123', 'Ormawa'),
(47, 'Nama Ketua HIMAS Jimbaran', 'himasjimbaran@gmail.com', 'himasjim', '123', 'Ormawa'),
(48, 'Ketua Teater Biner', 'biner@gmail.com', 'biner', '123', 'Ormawa'),
(49, 'Nama Ketua GHoST', 'ghost@gmail.com', 'ghost', '123', 'Ormawa'),
(50, 'ketua MCOS', 'mcos@gmail.com', 'mcos', '123', 'Ormawa'),
(51, 'Nama Ketua PMK', 'pmk@gmail.com', 'pmk', '123', 'Ormawa'),
(52, 'Nama Ketua HIMATOGRAPHY ', 'himato@gmail.com', 'himato', '123', 'Ormawa'),
(53, 'Ketua Justify', 'justify@gmail.com', 'justify', '123', 'Ormawa'),
(54, 'Ketua U2M', 'u2m@gmail.com', 'u2m', '123', 'Ormawa'),
(55, 'Ketua BOSS', 'boss@gmail.com', 'boss', '123', 'Ormawa'),
(56, 'ketua musik', 'musik@gmail.com', 'musik', '123', 'Ormawa'),
(57, 'ketua Basket', 'basket@gmail.com', 'basket', '123', 'Ormawa'),
(58, 'Ketua Futsal', 'futsal@gmail.com', 'futsal', '123', 'Ormawa'),
(59, 'ketua KSI', 'ksr@gmail.com', 'ksr', '123', 'Ormawa'),
(60, 'ketua kompas', 'kompas@gmail.com', 'kompas', '123', 'Ormawa'),
(61, 'ketua mutimedia', 'multimedia@gmail.com', 'multimedia', '123', 'Ormawa'),
(62, 'Ketua UKM PASKAMRAS', 'paskamras@gmail.com', 'paskamras', '123', 'Ormawa'),
(63, 'ketua UKM Tabuh', 'tabuh@gmail.com', 'tabuh', '123', 'Ormawa'),
(64, 'ketua UKM Tari', 'tari@gmail.com', 'tari', '123', 'Ormawa'),
(65, 'ketua UKM VOS', 'vos@gmail.com', 'vos', '123', 'Ormawa'),
(66, 'ketua UKM KSL', 'ksl@gmail.com', 'ksl', '123', 'Ormawa'),
(67, 'ketua UKM RADE', 'rade@gmail.com', 'rade', '123', 'Ormawa'),
(68, 'ketua UKM Syntax', 'syntx@gmail.com', 'syntax', '123', 'Ormawa'),
(69, 'Rista', 'bd@gmail.com', 'himabd', '123', 'Ormawa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_detail_bem`
--

CREATE TABLE `user_detail_bem` (
  `id_bem` int(10) NOT NULL,
  `id_user` int(10) NOT NULL,
  `jabatan` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user_detail_bem`
--

INSERT INTO `user_detail_bem` (`id_bem`, `id_user`, `jabatan`) VALUES
(6, 29, 'Verifikator');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_detail_kemahasiswaan`
--

CREATE TABLE `user_detail_kemahasiswaan` (
  `id_kemahasiswaan` int(10) NOT NULL,
  `id_user` int(10) NOT NULL,
  `nip` varchar(50) NOT NULL,
  `jabatan` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user_detail_kemahasiswaan`
--

INSERT INTO `user_detail_kemahasiswaan` (`id_kemahasiswaan`, `id_user`, `nip`, `jabatan`) VALUES
(5, 23, '1234215235123asdas', 'Verifikator'),
(6, 24, '1097412463', 'Verifikator');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_detail_mahasiswa`
--

CREATE TABLE `user_detail_mahasiswa` (
  `nim` int(20) NOT NULL,
  `id_user` int(10) NOT NULL,
  `prodi` varchar(50) NOT NULL,
  `angkatan` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user_detail_mahasiswa`
--

INSERT INTO `user_detail_mahasiswa` (`nim`, `id_user`, `prodi`, `angkatan`) VALUES
(190040014, 32, 'Teknologi Informasi', 2019),
(190040028, 36, 'Teknologi Informasi', 2019),
(200040045, 33, 'Teknologi Informasi', 2020),
(200040060, 34, 'Teknologi Informasi', 2020),
(200040061, 35, 'Teknologi Informasi', 2020),
(210030012, 19, 'Sistem Informasi', 2021),
(210030015, 20, 'Sistem Informasi', 2021),
(220030012, 27, 'Sistem Informasi', 2021),
(220030017, 28, 'Sistem Informasi', 2022),
(240010138, 26, 'Sistem Komputer', 2024);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_detail_ormawa`
--

CREATE TABLE `user_detail_ormawa` (
  `id_ormawa` int(10) NOT NULL,
  `id_user` int(10) NOT NULL,
  `nama_ormawa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user_detail_ormawa`
--

INSERT INTO `user_detail_ormawa` (`id_ormawa`, `id_user`, `nama_ormawa`) VALUES
(3, 37, 'UKM Progress'),
(4, 38, 'UKM KMHD'),
(5, 39, 'UKM JCOS'),
(6, 40, 'DPM'),
(7, 41, 'UKM DOS'),
(8, 23, 'Kemahasiswaan'),
(9, 43, 'BEM'),
(10, 44, 'Hima Prodi SI'),
(11, 45, 'Hima Prodi TI'),
(12, 46, 'Hima Prodi SK'),
(13, 47, 'Himas Jimbaran'),
(14, 48, 'UKM Teater Biner'),
(15, 49, 'UKM GHoST'),
(16, 50, 'UKM MCOS'),
(17, 51, 'UKM PMK'),
(18, 52, 'UKM HIMATOGRAPHY '),
(19, 53, 'UKM JUSTIFY'),
(20, 54, 'UKM U2M'),
(21, 55, 'UKM BOSS'),
(22, 56, 'UKM MUSIK'),
(23, 57, 'UKM BASKET'),
(24, 58, 'UKM FUTSAL'),
(25, 59, 'UKM KSR PMI WB'),
(26, 60, 'UKM MAPALA KOMPAS'),
(27, 61, 'UKM Multimedia'),
(28, 62, 'UKM PASKAMRAS'),
(29, 63, 'UKM Tabuh'),
(30, 64, 'UKM Tari Tradisional'),
(31, 65, 'UKM VOS'),
(32, 66, 'UKM KSL'),
(33, 67, 'UKM RADE'),
(34, 68, 'UKM Syntax'),
(35, 69, 'Hima Prodi BD');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `arsip_eksternal`
--
ALTER TABLE `arsip_eksternal`
  ADD PRIMARY KEY (`id_arsip_eksternal`),
  ADD KEY `id_berkas_eksternal` (`id_berkas_eksternal`);

--
-- Indeks untuk tabel `arsip_piagam`
--
ALTER TABLE `arsip_piagam`
  ADD PRIMARY KEY (`id_arsip_piagam`),
  ADD KEY `id_berkas_piagam` (`id_berkas_piagam`);

--
-- Indeks untuk tabel `arsip_skkm`
--
ALTER TABLE `arsip_skkm`
  ADD PRIMARY KEY (`id_arsip`);

--
-- Indeks untuk tabel `berkas_eksternal`
--
ALTER TABLE `berkas_eksternal`
  ADD PRIMARY KEY (`id_berkas_eksternal`);

--
-- Indeks untuk tabel `berkas_internal`
--
ALTER TABLE `berkas_internal`
  ADD PRIMARY KEY (`id_berkas_internal`);

--
-- Indeks untuk tabel `berkas_piagam`
--
ALTER TABLE `berkas_piagam`
  ADD PRIMARY KEY (`id_berkas_piagam`);

--
-- Indeks untuk tabel `pengajuan_skkm`
--
ALTER TABLE `pengajuan_skkm`
  ADD PRIMARY KEY (`id_pengajuan`);

--
-- Indeks untuk tabel `tabel_ketentuan_skkm`
--
ALTER TABLE `tabel_ketentuan_skkm`
  ADD PRIMARY KEY (`kategori_kegiatan`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indeks untuk tabel `user_detail_bem`
--
ALTER TABLE `user_detail_bem`
  ADD PRIMARY KEY (`id_bem`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `user_detail_kemahasiswaan`
--
ALTER TABLE `user_detail_kemahasiswaan`
  ADD PRIMARY KEY (`id_kemahasiswaan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `user_detail_mahasiswa`
--
ALTER TABLE `user_detail_mahasiswa`
  ADD PRIMARY KEY (`nim`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `user_detail_ormawa`
--
ALTER TABLE `user_detail_ormawa`
  ADD PRIMARY KEY (`id_ormawa`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `arsip_eksternal`
--
ALTER TABLE `arsip_eksternal`
  MODIFY `id_arsip_eksternal` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT untuk tabel `arsip_piagam`
--
ALTER TABLE `arsip_piagam`
  MODIFY `id_arsip_piagam` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `arsip_skkm`
--
ALTER TABLE `arsip_skkm`
  MODIFY `id_arsip` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `berkas_eksternal`
--
ALTER TABLE `berkas_eksternal`
  MODIFY `id_berkas_eksternal` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `berkas_internal`
--
ALTER TABLE `berkas_internal`
  MODIFY `id_berkas_internal` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `berkas_piagam`
--
ALTER TABLE `berkas_piagam`
  MODIFY `id_berkas_piagam` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_skkm`
--
ALTER TABLE `pengajuan_skkm`
  MODIFY `id_pengajuan` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT untuk tabel `user_detail_bem`
--
ALTER TABLE `user_detail_bem`
  MODIFY `id_bem` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `user_detail_kemahasiswaan`
--
ALTER TABLE `user_detail_kemahasiswaan`
  MODIFY `id_kemahasiswaan` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `user_detail_ormawa`
--
ALTER TABLE `user_detail_ormawa`
  MODIFY `id_ormawa` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `user_detail_bem`
--
ALTER TABLE `user_detail_bem`
  ADD CONSTRAINT `user_detail_bem_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_detail_kemahasiswaan`
--
ALTER TABLE `user_detail_kemahasiswaan`
  ADD CONSTRAINT `user_detail_kemahasiswaan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_detail_mahasiswa`
--
ALTER TABLE `user_detail_mahasiswa`
  ADD CONSTRAINT `user_detail_mahasiswa_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
