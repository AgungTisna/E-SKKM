<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../index.php';</script>";
    exit();
}

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];

$stmt = $conn->prepare("SELECT nama, email FROM user WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$total_internal = $conn->query("SELECT COUNT(*) AS total FROM berkas_internal WHERE id_bem IS NOT NULL")->fetch_assoc()['total'] ?? 0;
$total_eksternal = $conn->query("SELECT COUNT(*) AS total FROM berkas_eksternal")->fetch_assoc()['total'] ?? 0;
$total_piagam = $conn->query("SELECT COUNT(*) AS total FROM berkas_piagam")->fetch_assoc()['total'] ?? 0;
$total_pengajuan = $conn->query("SELECT COUNT(*) AS total FROM pengajuan_skkm WHERE status_verifikasi_kemahasiswaan = 'Pending' AND status_verifikasi_bem = 'Valid'")->fetch_assoc()['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kemahasiswaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .content { margin-top: 50px; }
        .card-stat { min-height: 180px; }
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
        width:50px;
        height:50px;
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
            <div class="name"><?= htmlspecialchars($user['nama']) ?></div>
            <div class="role"><?= htmlspecialchars($role) ?></div>
            <a href="profile/profile_kemahasiswaan.php">Profile</a>
            <a href="/e-skkm/logout.php">Logout</a>
        </div>
    </div>
</div>


<div class="container content">
    <div class="row text-center mb-5">
        <h3>📊 Rekapitulasi Berkas E-SKKM</h3>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-md-4">
            <div class="card card-stat border-primary shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title">Berkas Internal</h5>
                        <p class="display-6 text-primary"><?= $total_internal ?></p>
                        <p>Sudah Diverifikasi oleh BEM</p>
                    </div>
                    <a href="berkas_internal/detail_berkas_internal.php" class="btn btn-outline-primary mt-3">Lihat Detail</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat border-success shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title">Berkas Eksternal</h5>
                        <p class="display-6 text-success"><?= $total_eksternal ?></p>
                        <p>Terverifikasi Ormawa</p>
                    </div>
                    <a href="berkas_eksternal/detail_berkas_eksternal.php" class="btn btn-outline-success mt-3">Lihat Detail</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat border-warning shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title">Berkas Piagam</h5>
                        <p class="display-6 text-warning"><?= $total_piagam ?></p>
                        <p>Sertifikat Kegiatan</p>
                    </div>
                    <a href="berkas_piagam/detail_berkas_piagam.php" class="btn btn-outline-warning mt-3">Lihat Detail</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat border-danger shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title">Pengajuan Poin SKKM</h5>
                        <p class="display-6 text-danger"><?= $total_pengajuan ?></p>
                        <p>Menunggu Verifikasi</p>
                    </div>
                    <a href="pengajuan/detail_pengajuan.php" class="btn btn-outline-danger mt-3">Lihat Pengajuan</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
        <div class="card card-stat border-dark shadow-sm h-100">
            <div class="card-body text-center d-flex flex-column justify-content-between">
                <div>
                    <h5 class="card-title">Atur Ketentuan Poin SKKM</h5>
                    <p class="display-6 text-dark"><i class="bi bi-gear-fill"></i></p>
                    <p>Kelola Batas Minimal Poin Mahasiswa</p>
                </div>
                <a href="atur_poin/atur_ketentuan_skkm.php" class="btn btn-outline-dark mt-3">Kelola Ketentuan</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-stat border-info shadow-sm h-100">
            <div class="card-body text-center d-flex flex-column justify-content-between">
                <div>
                    <h5 class="card-title">Lihat Poin Mahasiswa</h5>
                    <p class="display-6 text-info"><i class="bi bi-gear-fill"></i></p>
                    <p>Daftar Poin Mahasiswa</p>
                </div>
                <a href="cari_poin/list_poin_mahasiswa.php" class="btn btn-outline-info mt-3">Cari</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat border-secondary shadow-sm h-100">
            <div class="card-body text-center d-flex flex-column justify-content-between">
                <div>
                    <h5 class="card-title">Arsip Berkas Internal</h5>
                    <p class="display-6 text-secondary"><i class="bi bi-gear-fill"></i></p>
                    <p>Semua Arsip Berkas Internal</p>
                </div>
                <a href="arsip/arsip_internal.php" class="btn btn-outline-secondary mt-3">Lihat</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat border-secondary shadow-sm h-100">
            <div class="card-body text-center d-flex flex-column justify-content-between">
                <div>
                    <h5 class="card-title">Arsip Berkas Eksternal</h5>
                    <p class="display-6 text-secondary"><i class="bi bi-gear-fill"></i></p>
                    <p>Semua Arsip Berkas Eksternal</p>
                </div>
                <a href="arsip/arsip_eksternal.php" class="btn btn-outline-secondary mt-3">Lihat</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat border-secondary shadow-sm h-100">
            <div class="card-body text-center d-flex flex-column justify-content-between">
                <div>
                    <h5 class="card-title">Arsip Berkas Piagam</h5>
                    <p class="display-6 text-secondary"><i class="bi bi-gear-fill"></i></p>
                    <p>Semua Arsip Berkas Piagam</p>
                </div>
                <a href="arsip/arsip_piagam.php" class="btn btn-outline-secondary mt-3">Lihat</a>
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

<?php $conn->close(); ?>
