<?php
require_once('../../koneksi.php');
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_GET['tahun'])) {
    die('Tahun arsip tidak ditentukan.');
}

$tahun = intval($_GET['tahun']);

// Ambil data dari database
$query = "SELECT * FROM arsip_skkm WHERE tahun_arsip = $tahun ORDER BY tanggal_kegiatan DESC";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    die('Tidak ada data untuk tahun tersebut.');
}

// Buat objek spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Arsip SKKM $tahun");

// Header kolom
$headers = [
    'No', 'NIM', 'Nama Kegiatan', 'Partisipasi', 'Kategori', 'Tingkat',
    'Poin', 'No. Sertifikat', 'Tanggal Kegiatan', 'Pengajuan', 'Dikeluarkan', 'Tahun Arsip'
];
$column = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($column . '1', $header);
    $column++;
}

// Isi data
$rowNum = 2;
$no = 1;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNum, $no++);
    $sheet->setCellValue('B' . $rowNum, $row['nim']);
    $sheet->setCellValue('C' . $rowNum, $row['nama_kegiatan']);
    $sheet->setCellValue('D' . $rowNum, $row['partisipasi']);
    $sheet->setCellValue('E' . $rowNum, $row['kategori_kegiatan']);
    $sheet->setCellValue('F' . $rowNum, $row['tingkat']);
    $sheet->setCellValue('G' . $rowNum, $row['poin_skkm']);
    $sheet->setCellValue('H' . $rowNum, $row['nomor_sertifikat_internal']);
    $sheet->setCellValue('I' . $rowNum, $row['tanggal_kegiatan']);
    $sheet->setCellValue('J' . $rowNum, $row['tanggal_pengajuan']);
    $sheet->setCellValue('K' . $rowNum, $row['tanggal_dikeluarkan']);
    $sheet->setCellValue('L' . $rowNum, $row['tahun_arsip']);
    $rowNum++;
}

// Buat file Excel
$filename = "arsip_skkm_$tahun.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
