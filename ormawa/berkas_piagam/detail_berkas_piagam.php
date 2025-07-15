<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php'; // Tambahkan PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Ormawa') {
    header("Location: ../index.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Ambil id_ormawa
$query_ormawa = "SELECT id_ormawa FROM user_detail_ormawa WHERE id_user = ?";
$stmt_ormawa = $conn->prepare($query_ormawa);
$stmt_ormawa->bind_param("i", $id_user);
$stmt_ormawa->execute();
$result_ormawa = $stmt_ormawa->get_result();
$row_ormawa = $result_ormawa->fetch_assoc();
$id_ormawa = $row_ormawa['id_ormawa'] ?? null;

if (!$id_ormawa) {
    die("❌ Gagal mendapatkan ID Ormawa.");
}

// ✅ Jika tombol download template ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_template'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->fromArray([
        ['Nama Kegiatan', 'Nama Penerima', 'Tanggal Kegiatan (YYYY-MM-DD)', 'Keterangan']
    ], null, 'A1');
    $sheet->getStyle('C2:C5')->getNumberFormat()->setFormatCode('yyyy-mm-dd');


    $filename = 'Template_Pengajuan_Piagam.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit();
}

// ✅ Jika tombol export semua ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_semua'])) {
    $stmt_export = $conn->prepare("SELECT nama_kegiatan, nama_penerima, tanggal_kegiatan, nomor_sertifikat_piagam, keterangan, tanggal_pengajuan, tanggal_dikeluarkan FROM berkas_piagam WHERE id_ormawa = ? ORDER BY tanggal_pengajuan DESC");
    $stmt_export->bind_param("i", $id_ormawa);
    $stmt_export->execute();
    $result_export = $stmt_export->get_result();

    if ($result_export->num_rows === 0) {
        echo "<script>alert('Tidak ada data untuk diekspor.'); window.history.back();</script>";
        exit;
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = ['No', 'Nama Kegiatan', 'Nama Penerima', 'Tanggal Kegiatan', 'Nomor Sertifikat', 'Keterangan', 'Tanggal Pengajuan', 'Tanggal Dikeluarkan'];
    $sheet->fromArray([$headers], null, 'A1');
    
    $no = 1;
    $rowNum = 2;
    while ($row = $result_export->fetch_assoc()) {
        $sheet->fromArray([
            $no++,
            $row['nama_kegiatan'],
            $row['nama_penerima'],
            $row['tanggal_kegiatan'],
            $row['nomor_sertifikat_piagam'],
            $row['keterangan'],
            $row['tanggal_pengajuan'],
            $row['tanggal_dikeluarkan']
        ], null, "A$rowNum");
        $rowNum++;
    }

    $filename = 'Data_Piagam_Ormawa.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit();
}

// ✅ Ambil data untuk ditampilkan di halaman
$query = "SELECT nama_kegiatan, nama_penerima, tanggal_kegiatan, nomor_sertifikat_piagam, keterangan,
                 tanggal_pengajuan, tanggal_dikeluarkan
          FROM berkas_piagam
          WHERE id_ormawa = ?
          ORDER BY tanggal_pengajuan DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("❌ Query Error: " . $conn->error);
}
$stmt->bind_param("i", $id_ormawa);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Berkas Piagam</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-4">
    <h3 class="text-center mb-4">Detail Berkas Piagam</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">← Kembali</a>

    <div class="d-flex justify-content-between mb-3">
        <a href="upload_batch_berkas_piagam.php" class="btn btn-primary">Pengajuan Piagam</a>
        <form method="POST" class="d-flex gap-2">
            <button type="submit" name="download_template" class="btn btn-success">📥 Download Template Excel</button>
            <button type="submit" name="export_semua" class="btn btn-warning">⬇️ Export Semua Data</button>
        </form>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>Nama Kegiatan</th>
                <th>Nama Penerima</th>
                <th>Tanggal Kegiatan</th>
                <th>Nomor Sertifikat</th>
                <th>Keterangan</th>
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
                        <td>{$row['nama_kegiatan']}</td>
                        <td>{$row['nama_penerima']}</td>
                        <td>{$row['tanggal_kegiatan']}</td>
                        <td>{$row['nomor_sertifikat_piagam']}</td>
                        <td>{$row['keterangan']}</td>
                        <td>{$row['tanggal_pengajuan']}</td>
                        <td>{$row['tanggal_dikeluarkan']}</td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='8' class='text-center'>Belum ada berkas piagam yang diajukan.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
