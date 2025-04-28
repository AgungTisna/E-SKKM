<?php
session_start();
include '../../../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../index.php';</script>";
    exit();
}

if (!isset($_SESSION['preview_data']) || count($_SESSION['preview_data']) === 0) {
    echo "<script>alert('Tidak ada data yang bisa dikonfirmasi.'); window.location.href='upload_kegiatan_wajib.php';</script>";
    exit();
}

$dataValid = $_SESSION['preview_data'];
$id_kemahasiswaan = $_SESSION['id_user'];

// Dapatkan nama mahasiswa berdasarkan NIM (sekali saja, simpan dalam cache array)
$namaMahasiswaMap = [];
$uniqueNims = array_unique(array_column($dataValid, 'nim'));
foreach ($uniqueNims as $nim) {
    $result = $conn->query("SELECT nama FROM user u 
        JOIN user_detail_mahasiswa m ON u.id_user = m.id_user 
        WHERE m.nim = '$nim' LIMIT 1");
    $namaMahasiswaMap[$nim] = ($result && $result->num_rows > 0) 
        ? $result->fetch_assoc()['nama'] 
        : '-';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_upload'])) {
    foreach ($dataValid as $row) {
        $stmt = $conn->prepare("
            INSERT INTO berkas_kemahasiswaan 
            (id_kemahasiswaan, nim, nama_kegiatan, partisipasi, tanggal_kegiatan, kategori_kegiatan, tingkat, poin_skkm)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issssssi",
            $id_kemahasiswaan, $row['nim'], $row['nama_kegiatan'],
            $row['partisipasi'], $row['tanggal_kegiatan'], $row['kategori'],
            $row['tingkat'], $row['poin']
        );
        $stmt->execute();
    }

    unset($_SESSION['preview_data']);
    echo "<script>alert('Data berhasil disimpan ke database!'); window.location.href='detail_kegiatan_wajib.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Konfirmasi Data Kegiatan Wajib</h2>
    <form method="POST">
        <a href="upload_kegiatan_wajib.php" class="btn btn-secondary mb-3">🔙 Batal</a>
        <button type="submit" name="confirm_upload" class="btn btn-success mb-3 float-end">✅ Simpan ke Database</button>

        <table class="table table-bordered table-striped">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Nama Kegiatan</th>
                    <th>Partisipasi</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Kategori</th>
                    <th>Tingkat</th>
                    <th>Poin</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach ($dataValid as $row) {
                    $nim = $row['nim'];
                    $nama = $namaMahasiswaMap[$nim] ?? '-';

                    echo "<tr>";
                    echo "<td class='text-center'>{$no}</td>";
                    echo "<td>" . htmlspecialchars($nim) . "</td>";
                    echo "<td>" . htmlspecialchars($nama) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_kegiatan']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['partisipasi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tanggal_kegiatan']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['kategori']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tingkat']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['poin']) . "</td>";
                    echo "</tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </form>
</div>
</body>
</html>

<?php $conn->close(); ?>
