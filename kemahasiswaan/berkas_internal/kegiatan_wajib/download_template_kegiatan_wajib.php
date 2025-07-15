<?php
require '../../../vendor/autoload.php';
include '../../../koneksi.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil ID terakhir dari berkas_internal
$result = $conn->query("SELECT MAX(id_berkas_internal) AS max_id FROM berkas_internal");
$lastId = ($result && $row = $result->fetch_assoc()) ? (int)$row['max_id'] : 0;

// Siapkan spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$headers = [
    'ID', 'NIM', 'Nama Mahasiswa', 'Nama Kegiatan', 'Partisipasi',
    'Tanggal Kegiatan', 'Tanggal Pengajuan', 'Nomor Sertifikat', 'Tanggal Dikeluarkan'
];
$sheet->fromArray([$headers], null, 'A1');

// Isi 10 baris kosong untuk diisi pengguna
$startId = $lastId + 1;
for ($i = 0; $i < 10; $i++) {
    $rowNum = $i + 2;
    $currentId = $startId + $i;

    $sheet->setCellValue("A$rowNum", $currentId); // ID
    // Kolom B sampai G dan I dibiarkan kosong untuk diisi manual
    // Kolom H diisi formula sesuai format
    $sheet->setCellValue("H$rowNum", "=A$rowNum&\"/Srtf/\"&\"KMHS/\"&ROMAN(MONTH(I$rowNum))&\"/\"&YEAR(I$rowNum)");
}

// Auto-width kolom
foreach (range('A', 'I') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Template_Kegiatan_Wajib.xlsx"');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
