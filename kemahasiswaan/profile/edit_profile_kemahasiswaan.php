<?php
session_start();
require '../../koneksi.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='../index.php';</script>";
    exit();
}

$id_user = $_SESSION['id_user'];

// Ambil data user + detail kemahasiswaan
$query = "
    SELECT u.id_user, u.nama, u.email, u.username, k.nip, k.jabatan
    FROM user u
    LEFT JOIN user_detail_kemahasiswaan k ON u.id_user = k.id_user
    WHERE u.id_user = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='profile_kemahasiswaan.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profil Kemahasiswaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Edit Profil Kemahasiswaan</h2>
    <form action="proses_edit_profile_kemahasiswaan.php" method="POST">
        <input type="hidden" name="id_user" value="<?= $data['id_user'] ?>">

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" readonly>
        </div>


        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($data['email']) ?>" required>
            <small id="emailError" class="text-danger"></small>
        </div>

        <div class="mb-3">
            <label class="form-label">NIP</label>
            <input type="text" class="form-control" name="nip" value="<?= htmlspecialchars($data['nip'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Jabatan</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($data['jabatan'] ?? '-') ?>" readonly>
        </div>

        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        <a href="profile_kemahasiswaan.php" class="btn btn-secondary w-100 mt-2">Batal</a>
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
