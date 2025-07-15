<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION["imported_data"])) {
    header("Location: upload_batch_mahasiswa.php");
    exit();
}

$importedData = $_SESSION["imported_data"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($importedData as $data) {
        $query_user = "INSERT INTO user (nama, email, username, password, role) VALUES (?, ?, ?, ?, 'Mahasiswa')";
        $stmt_user = $conn->prepare($query_user);
        $stmt_user->bind_param("ssss", $data["nama"], $data["email"], $data["username"], $data["password"]);
        $stmt_user->execute();
        $id_user = $conn->insert_id;

        $query_mahasiswa = "INSERT INTO user_detail_mahasiswa (id_user, nim, prodi, angkatan) VALUES (?, ?, ?, ?)";
        $stmt_mahasiswa = $conn->prepare($query_mahasiswa);
        $stmt_mahasiswa->bind_param("isss", $id_user, $data["nim"], $data["prodi"], $data["angkatan"]);
        $stmt_mahasiswa->execute();
    }

    unset($_SESSION["imported_data"]);
    echo "<script>alert('✅ Data mahasiswa berhasil disimpan!'); window.location.href='upload_batch_mahasiswa.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Upload</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h3>Konfirmasi Data Mahasiswa</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIM</th>
                <th>Prodi</th>
                <th>Angkatan</th>
                <th>Email</th>
                <th>Username</th>
                <th>Password</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($importedData as $data): ?>
                <tr>
                    <td><?= $data["nama"] ?></td>
                    <td><?= $data["nim"] ?></td>
                    <td><?= $data["prodi"] ?></td>
                    <td><?= $data["angkatan"] ?></td>
                    <td><?= $data["email"] ?></td>
                    <td><?= $data["username"] ?></td>
                    <td><?= $data["password"] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form method="POST">
        <button type="submit" class="btn btn-success">Simpan ke Database</button>
    </form>
</div>

</body>
</html>
