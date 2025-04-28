<?php
session_start();
require '../koneksi.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$nim = $_SESSION['nim'] ?? '190040014';

// Ambil detail mahasiswa
$query_detail = "
    SELECT u.nama, udm.prodi, udm.angkatan 
    FROM user u
    JOIN user_detail_mahasiswa udm ON u.id_user = udm.id_user
    WHERE udm.nim = ?
    LIMIT 1
";
$stmt_detail = $conn->prepare($query_detail);
$stmt_detail->bind_param("s", $nim);
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

// Fungsi bantu untuk menjamin kategori tersimpan
function tambah_poin(&$ketentuan, $kategori, $kolom, $poin) {
    $kategori = ucwords(strtolower(trim($kategori)));
    if (!isset($ketentuan[$kategori])) {
        $ketentuan[$kategori] = [
            'minimal' => 0,
            'poin_internal' => 0,
            'poin_pengajuan' => 0
        ];
    }
    $ketentuan[$kategori][$kolom] += $poin;
}

// 1. Ambil dari berkas_internal
$query_internal = "
    SELECT kategori_kegiatan, poin_skkm, nomor_sertifikat_internal
    FROM berkas_internal
    WHERE nim = ?
";
$stmt = $conn->prepare($query_internal);
$stmt->bind_param("s", $nim);
$stmt->execute();
$result_internal = $stmt->get_result();

while ($row = $result_internal->fetch_assoc()) {
    $kategori = $row['kategori_kegiatan'];
    $poin = (int)$row['poin_skkm'];
    $sertifikatValid = !empty($row['nomor_sertifikat_internal']) && $row['nomor_sertifikat_internal'] !== '0';

    if ($kategori === 'Kegiatan Wajib' || $sertifikatValid) {
        tambah_poin($ketentuan, $kategori, 'poin_internal', $poin);
    }
}

// 2. Ambil dari berkas_bem
$query_bem = "
    SELECT kategori_kegiatan, poin_skkm, nomor_sertifikat_internal
    FROM berkas_bem
    WHERE nim = ?
      AND nomor_sertifikat_internal IS NOT NULL
      AND nomor_sertifikat_internal != ''
      AND nomor_sertifikat_internal != '0'
";
$stmt_bem = $conn->prepare($query_bem);
$stmt_bem->bind_param("s", $nim);
$stmt_bem->execute();
$result_bem = $stmt_bem->get_result();

while ($row = $result_bem->fetch_assoc()) {
    tambah_poin($ketentuan, $row['kategori_kegiatan'], 'poin_internal', (int)$row['poin_skkm']);
}

// 3. Ambil dari pengajuan_skkm yang valid
$query_pengajuan = "
    SELECT kategori_kegiatan, SUM(poin_skkm) AS total_poin
    FROM pengajuan_skkm
    WHERE nim = ?
      AND status_verifikasi_bem = 'Valid'
      AND status_verifikasi_kemahasiswaan = 'Valid'
    GROUP BY kategori_kegiatan
";
$stmt2 = $conn->prepare($query_pengajuan);
$stmt2->bind_param("s", $nim);
$stmt2->execute();
$result_pengajuan = $stmt2->get_result();

while ($row = $result_pengajuan->fetch_assoc()) {
    tambah_poin($ketentuan, $row['kategori_kegiatan'], 'poin_pengajuan', (int)$row['total_poin']);
}

// 4. Ambil dari berkas_kemahasiswaan (semua dihitung)
$query_kemahasiswaan = "
    SELECT kategori_kegiatan, poin_skkm
    FROM berkas_kemahasiswaan
    WHERE nim = ?
";
$stmt_kemahasiswaan = $conn->prepare($query_kemahasiswaan);
$stmt_kemahasiswaan->bind_param("s", $nim);
$stmt_kemahasiswaan->execute();
$result_kemahasiswaan = $stmt_kemahasiswaan->get_result();

while ($row = $result_kemahasiswaan->fetch_assoc()) {
    tambah_poin($ketentuan, $row['kategori_kegiatan'], 'poin_internal', (int)$row['poin_skkm']);
}

// Urutkan kategori sesuai prioritas
$urutan_prioritas = [
    'Kegiatan Wajib' => 1,
    'Bidang Akademik & Ilmiah' => 2,
    'Bidang Minat Bakat Seni & Olahraga' => 3,
    'Bidang Organisasi & Sosial' => 4
];

uksort($ketentuan, function($a, $b) use ($urutan_prioritas) {
    $pa = $urutan_prioritas[$a] ?? 999;
    $pb = $urutan_prioritas[$b] ?? 999;
    return $pa <=> $pb;
});

// HTML ke PDF
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
            <th>Poin dari Berkas & Kemahasiswaan</th>
            <th>Poin dari Pengajuan SKKM</th>
            <th>Total Poin</th>
            <th>Minimal Poin</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>";

foreach ($ketentuan as $kategori => $info) {
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

// Generate PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Rekap_Poin_SKKM_$nim.pdf", ["Attachment" => false]);
exit;
