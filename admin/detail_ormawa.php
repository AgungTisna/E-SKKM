<?php
include "../koneksi.php";

// Ambil data ormawa dari database
$sql = "SELECT u.id_user, u.nama AS nama_ketua, o.nama_ormawa, u.email, u.username, u.password 
        FROM user u
        JOIN user_detail_ormawa o ON u.id_user = o.id_user";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Ormawa - E-SKKM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .table { text-align: center; }
        .container { margin-top: 20px; }
        footer { margin-top: 20px; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>E-SKKM</h2>
        <img src="img/logo.png" alt="Logo ITB STIKOM BALI" width="80">
    </div>

    <a href="index.php"><button class="btn btn-secondary mb-3">Kembali</button></a>
    
    <h3 class="text-center">Detail Ormawa</h3>

    <div class="d-flex justify-content-end">
        <a href="tambah_ormawa.php" class="btn btn-primary mb-3">Tambah Ormawa</a>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Ketua</th>
                <th>Nama Ormawa</th>
                <th>Email</th>
                <th>Username</th>
                <th>Password</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['nama_ketua']}</td>
                        <td>{$row['nama_ormawa']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['password']}</td>
                        <td>
                            <a href='hapus_ormawa.php?id={$row['id_user']}' class='btn btn-danger btn-sm'>Hapus</a>
                        </td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>Tidak ada data</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between">
        <button class="btn btn-light">Previous</button>
        <span>1</span>
        <button class="btn btn-light">Next</button>
    </div>

    <footer>
        <p>© Agung Tisna</p>
    </footer>
</div>

</body>
</html>
