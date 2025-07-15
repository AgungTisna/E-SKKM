<?php
include '../koneksi.php'; // Hubungkan ke database

// Query untuk mengambil data Administrator dari tabel user
$query_admin = "SELECT nama, email, role, username, password FROM user WHERE role = 'Administrator' LIMIT 1";
$result_admin = $conn->query($query_admin);

// Cek apakah ada data Administrator
if ($result_admin->num_rows > 0) {
    $row_admin = $result_admin->fetch_assoc();
    $admin_nama = $row_admin['nama'];
    $admin_email = $row_admin['email'];
    $admin_jabatan = $row_admin['role'];
    $admin_username = $row_admin['username'];
    $admin_password = $row_admin['password'];

    // Masking password: tampilkan hanya 3 karakter pertama, sisanya bintang
    $admin_password_hidden = substr($admin_password, 0, 3) . str_repeat("*", 5);
} else {
    // Jika tidak ditemukan, tampilkan default
    $admin_nama = "Administrator";
    $admin_email = "admin@example.com";
    $admin_jabatan = "Administrator";
    $admin_username = "admin";
    $admin_password_hidden = "*******"; // Tetap disamarkan
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Administrator</title>
    <!-- Tambahkan Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-container {
            display: flex;
            height: 100vh;
        }
        .profile-sidebar {
            background-color: #d3d3d3;
            width: 30%;
            padding: 20px;
            text-align: center;
        }
        .btn{
            margin-top: 80%;
        }
        .profile-content {
            flex: 1;
            padding: 40px;
        }
        .profile-info {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .btn-custom {
            width: 100%;
            border: 1px solid #000;
            padding: 10px;
            border-radius: 10px;
            background-color: white;
            text-align: center;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            padding: 10px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        .box{
            height: 50%;
            width: 20%;
        }
        .password-container { 
            position: relative; 
            align-items: center; 
        
        }
    </style>
</head>
<body>

<!-- Kontainer Profil -->
<div class="profile-container">
    <!-- Sidebar Profil -->
    <div class="profile-sidebar">
    <img src="../asset/img/profile.png" height="40%" width="58%"/>
        <br><br>
        <a href="index.php"><button class="btn btn-outline-dark">Kembali</button></a>
    </div>

    <!-- Konten Profil -->
    <div class="profile-content">
        <div class="row">
            <div class="col-md-6">
            <p class="profile-info"><strong>Nama</strong><br><?php echo $admin_nama; ?></p>
                <p class="profile-info"><strong>Email</strong><br><?php echo $admin_email; ?></p>
                <p class="profile-info"><strong>Jabatan</strong><br><?php echo $admin_jabatan; ?></p>
            </div>
            <div class="col-md-6">
            <p class="profile-info"><strong>Username</strong><br><?php echo $admin_username; ?></p>
            <p class="profile-info"><strong>Password</strong><br></p>
            <div class="password-container">
                    <input type="password" class="password-input" id="passwordField" value="<?php echo $admin_password; ?>" readonly>
                    <button type="button" class="toggle-password" onclick="togglePassword()">👁 Lihat</button>
                </div>
            </div>
        </div>
        <div class="box"></div>
        <a href="ubah_username.php"><button class="btn-custom">Ubah Username</button></a>
        <a href="ubah_password.php"><button class="btn-custom">Ubah Password</button></a>
    </div>
</div>

<!-- Footer -->
<div class="footer">© Agung Tisna</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
    var passwordField = document.getElementById("passwordField");
    var toggleButton = document.querySelector(".toggle-password");
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleButton.textContent = "🙈 Sembunyikan";
    } else {
        passwordField.type = "password";
        toggleButton.textContent = "👁 Lihat";
    }
}
</script>
</body>
</html>
