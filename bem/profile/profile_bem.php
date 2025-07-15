<?php
// Sertakan koneksi database
require '../../koneksi.php';

// Pastikan pengguna sudah login (contoh menggunakan session)
session_start();
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='../index.php';</script>";
    exit;
}

$id_user = $_SESSION['id_user']; // Menggunakan session untuk mendapatkan ID user

// 🔹 Ambil data user dari tabel `user` dan jabatan dari `user_detail_bem`
$query_user = "
    SELECT 
        u.nama, 
        u.email, 
        u.username, 
        u.password, -- ⚠ Jika password tidak di-hash, langsung tampilkan
        ud.jabatan
    FROM user u
    LEFT JOIN user_detail_bem ud ON u.id_user = ud.id_user
    WHERE u.id_user = ?
";

$stmt = $conn->prepare($query_user);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Jika data tidak ditemukan
if (!$user) {
    echo "<script>alert('Pengguna tidak ditemukan.'); window.history.back();</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil BEM</title>
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
        .btn {
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
        .box {
            height: 50%;
            width: 20%;
        }
        /* Style untuk input password */
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-container input {
            width: 100%;
            padding-right: 40px; /* Memberi ruang untuk ikon */
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            cursor: pointer;
            font-size: 18px;
        }
        .btn-eye {
            border: none;
            background: none;
            cursor: pointer;
            padding-left: 10px;
        }
    </style>
</head>
<body>

<!-- Kontainer Profil -->
<div class="profile-container">
    <!-- Sidebar Profil -->
    <div class="profile-sidebar">
        <img src="../../asset/img/profile.png" height="38%" width="58%"/>
        <br><br>
        <a href="../index.php"><button class="btn btn-outline-dark">Kembali</button></a>
    </div>

    <!-- Konten Profil -->
    <div class="profile-content">
        <div class="row">
            <div class="col-md-6">
                <p class="profile-info"><strong>Nama</strong><br><?php echo htmlspecialchars($user['nama']); ?></p>
                <p class="profile-info"><strong>Email</strong><br><?php echo htmlspecialchars($user['email']); ?></p>
                <p class="profile-info"><strong>Jabatan</strong><br><?php echo htmlspecialchars($user['jabatan'] ?? 'Tidak ada jabatan'); ?></p>
            </div>
            <div class="col-md-6">
            <p class="profile-info"><strong>Username</strong><br><?php echo htmlspecialchars($user['username']); ?></p>

                <!-- 🔹 Input Password dengan Toggle -->
                <p class="profile-info"><strong>Password</strong><br>
                    <div class="password-container">
                        <input type="password" class="form-control" id="passwordField" value="<?php echo htmlspecialchars($user['password']); ?>" readonly>
                        <button class="btn-eye" type="button" onclick="togglePassword('passwordField', 'eyeIcon')">
                            <span id="eyeIcon">👁</span>
                        </button>
                    </div>
                </p>
            </div>
        </div>
        <div class="box"></div>
        <a href="edit_profile_bem.php"><button class="btn-custom">Ubah Profile</button></a>
        <a href="ubah_username.php"><button class="btn-custom">Ubah Username</button></a>
        <a href="ubah_password.php"><button class="btn-custom">Ubah Password</button></a>
    </div>
</div>

<!-- Footer -->
<div class="footer">© Agung Tisna</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript Toggle Password -->
<script>
    function togglePassword() {
        var passwordField = document.getElementById("passwordField");
        if (passwordField.type === "password") {
            passwordField.type = "text"; // Tampilkan password
            eyeIcon.textContent = "🙈";
        } else {
            passwordField.type = "password"; // Sembunyikan password
            eyeIcon.textContent = "👁";
        }
    }
</script>

</body>
</html>
