<?php
include "../koneksi.php";
// Query untuk mengambil data Administrator
$query_admin = "SELECT nama, role FROM user WHERE role = 'Administrator' LIMIT 1";
$result_admin = $conn->query($query_admin);

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
 <footer>
        <center><p>© Agung Tisna</p></center>
    </footer>
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
