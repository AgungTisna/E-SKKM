-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 16 Apr 2025 pada 04.22
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
-- Struktur dari tabel `berkas_bem`
--

CREATE TABLE `berkas_bem` (
  `id_berkas_bem` int(10) NOT NULL,
  `id_bem` int(10) NOT NULL,
  `nim` int(20) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `partisipasi` varchar(100) NOT NULL,
  `tingkat` varchar(100) NOT NULL,
  `kategori_kegiatan` varchar(100) NOT NULL,
  `poin_skkm` int(10) NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `id_kemahasiswaan` int(10) NOT NULL,
  `nomor_sertifikat_internal` varchar(100) NOT NULL,
  `tanggal_dikeluarkan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `berkas_bem`
--

INSERT INTO `berkas_bem` (`id_berkas_bem`, `id_bem`, `nim`, `nama_kegiatan`, `partisipasi`, `tingkat`, `kategori_kegiatan`, `poin_skkm`, `tanggal_kegiatan`, `tanggal_pengajuan`, `id_kemahasiswaan`, `nomor_sertifikat_internal`, `tanggal_dikeluarkan`) VALUES
(1, 29, 190040014, 'GEMA MAHASISWA TEKNOLOGI INFORMASI (G.M.T.I) XXI', 'Peserta', 'Perguruan Tinggi', 'Kegiatan Wajib', 2, '2023-12-01', '2025-04-04', 23, '056/Srtf/KMHS/I/2023', '2025-04-05'),
(2, 29, 200040045, 'GEMA MAHASISWA TEKNOLOGI INFORMASI (G.M.T.I) XXI', 'Peserta', 'Perguruan Tinggi', 'Kegiatan Wajib', 2, '2023-12-01', '2025-04-04', 23, '057/Srtf/KMHS/I/2023', '2025-04-05'),
(3, 29, 200040060, 'GEMA MAHASISWA TEKNOLOGI INFORMASI (G.M.T.I) XXI', 'Peserta', 'Perguruan Tinggi', 'Kegiatan Wajib', 2, '2023-12-01', '2025-04-04', 23, '058/Srtf/KMHS/I/2023', '2025-04-05'),
(4, 29, 200040061, 'GEMA MAHASISWA TEKNOLOGI INFORMASI (G.M.T.I) XXI', 'Peserta', 'Perguruan Tinggi', 'Kegiatan Wajib', 2, '2023-12-01', '2025-04-04', 23, '059/Srtf/KMHS/I/2023', '2025-04-05'),
(5, 29, 190040028, 'GEMA MAHASISWA TEKNOLOGI INFORMASI (G.M.T.I) XXI', 'Peserta', 'Perguruan Tinggi', 'Kegiatan Wajib', 2, '2023-12-01', '2025-04-04', 23, '060/Srtf/KMHS/I/2023', '2025-04-05'),
(6, 29, 190040014, 'GEMA MAHASISWA TEKNOLOGI INFORMASI (G.M.T.I) XXII', 'Peserta123123123', 'Perguruan Tinggi', 'Kegiatan Wajib', 2, '2023-12-01', '2025-04-04', 23, '061/Srtf/KMHS/I/2023', '2025-04-05'),
(7, 29, 200040045, 'GEMA MAHASISWA TEKNOLOGI INFORMASI (G.M.T.I) XXII', 'Peserta', 'Perguruan Tinggi', 'Kegiatan Wajib', 2, '2023-12-01', '2025-04-04', 23, '062/Srtf/KMHS/I/2023', '2025-04-05'),
(8, 29, 200040060, 'GEMA MAHASISWA TEKNOLOGI INFORMASI (G.M.T.I) XXII', 'Peserta', 'Perguruan Tinggi', 'Kegiatan Wajib', 2, '2023-12-01', '2025-04-04', 23, '063/Srtf/KMHS/I/2023', '2025-04-05'),
(9, 29, 200040061, 'GEMA MAHASISWA TEKNOLOGI INFORMASI (G.M.T.I) XXII', 'Peserta', 'Perguruan Tinggi', 'Kegiatan Wajib', 2, '2023-12-01', '2025-04-04', 23, '064/Srtf/KMHS/I/2023', '2025-04-05'),
(10, 29, 190040028, 'GEMA MAHASISWA TEKNOLOGI INFORMASI (G.M.T.I) XXII', 'Peserta', 'Perguruan Tinggi', 'Kegiatan Wajib', 2, '2023-12-01', '2025-04-04', 23, '065/Srtf/KMHS/I/2023', '2025-04-05');

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
(86, 4, 'Lomba Bali Simbar', 23, 'PUTU LIANA SARASWATI DEWI', '2025-03-31', '85/Srtf.eks/KMHS/I/2023', '2025-03-31', '2025-04-01', 'Peserta Eksternal'),
(87, 4, 'Lomba Bali Simbar', 23, 'I GEDE SURYA DIVA ANANDA', '2025-03-31', '86/Srtf.eks/KMHS/I/2023', '2025-03-31', '2025-04-01', 'Peserta Eksternal'),
(88, 4, 'Lomba Bali Simbar', 23, 'SANG AYU PUTU JUWITA MAHARANI', '2025-03-31', '87/Srtf.eks/KMHS/I/2023', '2025-03-31', '2025-04-01', 'Peserta Eksternal'),
(89, 4, 'Lomba Bali Simbar', 23, 'I GUSTI AYU RANI ANJALI WIHARTATI', '2025-03-31', '88/Srtf.eks/KMHS/I/2023', '2025-03-31', '2025-04-01', 'Peserta Eksternal'),
(90, 4, 'Lomba Bali Simbar', 23, 'I WAYAN JATI WIDYANTARA', '2025-03-31', '89/Srtf.eks/KMHS/I/2023', '2025-03-31', '2025-04-01', 'Peserta Eksternal');

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
  `tingkat` varchar(100) NOT NULL,
  `poin_skkm` int(10) NOT NULL,
  `id_kemahasiswaan` int(10) NOT NULL,
  `nomor_sertifikat_internal` varchar(100) NOT NULL,
  `tanggal_dikeluarkan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `berkas_internal`
--

INSERT INTO `berkas_internal` (`id_berkas_internal`, `id_ormawa`, `nim`, `nama_kegiatan`, `partisipasi`, `tanggal_kegiatan`, `tanggal_pengajuan`, `id_bem`, `kategori_kegiatan`, `tingkat`, `poin_skkm`, `id_kemahasiswaan`, `nomor_sertifikat_internal`, `tanggal_dikeluarkan`) VALUES
(27, 3, 200040045, 'TIRTA YATRA DAN MELUKAT UKM KMHD', 'Peserta', '2025-03-30', '2025-03-30', 29, 'Bidang Organisasi & Sosial', 'Nasional', 4, 23, '50199/Srtf/KMHS/XII/2023', '2025-03-31'),
(28, 3, 200040060, 'TIRTA YATRA DAN MELUKAT UKM KMHD', 'Peserta', '2025-03-30', '2025-03-30', 29, 'Bidang Organisasi & Sosial', 'Nasional', 4, 23, '50200/Srtf/KMHS/XII/2023', '2025-03-31'),
(29, 3, 200040061, 'TIRTA YATRA DAN MELUKAT UKM KMHD', 'Peserta', '2025-03-30', '2025-03-30', 29, 'Bidang Organisasi & Sosial', 'Nasional', 4, 23, '50201/Srtf/KMHS/XII/2023', '2025-03-31'),
(30, 3, 190040028, 'TIRTA YATRA DAN MELUKAT UKM KMHD', 'Peserta', '2025-03-30', '2025-03-30', 29, 'Bidang Organisasi & Sosial', 'Nasional', 4, 23, '50202/Srtf/KMHS/XII/2023', '2025-03-31'),
(36, 6, 190040014, 'Audit Ormawa', 'Peserta Pelatihan Auditor', '2024-06-19', '2025-04-02', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '1449/Srtf/KMHS/I/2024', '2025-04-03'),
(37, 6, 200040045, 'Audit Ormawa', 'Peserta Pelatihan Auditor', '2024-06-19', '2025-04-02', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '1450/Srtf/KMHS/I/2024', '2025-04-03'),
(38, 6, 200040060, 'Audit Ormawa', 'Peserta Pelatihan Auditor', '2024-06-19', '2025-04-02', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '1451/Srtf/KMHS/I/2024', '2025-04-03'),
(39, 6, 200040061, 'Audit Ormawa', 'Peserta Pelatihan Auditor', '2024-06-19', '2025-04-02', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '1452/Srtf/KMHS/I/2024', '2025-04-03'),
(40, 6, 200040061, 'Audit Ormawa', 'Peserta Pelatihan Auditor', '2024-06-19', '2025-04-02', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '1453/Srtf/KMHS/I/2024', '2025-04-03'),
(41, 4, 190040014, 'TIRTA YATRA DAN MELUKAT UKM KMHD 2025', 'Peserta', '2023-11-05', '2025-04-03', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '50193/Srtf/KMHS/XII/2023', '2025-04-04'),
(42, 4, 200040045, 'TIRTA YATRA DAN MELUKAT UKM KMHD 2025', 'Peserta', '2023-11-05', '2025-04-03', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '50194/Srtf/KMHS/XII/2023', '2025-04-04'),
(43, 4, 200040060, 'TIRTA YATRA DAN MELUKAT UKM KMHD 2025', 'Peserta', '2023-11-05', '2025-04-03', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '50195/Srtf/KMHS/XII/2023', '2025-04-04'),
(44, 4, 200040061, 'TIRTA YATRA DAN MELUKAT UKM KMHD 2025', 'Peserta', '2023-11-05', '2025-04-03', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '1453/Srtf/KMHS/I/2024', '2025-04-04'),
(45, 4, 190040028, 'TIRTA YATRA DAN MELUKAT UKM KMHD 2025', 'Peserta', '2023-11-05', '2025-04-03', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '50197/Srtf/KMHS/XII/2023', '2025-04-04'),
(51, 7, 200040045, 'CHARITY NIGHT', 'Panitia', '2023-01-17', '2025-04-07', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '002/Srtf/KMHS/I/2023', '2025-04-08'),
(52, 7, 200040060, 'CHARITY NIGHT', 'Panitia', '2023-01-17', '2025-04-07', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '001/Srtf/KMHS/I/2023', '2025-04-08'),
(53, 7, 200040061, 'CHARITY NIGHT', 'Panitia', '2023-01-17', '2025-04-07', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '005/Srtf/KMHS/I/2023', '2025-04-08'),
(55, 7, 190040014, 'CHARITY NIGHT', 'Panitia', '2023-01-17', '2025-04-07', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '003/Srtf/KMHS/I/2023', '2025-04-08'),
(56, 7, 200040045, 'CHARITY NIGHT 2025', 'Panitia', '2023-01-17', '2025-04-11', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '133/Srtf/KMHS/I/2023', '2025-04-12'),
(57, 7, 200040060, 'CHARITY NIGHT 2025', 'Panitia', '2023-01-17', '2025-04-11', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '132/Srtf/KMHS/I/2023', '2025-04-12'),
(58, 7, 200040061, 'CHARITY NIGHT 2025', 'Panitia', '2023-01-17', '2025-04-11', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '136/Srtf/KMHS/I/2023', '2025-04-12'),
(59, 7, 190040028, 'CHARITY NIGHT 2025', 'Panitia', '2023-01-17', '2025-04-11', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '135/Srtf/KMHS/I/2023', '2025-04-12'),
(60, 7, 190040014, 'CHARITY NIGHT 2025', 'Panitia', '2023-01-17', '2025-04-11', 29, 'Bidang Organisasi & Sosial', 'Perguruan Tinggi', 2, 23, '134/Srtf/KMHS/I/2023', '2025-04-12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `berkas_kemahasiswaan`
--

CREATE TABLE `berkas_kemahasiswaan` (
  `id_berkas_kemahasiswaan` int(10) NOT NULL,
  `id_kemahasiswaan` int(10) NOT NULL,
  `nim` int(20) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `partisipasi` varchar(100) NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `kategori_kegiatan` varchar(100) NOT NULL,
  `tingkat` varchar(100) NOT NULL,
  `poin_skkm` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `berkas_kemahasiswaan`
--

INSERT INTO `berkas_kemahasiswaan` (`id_berkas_kemahasiswaan`, `id_kemahasiswaan`, `nim`, `nama_kegiatan`, `partisipasi`, `tanggal_kegiatan`, `kategori_kegiatan`, `tingkat`, `poin_skkm`) VALUES
(2, 23, 190040014, 'Kuliah Insdustri 2024', 'Peserta', '2024-06-19', 'Kegiatan Wajib', 'Perguruan Tinggi', 2),
(3, 23, 200040045, 'Kuliah Insdustri 2024', 'Peserta', '2024-06-19', 'Kegiatan Wajib', 'Perguruan Tinggi', 2),
(4, 23, 200040060, 'Kuliah Insdustri 2024', 'Peserta', '2024-06-19', 'Kegiatan Wajib', 'Perguruan Tinggi', 2),
(5, 23, 200040061, 'Kuliah Insdustri 2024', 'Peserta', '2024-06-19', 'Kegiatan Wajib', 'Perguruan Tinggi', 2),
(6, 23, 190040028, 'Kuliah Insdustri 2024', 'Peserta', '2024-06-19', 'Kegiatan Wajib', 'Perguruan Tinggi', 2),
(7, 23, 190040014, 'Kuliah Insdustri 2024', 'Peserta', '2025-04-01', 'Kegiatan Wajib', 'Perguruan Tinggi', 2);

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
  `keterangan` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `berkas_piagam`
--

INSERT INTO `berkas_piagam` (`id_berkas_piagam`, `nama_kegiatan`, `id_ormawa`, `id_kemahasiswaan`, `nama_penerima`, `tanggal_kegiatan`, `tanggal_pengajuan`, `tanggal_dikeluarkan`, `nomor_sertifikat_piagam`, `keterangan`) VALUES
(18, 'Seminar nasional ukm kmhd', 4, 23, 'Prof. Dr. I Made Bandem, M.A.', '2023-04-28', '2025-03-31', '2025-04-02', '123/Piagam/KMHS/IV/2023', 'pembicara'),
(19, 'Seminar nasional ukm kmhd', 4, 23, 'Ni Putu Eka Laksmi Dewi', '2023-04-28', '2025-03-31', '2025-04-02', '124/Piagam/KMHS/IV/2023', 'pembicara'),
(20, 'Seminar nasional ukm kmhd', 4, 23, 'I Wayan Gede Narayana, S.Kom., M.Kom', '2023-04-28', '2025-03-31', '2025-04-02', '125/Piagam/KMHS/IV/2023', 'moderator');

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

INSERT INTO `pengajuan_skkm` (`id_pengajuan`, `nim`, `id_kemahasiswaan`, `id_bem`, `file_bukti`, `nama_kegiatan`, `kategori_kegiatan`, `tingkat`, `partisipasi`, `poin_skkm`, `status_verifikasi_bem`, `tanggal_verifikasi_bem`, `catatan_bem`, `status_verifikasi_kemahasiswaan`, `tanggal_verifikasi_kemahasiswaan`, `catatan_kemahasiswaan`) VALUES
(4, 210030012, 23, 21, 'bukti_67ee4b73ee699.jpg', 'Seminar Nasional 2024', 'Bidang Organisasi & Sosial', 'PT', 'Peserta', 3, 'Valid', '2025-04-03', 'Sesuai', 'Valid', '2025-04-03', 'sesuai'),
(5, 190040014, 23, 29, 'bukti_67ee50c8c590d.jpg', 'Donor darah 2024', 'Bidang Organisasi & Sosial', 'PT1', 'Peserta', 2, 'Valid', '2025-04-03', 'Sesuai', 'Valid', '2025-04-03', 'sesuai'),
(7, 190040014, 0, 0, 'bukti_67ee8970e467a.jpg', 'Donor darah 2024', '', '', '', 0, 'Pending', '0000-00-00', '', 'Pending', '0000-00-00', ''),
(8, 190040014, 23, 29, 'bukti_67f887803f3db.png', 'Fastekkno 2024', 'Bidang Minat Bakat Seni & Olahraga', 'Perguruan Tinggi', 'Anggota', 2, 'Valid', '2025-04-11', 'Sesuai', 'Valid', '2025-04-11', 'sesuai');

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
('Bidang Akademik & Ilmiah', 20),
('Bidang Minat Bakat Seni & Olahraga', 20),
('Bidang Organisasi & Sosial', 20),
('Kegiatan Wajib', 12);

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
(21, 'Ridho1234', 'Ridho1234@gmail.com', 'Ridho', '09123', 'BEM'),
(23, 'Dayu123', 'dayu123123@gmail.com', 'dayu', '697846734', 'Kemahasiswaan'),
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
(41, 'Candra', 'dos@gmail.com', 'dos', '123', 'Ormawa');

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
(4, 21, 'Verifikator'),
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
(190040014, 32, 'Teknoogi Informasi', 2019),
(190040028, 36, 'Teknoogi Informasi', 2019),
(200040045, 33, 'Teknoogi Informasi', 2020),
(200040060, 34, 'Teknoogi Informasi', 2020),
(200040061, 35, 'Teknoogi Informasi', 2020),
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
(7, 41, 'UKM DOS');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `berkas_bem`
--
ALTER TABLE `berkas_bem`
  ADD PRIMARY KEY (`id_berkas_bem`);

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
-- Indeks untuk tabel `berkas_kemahasiswaan`
--
ALTER TABLE `berkas_kemahasiswaan`
  ADD PRIMARY KEY (`id_berkas_kemahasiswaan`);

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
-- AUTO_INCREMENT untuk tabel `berkas_bem`
--
ALTER TABLE `berkas_bem`
  MODIFY `id_berkas_bem` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `berkas_eksternal`
--
ALTER TABLE `berkas_eksternal`
  MODIFY `id_berkas_eksternal` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT untuk tabel `berkas_internal`
--
ALTER TABLE `berkas_internal`
  MODIFY `id_berkas_internal` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT untuk tabel `berkas_kemahasiswaan`
--
ALTER TABLE `berkas_kemahasiswaan`
  MODIFY `id_berkas_kemahasiswaan` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `berkas_piagam`
--
ALTER TABLE `berkas_piagam`
  MODIFY `id_berkas_piagam` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_skkm`
--
ALTER TABLE `pengajuan_skkm`
  MODIFY `id_pengajuan` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

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
  MODIFY `id_ormawa` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
