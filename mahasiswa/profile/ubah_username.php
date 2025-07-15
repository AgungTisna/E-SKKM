<?php
session_start();
require_once '../../koneksi.php';

$id_user = $_SESSION['id_user'] ?? null;
if (!$id_user) {
    header("Location: ../../login.php");
    exit;
}

$notif = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_lama = $_POST['username_lama'] ?? '';
    $username_baru = $_POST['username_baru'] ?? '';

    // Cek apakah username lama sesuai
    $cek = "SELECT * FROM user WHERE id_user = ? AND username = ?";
    $stmt = $conn->prepare($cek);
    $stmt->bind_param("is", $id_user, $username_lama);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update ke username baru
        $update = "UPDATE user SET username = ? WHERE id_user = ?";
        $stmt_upd = $conn->prepare($update);
        $stmt_upd->bind_param("si", $username_baru, $id_user);
        if ($stmt_upd->execute()) {
            $notif = "<div class='alert alert-success'>Username berhasil diubah!</div>";
        } else {
            $notif = "<div class='alert alert-danger'>Gagal mengubah username.</div>";
        }
    } else {
        $notif = "<div class='alert alert-warning'>Username lama tidak cocok!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ubah Username Mahasiswa</title>
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
        .form-control {
            width: 100%;
            border: 1px solid #000;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 15px;
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
        <img src="../../asset/img/profile.png" height="38%" width="58%"/>
        <br><br>
        <a href="profile_mahasiswa.php"><button class="btn btn-outline-dark">Kembali</button></a>
    </div>

    <div class="profile-content">
        <form method="POST">
            <?= $notif ?>
            <input type="text" class="form-control" name="username_lama" placeholder="Username Lama" required>
            <input type="text" class="form-control" name="username_baru" placeholder="Username Baru" required>
            <button type="submit" class="btn-custom">Ubah Username</button>
        </form>
    </div>
</div>

<div class="footer">© Agung Tisna</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
