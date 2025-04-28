<?php
require '../../../vendor/autoload.php'; // atau sesuaikan path jika berbeda
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header template
$headers = ['NIM', 'Nama Mahasiswa', 'Nama Kegiatan', 'Partisipasi', 'Tanggal Kegiatan', 'Kategori Kegiatan', 'Tingkat', 'Poin'];
$sheet->fromArray([$headers], null, 'A1');

// Styling (opsional)
$style = [
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFCCE5FF']
    ],
    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
];
$sheet->getStyle('A1:H1')->applyFromArray($style);

// Set nama file
$filename = 'Template_Kegiatan_Wajib.xlsx';

// Output file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
