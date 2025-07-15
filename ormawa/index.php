<?php
session_start();
include '../koneksi.php'; // Koneksi ke database

// 🔹 **Cek apakah user sudah login sebagai Ormawa**
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Ormawa') {
    header("Location: ../index.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// 🔹 **Ambil `id_ormawa` berdasarkan `id_user` yang login**
$query_ormawa = "SELECT id_ormawa, nama_ormawa FROM user_detail_ormawa WHERE id_user = ?";
$stmt = $conn->prepare($query_ormawa);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result_ormawa = $stmt->get_result();
$data_ormawa = $result_ormawa->fetch_assoc();

if (!$data_ormawa) {
    die("❌ Gagal menemukan data Ormawa untuk user yang login.");
}

$id_ormawa = $data_ormawa['id_ormawa'];
$nama_ormawa = $data_ormawa['nama_ormawa'];

// 🔹 **Ambil jumlah berkas internal berdasarkan `id_ormawa`**
$query_internal = "SELECT COUNT(*) AS total FROM berkas_internal WHERE id_ormawa = ?";
$stmt = $conn->prepare($query_internal);
$stmt->bind_param("i", $id_ormawa);
$stmt->execute();
$result_internal = $stmt->get_result();
$data_internal = $result_internal->fetch_assoc();
$total_internal = $data_internal['total'] ?? 0;

// 🔹 **Ambil jumlah berkas eksternal berdasarkan `id_ormawa`**
$query_eksternal = "SELECT COUNT(*) AS total FROM berkas_eksternal WHERE id_ormawa = ?";
$stmt = $conn->prepare($query_eksternal);
$stmt->bind_param("i", $id_ormawa);
$stmt->execute();
$result_eksternal = $stmt->get_result();
$data_eksternal = $result_eksternal->fetch_assoc();
$total_eksternal = $data_eksternal['total'] ?? 0;

// 🔹 **Ambil jumlah piagam berdasarkan `id_ormawa`**
$query_piagam = "SELECT COUNT(*) AS total FROM berkas_piagam WHERE id_ormawa = ?";
$stmt = $conn->prepare($query_piagam);
$stmt->bind_param("i", $id_ormawa);
$stmt->execute();
$result_piagam = $stmt->get_result();
$data_piagam = $result_piagam->fetch_assoc();
$total_piagam = $data_piagam['total'] ?? 0;

// 🔹 Ambil jumlah arsip internal dari tabel arsip_skkm
$query_arsip_internal = "SELECT COUNT(*) AS total FROM arsip_skkm WHERE id_ormawa = ?";
$stmt = $conn->prepare($query_arsip_internal);
$stmt->bind_param("i", $id_ormawa);
$stmt->execute();
$result_arsip_internal = $stmt->get_result();
$data_arsip_internal = $result_arsip_internal->fetch_assoc();
$total_arsip_internal = $data_arsip_internal['total'] ?? 0;

// 🔹 Ambil jumlah arsip eksternal dari tabel arsip_eksternal
$query_arsip_eksternal = "SELECT COUNT(*) AS total FROM arsip_eksternal WHERE id_ormawa = ?";
$stmt = $conn->prepare($query_arsip_eksternal);
$stmt->bind_param("i", $id_ormawa);
$stmt->execute();
$result_arsip_eksternal = $stmt->get_result();
$data_arsip_eksternal = $result_arsip_eksternal->fetch_assoc();
$total_arsip_eksternal = $data_arsip_eksternal['total'] ?? 0;

// 🔹 Ambil jumlah arsip piagam dari tabel arsip_piagam
$query_arsip_piagam = "SELECT COUNT(*) AS total FROM arsip_piagam WHERE id_ormawa = ?";
$stmt = $conn->prepare($query_arsip_piagam);
$stmt->bind_param("i", $id_ormawa);
$stmt->execute();
$result_arsip_piagam = $stmt->get_result();
$data_arsip_piagam = $result_arsip_piagam->fetch_assoc();
$total_arsip_piagam = $data_arsip_piagam['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Ormawa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .card { transition: 0.3s; }
        .card:hover { transform: scale(1.05); }
        .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        border: 1px solid black;
        background-color: white;
    }

    .navbar-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .logo {
        width: 60px;
        height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 10px;
        text-align: center;
    }
    .logo img{
        width: 50px;
    }

    .navbar-title {
        font-size: 18px;
        font-weight: bold;
    }

    .profile-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: black;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .profile-icon img {
        width: 35px;
        height: 35px;
    }

    .profile-icon::before {
        font-size: 18px;
        color: white;
    }
    .dropdown {
        position: absolute;
        top: 45px;
        right: 0;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 200px;
        display: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        z-index: 10;
    }

    .dropdown a, .dropdown div {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: black;
        border-bottom: 1px solid #eee;
    }

    .dropdown a:hover {
        background-color: #f0f0f0;
    }

    .dropdown div:last-child {
        border-bottom: none;
    }
    </style>
</head>
<body>
<div class="navbar">
    <div class="navbar-left">
        <div class="logo">
            <a href="index.php"><img src="../asset/img/itb.png"></a>
        </div>
        <div class="navbar-title">E-SKKM</div>
    </div>
    <div class="profile-container" onclick="toggleDropdown()">
        <div class="profile-icon"><img src="../asset/img/profile.png"></div>
        <div id="profileDropdown" class="dropdown">
            <div><strong><?= htmlspecialchars($nama_ormawa) ?></strong></div>
            <a href="/e-skkm/ormawa/profile/profile_ormawa.php">Profil</a>
            <a href="/e-skkm/logout.php">Logout</a>
        </div>
    </div>
</div>

<div class="container mt-5">
    <h3 class="text-center mb-4">Dashboard Ormawa</h3>
    <h3 class="text-center mb-4">Selamat Datang, <strong><?= htmlspecialchars($nama_ormawa); ?></strong></h3>

    <div class="row">
        <!-- Kartu 1: Berkas Internal -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Berkas Internal</h5>
                    <p class="card-text">Total Pengajuan: <strong><?= $total_internal; ?></strong></p>
                    <a href="berkas_internal/detail_berkas_internal.php" class="btn btn-light">Lihat Detail</a>
                </div>
            </div>
        </div>

        <!-- Kartu 2: Berkas Eksternal -->
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Berkas Eksternal</h5>
                    <p class="card-text">Total Pengajuan: <strong><?= $total_eksternal; ?></strong></p>
                    <a href="berkas_eksternal/detail_berkas_eksternal.php" class="btn btn-light">Lihat Detail</a>
                </div>
            </div>
        </div>

        <!-- Kartu 3: Piagam -->
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Piagam</h5>
                    <p class="card-text">Total Pengajuan: <strong><?= $total_piagam; ?></strong></p>
                    <a href="berkas_piagam/detail_berkas_piagam.php" class="btn btn-light">Lihat Detail</a>
                </div>
            </div>
        </div>
        <!-- Kartu 4: Arsip Internal -->
        <div class="col-md-4">
            <div class="card text-bg-secondary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Arsip Internal</h5>
                    <p class="card-text">Total Arsip: <strong><?= $total_arsip_internal; ?></strong></p>
                    <a href="arsip/arsip_berkas_internal.php" class="btn btn-light">Lihat Arsip</a>
                </div>
            </div>
        </div>

        <!-- Kartu 5: Arsip Eksternal -->
        <div class="col-md-4">
            <div class="card text-bg-dark mb-3">
                <div class="card-body">
                    <h5 class="card-title">Arsip Eksternal</h5>
                    <p class="card-text">Total Arsip: <strong><?= $total_arsip_eksternal; ?></strong></p>
                    <a href="arsip/arsip_berkas_eksternal.php" class="btn btn-light">Lihat Arsip</a>
                </div>
            </div>
        </div>

        <!-- Kartu 6: Arsip Piagam -->
        <div class="col-md-4">
            <div class="card text-bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Arsip Piagam</h5>
                    <p class="card-text">Total Arsip: <strong><?= $total_arsip_piagam; ?></strong></p>
                    <a href="arsip/arsip_berkas_piagam.php" class="btn btn-light">Lihat Arsip</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById("profileDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    // Tutup dropdown jika klik di luar
    document.addEventListener("click", function(event) {
        const profileContainer = document.querySelector(".profile-container");
        const dropdown = document.getElementById("profileDropdown");
        if (!profileContainer.contains(event.target)) {
            dropdown.style.display = "none";
        }
    });
</script>
</body>
</html>
