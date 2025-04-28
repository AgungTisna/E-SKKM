<?php
session_start();
include '../../../koneksi.php';
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Cek role login
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../../index.php';</script>";
    exit;
}

// Fungsi ambil semua data kegiatan
function getAllKegiatan($conn, $onlyEmpty = false) {
    $sql = "
        SELECT 
            bb.id_berkas_bem AS id,
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
    ";

    if ($onlyEmpty) {
        $sql .= " WHERE bb.nomor_sertifikat_internal IS NULL 
                   OR bb.nomor_sertifikat_internal = '' 
                   OR bb.nomor_sertifikat_internal = '0'";
    }

    $sql .= " ORDER BY bb.id_berkas_bem ASC";
    return $conn->query($sql);
}

// Handle ekspor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_belum'])) {
    $result = getAllKegiatan($conn, true);

    if (!$result || $result->num_rows === 0) {
        echo "<script>alert('Tidak ada data yang belum memiliki nomor sertifikat.'); window.history.back();</script>";
        exit;
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Belum Sertifikat");

    $headers = ['ID Berkas', 'NIM', 'Nama Mahasiswa', 'Nama Kegiatan', 'Partisipasi', 'Kategori', 'Tingkat', 'Poin SKKM', 'Tanggal Kegiatan', 'Tanggal Pengajuan','Nomor Sertifikat', 'Tanggal Dikeluarkan'];
    $sheet->fromArray([$headers], null, 'A1');

    $rowNum = 2;
    while ($row = $result->fetch_assoc()) {
        $sheet->fromArray([
            $row['id'],
            $row['nim'],
            $row['nama_mahasiswa'],
            $row['nama_kegiatan'],
            $row['partisipasi'],
            $row['kategori_kegiatan'],
            $row['tingkat'],
            $row['poin_skkm'],
            $row['tanggal_kegiatan'],
            $row['tanggal_pengajuan'],
            $row['nomor_sertifikat_internal'],
            $row['tanggal_dikeluarkan']
        ], null, "A$rowNum");
        $rowNum++;
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Kegiatan_BEM_Belum_Sertifikat.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

$result_kegiatan = getAllKegiatan($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Semua Kegiatan BEM - Kemahasiswaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">📋 Detail Semua Kegiatan Mahasiswa (BEM)</h2>

    <a href="../detail_berkas_internal.php" class="btn btn-secondary mb-3">← Kembali</a>

    <form method="POST" class="mb-3">
        <button type="submit" name="download_belum" class="btn btn-warning">⚠️ Download Belum Ada Nomor Sertifikat</button>
    </form>
    <a href="upload_kegiatan_bem.php" class="btn btn-primary mb-3">✏️ Update Data Sertifikat</a>

    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari NIM, Nama Mahasiswa, atau Nama Kegiatan...">
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
        <?php if ($result_kegiatan && $result_kegiatan->num_rows > 0): ?>
            <?php $no = 1; while ($row = $result_kegiatan->fetch_assoc()): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nim']) ?></td>
                    <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                    <td><?= htmlspecialchars($row['partisipasi']) ?></td>
                    <td><?= htmlspecialchars($row['kategori_kegiatan']) ?></td>
                    <td><?= htmlspecialchars($row['tingkat']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['poin_skkm']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_kegiatan']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                    <td><?= htmlspecialchars($row['nomor_sertifikat_internal']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_dikeluarkan']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="12" class="text-center">Tidak ada data kegiatan ditemukan.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<script>
    const input = document.getElementById("searchInput");
    input.addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll("tbody tr");
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>

</body>
</html>
