<?php
include '../koneksi.php'; // Hubungkan ke database

// Cek jika tombol submit ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_lama = $_POST['username_lama'];
    $username_baru = $_POST['username_baru'];

    // Validasi input tidak boleh kosong
    if (empty($username_lama) || empty($username_baru)) {
        $pesan = "Username lama dan baru tidak boleh kosong!";
    } else {
        // Periksa apakah username lama cocok dengan database
        $query = "SELECT * FROM user WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username_lama);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Jika username lama cocok, update ke username baru
            $update_query = "UPDATE user SET username = ? WHERE username = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param("ss", $username_baru, $username_lama);

            if ($stmt_update->execute()) {
                $pesan = "Username berhasil diubah!";
            } else {
                $pesan = "Gagal mengubah username.";
            }
        } else {
            $pesan = "Username lama tidak ditemukan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ubah Username</title>
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
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .form-control {
            width: 500px;
            border: 1px solid #000;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .btn-custom {
            width: 30%;
            border: 1px solid #000;
            padding: 10px;
            border-radius: 10px;
            background-color: white;
            text-align: center;
            margin-top: 40px;
            margin-left: 40%;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            padding: 10px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        .tombol{
            width: 100px;
            height: 50px;
            margin-left: 40%;
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
        <a href="profile_admin.php"><button class="btn btn-outline-dark">Kembali</button></a>
    </div>

    <!-- Konten Form Ubah Username -->
    <div class="profile-content">
        <h3>Ubah Username</h3>
        
        <!-- Menampilkan Pesan -->
        <?php if (isset($pesan)): ?>
            <div class="alert alert-info"><?php echo $pesan; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" class="form-control" name="username_lama" placeholder="Username Lama" required>
            <input type="text" class="form-control" name="username_baru" placeholder="Username Baru" required>
            <button type="submit" class="btn-custom">Ubah Username</button>
        </form>
    </div>
</div>

<!-- Footer -->
<div class="footer">© Agung Tisna</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
