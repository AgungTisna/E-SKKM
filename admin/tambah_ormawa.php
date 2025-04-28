<?php
include "../koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_ketua = $_POST["nama_ketua"];
    $nama_ormawa = $_POST["nama_ormawa"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "INSERT INTO user (nama, email, username, password, role) VALUES ('$nama_ketua', '$email', '$username', '$password', 'Ormawa')";
    if ($conn->query($sql) === TRUE) {
        $user_id = $conn->insert_id;
        $sql_ormawa = "INSERT INTO user_detail_ormawa (id_user, nama_ormawa) VALUES ('$user_id', '$nama_ormawa')";
        $conn->query($sql_ormawa);
        header("Location: detail_ormawa.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Ormawa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h3>Tambah Ormawa</h3>

    <form method="POST">
        <div class="mb-3">
            <label>Nama Ketua</label>
            <input type="text" name="nama_ketua" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Nama Ormawa</label>
            <input type="text" name="nama_ormawa" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="detail_ormawa.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

</body>
</html>
