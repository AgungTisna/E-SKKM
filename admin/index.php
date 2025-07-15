<?php
include '../koneksi.php'; // Menggunakan koneksi dari file terpisah
// Query untuk mengambil data Administrator
$query_admin = "SELECT nama, role FROM user WHERE role = 'Administrator' LIMIT 1";
$result_admin = $conn->query($query_admin);

// Periksa apakah query berhasil
if (!$result_admin) {
    die("Query gagal: " . $conn->error);
}

// Ambil data Administrator
if ($result_admin->num_rows > 0) {
    $row_admin = $result_admin->fetch_assoc();
    $admin_nama = $row_admin['nama'];
    $admin_jabatan = $row_admin['role'];
} else {
    $admin_nama = "Administrator";
    $admin_jabatan = "Tidak Diketahui";
}

// Query untuk menghitung jumlah Mahasiswa
$query_mahasiswa = "SELECT COUNT(*) AS total FROM user_detail_mahasiswa";
$result_mahasiswa = $conn->query($query_mahasiswa);
$row_mahasiswa = $result_mahasiswa->fetch_assoc();
$total_mahasiswa = $row_mahasiswa['total'];

// Query untuk menghitung jumlah BEM
$query_bem = "SELECT COUNT(*) AS total FROM user_detail_bem";
$result_bem = $conn->query($query_bem);
$row_bem = $result_bem->fetch_assoc();
$total_bem = $row_bem['total'];

// Query untuk menghitung jumlah Kemahasiswaan
$query_kemahasiswaan = "SELECT COUNT(*) AS total FROM user_detail_kemahasiswaan";
$result_kemahasiswaan = $conn->query($query_kemahasiswaan);
$row_kemahasiswaan = $result_kemahasiswaan->fetch_assoc();
$total_kemahasiswaan = $row_kemahasiswaan['total'];

// Query untuk menghitung jumlah Ormawa
$query_ormawa = "SELECT COUNT(*) AS total FROM user_detail_ormawa";
$result_ormawa = $conn->query($query_ormawa);
$row_ormawa = $result_ormawa->fetch_assoc();
$total_ormawa = $row_ormawa['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mengelola Pengguna</title>
    <!-- Tambahkan Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: white;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
        }
        .logo-box {
            width: 80px;
            height: 80px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }
        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }
        .card-custom {
            border-radius: 15px;
            border: 1px solid #000;
            padding: 20px;
            text-align: center;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 270px;
        }
        .detail-button {
            margin-top: 10px;
            width: 100%;
            border: 1px solid #000;
            padding: 5px;
            border-radius: 10px;
            background-color: white;
        }
        .footer {
            padding: 10px;
            text-align: center;
            font-size: 12px;
            width: 100%;
            margin-top: 30px;
        }
        /* Dropdown Profile */
        .profile-dropdown {
            position: absolute;
            top: 60px;
            right: 20px;
            background: #ddd;
            border: 1px solid #000;
            padding: 10px;
            border-radius: 5px;
            width: 200px;
            display: none; /* Default hidden */
        }
        .profile-dropdown p {
            margin: 5px 0;
        }
        .profile-dropdown .btn {
            width: 100%;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar d-flex justify-content-between">
    <div class="d-flex align-items-center">
    <div class="logo-box"><img height="50" src="../asset/img/itb.png" width="50"/></div>
    <h4 class="ms-3">E-SKKM</h4>
    </div>
    <div class="position-relative">
        <div class="profile-icon" id="profileIcon"><img src="../asset/img/profile.png" height="50"/></div>
        <!-- Dropdown Profile -->
        <div class="profile-dropdown" id="profileDropdown">
            <p><strong>Nama:</strong> <?php echo $admin_nama; ?></p>
            <p><strong><?php echo $admin_jabatan; ?></strong></p>
            <a href="profile_admin.php"><button class="btn btn-outline-dark">Profile</button></a>
            <a href="../logout.php">
                <button class="btn btn-outline-dark">Logout</button>
            </a>
        </div>
    </div>
</nav>

<!-- Judul -->
<h3 class="text-center mt-4">Mengelola Pengguna</h3>

<!-- Konten -->
<div class="container mt-4">
    <div class="row justify-content-center g-4">
        <!-- Kartu 1 -->
        <div class="col-md-3">
            <div class="card-custom">
                <h2><?php echo $total_mahasiswa; ?> <br> Mahasiswa</h2>
                <a href="detail_mahasiswa.php"><button class="detail-button">Detail Info</button></a>
            </div>
        </div>

        <!-- Kartu 2 -->
        <div class="col-md-3">
            <div class="card-custom">
                <h2><?php echo $total_bem; ?> <br> BEM</h2>
                <a href="detail_bem.php"><button class="detail-button">Detail Info</button></a>
            </div>
        </div>

        <!-- Kartu 3 -->
        <div class="col-md-3">
            <div class="card-custom">
                <h2><?php echo $total_kemahasiswaan; ?> <br> Kemahasiswaan </h2>
                <a href="detail_kemahasiswaan.php"><button class="detail-button">Detail Info</button></a>
            </div>
        </div>
        <!-- Kartu 4 -->
        <div class="col-md-3">
            <div class="card-custom">
                <h2><?php echo $total_ormawa; ?> <br> Ormawa </h2>
                <a href="detail_ormawa.php"><button class="detail-button">Detail Info</button></a>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">© Agung Tisna</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle dropdown profile
    document.getElementById("profileIcon").addEventListener("click", function() {
        var dropdown = document.getElementById("profileDropdown");
        if (dropdown.style.display === "none" || dropdown.style.display === "") {
            dropdown.style.display = "block";
        } else {
            dropdown.style.display = "none";
        }
    });

    // Tutup dropdown jika klik di luar
    document.addEventListener("click", function(event) {
        var profileIcon = document.getElementById("profileIcon");
        var dropdown = document.getElementById("profileDropdown");

        if (!profileIcon.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = "none";
        }
    });
</script>
</body>
</html>
