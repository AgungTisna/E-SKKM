<?php
session_start();
include '../../../koneksi.php';
// Cek role login
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../../index.php';</script>";
    exit;
}

$query = "SELECT * FROM berkas_internal WHERE kategori_kegiatan = 'Kegiatan Wajib'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kegiatan Wajib</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../navbar.php'; ?>

<div class="container py-4">

    <h2>Data Kegiatan Wajib</h2>
    <a href="../detail_berkas_internal.php" class="btn btn-secondary mb-3">Kembali</a>
    <a href="upload_kegiatan_wajib.php" class="btn btn-success mb-3">Upload Data Excel</a>
    <a href="download_template_kegiatan_wajib.php" class="btn btn-success mb-3">📥 Download Template</a>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama Kegiatan</th>
                <th>Partisipasi</th>
                <th>Tanggal Kegiatan</th>
                <th>Nomor Sertifikat</th>
                <th>Tanggal Dikeluarkan</th>
            </tr>
        </thead>
        <tbody>
        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nim']) ?></td>
                <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                <td><?= htmlspecialchars($row['partisipasi']) ?></td>
                <td><?= htmlspecialchars($row['tanggal_kegiatan']) ?></td>
                <td><?= htmlspecialchars($row['nomor_sertifikat_internal']) ?></td>
                <td><?= htmlspecialchars($row['tanggal_dikeluarkan']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
