<?php
session_start();
require '../../koneksi.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../index.php';</script>";
    exit();
}

// Query gabungan dari internal dan bem
$sql = "
    SELECT 
        bi.id_berkas_internal AS id,
        bi.nim, 
        u.nama AS nama_mahasiswa,
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
    JOIN user_detail_mahasiswa m ON bi.nim = m.nim
    JOIN user u ON m.id_user = u.id_user
    JOIN user_detail_ormawa o ON bi.id_ormawa = o.id_ormawa
    WHERE bi.id_bem IS NOT NULL

    UNION ALL

    SELECT 
        bb.id_berkas_bem AS id,
        bb.nim,
        u.nama AS nama_mahasiswa,
        bb.nama_kegiatan,
        bb.tanggal_kegiatan,
        bb.partisipasi,
        bb.kategori_kegiatan,
        bb.tingkat,
        bb.poin_skkm,
        bb.nomor_sertifikat_internal,
        bb.tanggal_pengajuan,
        bb.tanggal_dikeluarkan,
        'BEM' AS nama_ormawa
    FROM berkas_bem bb
    LEFT JOIN user_detail_mahasiswa udm ON bb.nim = udm.nim
    LEFT JOIN user u ON udm.id_user = u.id_user
    WHERE bb.id_kemahasiswaan IS NOT NULL

    ORDER BY tanggal_pengajuan ASC
";

$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
    echo "<script>alert('Tidak ada data sertifikat ditemukan.'); window.history.back();</script>";
    exit();
}

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

// Export file
$filename = "Seluruh_Sertifikat_Internal.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
