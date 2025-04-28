<?php
session_start();
require '../../koneksi.php';
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validasi login
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'BEM') {
    echo "<script>alert('Anda harus login sebagai BEM!'); window.location.href='../index.php';</script>";
    exit;
}

$id_bem = $_SESSION['id_user'];
$tanggal_pengajuan = date('Y-m-d');

// ✅ SIMPAN DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data'])) {
    $data = $_POST['data'];
    $jumlah_data = 0;

    foreach ($data as $row) {
        [$nim, $nama_mhs, $nama_kegiatan, $partisipasi, $kategori, $tingkat, $poin, $tanggal_kegiatan] = $row;

        // Field default
        $id_kemahasiswaan = 0;
        $nomor_sertifikat_internal = '';
        $tanggal_dikeluarkan = '0000-00-00'; // <- Fix: Tidak boleh NULL karena kolom NOT NULL

        // Perhatikan urutan kolom sesuai dengan struktur tabel
        $stmt = $conn->prepare("
            INSERT INTO berkas_bem 
            (id_bem, nim, nama_kegiatan, partisipasi, tingkat, kategori_kegiatan, poin_skkm, tanggal_kegiatan, tanggal_pengajuan, id_kemahasiswaan, nomor_sertifikat_internal, tanggal_dikeluarkan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "isssssississ", 
            $id_bem, $nim, $nama_kegiatan, $partisipasi, $tingkat, $kategori, 
            $poin, $tanggal_kegiatan, $tanggal_pengajuan, 
            $id_kemahasiswaan, $nomor_sertifikat_internal, $tanggal_dikeluarkan
        );

        if ($stmt->execute()) $jumlah_data++;
    }

    echo "<script>alert('$jumlah_data data berhasil disimpan ke berkas_bem.'); window.location.href='detail_kegiatan_bem.php';</script>";
    exit;
}

// ✅ PREVIEW EXCEL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
    $file = $_FILES['file_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();
        unset($data[0]); // Hapus header

        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <title>Preview Data Kegiatan Mahasiswa</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body>
        <div class='container mt-5'>
            <h3 class='mb-4'>📋 Preview Data Kegiatan Mahasiswa</h3>
            <form method='POST'>
            <div class='table-responsive'>
            <table class='table table-bordered table-hover'>
                <thead class='table-dark text-center'>
                    <tr>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Nama Kegiatan</th>
                        <th>Partisipasi</th>
                        <th>Kategori</th>
                        <th>Tingkat</th>
                        <th>Poin SKKM</th>
                        <th>Tanggal Kegiatan</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($data as $index => $row) {
            if (count(array_filter($row)) < 8) continue;

            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";

            for ($i = 0; $i < 8; $i++) {
                $val = htmlspecialchars($row[$i]);
                echo "<input type='hidden' name='data[$index][$i]' value='$val'>";
            }
        }

        echo "</tbody>
            </table>
            </div>
            <div class='d-flex justify-content-between mt-3'>
                <a href='pengajuan_kegiatan_bem.php' class='btn btn-secondary'>← Batal</a>
                <button type='submit' class='btn btn-success'>💾 Simpan ke berkas_bem</button>
            </div>
            </form>
        </div>
        </body>
        </html>";

    } catch (Exception $e) {
        echo "<script>alert('Gagal membaca file: " . $e->getMessage() . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('File tidak ditemukan!'); window.history.back();</script>";
}
?>
