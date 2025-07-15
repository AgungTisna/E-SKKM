<?php
session_start();
require '../koneksi.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$nim = $_SESSION['nim'] ?? 190040014;

// Ambil detail mahasiswa
$query_detail = "
    SELECT u.nama, udm.prodi, udm.angkatan 
    FROM user u
    JOIN user_detail_mahasiswa udm ON u.id_user = udm.id_user
    WHERE udm.nim = ?
    LIMIT 1
";
$stmt_detail = $conn->prepare($query_detail);
if (!$stmt_detail) die("Prepare failed (detail): " . $conn->error);
$stmt_detail->bind_param("i", $nim);
$stmt_detail->execute();
$result_detail = $stmt_detail->get_result();
$mahasiswa = $result_detail->fetch_assoc();

$nama     = $mahasiswa['nama'] ?? 'Mahasiswa';
$prodi    = $mahasiswa['prodi'] ?? '-';
$angkatan = $mahasiswa['angkatan'] ?? '-';

// Ambil ketentuan minimal poin
$ketentuan = [];
$result_ketentuan = $conn->query("SELECT kategori_kegiatan, minimal_poin FROM tabel_ketentuan_skkm");
while ($row = $result_ketentuan->fetch_assoc()) {
    $kategori = ucwords(strtolower(trim($row['kategori_kegiatan'])));
    $ketentuan[$kategori] = [
        'minimal' => (int)$row['minimal_poin'],
        'poin_internal' => 0,
        'poin_pengajuan' => 0
    ];
}

// Fungsi bantu
function tambah_poin(&$arr, $kategori, $kolom, $poin) {
    $kategori = ucwords(strtolower(trim($kategori)));
    if (!isset($arr[$kategori])) {
        $arr[$kategori] = [
            'minimal' => 0,
            'poin_internal' => 0,
            'poin_pengajuan' => 0
        ];
    }
    $arr[$kategori][$kolom] += $poin;
}

// --- Ambil dari berkas_internal
$query_internal = "
    SELECT kategori_kegiatan, poin_skkm, nomor_sertifikat_internal
    FROM berkas_internal
    WHERE nim = ?
";
$stmt = $conn->prepare($query_internal);
if (!$stmt) die("Prepare failed (berkas_internal): " . $conn->error);
$stmt->bind_param("i", $nim);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if ($row['kategori_kegiatan'] !== '-' && ($row['kategori_kegiatan'] === 'Kegiatan Wajib' || !empty($row['nomor_sertifikat_internal']))) {
        tambah_poin($ketentuan, $row['kategori_kegiatan'], 'poin_internal', (int)$row['poin_skkm']);
    }
}

// --- Ambil dari arsip_skkm
$query_arsip = "
    SELECT kategori_kegiatan, poin_skkm
    FROM arsip_skkm
    WHERE nim = ?
";
$stmt = $conn->prepare($query_arsip);
if (!$stmt) die("Prepare failed (arsip_skkm): " . $conn->error);
$stmt->bind_param("i", $nim);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    tambah_poin($ketentuan, $row['kategori_kegiatan'], 'poin_internal', (int)$row['poin_skkm']);
}

// --- Ambil dari pengajuan_skkm (Valid)
$query_pengajuan = "
    SELECT kategori_kegiatan, SUM(poin_skkm) as total_poin
    FROM pengajuan_skkm
    WHERE nim = ?
      AND status_verifikasi_bem = 'Valid'
      AND status_verifikasi_kemahasiswaan = 'Valid'
    GROUP BY kategori_kegiatan
";
$stmt = $conn->prepare($query_pengajuan);
if (!$stmt) die("Prepare failed (pengajuan_skkm): " . $conn->error);
$stmt->bind_param("i", $nim);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    tambah_poin($ketentuan, $row['kategori_kegiatan'], 'poin_pengajuan', (int)$row['total_poin']);
}

// --- Urutkan kategori sesuai prioritas
$urutan_prioritas = [
    'Kegiatan Wajib' => 1,
    'Bidang Akademik & Ilmiah' => 2,
    'Bidang Minat Bakat Seni & Olahraga' => 3,
    'Bidang Organisasi & Sosial' => 4
];

uksort($ketentuan, function($a, $b) use ($urutan_prioritas) {
    return ($urutan_prioritas[$a] ?? 999) <=> ($urutan_prioritas[$b] ?? 999);
});

// --- Buat HTML untuk PDF
$html = "
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
    table { border-collapse: collapse; width: 100%; margin-top: 15px; }
    th, td { border: 1px solid #000; padding: 8px; }
</style>
<h2 style='text-align:center;'>Rekap Poin SKKM Mahasiswa</h2>

<p><strong>Nama:</strong> $nama</p>
<p><strong>NIM:</strong> $nim</p>
<p><strong>Program Studi:</strong> $prodi</p>
<p><strong>Angkatan:</strong> $angkatan</p>

<table>
    <thead>
        <tr>
            <th>Kategori</th>
            <th>Poin Internal & Arsip</th>
            <th>Poin Pengajuan</th>
            <th>Total</th>
            <th>Minimal</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>";

foreach ($ketentuan as $kategori => $info) {
    if (trim($kategori) === "-") continue;
    $total_poin = $info['poin_internal'] + $info['poin_pengajuan'];
    $status = $total_poin >= $info['minimal'] ? 'Terpenuhi' : 'Belum Terpenuhi';

    $html .= "
        <tr>
            <td>$kategori</td>
            <td align='center'>{$info['poin_internal']}</td>
            <td align='center'>{$info['poin_pengajuan']}</td>
            <td align='center'>$total_poin</td>
            <td align='center'>{$info['minimal']}</td>
            <td align='center'>$status</td>
        </tr>";
}

$html .= "</tbody></table>
<p style='margin-top:15px; font-size:12px; color:gray;'>* Kegiatan wajib dihitung walau belum memiliki sertifikat.<br>* Kegiatan non-wajib hanya dihitung jika sertifikat valid atau pengajuan disetujui.</p>";

// --- Generate PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Rekap_Poin_SKKM_$nim.pdf", ["Attachment" => false]);
exit;
