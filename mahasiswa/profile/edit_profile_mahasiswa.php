<?php
session_start();
require '../../koneksi.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='../index.php';</script>";
    exit();
}

$id_user = $_SESSION['id_user'];

// Ambil data user dan detail mahasiswa
$query = "
    SELECT 
        u.id_user, u.nama, u.email, u.username, 
        udm.nim, udm.prodi, udm.angkatan
    FROM user u
    LEFT JOIN user_detail_mahasiswa udm ON u.id_user = udm.id_user
    WHERE u.id_user = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$mahasiswa = $result->fetch_assoc();

if (!$mahasiswa) {
    echo "<script>alert('Data mahasiswa tidak ditemukan!'); window.location.href='profile_mahasiswa.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profil Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Edit Email Mahasiswa</h2>
    <form action="proses_edit_profile_mahasiswa.php" method="POST">
        <input type="hidden" name="id_user" value="<?= $mahasiswa['id_user'] ?>">

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($mahasiswa['nama']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($mahasiswa['username']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">NIM</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($mahasiswa['nim']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Program Studi</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($mahasiswa['prodi']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Angkatan</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($mahasiswa['angkatan']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($mahasiswa['email']) ?>" required>
            <small id="emailError" class="text-danger"></small>
        </div>

        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        <a href="profile_mahasiswa.php" class="btn btn-secondary w-100 mt-2">Batal</a>
    </form>
</div>

<script>
    document.querySelector("form").addEventListener("submit", function(event) {
        let email = document.getElementById("email").value;
        let emailError = document.getElementById("emailError");

        if (!email.includes("@")) {
            event.preventDefault();
            emailError.innerText = "Email harus mengandung '@'";
        } else {
            emailError.innerText = "";
        }
    });
</script>

</body>
</html>
