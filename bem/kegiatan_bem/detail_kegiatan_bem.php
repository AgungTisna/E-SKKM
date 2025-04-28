<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Cek role login
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'BEM') {
    echo "<script>alert('Anda harus login sebagai BEM!'); window.location.href='../../index.php';</script>";
    exit;
}

// Fungsi ambil data berkas BEM
function getDataBerkasBEM($conn) {
    $sql = "
        SELECT 
            bb.id_berkas_bem AS id,
            bb.id_bem,
            bb.nim,
            u.nama AS nama_mahasiswa,
            bb.nama_kegiatan,
            bb.partisipasi,
            bb.kategori_kegiatan,
            bb.tingkat,
            bb.poin_skkm,
            bb.nomor_sertifikat_internal,
            bb.tanggal_kegiatan,
            bb.tanggal_pengajuan,
            bb.tanggal_dikeluarkan
        FROM berkas_bem bb
        LEFT JOIN user_detail_mahasiswa udm ON bb.nim = udm.nim
        LEFT JOIN user u ON udm.id_user = u.id_user
        ORDER BY bb.id_berkas_bem ASC
    ";

    return $conn->query($sql);
}

// ✅ Ambil data
$result_berkas = getDataBerkasBEM($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Berkas BEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">📋 Detail Berkas Kegiatan BEM</h2>

    <a href="../index.php" class="btn btn-secondary mb-3">← Kembali</a>
    <a href="pengajuan_kegiatan_bem.php" class="btn btn-primary mb-3">➕ Pengajuan Poin SKKM</a>
    <a href="download_template_excel.php" class="btn btn-success mb-3">⬇️ Download Template Excel</a>

    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari NIM, Nama Mahasiswa, Kegiatan...">
    </div>

    <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Nama Kegiatan</th>
                <th>Partisipasi</th>
                <th>Kategori</th>
                <th>Tingkat</th>
                <th>Poin SKKM</th>
                <th>Tanggal Kegiatan</th>
                <th>Tanggal Pengajuan</th>
                <th>Nomor Sertifikat</th>
                <th>Tanggal Dikeluarkan</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result_berkas && $result_berkas->num_rows > 0):
            $no = 1;
            while ($row = $result_berkas->fetch_assoc()):
        ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nim']) ?></td>
                <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                <td><?= htmlspecialchars($row['partisipasi']) ?></td>
                <td><?= htmlspecialchars($row['kategori_kegiatan']) ?></td>
                <td><?= htmlspecialchars($row['tingkat']) ?></td>
                <td class="text-center"><?= (int)$row['poin_skkm'] ?></td>
                <td class="text-center"><?= htmlspecialchars($row['tanggal_kegiatan']) ?></td>
                <td class="text-center"><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                <td class="text-center"><?= !empty($row['nomor_sertifikat_internal']) ? htmlspecialchars($row['nomor_sertifikat_internal']) : '<em>Belum Diisi</em>' ?></td>
                <td class="text-center"><?= $row['tanggal_dikeluarkan'] === '0000-00-00' || empty($row['tanggal_dikeluarkan']) ? '<em>Belum Dikeluarkan</em>' : htmlspecialchars($row['tanggal_dikeluarkan']) ?></td>
            </tr>
        <?php
            endwhile;
        else:
            echo "<tr><td colspan='13' class='text-center'>Tidak ada data ditemukan.</td></tr>";
        endif;
        ?>
        </tbody>
    </table>
    </div>
</div>

<script>
// Live search
document.getElementById('searchInput').addEventListener('input', function () {
    const keyword = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(keyword) ? '' : 'none';
    });
});
</script>

</body>
</html>

<?php $conn->close(); ?>
