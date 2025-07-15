<?php
session_start();
require_once('../koneksi.php');

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Mahasiswa') {
    header("Location: ../index.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$nim     = $_SESSION['nim'];
$role    = $_SESSION['role'];

// Ambil data nama dan nim dari user_detail_mahasiswa
$query = "
    SELECT u.nama
    FROM user u
    JOIN user_detail_mahasiswa udm ON u.id_user = udm.id_user
    WHERE u.id_user = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>alert('Data pengguna tidak ditemukan.'); window.location.href='../index.php';</script>";
    exit;
}

// Ambil data minimal poin dari tabel_ketentuan_skkm
$data_poin = [];
$query_ketentuan = "SELECT kategori_kegiatan, minimal_poin FROM tabel_ketentuan_skkm";
$res_ketentuan = $conn->query($query_ketentuan);
while ($row = $res_ketentuan->fetch_assoc()) {
    $data_poin[$row['kategori_kegiatan']] = [
        'poin' => 0,
        'minimal' => $row['minimal_poin'],
        'detail' => []
    ];
}

// Ambil data dari berkas_internal
$query = "
    SELECT kategori_kegiatan, nama_kegiatan, partisipasi, tingkat, poin_skkm, tanggal_kegiatan, nomor_sertifikat_internal
    FROM berkas_internal
    WHERE nim = ?
    ORDER BY kategori_kegiatan, tanggal_pengajuan DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nim);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $kategori = $row['kategori_kegiatan'];
    $punyaSertifikat = !empty($row['nomor_sertifikat_internal']) && $row['nomor_sertifikat_internal'] !== '0';

    if (!isset($data_poin[$kategori])) {
        $data_poin[$kategori] = [
            'poin' => 0,
            'minimal' => 0,
            'detail' => []
        ];
    }

    $data_poin[$kategori]['detail'][] = $row;

    if ($kategori === 'Kegiatan Wajib' || $punyaSertifikat) {
        $data_poin[$kategori]['poin'] += $row['poin_skkm'];
    }
}

// Tambah dari arsip_skkm
$query_arsip = "
    SELECT kategori_kegiatan, nama_kegiatan, partisipasi, tingkat, poin_skkm, tanggal_kegiatan, nomor_sertifikat_internal
    FROM arsip_skkm
    WHERE nim = ? AND nomor_sertifikat_internal IS NOT NULL AND nomor_sertifikat_internal != ''
";

$stmt_arsip = $conn->prepare($query_arsip);
$stmt_arsip->bind_param("s", $nim);
$stmt_arsip->execute();
$result_arsip = $stmt_arsip->get_result();

while ($row = $result_arsip->fetch_assoc()) {
    $kategori = $row['kategori_kegiatan'];
    $punyaSertifikat = !empty($row['nomor_sertifikat_internal']) && $row['nomor_sertifikat_internal'] !== '0';

    if (!isset($data_poin[$kategori])) {
        $data_poin[$kategori] = [
            'poin' => 0,
            'minimal' => 0,
            'detail' => []
        ];
    }

    $data_poin[$kategori]['detail'][] = $row;

    if ($kategori === 'Kegiatan Wajib' || $punyaSertifikat) {
        $data_poin[$kategori]['poin'] += $row['poin_skkm'];
    }
}

// Tambah dari pengajuan_skkm yang sudah valid
$query_pengajuan = "
    SELECT kategori_kegiatan, nama_kegiatan, partisipasi, tingkat, poin_skkm, tanggal_verifikasi_kemahasiswaan AS tanggal_kegiatan
    FROM pengajuan_skkm
    WHERE nim = ?
      AND status_verifikasi_bem = 'Valid'
      AND status_verifikasi_kemahasiswaan = 'Valid'
";
$stmt_pengajuan = $conn->prepare($query_pengajuan);
$stmt_pengajuan->bind_param("s", $nim);
$stmt_pengajuan->execute();
$result_pengajuan = $stmt_pengajuan->get_result();

while ($row = $result_pengajuan->fetch_assoc()) {
    $kategori = $row['kategori_kegiatan'];
    if (!isset($data_poin[$kategori])) {
        $data_poin[$kategori] = [
            'poin' => 0,
            'minimal' => 0,
            'detail' => []
        ];
    }

    $data_poin[$kategori]['poin'] += $row['poin_skkm'];
    $data_poin[$kategori]['detail'][] = $row;
}





// Urutkan agar 'Kegiatan Wajib' muncul pertama
uksort($data_poin, function ($a, $b) {
    if ($a === 'Kegiatan Wajib') return -1;
    if ($b === 'Kegiatan Wajib') return 1;
    return strcmp($a, $b);
});
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
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

    .logo img {
        width: 50px;
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
            <a href="index.php"><img src="../asset/img/itb.png" alt="Logo ITB STIKOM"></a>
        </div>
        <div class="navbar-title">E-SKKM</div>
    </div>

    <div class="profile-container" onclick="toggleDropdown()">
        <div class="profile-icon"><img src="../asset/img/profile.png" alt="Profile"></div>
        <div id="profileDropdown" class="dropdown">
            <div class="name"><?= htmlspecialchars($user['nama']) ?></div>
            <div><?= htmlspecialchars($nim) ?></div>
            <a href="profile/profile_mahasiswa.php">Profil</a>
            <a href="/e-skkm/logout.php">Logout</a>
        </div>
    </div>
</div>
<div class="container py-5">
    <h2 class="mb-4 text-primary">Dashboard Mahasiswa</h2>
    <div class="d-flex justify-content-between mb-4">
    <a href="pengajuan/detail_pengajuan.php" class="btn btn-primary">
        ➕ Ajukan SKKM Baru
    </a>
</div>
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light text-center">
                    <tr>
                        <th>Kategori Kegiatan</th>
                        <th>Poin Saat Ini</th>
                        <th>Minimal Poin</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = 1; foreach ($data_poin as $kategori => $info): ?>
                        <?php if (trim($kategori) === "") continue; ?>
                    <tr>
                        <td><?= htmlspecialchars($kategori) ?></td>
                        <td class="text-center"><?= $info['poin'] ?></td>
                        <td class="text-center"><?= $info['minimal'] ?></td>
                        <td class="text-center">
                            <span class="badge <?= $info['poin'] >= $info['minimal'] ? 'bg-success' : 'bg-danger' ?>">
                                <?= $info['poin'] >= $info['minimal'] ? 'Terpenuhi' : 'Belum Terpenuhi' ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#detail<?= $index ?>">Lihat Detail</button>
                        </td>
                    </tr>
                    <tr class="collapse" id="detail<?= $index ?>">
                        <td colspan="5">
                            <?php if (!empty($info['detail'])): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-secondary text-center">
                                        <tr>
                                            <th>Nama Kegiatan</th>
                                            <th>Partisipasi</th>
                                            <th>Tingkat</th>
                                            <th>Poin</th>
                                            <th>Tanggal Kegiatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($info['detail'] as $detail): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($detail['nama_kegiatan']) ?></td>
                                            <td><?= htmlspecialchars($detail['partisipasi']) ?></td>
                                            <td><?= htmlspecialchars($detail['tingkat']) ?></td>
                                            <td class="text-center"><?= $detail['poin_skkm'] ?></td>
                                            <td class="text-center"><?= htmlspecialchars($detail['tanggal_kegiatan']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                                <em>Belum ada kegiatan</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php $index++; endforeach; ?>
                </tbody>
            </table>
            <a href="export_poin_mahasiswa.php" class="btn btn-danger" target="_blank">
                📄 Export Rekap Poin
            </a>
            <div class="text-muted small mt-3">
                * Poin hanya dihitung jika sertifikat valid, kecuali kegiatan wajib yang tetap dihitung.<br>
                * Semua kegiatan wajib tetap ditampilkan walau belum memiliki sertifikat.
            </div>
        </div>
    </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
