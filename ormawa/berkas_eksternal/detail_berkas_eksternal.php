<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php'; // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// 🔐 Cek login dan role Ormawa
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Ormawa') {
    header("Location: ../index.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Ambil id_ormawa dari user
$stmt_ormawa = $conn->prepare("SELECT id_ormawa FROM user_detail_ormawa WHERE id_user = ?");
$stmt_ormawa->bind_param("i", $id_user);
$stmt_ormawa->execute();
$res_ormawa = $stmt_ormawa->get_result();
$id_ormawa = $res_ormawa->fetch_assoc()['id_ormawa'] ?? null;

if (!$id_ormawa) {
    die("❌ ID Ormawa tidak ditemukan.");
}

// ✅ Jika tombol download template ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_template'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header kolom
   $sheet->setCellValue('A1', 'Nama Kegiatan');
$sheet->setCellValue('B1', 'Nama Peserta');
$sheet->setCellValue('C1', 'Tanggal Kegiatan');
$sheet->setCellValue('D1', 'Keterangan');

// Set format kolom C (Tanggal Kegiatan) ke yyyy-mm-dd
$sheet->getStyle('C2:C10')->getNumberFormat()->setFormatCode('yyyy-mm-dd');


    $filename = 'Template_Eksternal_Ormawa.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit();
}

// ✅ Jika tombol export semua ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_semua'])) {
    $stmt = $conn->prepare("SELECT nama_kegiatan, nama_peserta, tanggal_kegiatan, nomor_sertifikat_eksternal, 
                                   tanggal_pengajuan, tanggal_dikeluarkan, keterangan
                            FROM berkas_eksternal
                            WHERE id_ormawa = ?
                            ORDER BY id_berkas_eksternal ASC");
    $stmt->bind_param("i", $id_ormawa);
    $stmt->execute();
    $result_export = $stmt->get_result();

    if ($result_export->num_rows === 0) {
        echo "<script>alert('Tidak ada data untuk diekspor.'); window.history.back();</script>";
        exit();
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = ['No', 'Nama Kegiatan', 'Nama Peserta', 'Tanggal Kegiatan', 'Nomor Sertifikat', 'Tanggal Pengajuan', 'Tanggal Dikeluarkan', 'Keterangan'];
    $sheet->fromArray([$headers], null, 'A1');

    $rowNumber = 2;
    $no = 1;
    while ($row = $result_export->fetch_assoc()) {
        $sheet->fromArray([
            $no++,
            $row['nama_kegiatan'],
            $row['nama_peserta'],
            $row['tanggal_kegiatan'],
            $row['nomor_sertifikat_eksternal'],
            $row['tanggal_pengajuan'],
            $row['tanggal_dikeluarkan'],
            $row['keterangan']
        ], null, "A$rowNumber");
        $rowNumber++;
    }

    $filename = 'Data_Eksternal_Ormawa.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit();
}

// ✅ Ambil data untuk ditampilkan di halaman
$query = "SELECT nama_kegiatan, nama_peserta, tanggal_kegiatan, nomor_sertifikat_eksternal, 
                 tanggal_pengajuan, tanggal_dikeluarkan, keterangan
          FROM berkas_eksternal
          WHERE id_ormawa = ?
          ORDER BY id_berkas_eksternal ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_ormawa);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Berkas Eksternal - Ormawa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-4">
    <h3 class="text-center mb-4">Detail Berkas Eksternal</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="d-flex justify-content-between mb-3">
        <a href="upload_batch_berkas_eksternal.php" class="btn btn-primary">Pengajuan E-SKKM Eksternal</a>
        <form method="POST" class="d-flex gap-2">
            <button type="submit" name="download_template" class="btn btn-success">📥 Download Template Excel</button>
            <button type="submit" name="export_semua" class="btn btn-warning">⬇️ Export Semua Data</button>
        </form>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Kegiatan</th>
                <th>Nama Peserta</th>
                <th>Tanggal Kegiatan</th>
                <th>Nomor Sertifikat</th>
                <th>Tanggal Pengajuan</th>
                <th>Tanggal Dikeluarkan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['nama_kegiatan']}</td>
                        <td>{$row['nama_peserta']}</td>
                        <td>{$row['tanggal_kegiatan']}</td>
                        <td>{$row['nomor_sertifikat_eksternal']}</td>
                        <td>{$row['tanggal_pengajuan']}</td>
                        <td>{$row['tanggal_dikeluarkan']}</td>
                        <td>{$row['keterangan']}</td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='8' class='text-center'>Belum ada berkas eksternal yang diajukan.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
