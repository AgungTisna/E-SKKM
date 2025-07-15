<?php
session_start();
include '../koneksi.php'; // Koneksi ke database

// 🔹 **Pastikan Pengguna Sudah Login Sebagai BEM**
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'BEM') {
    echo "<script>alert('Anda harus login sebagai BEM!'); window.location.href='../index.php';</script>";
    exit;
}

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];

// 🔹 **Pastikan Koneksi Database Ada**
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// 🔹 **Ambil Nama & Jabatan dari Database**
$query_user = "
    SELECT u.nama, u.email, ud.jabatan
    FROM user u
    LEFT JOIN user_detail_bem ud ON u.id_user = ud.id_user
    WHERE u.id_user = ?
";

$stmt = $conn->prepare($query_user);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>alert('Pengguna tidak ditemukan.'); window.history.back();</script>";
    exit;
}

// 🔹 **Query untuk Menghitung Berkas Internal dengan INNER JOIN**
$query_ormawa = "
    SELECT 
        udo.id_ormawa, 
        udo.nama_ormawa,
        COALESCE(bi.total_internal, 0) AS total_internal,
        COALESCE(bi.belum_terverifikasi, 0) AS belum_terverifikasi,
        COALESCE(be.total_eksternal, 0) AS total_eksternal,
        COALESCE(bp.total_piagam, 0) AS total_piagam
    FROM user_detail_ormawa udo
    LEFT JOIN (
        SELECT 
            id_ormawa, 
            COUNT(*) AS total_internal,
            SUM(CASE WHEN poin_skkm IS NULL OR poin_skkm = 0 THEN 1 ELSE 0 END) AS belum_terverifikasi
        FROM berkas_internal 
        WHERE tanggal_pengajuan IS NOT NULL 
        GROUP BY id_ormawa
    ) bi ON udo.id_ormawa = bi.id_ormawa
    LEFT JOIN (
        SELECT id_ormawa, COUNT(*) AS total_eksternal 
        FROM berkas_eksternal 
        GROUP BY id_ormawa
    ) be ON udo.id_ormawa = be.id_ormawa
    LEFT JOIN (
        SELECT id_ormawa, COUNT(*) AS total_piagam 
        FROM berkas_piagam 
        GROUP BY id_ormawa
    ) bp ON udo.id_ormawa = bp.id_ormawa
    ORDER BY udo.nama_ormawa ASC
";


$result_ormawa = $conn->query($query_ormawa);

// 🔸 Hitung jumlah pengajuan yang masih Pending oleh BEM
$query_pending = "SELECT COUNT(*) as total_pending FROM pengajuan_skkm WHERE status_verifikasi_bem = 'Pending'";
$result_pending = $conn->query($query_pending);
$row_pending = $result_pending->fetch_assoc();
$total_pending = $row_pending['total_pending'];

$belum_terverifikasi ="

";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-SKKM ITB STIKOM Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body { background-color: #f8f9fa; }
        .content { margin-top: 30px; }
        .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        border: 1px solid black;
        background-color: white;
        position: relative;
    }

    .navbar-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .logo {
        width: 60px;
        height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 10px;
        text-align: center;
    }
    .logo img{
        width: 60px;
    }


    .navbar-title {
        font-size: 18px;
        font-weight: bold;
    }

    .profile-container {
        position: relative;
    }

    .profile-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: black;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .profile-icon img {
        width: 35px;
        height: 35px;
    }

    .profile-icon::before {
        font-size: 18px;
        color: white;
    }

    .dropdown {
        position: absolute;
        top: 45px;
        right: 0;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 220px;
        display: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        z-index: 10;
    }

    .dropdown div, .dropdown a {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: black;
        border-bottom: 1px solid #eee;

    }

    .dropdown div:last-child {
        border-bottom: none;
    }

    .dropdown a:hover {
        background-color: #f0f0f0;
    }

    .dropdown .name {
        font-weight: bold;
    }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="navbar-left">
        <div class="logo">
        <a href="index.php"><img src="../asset/img/itb.png"></a>
        </div>
        <div class="navbar-title">E-SKKM</div>
    </div>

    <div class="profile-container" onclick="toggleDropdown()">
        <div class="profile-icon"><img src="../asset/img/profile.png"></div>
        <div id="profileDropdown" class="dropdown">
            <div class="name"><?= htmlspecialchars($user['nama']) ?></div>
            <div class="role"><?= htmlspecialchars($role) ?></div>
            <a href="profile/profile_bem.php">Profile</a>
            <a href="/e-skkm/logout.php">Logout</a>
        </div>
    </div>
</div>
<!-- Konten -->
<div class="container content">
    <h2 class="text-center mb-4">Daftar Ormawa yang Mengajukan Berkas</h2>
    <div class="mb-3 text-end">
    <a href="pengajuan/detail_pengajuan.php" class="btn btn-warning">
        📥 Lihat Pengajuan SKKM <span class="badge bg-danger"><?= $total_pending ?></span>
    </a>
</div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Ormawa</th>
                <th>Berkas Internal</th>
                <th>Status Verifikasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_ormawa->num_rows > 0) {
                $no = 1;
                while ($row = $result_ormawa->fetch_assoc()) {
                    $status = ($row['belum_terverifikasi'] > 0)
    ? "<span class='badge bg-warning text-dark'>Belum Terverifikasi</span>"
    : "<span class='badge bg-success'>Terverifikasi</span>";

                    echo "<tr>
                            <td>{$no}</td>
                            <td>" . htmlspecialchars($row['nama_ormawa']) . "</td>
                            <td>{$row['total_internal']} Berkas</td>
                            <td>$status</td>

                            <td>
                                <a href='berkas_internal/list_kegiatan.php?id_ormawa=" . urlencode($row['id_ormawa']) . "' class='btn btn-primary btn-sm'>Lihat Detail</a>
                            </td>
                          </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>Belum ada pengajuan berkas</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById("profileDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    document.addEventListener("click", function(event) {
        const profileContainer = document.querySelector(".profile-container");
        const dropdown = document.getElementById("profileDropdown");
        if (!profileContainer.contains(event.target)) {
            dropdown.style.display = "none";
        }
    });
</script>
</body>
</html>

<?php
$conn->close();
?>
