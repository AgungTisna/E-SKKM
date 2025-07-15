<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php'; // PHPSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

// 🔒 Validasi role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Akses ditolak!'); window.location.href='../index.php';</script>";
    exit();
}

// ✅ Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
    $file = $_FILES['file_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        array_shift($rows); // Buang header baris pertama

        $dataPreview = [];

        foreach ($rows as $row) {
            $id_berkas = (int)($row[1] ?? 0); // Kolom B = Index 1
            $nomor_sertifikat = trim($row[10] ?? ''); // Kolom K = Index 10
            $tanggal_dikeluarkan = trim($row[12] ?? ''); // Kolom M = Index 12

            if ($id_berkas > 0) {
                $dataPreview[] = [
                    'id_berkas_internal' => $id_berkas,
                    'nomor_sertifikat' => $nomor_sertifikat,
                    'tanggal_dikeluarkan' => $tanggal_dikeluarkan
                ];
            }
        }

        if (empty($dataPreview)) {
            echo "<script>alert('File Excel tidak berisi data yang valid.'); window.history.back();</script>";
            exit();
        }

        $_SESSION['excel_preview_internal'] = $dataPreview;
        header("Location: confirm_upload_internal.php");
        exit();

    } catch (Exception $e) {
        echo "<div class='container mt-4 text-danger'><strong>❌ Gagal membaca Excel:</strong> " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Batch Sertifikat Internal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h3 class="mb-4 text-center">📤 Upload Batch Update Sertifikat Internal</h3>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="file_excel" class="form-label">Pilih File Excel (.xlsx)</label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
        </div>
        <button type="submit" class="btn btn-success">📥 Upload & Preview</button>
        <a href="detail_berkas_internal.php" class="btn btn-secondary">← Kembali</a>
    </form>

    <div class="mt-4">
        <p><strong>📌 Format Excel yang digunakan:</strong></p>
        <ul>
            <li><strong>Kolom C</strong>: <code>ID Berkas</code> (wajib)</li>
            <li><strong>Kolom K</strong>: <code>Nomor Sertifikat</code></li>
            <li><strong>Kolom M</strong>: <code>Tanggal Dikeluarkan</code> (format: YYYY-MM-DD)</li>
        </ul>
    </div>
</div>
</body>
</html>
