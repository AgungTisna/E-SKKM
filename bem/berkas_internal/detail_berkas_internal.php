<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Validasi ID Ormawa
if (!isset($_GET['id_ormawa'])) {
    echo "<script>alert('ID Ormawa tidak ditemukan.'); window.history.back();</script>";
    exit;
}
$id_ormawa = (int)$_GET['id_ormawa'];

// Ambil nama ormawa
$stmt = $conn->prepare("SELECT nama_ormawa FROM user_detail_ormawa WHERE id_ormawa = ?");
$stmt->bind_param("i", $id_ormawa);
$stmt->execute();
$result_ormawa = $stmt->get_result();
$ormawa = $result_ormawa->fetch_assoc();

if (!$ormawa) {
    echo "<script>alert('Ormawa tidak ditemukan.'); window.history.back();</script>";
    exit;
}

// Fungsi ambil data internal
function getDataInternal($conn, $id_ormawa, $hanyaKosongPoin = false) {
    $sql = "
        SELECT  
            bi.id_berkas_internal AS id, 
            bi.nim, 
            u.nama AS nama_mahasiswa,
            bi.nama_kegiatan, 
            bi.partisipasi, 
            bi.kategori_kegiatan, 
            bi.tingkat, 
            bi.poin_skkm, 
            bi.nomor_sertifikat_internal, 
            bi.tanggal_pengajuan, 
            bi.tanggal_dikeluarkan,
            bi.tanggal_kegiatan,
            ubem.nama AS nama_bem
        FROM berkas_internal bi
        LEFT JOIN user_detail_mahasiswa m ON bi.nim = m.nim
        LEFT JOIN user u ON m.id_user = u.id_user
        LEFT JOIN user_detail_bem b ON bi.id_bem = b.id_user
        LEFT JOIN user ubem ON b.id_user = ubem.id_user
        WHERE bi.id_ormawa = ?
    ";

    if ($hanyaKosongPoin) {
        $sql .= " AND (bi.poin_skkm IS NULL OR bi.poin_skkm = 0)";
    }

    $sql .= " ORDER BY bi.id_berkas_internal ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_ormawa);
    $stmt->execute();
    return $stmt->get_result();
}

// ✅ Handle download Excel
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filterKosong = isset($_POST['export_belum_isi_poin']);
    $result_berkas = getDataInternal($conn, $id_ormawa, $filterKosong);

    if ($result_berkas->num_rows === 0) {
        echo "<script>alert('Tidak ada data untuk diekspor.'); window.history.back();</script>";
        exit;
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $headers = ['No', 'ID Berkas', 'NIM', 'Nama Mahasiswa', 'Nama Kegiatan', 'Partisipasi',
            'Kategori Kegiatan', 'Tingkat', 'Poin SKKM', 'Nomor Sertifikat', 'Tanggal Kegiatan',
            'Tanggal Pengajuan', 'Tanggal Dikeluarkan', 'Nama BEM'];
    $sheet->fromArray([$headers], null, 'A1');

    $rowNum = 2;
    $no = 1;
    while ($row = $result_berkas->fetch_assoc()) {
        $sheet->fromArray([
            $no++, $row['id'], $row['nim'], $row['nama_mahasiswa'] ?? 'Mahasiswa belum ditambahkan ke sistem',
            $row['nama_kegiatan'], $row['partisipasi'], $row['kategori_kegiatan'],
            $row['tingkat'], $row['poin_skkm'], $row['nomor_sertifikat_internal'],
            $row['tanggal_kegiatan'], $row['tanggal_pengajuan'], $row['tanggal_dikeluarkan'],
            $row['nama_bem']
        ], null, "A$rowNum");
        $rowNum++;
    }

    $filename = $filterKosong 
        ? "Data_Internal_Tanpa_Poin_SKKM_{$id_ormawa}.xlsx" 
        : "Data_Internal_Semua_{$id_ormawa}.xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

// ✅ Ambil data untuk ditampilkan
$result_berkas = getDataInternal($conn, $id_ormawa);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Berkas Internal - <?= htmlspecialchars($ormawa['nama_ormawa']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-5">
    <h2 class="text-center mb-4">Detail Berkas Internal - <?= htmlspecialchars($ormawa['nama_ormawa']) ?></h2>

    <a href="../index.php" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="d-flex justify-content-between mb-3">
        <a href="upload_batch_berkas_internal.php?id_ormawa=<?= urlencode($id_ormawa) ?>" class="btn btn-primary">Update Pengajuan E-SKKM</a>

        <form method="POST" class="d-flex gap-2">
            <button type="submit" name="export_semua" class="btn btn-success">⬇️ Export Semua</button>
            <button type="submit" name="export_belum_isi_poin" class="btn btn-warning">⚠️ Export Belum Isi Poin</button>
        </form>
    </div>

    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari NIM, Nama Mahasiswa, Kegiatan, BEM...">
    </div>

    <table class="table table-bordered">
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
                <th>Nomor Sertifikat</th>
                <th>Tanggal Kegiatan</th>
                <th>Tanggal Pengajuan</th>
                <th>Tanggal Dikeluarkan</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        $result_berkas->data_seek(0);
        while ($row = $result_berkas->fetch_assoc()): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nim']) ?></td>
                <td>
                    <?= $row['nama_mahasiswa'] 
                        ? htmlspecialchars($row['nama_mahasiswa']) 
                        : '<em class="text-danger">Mahasiswa belum ditambahkan ke sistem</em>' ?>
                </td>
                <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                <td><?= htmlspecialchars($row['partisipasi']) ?></td>
                <td><?= htmlspecialchars($row['kategori_kegiatan']) ?></td>
                <td><?= htmlspecialchars($row['tingkat']) ?></td>
                <td><?= htmlspecialchars($row['poin_skkm']) ?></td>
                <td><?= htmlspecialchars($row['nomor_sertifikat_internal']) ?></td>
                <td><?= htmlspecialchars($row['tanggal_kegiatan']) ?></td>
                <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                <td><?= htmlspecialchars($row['tanggal_dikeluarkan']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    document.getElementById('searchInput').addEventListener('input', function () {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(keyword) ? '' : 'none';
        });
    });
</script>

</body>
</html>

<?php $conn->close(); ?>
