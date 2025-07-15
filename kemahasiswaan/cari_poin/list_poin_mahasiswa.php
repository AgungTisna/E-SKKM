<?php
session_start();
require_once('../../koneksi.php'); // Pastikan path sudah sesuai

// Cek apakah ada parameter pencarian
$nim_cari = isset($_GET['nim']) ? trim($_GET['nim']) : '';

// Ambil semua NIM dari sumber
$query = "
    SELECT DISTINCT nim FROM (
        SELECT nim FROM berkas_internal
        UNION
        SELECT nim FROM arsip_skkm
        UNION
        SELECT nim FROM pengajuan_skkm
    ) AS all_nim
";
$result = $conn->query($query);

// Inisialisasi array mahasiswa
$mahasiswa = [];

while ($row = $result->fetch_assoc()) {
    $nim = $row['nim'];

    // Cek apakah NIM ada di user_detail_mahasiswa
    $query_nama = "
        SELECT u.nama 
        FROM user_detail_mahasiswa udm
        JOIN user u ON udm.id_user = u.id_user
        WHERE udm.nim = ?
        LIMIT 1
    ";
    $stmt = $conn->prepare($query_nama);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $res_nama = $stmt->get_result();

    $nama = $res_nama->num_rows > 0 
        ? $res_nama->fetch_assoc()['nama'] 
        : 'Mahasiswa belum terdaftar di sistem';

    $mahasiswa[] = [
        'nim' => $nim,
        'nama' => $nama
    ];
}


// Ambil data poin dari semua sumber
$poin_mahasiswa = [];

// Fungsi untuk menambah poin
function tambah_poin(&$array, $nim, $poin) {
    if (isset($array[$nim])) {
        $array[$nim] += $poin;
    } else {
        $array[$nim] = $poin;
    }
}

// Ambil dari berkas_internal
$query_internal = "SELECT nim, SUM(poin_skkm) as total_poin FROM berkas_internal GROUP BY nim";
$res_internal = $conn->query($query_internal);
while ($row = $res_internal->fetch_assoc()) {
    tambah_poin($poin_mahasiswa, $row['nim'], $row['total_poin']);
}

// Ambil dari arsip_skkm
$query_arsip = "SELECT nim, SUM(poin_skkm) as total_poin FROM arsip_skkm GROUP BY nim";
$res_arsip = $conn->query($query_arsip);
if (!$res_arsip) {
    die("Query arsip_skkm gagal: " . $conn->error);
}
while ($row = $res_arsip->fetch_assoc()) {
    tambah_poin($poin_mahasiswa, $row['nim'], $row['total_poin']);
}


// Ambil dari pengajuan_skkm (hanya yang valid)
$query_pengajuan = "SELECT nim, SUM(poin_skkm) as total_poin FROM pengajuan_skkm WHERE status_verifikasi_kemahasiswaan = 'Valid' GROUP BY nim";
$res_pengajuan = $conn->query($query_pengajuan);
while ($row = $res_pengajuan->fetch_assoc()) {
    tambah_poin($poin_mahasiswa, $row['nim'], $row['total_poin']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Poin Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php include '../navbar.php'; ?>
<body>
<div class="container mt-5">
    <h3>Daftar Poin Mahasiswa</h3>
    <a href="../index.php" class="btn btn-secondary mt-3">Kembali</a>

    <!-- Form Pencarian -->
    <form method="GET" action="list_poin_mahasiswa.php" class="row g-3 mb-4 mt-3">
        <div class="col-auto">
            <input type="text" name="nim" class="form-control" placeholder="Cari NIM..." value="<?php echo htmlspecialchars($nim_cari); ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary mb-3">Cari</button>
        </div>
    </form>

    <!-- Tabel Data -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Total Poin</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
    <?php
    $no = 1;
    foreach ($mahasiswa as $mhs) :
        if ($nim_cari && stripos($mhs['nim'], $nim_cari) === false) {
            continue;
        }
        $nim = $mhs['nim'];
        $total_poin = isset($poin_mahasiswa[$nim]) ? $poin_mahasiswa[$nim] : 0;
    ?>
    <tr>
        <td><?php echo $no++; ?></td>
        <td><?php echo htmlspecialchars($nim); ?></td>
        <td><?php echo htmlspecialchars($mhs['nama']); ?></td>
        <td><?php echo $total_poin; ?></td>
        <td>
            <a href="detail_mahasiswa.php?nim=<?php echo urlencode($nim); ?>" class="btn btn-sm btn-info">Lihat Detail</a>
        </td>
    </tr>
    <?php endforeach; ?>
        </tbody>
    </table>

</div>
</body>
</html>
