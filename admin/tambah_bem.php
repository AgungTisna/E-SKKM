<?php
include '../koneksi.php';
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah BEM</title>
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
        .btn-custom {
            border: 1px solid #000;
            padding: 5px 15px;
            border-radius: 10px;
            background-color: white;
        }
        .container-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }
        .form-group {
            width: 500px;
            margin-bottom: 15px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            padding: 10px;
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
        .tombol{
            width: 100px;
            height: 50px;
            margin-left: 40%;
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

<!-- Header -->
<div class="container mt-3 d-flex justify-content-between">
    <a href="detail_bem.php"><button class="btn-custom">Kembali</button></a>
    <h3 class="text-center flex-grow-1">Tambah BEM</h3>
</div>

<!-- Form Tambah BEM -->
<div class="container container-form">
    <form action="proses_tambah_bem.php" method="POST">
        <div class="form-group">
            <label>Nama</label>
            <input type="text" class="form-control" name="nama" placeholder="Masukkan Nama" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" placeholder="Masukkan Email" required>
        </div>
        <div class="form-group">
            <label>Jabatan</label>
            <input type="text" class="form-control" value="Verifikator" readonly>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" name="username" placeholder="Masukkan Username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" placeholder="Masukkan Password" required>
        </div>
        <div class="tombol"><button type="submit" class="btn btn-primary mt-3">Tambah</button></div>
    </form>
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
