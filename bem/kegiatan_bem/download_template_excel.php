<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Buat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul kolom/template
$headers = [
    'NIM',
    'Nama Mahasiswa',
    'Nama Kegiatan',
    'Partisipasi',
    'Kategori',
    'Tingkat',
    'Poin SKKM',
    'Tanggal Kegiatan (YYYY-MM-DD)'
];

// Isi baris pertama dengan header
$sheet->fromArray([$headers], null, 'A1');

// Nama file
$filename = 'Template_Pengajuan_BEM.xlsx';

// Header untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");

// Buat writer dan outputkan ke browser
$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
?>
