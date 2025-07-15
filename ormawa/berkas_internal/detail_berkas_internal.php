<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php'; // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Ormawa') {
    header("Location: ../index.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Ambil ID Ormawa berdasarkan user login
$stmt_ormawa = $conn->prepare("SELECT id_ormawa FROM user_detail_ormawa WHERE id_user = ?");
$stmt_ormawa->bind_param("i", $id_user);
$stmt_ormawa->execute();
$result_ormawa = $stmt_ormawa->get_result();
$id_ormawa = $result_ormawa->fetch_assoc()['id_ormawa'] ?? null;

if (!$id_ormawa) {
    die("❌ Gagal menemukan ID Ormawa.");
}

// ✅ Download Template
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_template'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = ['NIM', 'Nama Mahasiswa', 'Nama Kegiatan', 'Partisipasi', 'Tanggal Kegiatan (YYYY-MM-DD)'];
    $sheet->fromArray([$headers], null, 'A1');
    $sheet->getStyle('E2:E5')->getNumberFormat()->setFormatCode('yyyy-mm-dd');


    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"Template_Pengajuan_E-SKKM.xlsx\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

// ✅ Export Semua Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_semua'])) {
    $stmt_export = $conn->prepare("
        SELECT b.nim, u.nama AS nama_mahasiswa, b.nama_kegiatan, b.partisipasi,
               b.kategori_kegiatan, b.tingkat, b.poin_skkm,
               b.nomor_sertifikat_internal, b.tanggal_kegiatan,
               b.tanggal_pengajuan, b.tanggal_dikeluarkan
        FROM berkas_internal b
        JOIN user_detail_mahasiswa m ON b.nim = m.nim
        JOIN user u ON m.id_user = u.id_user
        WHERE b.id_ormawa = ?
        ORDER BY b.tanggal_pengajuan DESC
    ");
    $stmt_export->bind_param("i", $id_ormawa);
    $stmt_export->execute();
    $result_export = $stmt_export->get_result();

    if ($result_export->num_rows === 0) {
        echo "<script>alert('Tidak ada data untuk diekspor.'); window.history.back();</script>";
        exit;
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $headers = ['No', 'NIM', 'Nama Mahasiswa', 'Nama Kegiatan', 'Partisipasi', 'Kategori Kegiatan', 'Tingkat', 'Poin SKKM', 'Nomor Sertifikat', 'Tanggal Kegiatan', 'Tanggal Pengajuan', 'Tanggal Dikeluarkan'];
    $sheet->fromArray([$headers], null, 'A1');

    $rowNum = 2;
    $no = 1;
    while ($row = $result_export->fetch_assoc()) {
        $sheet->fromArray([
            $no++,
            $row['nim'],
            $row['nama_mahasiswa'],
            $row['nama_kegiatan'],
            $row['partisipasi'],
            $row['kategori_kegiatan'],
            $row['tingkat'],
            $row['poin_skkm'],
            $row['nomor_sertifikat_internal'],
            $row['tanggal_kegiatan'],
            $row['tanggal_pengajuan'],
            $row['tanggal_dikeluarkan']
        ], null, "A$rowNum");
        $rowNum++;
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"Data_Berkas_Internal_Ormawa.xlsx\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

// ✅ Ambil data untuk ditampilkan di halaman
$query = "
    SELECT b.nim, u.nama AS nama_mahasiswa, b.nama_kegiatan, b.partisipasi,
           b.kategori_kegiatan, b.tingkat, b.poin_skkm,
           b.nomor_sertifikat_internal, b.tanggal_pengajuan,
           b.tanggal_dikeluarkan, b.tanggal_kegiatan
    FROM berkas_internal b
    JOIN user_detail_mahasiswa m ON b.nim = m.nim
    JOIN user u ON m.id_user = u.id_user
    WHERE b.id_ormawa = ?
    ORDER BY b.nama_kegiatan ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_ormawa);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Berkas Internal - Ormawa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-4">
    <h3 class="text-center mb-4">Detail Berkas Internal</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="d-flex justify-content-between mb-3">
        <a href="upload_batch_berkas_internal.php" class="btn btn-primary">Pengajuan E-SKKM</a>
        <form method="POST" class="d-flex gap-2">
            <button type="submit" name="download_template" class="btn btn-success">📥 Download Template Excel</button>
            <button type="submit" name="export_semua" class="btn btn-warning">⬇️ Export Semua Data</button>
        </form>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Nama Kegiatan</th>
                <th>Partisipasi</th>
                <th>Kategori Kegiatan</th>
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
        if ($result->num_rows > 0) {
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['nim']}</td>
                    <td>{$row['nama_mahasiswa']}</td>
                    <td>{$row['nama_kegiatan']}</td>
                    <td>{$row['partisipasi']}</td>
                    <td>{$row['kategori_kegiatan']}</td>
                    <td>{$row['tingkat']}</td>
                    <td>{$row['poin_skkm']}</td>
                    <td>{$row['nomor_sertifikat_internal']}</td>
                    <td>{$row['tanggal_kegiatan']}</td>
                    <td>{$row['tanggal_pengajuan']}</td>
                    <td>{$row['tanggal_dikeluarkan']}</td>
                </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='12' class='text-center'>Belum ada berkas yang diajukan</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
