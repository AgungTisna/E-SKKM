<?php
session_start();
include '../../koneksi.php'; // Koneksi ke database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='../index.php';</script>";
    exit();
}

$id_user = $_SESSION['id_user'];

// 🔹 Ambil data user dari tabel `user` dan `user_detail_bem`
$query = "
    SELECT u.id_user, u.nama, u.email, u.username, ud.jabatan 
    FROM user u
    LEFT JOIN user_detail_bem ud ON u.id_user = ud.id_user
    WHERE u.id_user = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profil BEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Edit Profil BEM</h2>
    <form action="proses_edit_profile_bem.php" method="POST">
        <input type="hidden" name="id_user" value="<?php echo $user['id_user']; ?>">

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <small id="emailError" class="text-danger"></small>
        </div>

        <!-- Jabatan tidak bisa diubah -->
        <div class="mb-3">
            <label class="form-label">Jabatan</label>
            <p class="form-control-plaintext"><strong><?php echo htmlspecialchars($user['jabatan'] ?? 'Tidak ada jabatan'); ?></strong></p>
        </div>

        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        <a href="profile_bem.php" class="btn btn-secondary w-100 mt-2">Batal</a>
    </form>
</div>

<script>
    document.querySelector("form").addEventListener("submit", function(event) {
        let email = document.getElementById("email").value;
        let emailError = document.getElementById("emailError");

        if (!email.includes("@")) { // Validasi hanya cek '@'
            event.preventDefault();
            emailError.innerText = "Email harus mengandung '@'!";
        } else {
            emailError.innerText = "";
        }
    });
</script>

</body>
</html>
