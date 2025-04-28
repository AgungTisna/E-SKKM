<?php
session_start();
include '../../../koneksi.php';
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../index.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        unset($rows[0]); // Hapus header
        $previewData = [];

        foreach ($rows as $row) {
            list($nim, $nama_mahasiswa, $nama_kegiatan, $partisipasi, $tanggal_kegiatan, $kategori, $tingkat, $poin) = $row;

            if (strtolower(trim($kategori)) === 'kegiatan wajib') {
                $previewData[] = [
                    'nim' => trim($nim),
                    'nama_kegiatan' => trim($nama_kegiatan),
                    'partisipasi' => trim($partisipasi),
                    'tanggal_kegiatan' => date('Y-m-d', strtotime($tanggal_kegiatan)),
                    'kategori' => trim($kategori),
                    'tingkat' => trim($tingkat),
                    'poin' => (int)$poin
                ];
            }
        }

        $_SESSION['preview_data'] = $previewData;
        header("Location: confirm_upload.php");
        exit();

    } catch (Exception $e) {
        echo "<script>alert('Gagal membaca file Excel: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Batch Kegiatan Wajib</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Upload Batch Data Kegiatan Wajib</h2>
    <a href="detail_kegiatan_wajib.php" class="btn btn-secondary mb-3"> Kembali</a>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="excel_file" class="form-label">Pilih File Excel (.xlsx)</label>
            <input type="file" class="form-control" name="excel_file" accept=".xlsx" required>
        </div>
        <button type="submit" class="btn btn-primary">🚀 Upload Sekarang</button>
    </form>

    <div class="mt-4">
        <strong>Contoh format file Excel:</strong>
        <ul>
            <li>NIM</li>
            <li>Nama Kegiatan</li>
            <li>Partisipasi</li>
            <li>Tanggal Kegiatan (YYYY-MM-DD)</li>
            <li>Kategori Kegiatan (harus <code>Wajib</code>)</li>
            <li>Tingkat</li>
            <li>Poin SKKM</li>
        </ul>
    </div>
</div>
</body>
</html>