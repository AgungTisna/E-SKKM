<?php
require_once('../../koneksi.php');
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_GET['tahun'])) {
    die('Tahun tidak ditentukan.');
}

$tahun = intval($_GET['tahun']);
$sql = "
    SELECT a.*, o.nama_ormawa
    FROM arsip_piagam a
    JOIN user_detail_ormawa o ON a.id_ormawa = o.id_ormawa
    WHERE a.tahun_arsip = $tahun
    ORDER BY a.tanggal_dikeluarkan DESC
";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Tidak ada data ditemukan untuk tahun $tahun.");
}

// Buat spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Piagam $tahun");

// Header
$headers = ['No', 'Nama Ormawa', 'Nama Kegiatan', 'Nama Penerima', 'Tanggal Kegiatan', 'Tanggal Pengajuan', 'Tanggal Dikeluarkan', 'Nomor Sertifikat', 'Keterangan', 'Tahun Arsip'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

// Data
$rowNum = 2;
$no = 1;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue("A$rowNum", $no++);
    $sheet->setCellValue("B$rowNum", $row['nama_ormawa']);
    $sheet->setCellValue("C$rowNum", $row['nama_kegiatan']);
    $sheet->setCellValue("D$rowNum", $row['nama_penerima']);
    $sheet->setCellValue("E$rowNum", $row['tanggal_kegiatan']);
    $sheet->setCellValue("F$rowNum", $row['tanggal_pengajuan']);
    $sheet->setCellValue("G$rowNum", $row['tanggal_dikeluarkan']);
    $sheet->setCellValue("H$rowNum", $row['nomor_sertifikat_piagam']);
    $sheet->setCellValue("I$rowNum", $row['keterangan']);
    $sheet->setCellValue("J$rowNum", $row['tahun_arsip']);
    $rowNum++;
}

// Output
$filename = "arsip_piagam_$tahun.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
