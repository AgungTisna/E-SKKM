<?php
session_start();
require_once('../../koneksi.php');

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'BEM') {
    header("Location: ../../index.php");
    exit();
}
$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];
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
// Ambil semua pengajuan dan join ke user
$query = "
    SELECT p.*, u.nama AS nama_mahasiswa
    FROM pengajuan_skkm p
    JOIN user_detail_mahasiswa udm ON p.nim = udm.nim
    JOIN user u ON udm.id_user = u.id_user
    ORDER BY p.id_pengajuan DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pengajuan SKKM - BEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
<body class="bg-light">
<div class="navbar">
    <div class="navbar-left">
        <div class="logo">
        <a href="../index.php"><img src="../../asset/img/itb.png"></a>
        </div>
        <div class="navbar-title">E-SKKM</div>
    </div>

    <div class="profile-container" onclick="toggleDropdown()">
        <div class="profile-icon"><img src="../../asset/img/profile.png"></div>
        <div id="profileDropdown" class="dropdown">
            <div class="name"><?= htmlspecialchars($user['nama']) ?></div>
            <div class="role"><?= htmlspecialchars($role) ?></div>
            <a href="../profile/profile_bem.php">Profile</a>
            <a href="/e-skkm/logout.php">Logout</a>
        </div>
    </div>
</div>
<div class="container mt-5">
    <h3 class="text-center mb-4">📋 Detail Pengajuan SKKM Mahasiswa</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

    <div class="card shadow">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>File</th>
                        <th>Bukti Keikutsertaan</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Nama Kegiatan</th>
                        <th>Kategori Kegiatan</th> <!-- 🔹 Tambahan -->
                        <th>Tingkat</th>
                        <th>Partisipasi</th>
                        <th>Poin</th>
                        <th>Status BEM</th>
                        <th>Status KMHS</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
<?php
$no = 1;
while ($row = $result->fetch_assoc()):
    $canValidate = $row['status_verifikasi_bem'] !== 'Valid';
?>
    <tr>
        <td class="text-center"><?= $no++ ?></td>
        <td class="text-center">
            <a href="../../asset/upload/<?= $row['file_bukti'] ?>" target="_blank">
                <img src="../../asset/upload/<?= $row['file_bukti'] ?>" width="50">
            </a>
        </td>
        <td class="text-center">
    <?php if (!empty($row['bukti_keikutsertaan'])):
        $ext = pathinfo($row['bukti_keikutsertaan'], PATHINFO_EXTENSION);
        $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png']);
        if ($isImage): ?>
            <a href="../../asset/upload/<?= $row['bukti_keikutsertaan'] ?>" target="_blank">
                <img src="../../asset/upload/<?= $row['bukti_keikutsertaan'] ?>" width="50">
            </a>
        <?php else: ?>
            <a href="../../asset/upload/<?= $row['bukti_keikutsertaan'] ?>" target="_blank">📎 Lihat Dokumen</a>
        <?php endif; ?>
    <?php else: ?>
        <span class="text-muted">-</span>
    <?php endif; ?>
</td>

        <td class="text-center"><?= $row['nim'] ?></td>
        <td class="text-center"><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
        <td class="text-center"><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
        <td class="text-center"><?= htmlspecialchars($row['kategori_kegiatan']) ?></td> <!-- 🔹 Tambahan -->
        <td class="text-center"><?= $row['tingkat'] ?></td>
        <td class="text-center"><?= $row['partisipasi'] ?></td>
        <td class="text-center"><?= $row['poin_skkm'] ?></td>
        <td class="text-center">
            <span class="badge bg-<?= getColor($row['status_verifikasi_bem']) ?>">
                <?= $row['status_verifikasi_bem'] ?>
            </span>
        </td>
        <td class="text-center">
            <span class="badge bg-<?= getColor($row['status_verifikasi_kemahasiswaan']) ?>">
                <?= $row['status_verifikasi_kemahasiswaan'] ?>
            </span>
        </td>
        <td class="text-center">
<?php if ($canValidate): ?>
    <form method="POST" action="pengajuan_skkm.php">
        <input type="hidden" name="id_pengajuan" value="<?= $row['id_pengajuan'] ?>">
        <input type="hidden" name="nim" value="<?= $row['nim'] ?>">
        <button type="submit" class="btn btn-primary btn-sm">🔍 Validasi</button>
    </form>
<?php else: ?>
    <span class="text-muted">Sudah Valid</span>
<?php endif; ?>
        </td>
    </tr>
    <tr class="table-light">
        <td colspan="13">
            <strong>Catatan BEM:</strong> <?= htmlspecialchars($row['catatan_bem']) ?><br>
            <strong>Catatan KMHS:</strong> <?= htmlspecialchars($row['catatan_kemahasiswaan']) ?>
        </td>
    </tr>
<?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
function getColor($status) {
    if ($status === 'Valid') return 'success';
    if ($status === 'Invalid') return 'danger';
    if ($status === 'Pending') return 'secondary';
    return 'light';
}
?>
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
