<?php
include '../koneksi.php';
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
// Tentukan jumlah data per halaman
$limit = 5;

// Ambil halaman saat ini dari parameter URL (default: halaman 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Query untuk menghitung total data Kemahasiswaan
$totalQuery = "SELECT COUNT(*) AS total FROM user WHERE role = 'Kemahasiswaan'";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalKemahasiswaan = $totalRow['total'];
$totalPages = ceil($totalKemahasiswaan / $limit);

// Query untuk mengambil data Kemahasiswaan dengan pagination
$query = "SELECT u.id_user, u.nama, u.email, u.username, u.password, d.nip, d.jabatan 
          FROM user u 
          INNER JOIN user_detail_kemahasiswaan d ON u.id_user = d.id_user 
          WHERE u.role = 'Kemahasiswaan'
          LIMIT $start, $limit";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Kemahasiswaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background-color: white; padding: 10px 20px; border-bottom: 1px solid #ddd; }
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

<!-- Header -->
<div class="container mt-3 d-flex justify-content-between">
    <a href="index.php"><button class="btn-custom">Kembali</button></a>
    <h3 class="text-center flex-grow-1">Detail Kemahasiswaan</h3>
    <a href="tambah_kemahasiswaan.php"><button class="btn-custom">Tambah Kemahasiswaan</button></a>
</div>

<!-- Tabel Data Kemahasiswaan -->
<div class="container mt-3">
    <div class="table-responsive">
        <table class="table table-bordered table-custom">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Email</th>
                    <th>Jabatan</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $no = $start + 1; // Penomoran sesuai halaman
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['nama']}</td>
                                <td>{$row['nip']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['jabatan']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['password']}</td>
                                <td>
                                    <a href='hapus_kemahasiswaan.php?id={$row['id_user']}' class='btn btn-danger' onclick='return confirm(\"Apakah Anda yakin ingin menghapus?\")'>Hapus</a>
                                </td>
                              </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>Tidak ada data</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="container text-center mt-3">
    <p>Menampilkan <?php echo min($limit, $totalKemahasiswaan - $start); ?> dari <?php echo $totalKemahasiswaan; ?> data Kemahasiswaan</p>
    <nav>
        <ul class="pagination justify-content-center">
            <!-- Tombol Previous -->
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo ($page > 1) ? ($page - 1) : 1; ?>">Previous</a>
            </li>

            <!-- Nomor Halaman -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <!-- Tombol Next -->
            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo ($page < $totalPages) ? ($page + 1) : $totalPages; ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>

<!-- Footer -->
<div class="footer">© Agung Tisna</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

<?php
$conn->close();
?>
