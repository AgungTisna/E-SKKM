<?php
include '../koneksi.php'; // Hubungkan ke database

$pesan = ""; // Variabel untuk menampilkan pesan ke pengguna

// Periksa apakah form telah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // Validasi input tidak boleh kosong
    if (empty($password_lama) || empty($password_baru) || empty($konfirmasi_password)) {
        $pesan = "Semua kolom harus diisi!";
    } else {
        // Ambil password administrator dari database
        $query = "SELECT password FROM user WHERE role = 'Administrator' LIMIT 1";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $password_db = $row['password'];

            // Periksa apakah password lama cocok
            if ($password_lama === $password_db) {
                // Periksa apakah password baru sama dengan konfirmasi
                if ($password_baru === $konfirmasi_password) {
                    // Update password baru ke database
                    $update_query = "UPDATE user SET password = ? WHERE role = 'Administrator'";
                    $stmt = $conn->prepare($update_query);
                    $stmt->bind_param("s", $password_baru);

                    if ($stmt->execute()) {
                        $pesan = "Password berhasil diubah!";
                    } else {
                        $pesan = "Gagal mengubah password.";
                    }
                } else {
                    $pesan = "Konfirmasi password tidak cocok!";
                }
            } else {
                $pesan = "Password lama salah!";
            }
        } else {
            $pesan = "Data administrator tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ubah Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .profile-container { display: flex; height: 100vh; }
        .profile-sidebar { background-color: #d3d3d3; width: 30%; padding: 20px; text-align: center; }
        .btn { margin-top: 80%; }
        .profile-content { flex: 1; padding: 40px; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        .input-group { width: 500px; margin-bottom: 15px; }
        .form-control { border: 1px solid #000; padding: 10px; border-radius: 10px; width: 500px; }
        .btn-eye { border: none; background: none; cursor: pointer; padding: 0 15px; }
        .btn-custom { width: 30%; border: 1px solid #000; padding: 10px; border-radius: 10px; background-color: white; text-align: center; margin-top: 40px; margin-left: 30%; }
        .footer { text-align: center; font-size: 12px; padding: 10px; position: absolute; bottom: 0; width: 100%; }
        .alert { width: 80%; margin-top: 10px; text-align: center; }
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

    <!-- Konten Form Ubah Password -->
    <div class="profile-content">
        <h3>Ubah Password</h3>

        <!-- Menampilkan Pesan -->
        <?php if (!empty($pesan)): ?>
            <div class="alert alert-info"><?php echo $pesan; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- Password Lama -->
            <div class="input-group">
                <input type="password" class="form-control" name="password_lama" id="passwordLama" placeholder="Password Lama" required>
                <button class="btn-eye" type="button" onclick="togglePassword('passwordLama', 'eyeIcon1')">
                    <span id="eyeIcon1">👁</span>
                </button>
            </div>

            <!-- Password Baru -->
            <div class="input-group">
                <input type="password" class="form-control" name="password_baru" id="passwordBaru" placeholder="Password Baru" required>
                <button class="btn-eye" type="button" onclick="togglePassword('passwordBaru', 'eyeIcon2')">
                    <span id="eyeIcon2">👁</span>
                </button>
            </div>

            <!-- Konfirmasi Password Baru -->
            <div class="input-group">
                <input type="password" class="form-control" name="konfirmasi_password" id="konfirmasiPassword" placeholder="Konfirmasi Password Baru" required>
                <button class="btn-eye" type="button" onclick="togglePassword('konfirmasiPassword', 'eyeIcon3')">
                    <span id="eyeIcon3">👁</span>
                </button>
            </div>

            <button type="submit" class="btn-custom">Ubah Password</button>
        </form>
    </div>
</div>

<!-- Footer -->
<div class="footer">© Agung Tisna</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript untuk Toggle Password -->
<script>
    function togglePassword(inputId, iconId) {
        var inputField = document.getElementById(inputId);
        var eyeIcon = document.getElementById(iconId);

        if (inputField.type === "password") {
            inputField.type = "text";
            eyeIcon.textContent = "🙈"; // Ubah ikon ke "sembunyi"
        } else {
            inputField.type = "password";
            eyeIcon.textContent = "👁"; // Ubah ikon ke "lihat"
        }
    }
</script>

</body>
</html>
