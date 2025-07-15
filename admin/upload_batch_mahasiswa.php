<?php
session_start();
include '../koneksi.php'; // Koneksi ke database
require '../vendor/autoload.php'; // Load PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file_excel"])) {
    $file = $_FILES["file_excel"]["tmp_name"];

    try {
        // Membaca file Excel
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Validasi jika file kosong
        if (empty($data)) {
            die("<script>alert('❌ File Excel kosong!'); window.location.href='upload_batch_mahasiswa.php';</script>");
        }

        // Lewati baris pertama (header)
        $isFirstRow = true;
        $importedData = [];

        foreach ($data as $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            // Ambil data dari setiap baris
            $nama = trim($row[0]);
            $nim = trim($row[1]);
            $prodi = trim($row[2]);
            $angkatan = trim($row[3]);
            $email = trim($row[4]);
            $username = trim($row[5]);
            $password = trim($row[6]); // Tidak dihash sesuai permintaan

            // Simpan ke array untuk ditampilkan di tabel sebelum disimpan ke database
            $importedData[] = [
                "nama" => $nama,
                "nim" => $nim,
                "prodi" => $prodi,
                "angkatan" => $angkatan,
                "email" => $email,
                "username" => $username,
                "password" => $password
            ];
        }

        $_SESSION["imported_data"] = $importedData; // Simpan ke session
        header("Location: confirm_upload.php"); // Redirect ke halaman konfirmasi
        exit();
    } catch (Exception $e) {
        die("<script>alert('❌ Terjadi kesalahan: " . $e->getMessage() . "'); window.location.href='upload_batch_mahasiswa.php';</script>");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Batch Mahasiswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h3>Upload Batch Data Mahasiswa</h3>
    <p>Silakan unggah file Excel (.xlsx) dengan format berikut:</p>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nama</th>
                <th>NIM</th>
                <th>Prodi</th>
                <th>Angkatan</th>
                <th>Email</th>
                <th>Username</th>
                <th>Password</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Ahmad</td>
                <td>210123456</td>
                <td>Informatika</td>
                <td>2021</td>
                <td>ahmad@email.com</td>
                <td>ahmad21</td>
                <td>pass123</td>
            </tr>
        </tbody>
    </table>

    <form action="upload_batch_mahasiswa.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="file_excel" class="form-label">Pilih File Excel</label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
        </div>
        <button type="submit" class="btn btn-success">Upload</button>
    </form>
</div>

</body>
</html>
