<?php
session_start();
require_once('../../koneksi.php');
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan'); window.location.href='../../index.php';</script>";
    exit();
}
$id_user = $_SESSION['id_user'];
$user_kmhs = ['nama' => 'Kemahasiswaan'];

$stmt = $conn->prepare("SELECT nama FROM user WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $user_kmhs['nama'] = $row['nama'];
}


if ($id_user) {
    $stmt = $conn->prepare("SELECT nama FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $user_kmhs = $stmt->get_result()->fetch_assoc();
}
$role = $_SESSION['role'];

if (!isset($_GET['nim']) || empty($_GET['nim'])) {
    echo "<script>alert('NIM tidak ditemukan.'); window.location.href='list_poin_mahasiswa.php';</script>";
    exit();
}

$nim = $_GET['nim'];
$nama_mahasiswa = 'Mahasiswa belum terdaftar di sistem';

// Coba ambil nama dari user_detail_mahasiswa + user
$query_user = "
    SELECT u.nama
    FROM user_detail_mahasiswa udm
    JOIN user u ON udm.id_user = u.id_user
    WHERE udm.nim = ?
    LIMIT 1
";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("s", $nim);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user && $row = $result_user->fetch_assoc()) {
    $nama_mahasiswa = $row['nama'];
}


// Ambil semua kategori
$kategori_list = [];
$query_kategori = "SELECT kategori_kegiatan, minimal_poin FROM tabel_ketentuan_skkm";
$result_kategori = $conn->query($query_kategori);
while ($row = $result_kategori->fetch_assoc()) {
    $kategori_list[] = $row['kategori_kegiatan'];
    $data_kategori[$row['kategori_kegiatan']] = [
        'poin_total' => 0,
        'minimal' => $row['minimal_poin'],
        'kegiatan' => []
    ];
}


// Gabungkan data dari semua sumber
$sources = [
    ['table' => 'berkas_internal', 'condition' => ''],
    ['table' => 'arsip_skkm', 'condition' => ''],
    ['table' => 'pengajuan_skkm', 'condition' => "AND status_verifikasi_kemahasiswaan = 'Valid'"]
];

foreach ($sources as $src) {
    $table = $src['table'];
    $extra = $src['condition'];

    $is_pengajuan = $table === 'pengajuan_skkm';
    $select_field = $is_pengajuan
        ? "tanggal_verifikasi_kemahasiswaan AS tanggal_kegiatan"
        : "tanggal_kegiatan";

    $query = "
        SELECT kategori_kegiatan, nama_kegiatan, partisipasi, tingkat, poin_skkm, $select_field
        FROM $table
        WHERE nim = ? $extra
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Query gagal dipersiapkan untuk tabel: $table. Error: " . $conn->error);
    }

    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $kategori = $row['kategori_kegiatan'];
        if (!isset($data_kategori[$kategori])) {
            $data_kategori[$kategori] = [
                'poin_total' => 0,
                'minimal' => 0,
                'kegiatan' => []
            ];
        }
        $data_kategori[$kategori]['poin_total'] += $row['poin_skkm'];
        $data_kategori[$kategori]['kegiatan'][] = $row;
    }
}
// Hitung total semua poin SKKM mahasiswa
$total_poin_mahasiswa = 0;
foreach ($data_kategori as $data) {
    $total_poin_mahasiswa += $data['poin_total'];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Mahasiswa - SKKM</title>
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
    h3{
        text-align: center;
    }
    .badge {
        font-size: 0.85em;
        padding: 6px 12px;
    }

</style>
</head>
<body>
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
            <div class="name"><?= htmlspecialchars($user_kmhs['nama']) ?></div>
            <div class="role"><?= htmlspecialchars($role) ?></div>
            <a href="../profile/profile_kemahasiswaan.php">Profile</a>
            <a href="/e-skkm/logout.php">Logout</a>
        </div>
    </div>
</div>
<div class="container mt-5">
    <h3 class="mb-4 text-center">Detail SKKM Mahasiswa</h3>
    <p><strong>Nama:</strong> <?= htmlspecialchars($nama_mahasiswa); ?></p>
    <p><strong>NIM:</strong> <?php echo htmlspecialchars($nim); ?></p>
    <p><strong>Total Semua Poin SKKM:</strong> <?php echo $total_poin_mahasiswa; ?> Poin</p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori Kegiatan</th>
                <th>Total Poin SKKM</th>
                <th>Minimal</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php $no = 1;
            foreach ($data_kategori as $kategori => $data):  ?>
                <?php if (trim($kategori) === '') continue; ?>
                <tr>
    <td><?= $no++; ?></td>
    <td><?= htmlspecialchars($kategori); ?></td>
    <td><?= $data['poin_total']; ?> Poin</td>
    <td><?= $data['minimal']; ?> Poin</td>
    <td>
        <?php if ($data['poin_total'] >= $data['minimal']): ?>
            <span class="badge bg-success rounded-pill">Tercapai</span>
        <?php else: ?>
            <span class="badge bg-danger rounded-pill">Belum Tercapai</span>
        <?php endif; ?>
    </td>
    <td>
        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapse_<?= md5($kategori); ?>" aria-expanded="false"
                aria-controls="collapse_<?= md5($kategori); ?>">
            Detail
        </button>
    </td>
</tr>

<tr class="collapse" id="collapse_<?= md5($kategori); ?>">
    <td colspan="6"> <!-- Gunakan colspan 6 agar sejajar -->
        <div class="p-2">
            <?php if (!empty($data['kegiatan'])): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Kegiatan</th>
                                <th>Partisipasi</th>
                                <th>Tingkat</th>
                                <th>Poin SKKM</th>
                                <th>Tanggal Kegiatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['kegiatan'] as $kegiatan): ?>
                            <tr>
                                <td><?= htmlspecialchars($kegiatan['nama_kegiatan']); ?></td>
                                <td><?= htmlspecialchars($kegiatan['partisipasi']); ?></td>
                                <td><?= htmlspecialchars($kegiatan['tingkat']); ?></td>
                                <td><?= $kegiatan['poin_skkm']; ?></td>
                                <td><?= date('d-m-Y', strtotime($kegiatan['tanggal_kegiatan'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-muted">Belum ada kegiatan di kategori ini.</div>
            <?php endif; ?>
        </div>
    </td>
</tr>

            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="list_poin_mahasiswa.php" class="btn btn-secondary mt-3">Kembali</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
