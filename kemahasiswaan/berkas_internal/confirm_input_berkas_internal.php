<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Akses ditolak!'); window.location.href='../index.php';</script>";
    exit();
}

require '../../vendor/autoload.php';
include '../../koneksi.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Fungsi ambil nama ormawa
function getNamaOrmawa($id_ormawa, $conn) {
    $id_ormawa = intval($id_ormawa);
    $result = $conn->query("SELECT nama_ormawa FROM user_detail_ormawa WHERE id_ormawa = $id_ormawa");
    if ($result && $row = $result->fetch_assoc()) {
        return $row['nama_ormawa'];
    }
    return "Tidak diketahui (ID: $id_ormawa)";
}

// SIMPAN DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan']) && isset($_POST['data'])) {
    $dataRows = $_POST['data'];
    $success = 0;

    foreach ($dataRows as $row) {
        list($no, $id_ormawa, $nama_kegiatan, $nim, $nama_mhs, $tgl_kegiatan, $tgl_pengajuan, $no_sertifikat, $partisipasi) = explode('|', $row);

        $id_ormawa = intval($id_ormawa);
        $tgl_kegiatan = date('Y-m-d', strtotime($tgl_kegiatan));
        $tgl_pengajuan = date('Y-m-d', strtotime($tgl_pengajuan));

        $sql = "INSERT INTO berkas_internal 
            (id_ormawa, nim, nama_kegiatan, partisipasi, tanggal_kegiatan, tanggal_pengajuan, id_bem, kategori_kegiatan, tingkat, poin_skkm, id_kemahasiswaan, nomor_sertifikat_internal, tanggal_dikeluarkan)
            VALUES (
                '$id_ormawa',
                '$nim',
                '$nama_kegiatan',
                '$partisipasi',
                '$tgl_kegiatan',
                '$tgl_pengajuan',
                '0',
                '-',
                '-',
                '-',
                '0',
                '$no_sertifikat',
                '$tgl_pengajuan'
            )";

        if ($conn->query($sql)) {
            $success++;
        } else {
            echo "<div class='alert alert-danger'>Gagal menyimpan data baris $no: " . $conn->error . "</div>";
        }
    }

    echo <<<HTML
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="container mt-5">
        <div class="alert alert-success">
            <strong>$success data</strong> berhasil disimpan ke database.
        </div>
        <a href="detail_berkas_internal.php" class="btn btn-primary">Menuju ke Berkas Internal</a>
    </div>
    HTML;
    exit;
}
include '../navbar.php';

// PREVIEW FILE
if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $tmpName = $_FILES['file']['tmp_name'];
    $spreadsheet = IOFactory::load($tmpName);
    $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    echo <<<HTML
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="container mt-5">
        <h2>Konfirmasi Data Berkas Internal</h2>
        <form method="post">
            <div class="mt-3">
            <button type="submit" name="simpan" class="btn btn-success">Simpan ke Database</button>
            <a href="input_berkas_internal.php" class="btn btn-secondary ms-2">← Kembali</a>
        </div>
        <div class="table-responsive">
        <table class="table table-bordered mt-4">
            <thead class="table-light">
HTML;

    $isHeader = true;
    foreach ($data as $index => $row) {
        if ($isHeader) {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<th>" . htmlspecialchars($cell) . "</th>";
            }
            echo "<th>Nama Ormawa</th></tr>";
            $isHeader = false;
        } else {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }

            $id_ormawa = intval($row['B']); // Kolom B = ID Ormawa
            $nama_ormawa = getNamaOrmawa($id_ormawa, $conn);
            echo "<td>" . htmlspecialchars($nama_ormawa) . "</td>";

            echo "<input type='hidden' name='data[]' value='" . implode('|', $row) . "'>";
            echo "</tr>";
        }
    }

    echo <<<HTML
        </thead></table>
        </div>
        
        </form>
    </div>
    HTML;

} else {
    echo "<div class='container mt-5'><div class='alert alert-danger'>File tidak valid atau gagal diunggah.</div></div>";
}
?>
