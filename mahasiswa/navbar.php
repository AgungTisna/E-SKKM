<?php
require_once('../../koneksi.php');

// Validasi login dan role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Mahasiswa') {
    header("Location: ../index.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$nim     = $_SESSION['nim'];
$role    = $_SESSION['role'];

// Ambil data nama dan nim dari user_detail_mahasiswa
$query = "
    SELECT u.nama
    FROM user u
    JOIN user_detail_mahasiswa udm ON u.id_user = udm.id_user
    WHERE u.id_user = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>alert('Data pengguna tidak ditemukan.'); window.location.href='../index.php';</script>";
    exit;
}
?>

<style>
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        border: 1px solid black;
        background-color: white;
        position: relative;
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

    .logo img {
        width: 50px;
    }

    .navbar-title {
        font-size: 18px;
        font-weight: bold;
    }

    .profile-container {
        position: relative;
    }

    .profile-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: black;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .profile-icon img {
        width: 35px;
        height: 35px;
    }

    .dropdown {
        position: absolute;
        top: 45px;
        right: 0;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 220px;
        display: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        z-index: 10;
    }

    .dropdown div, .dropdown a {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: black;
        border-bottom: 1px solid #eee;
    }

    .dropdown div:last-child {
        border-bottom: none;
    }

    .dropdown a:hover {
        background-color: #f0f0f0;
    }

    .dropdown .name {
        font-weight: bold;
    }
</style>

<div class="navbar">
    <div class="navbar-left">
        <div class="logo">
            <a href="index.php"><img src="../../asset/img/itb.png" alt="Logo ITB STIKOM"></a>
        </div>
        <div class="navbar-title">E-SKKM</div>
    </div>

    <div class="profile-container" onclick="toggleDropdown()">
        <div class="profile-icon"><img src="../../asset/img/profile.png" alt="Profile"></div>
        <div id="profileDropdown" class="dropdown">
            <div class="name"><?= htmlspecialchars($user['nama']) ?></div>
            <div><?= htmlspecialchars($nim) ?></div>
            <a href="profile/profile_mahasiswa.php">Profil</a>
            <a href="/e-skkm/logout.php">Logout</a>
        </div>
    </div>
</div>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById("profileDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    document.addEventListener("click", function(event) {
        const profileContainer = document.querySelector(".profile-container");
        const dropdown = document.getElementById("profileDropdown");
        if (!profileContainer.contains(event.target)) {
            dropdown.style.display = "none";
        }
    });
</script>
