<?php
include "../koneksi.php";
// Query untuk mengambil data Administrator
$query_admin = "SELECT nama, role FROM user WHERE role = 'Administrator' LIMIT 1";
$result_admin = $conn->query($query_admin);
// Periksa apakah query berhasil
if (!$result_admin) {
    die("Query gagal: " . $conn->error);
}

// Ambil data Administrator
if ($result_admin->num_rows > 0) {
    $row_admin = $result_admin->fetch_assoc();
    $admin_nama = $row_admin['nama'];
    $admin_jabatan = $row_admin['role'];
} else {
    $admin_nama = "Administrator";
    $admin_jabatan = "Tidak Diketahui";
}
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
        .navbar { background-color: white; padding: 10px 20px; border-bottom: 1px solid #ddd; }
        .logo-box { width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; }
        .table-custom th, .table-custom td { text-align: center; border: 2px solid #000; padding: 10px; }
        .btn-custom { border: 1px solid #000; padding: 5px 15px; border-radius: 10px; background-color: white; }
        .pagination .page-item.active .page-link { background-color: deepskyblue; color: white; border: 1px solid #000; }
        .footer { text-align: center; font-size: 12px; padding: 10px; width: 100%; margin-top: 20px; }
        /* Dropdown Profile */
        .profile-dropdown {
            position: absolute;
            top: 60px;
            right: 20px;
            background: #ddd;
            border: 1px solid #000;
            padding: 10px;
            border-radius: 5px;
            width: 200px;
            display: none; /* Default hidden */
        }
        .profile-dropdown p {
            margin: 5px 0;
        }
        .profile-dropdown .btn {
            width: 100%;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar d-flex justify-content-between">
    <div class="d-flex align-items-center">
        <div class="logo-box"><img height="50" src="../asset/img/itb.png" width="50"/></div>
        <h4 class="ms-3">E-SKKM</h4>
    </div>
    <div class="position-relative">
        <div class="profile-icon" id="profileIcon"><img src="../asset/img/profile.png" height="50"/></div>
        <!-- Dropdown Profile -->
        <div class="profile-dropdown" id="profileDropdown">
            <p><strong>Nama:</strong> <?php echo $admin_nama; ?></p>
            <p><strong><?php echo $admin_jabatan; ?></strong></p>
            <a href="profile_admin.php"><button class="btn btn-outline-dark">Profile</button></a>
            <a href="../logout.php">
                <button class="btn btn-outline-dark">Logout</button>
            </a>
        </div>
    </div>
</nav>
<div class="container">

    <a href="index.php"><button class="btn btn-secondary mb-3 mt-3">Kembali</button></a>
    
    <h3 class="text-center">Detail Ormawa</h3>
        <a href="print_ormawa.php" target="_blank" class="btn btn-danger mb-3 ms-2">Export ke PDF</a>

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


    <footer>
        <center><p>© Agung Tisna</p></center>
    </footer>
</div>
<script>
    // Toggle dropdown profile
    document.getElementById("profileIcon").addEventListener("click", function() {
        var dropdown = document.getElementById("profileDropdown");
        if (dropdown.style.display === "none" || dropdown.style.display === "") {
            dropdown.style.display = "block";
        } else {
            dropdown.style.display = "none";
        }
    });

    // Tutup dropdown jika klik di luar
    document.addEventListener("click", function(event) {
        var profileIcon = document.getElementById("profileIcon");
        var dropdown = document.getElementById("profileDropdown");

        if (!profileIcon.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = "none";
        }
    });
</script>
</body>
</html>
