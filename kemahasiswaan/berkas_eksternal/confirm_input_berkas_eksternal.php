<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Akses ditolak!'); window.location.href='../index.php';</script>";
    exit();
}

// Fungsi ambil nama ormawa
function getNamaOrmawa($id_ormawa, $conn) {
    $result = $conn->query("SELECT nama_ormawa FROM user_detail_ormawa WHERE id_ormawa = " . intval($id_ormawa));
    if ($result && $row = $result->fetch_assoc()) {
        return $row['nama_ormawa'];
    }
    return "Tidak ditemukan (ID $id_ormawa)";
}

// === SIMPAN DATA ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan']) && isset($_POST['data'])) {
    $dataRows = $_POST['data'];
    $berhasil = 0;
    $id_kemahasiswaan = $_SESSION['id_user']; // Gantilah sesuai sistem login Anda

    foreach ($dataRows as $row) {
        list($id_ormawa, $nama_kegiatan, $nama_peserta, $tanggal_kegiatan, $tanggal_pengajuan, $nomor_sertifikat, $keterangan) = explode('|', $row);

        $tanggal_kegiatan = date('Y-m-d', strtotime($tanggal_kegiatan));
        $tanggal_pengajuan = date('Y-m-d', strtotime($tanggal_pengajuan));
        $tanggal_dikeluarkan = $tanggal_pengajuan;

        $stmt = $conn->prepare("INSERT INTO berkas_eksternal 
            (id_ormawa, nama_kegiatan, id_kemahasiswaan, nama_peserta, tanggal_kegiatan, nomor_sertifikat_eksternal, tanggal_pengajuan, tanggal_dikeluarkan, keterangan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "isissssss",
            $id_ormawa,
            $nama_kegiatan,
            $id_kemahasiswaan,
            $nama_peserta,
            $tanggal_kegiatan,
            $nomor_sertifikat,
            $tanggal_pengajuan,
            $tanggal_dikeluarkan,
            $keterangan
        );

        if ($stmt->execute()) {
            $berhasil++;
        }
    }

    echo <<<HTML
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="container mt-5">
        <div class="alert alert-success">
            <strong>{$berhasil}</strong> data berhasil disimpan ke Berkas Eksternal.
        </div>
        <a href="input_berkas_eksternal.php" class="btn btn-primary">← Upload Lagi</a>
        <a href="detail_berkas_eksternal.php" class="btn btn-secondary ms-2">Lihat Data</a>
    </div>
    HTML;
    exit;
}
include '../navbar.php';
// === PREVIEW DATA ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $tmpName = $_FILES['file']['tmp_name'];
    $spreadsheet = IOFactory::load($tmpName);
    $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    echo <<<HTML
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Konfirmasi Berkas Eksternal</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background-color: #f8f9fa; }
            table td, table th { vertical-align: middle !important; }
        </style>
    </head>
    <body>
    <div class="container mt-5">
        <h3 class="mb-4">Konfirmasi Data Berkas Eksternal</h3>
        <form method="post">
        <div class="table-responsive bg-white p-3 rounded shadow-sm">
        <table class="table table-bordered bg-white">
            <thead class="table-white text-center">
HTML;

    $isHeader = true;
    foreach ($data as $index => $row) {
        echo "<tr>";
        if ($isHeader) {
            foreach ($row as $cell) echo "<th>" . htmlspecialchars($cell) . "</th>";
            echo "<th>Nama Ormawa</th></tr>";
            $isHeader = false;
        } else {
            $id_ormawa = intval($row['B']);
            $nama_ormawa = getNamaOrmawa($id_ormawa, $conn);

            foreach ($row as $cell) echo "<td>" . htmlspecialchars($cell) . "</td>";
            echo "<td>" . htmlspecialchars($nama_ormawa) . "</td>";

            echo "<input type='hidden' name='data[]' value='" . implode('|', [
                $row['B'], // ID Ormawa
                $row['C'], // Nama Kegiatan
                $row['D'], // Nama Peserta
                $row['E'], // Tanggal Kegiatan
                $row['F'], // Tanggal Pengajuan
                $row['G'], // Nomor Sertifikat
                $row['H']  // Keterangan
            ]) . "'>";
            echo "</tr>";
        }
    }

    echo <<<HTML
        </thead>
        </table>
        </div>
        <div class="mt-3">
            <button type="submit" name="simpan" class="btn btn-success">Simpan ke Database</button>
            <a href="input_berkas_eksternal.php" class="btn btn-secondary ms-2">← Kembali</a>
        </div>
        </form>
    </div>
    </body>
    </html>
    HTML;
} else {
    echo "<script>alert('File tidak valid atau tidak ditemukan.'); window.location.href='input_berkas_eksternal.php';</script>";
}
?>
