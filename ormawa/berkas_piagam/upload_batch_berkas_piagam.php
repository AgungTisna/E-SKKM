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
            die("<script>alert('❌ File Excel kosong atau format tidak sesuai!'); window.location.href='upload_batch_berkas_piagam.php';</script>");
        }

        $isFirstRow = true;
        $importedData = [];

        foreach ($data as $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            // Ambil 4 kolom: Nama Kegiatan, Nama Penerima, Tanggal Kegiatan, Keterangan
            $nama_kegiatan     = trim($row[0] ?? '');
            $nama_penerima     = trim($row[1] ?? '');
            $tanggal_kegiatan  = trim($row[2] ?? '');
            $keterangan        = trim($row[3] ?? '');

            // Validasi opsional: cek format tanggal jika perlu
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_kegiatan)) {
                $tanggal_kegiatan = ''; // kosongkan jika format salah
            }

            $importedData[] = [
                "nama_kegiatan"     => $nama_kegiatan,
                "nama_penerima"     => $nama_penerima,
                "tanggal_kegiatan"  => $tanggal_kegiatan,
                "keterangan"        => $keterangan
            ];
        }

        $_SESSION["imported_piagam"] = $importedData;
        header("Location: confirm_upload_piagam.php");
        exit();

    } catch (Exception $e) {
        die("<script>alert('❌ Terjadi kesalahan: " . $e->getMessage() . "'); window.location.href='upload_batch_berkas_piagam.php';</script>");
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Batch Berkas Piagam</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-4">
    <h3>Upload Batch Berkas Piagam</h3>
    <p>Silakan unggah file Excel (.xlsx) dengan format berikut:</p>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nama Kegiatan</th>
                <th>Nama Penerima</th>
                <th>Tanggal Kegiatan (YYYY-MM-DD)</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Workshop Leadership</td>
                <td>Siti Aminah</td>
                <td>2024-12-10</td>
                <td>Peserta</td>
            </tr>
        </tbody>
    </table>


    <form action="upload_batch_berkas_piagam.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="file_excel" class="form-label">Pilih File Excel</label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
        </div>
        <button type="submit" class="btn btn-success">Upload</button>
        <a href="detail_berkas_piagam.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

</body>
</html>
