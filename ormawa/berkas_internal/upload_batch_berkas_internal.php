<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php'; // Load PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file_excel"])) {
    $file = $_FILES["file_excel"]["tmp_name"];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        if (empty($data) || count($data) < 2) {
            die("<script>alert('❌ File Excel kosong atau format tidak sesuai!'); window.location.href='upload_batch_berkas_internal.php';</script>");
        }

        $isFirstRow = true;
        $importedData = [];

        foreach ($data as $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            $nim = trim($row[0]);
            $nama_mahasiswa = trim($row[1]);
            $nama_kegiatan = trim($row[2]);
            $partisipasi = trim($row[3]);
            $tanggal_kegiatan = isset($row[4]) ? trim($row[4]) : '';

            $importedData[] = [
                "nim" => $nim,
                "nama_mahasiswa" => $nama_mahasiswa,
                "nama_kegiatan" => $nama_kegiatan,
                "partisipasi" => $partisipasi,
                "tanggal_kegiatan" => $tanggal_kegiatan
            ];
        }

        $_SESSION["imported_data"] = $importedData;
        header("Location: confirm_upload.php");
        exit();
    } catch (Exception $e) {
        die("<script>alert('❌ Terjadi kesalahan: " . $e->getMessage() . "'); window.location.href='upload_batch_berkas_internal.php';</script>");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Batch Berkas Internal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-4">
    <h3>Upload Batch Berkas Internal</h3>
    <p>Silakan unggah file Excel (.xlsx) dengan format berikut:</p>
    <table class="table table-bordered">
        <thead class="table-dark text-center">
            <tr>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Nama Kegiatan</th>
                <th>Partisipasi</th>
                <th>Tanggal Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>210123456</td>
                <td>Ahmad</td>
                <td>Seminar AI</td>
                <td>Peserta</td>
                <td>2025-04-15</td>
            </tr>
        </tbody>
    </table>

    <form action="upload_batch_berkas_internal.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="file_excel" class="form-label">Pilih File Excel</label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
        </div>
        <button type="submit" class="btn btn-success">Upload</button>
        <a href="detail_berkas_internal.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

</body>
</html>
