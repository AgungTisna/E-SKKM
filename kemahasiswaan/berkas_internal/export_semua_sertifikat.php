<?php
session_start();
require '../../koneksi.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Validasi login dan role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../index.php';</script>";
    exit();
}

// Ambil data dari berkas_internal yang memiliki nomor sertifikat
$sql = "
    SELECT 
        bi.id_berkas_internal AS id,
        bi.nim, 
        COALESCE(u.nama, 'Mahasiswa belum ada di sistem') AS nama_mahasiswa,
        bi.nama_kegiatan, 
        bi.tanggal_kegiatan, 
        bi.partisipasi, 
        bi.kategori_kegiatan, 
        bi.tingkat, 
        bi.poin_skkm, 
        bi.nomor_sertifikat_internal,
        bi.tanggal_pengajuan,
        bi.tanggal_dikeluarkan,
        o.nama_ormawa
    FROM berkas_internal bi
    LEFT JOIN user_detail_mahasiswa m ON bi.nim = m.nim
    LEFT JOIN user u ON m.id_user = u.id_user
    LEFT JOIN user_detail_ormawa o ON bi.id_ormawa = o.id_ormawa
    WHERE bi.nomor_sertifikat_internal IS NOT NULL 
      AND bi.nomor_sertifikat_internal != ''
    ORDER BY bi.tanggal_pengajuan ASC
";

$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
    echo "<script>alert('Tidak ada data sertifikat ditemukan.'); window.history.back();</script>";
    exit();
}

// Siapkan spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$headers = ['No', 'Nama Ormawa', 'NIM', 'Nama Mahasiswa', 'Nama Kegiatan', 'Tanggal Kegiatan',
            'Partisipasi', 'Kategori Kegiatan', 'Tingkat', 'Poin SKKM', 'Nomor Sertifikat',
            'Tanggal Pengajuan', 'Tanggal Dikeluarkan'];
$sheet->fromArray([$headers], null, 'A1');

// Isi data
$rowNumber = 2;
$no = 1;
while ($row = $result->fetch_assoc()) {
    $sheet->fromArray([
        $no++,
        $row['nama_ormawa'],
        $row['nim'],
        $row['nama_mahasiswa'],
        $row['nama_kegiatan'],
        $row['tanggal_kegiatan'],
        $row['partisipasi'],
        $row['kategori_kegiatan'],
        $row['tingkat'],
        $row['poin_skkm'],
        $row['nomor_sertifikat_internal'],
        $row['tanggal_pengajuan'],
        $row['tanggal_dikeluarkan']
    ], null, "A$rowNumber");
    $rowNumber++;
}

// Export file Excel
$filename = "Data_Sertifikat_Internal.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
