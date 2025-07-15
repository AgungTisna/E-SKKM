<?php
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='../index.php';</script>";
    exit();
}

$id_user = $_SESSION['id_user'];

$query = "
    SELECT 
        u.id_user, u.nama, u.email, u.username,
        o.nama_ormawa
    FROM user u
    LEFT JOIN user_detail_ormawa o ON u.id_user = o.id_user
    WHERE u.id_user = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='profile_ormawa.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profil Ormawa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 20px; }
        .container {
            max-width: 600px; margin: auto;
            background: white; padding: 20px;
            border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Edit Profil Ormawa</h2>
    <form action="proses_edit_profile_ormawa.php" method="POST">
        <input type="hidden" name="id_user" value="<?= $data['id_user'] ?>">

        <div class="mb-3">
            <label class="form-label">Nama Ketua</label>
            <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($data['email']) ?>" required>
            <small id="emailError" class="text-danger"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Ormawa</label>
            <input type="text" class="form-control" name="nama_ormawa" value="<?= htmlspecialchars($data['nama_ormawa'] ?? '') ?>" readonly>
        </div>

        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        <a href="profile_ormawa.php" class="btn btn-secondary w-100 mt-2">Batal</a>
    </form>
</div>

<script>
    document.querySelector("form").addEventListener("submit", function(event) {
        const email = document.getElementById("email").value;
        const emailError = document.getElementById("emailError");
        if (!email.includes("@")) {
            event.preventDefault();
            emailError.textContent = "Email harus valid dan mengandung '@'";
        } else {
            emailError.textContent = "";
        }
    });
</script>

</body>
</html>
