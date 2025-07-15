<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php'; // PHPSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

// 🔐 Cek login & role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Akses ditolak.'); window.location.href='../index.php';</script>";
    exit();
}

// ✅ Handle unggah file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
    $file = $_FILES['file_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        array_shift($rows); // Hapus baris header

        $dataPreview = [];
        foreach ($rows as $row) {
            $dataPreview[] = [
                'id_berkas'           => (int)($row[1] ?? 0),
                'nama_ormawa'         => trim($row[2] ?? ''),
                'nama_kegiatan'       => trim($row[3] ?? ''),
                'nama_penerima'       => trim($row[4] ?? ''),
                'tanggal_kegiatan'    => trim($row[5] ?? ''), // ✅ Tambahan
                'nomor_sertifikat'    => trim($row[6] ?? ''),
                'tanggal_pengajuan'   => trim($row[7] ?? ''),
                'tanggal_dikeluarkan' => trim($row[8] ?? ''),
                'keterangan'          => trim($row[9] ?? '')
            ];
        }

        if (empty($dataPreview)) {
            echo "<script>alert('❌ Tidak ada data ditemukan di file Excel.'); window.history.back();</script>";
            exit();
        }

        $_SESSION['excel_preview_piagam'] = $dataPreview;
        header("Location: confirm_upload_piagam.php");
        exit();

    } catch (Exception $e) {
        echo "<div class='alert alert-danger mt-4'>Gagal membaca file: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Batch Piagam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h3 class="text-center mb-4">📤 Upload Batch Data Piagam Mahasiswa</h3>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Pilih File Excel (.xlsx)</label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
        </div>
        <div class="d-flex justify-content-between">
            <a href="detail_berkas_piagam.php" class="btn btn-secondary">← Kembali</a>
            <button type="submit" class="btn btn-success">📤 Upload & Preview</button>
        </div>
    </form>

    <div class="mt-4">
        <p><strong>📌 Format Excel yang diperlukan:</strong></p>
        <ol>
            <li>No</li>
            <li>ID Berkas</li>
            <li>Nama Ormawa</li>
            <li>Nama Kegiatan</li>
            <li>Nama Penerima</li>
            <li><strong>Tanggal Kegiatan (YYYY-MM-DD)</strong></li>
            <li>Nomor Sertifikat</li>
            <li>Tanggal Pengajuan (YYYY-MM-DD)</li>
            <li>Tanggal Dikeluarkan (YYYY-MM-DD)</li>
            <li>Keterangan</li>
        </ol>
    </div>
</div>
</body>
</html>
