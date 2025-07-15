<?php
require_once('../../koneksi.php');
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_GET['tahun'])) {
    die('Tahun arsip tidak ditentukan.');
}

$tahun = intval($_GET['tahun']);
$query = "SELECT * FROM arsip_eksternal WHERE tahun_arsip = $tahun ORDER BY tanggal_kegiatan DESC";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    die('Data tidak ditemukan untuk tahun tersebut.');
}

// Buat spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Arsip Eksternal $tahun");

// Header
$headers = [
    'No', 'ID Arsip', 'ID Berkas', 'ID Ormawa', 'Nama Kegiatan', 'ID Kemahasiswaan',
    'Nama Peserta', 'Tanggal Kegiatan', 'No. Sertifikat', 'Tanggal Pengajuan',
    'Tanggal Dikeluarkan', 'Keterangan', 'Tahun Arsip'
];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

// Isi data
$rowNum = 2;
$no = 1;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue("A$rowNum", $no++);
    $sheet->setCellValue("B$rowNum", $row['id_arsip_eksternal']);
    $sheet->setCellValue("C$rowNum", $row['id_berkas_eksternal']);
    $sheet->setCellValue("D$rowNum", $row['id_ormawa']);
    $sheet->setCellValue("E$rowNum", $row['nama_kegiatan']);
    $sheet->setCellValue("F$rowNum", $row['id_kemahasiswaan']);
    $sheet->setCellValue("G$rowNum", $row['nama_peserta']);
    $sheet->setCellValue("H$rowNum", $row['tanggal_kegiatan']);
    $sheet->setCellValue("I$rowNum", $row['nomor_sertifikat_eksternal']);
    $sheet->setCellValue("J$rowNum", $row['tanggal_pengajuan']);
    $sheet->setCellValue("K$rowNum", $row['tanggal_dikeluarkan']);
    $sheet->setCellValue("L$rowNum", $row['keterangan']);
    $sheet->setCellValue("M$rowNum", $row['tahun_arsip']);
    $rowNum++;
}

// Output Excel
$filename = "arsip_eksternal_$tahun.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
