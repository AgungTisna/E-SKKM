<?php
session_start();
require_once '../../koneksi.php';

// Cek login mahasiswa
$id_user = $_SESSION['id_user'] ?? null;
if (!$id_user) {
    header("Location: ../../login.php");
    exit;
}

$notif = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_lama       = $_POST['password_lama'] ?? '';
    $password_baru       = $_POST['password_baru'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // Ambil password dari database
    $query = "SELECT password FROM user WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data && $data['password'] === $password_lama) {
        if ($password_baru === $konfirmasi_password) {
            $update = "UPDATE user SET password = ? WHERE id_user = ?";
            $stmt_update = $conn->prepare($update);
            $stmt_update->bind_param("si", $password_baru, $id_user);
            if ($stmt_update->execute()) {
                $notif = "<div class='alert alert-success'>Password berhasil diubah.</div>";
            } else {
                $notif = "<div class='alert alert-danger'>Gagal mengubah password.</div>";
            }
        } else {
            $notif = "<div class='alert alert-warning'>Konfirmasi password tidak cocok.</div>";
        }
    } else {
        $notif = "<div class='alert alert-danger'>Password lama salah.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ubah Password Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .profile-container { display: flex; height: 100vh; }
        .profile-sidebar {
            background-color: #d3d3d3;
            width: 30%;
            padding: 20px;
            text-align: center;
        }
        .btn { margin-top: 80%; }
        .profile-content {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .input-group { width: 100%; margin-bottom: 15px; }
        .form-control {
            border: 1px solid #000;
            padding: 10px;
            border-radius: 10px;
        }
        .btn-eye {
            border: none;
            background: none;
            cursor: pointer;
            padding: 0 15px;
        }
        .btn-custom {
            width: 80%;
            border: 1px solid #000;
            padding: 10px;
            border-radius: 10px;
            background-color: white;
            text-align: center;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            padding: 10px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="profile-sidebar">
        <img src="../../asset/img/profile.png" height="38%" width="58%" />
        <br><br>
        <a href="profile_mahasiswa.php"><button class="btn btn-outline-dark">Kembali</button></a>
    </div>

    <div class="profile-content">
        <form method="POST">
            <?= $notif ?>
            <div class="input-group">
                <input type="password" name="password_lama" class="form-control" id="passwordLama" placeholder="Password Lama" required>
                <button class="btn-eye" type="button" onclick="togglePassword('passwordLama', 'eyeIcon1')">
                    <span id="eyeIcon1">👁</span>
                </button>
            </div>

            <div class="input-group">
                <input type="password" name="password_baru" class="form-control" id="passwordBaru" placeholder="Password Baru" required>
                <button class="btn-eye" type="button" onclick="togglePassword('passwordBaru', 'eyeIcon2')">
                    <span id="eyeIcon2">👁</span>
                </button>
            </div>

            <div class="input-group">
                <input type="password" name="konfirmasi_password" class="form-control" id="konfirmasiPassword" placeholder="Konfirmasi Password Baru" required>
                <button class="btn-eye" type="button" onclick="togglePassword('konfirmasiPassword', 'eyeIcon3')">
                    <span id="eyeIcon3">👁</span>
                </button>
            </div>

            <button type="submit" class="btn-custom">Ubah Password</button>
        </form>
    </div>
</div>

<div class="footer">© Agung Tisna</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.textContent = "🙈";
        } else {
            input.type = "password";
            icon.textContent = "👁";
        }
    }
</script>

</body>
</html>
