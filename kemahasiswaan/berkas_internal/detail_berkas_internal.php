<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../index.php';</script>";
    exit();
}

function getGabunganBerkas($conn, $filterKosong = false) {
    $whereKosong = "";
    if ($filterKosong) {
        $whereKosong = "AND (nomor_sertifikat_internal IS NULL OR nomor_sertifikat_internal = '' OR nomor_sertifikat_internal = '0')";
    }

    $sql = "
        SELECT 
            bi.id_berkas_internal AS id,
            bi.nim,
            u.nama AS nama_mahasiswa,
            bi.nama_kegiatan,
            bi.partisipasi,
            bi.kategori_kegiatan,
            bi.tingkat,
            bi.poin_skkm,
            bi.nomor_sertifikat_internal,
            bi.tanggal_kegiatan,
            bi.tanggal_pengajuan,
            bi.tanggal_dikeluarkan,
            o.nama_ormawa,
            ubem.nama AS nama_bem,
            'internal' AS sumber
        FROM berkas_internal bi
        JOIN user_detail_mahasiswa m ON bi.nim = m.nim
        JOIN user u ON m.id_user = u.id_user
        JOIN user_detail_ormawa o ON bi.id_ormawa = o.id_ormawa
        LEFT JOIN user_detail_bem b ON bi.id_bem = b.id_user
        LEFT JOIN user ubem ON b.id_user = ubem.id_user
        WHERE bi.id_bem IS NOT NULL $whereKosong

        UNION ALL

        SELECT 
            bb.id_berkas_bem AS id,
            bb.nim,
            u.nama AS nama_mahasiswa,
            bb.nama_kegiatan,
            bb.partisipasi,
            bb.kategori_kegiatan,
            bb.tingkat,
            bb.poin_skkm,
            bb.nomor_sertifikat_internal,
            bb.tanggal_kegiatan,
            bb.tanggal_pengajuan,
            bb.tanggal_dikeluarkan,
            'BEM' AS nama_ormawa,
            '-' AS nama_bem,
            'bem' AS sumber
        FROM berkas_bem bb
        LEFT JOIN user_detail_mahasiswa udm ON bb.nim = udm.nim
        LEFT JOIN user u ON udm.id_user = u.id_user
        WHERE bb.id_kemahasiswaan IS NOT NULL 
        " . ($filterKosong ? "AND (bb.nomor_sertifikat_internal IS NULL OR bb.nomor_sertifikat_internal = '' OR bb.nomor_sertifikat_internal = '0')" : "AND (bb.nomor_sertifikat_internal IS NOT NULL AND bb.nomor_sertifikat_internal != '' AND bb.nomor_sertifikat_internal != '0')") . "

        ORDER BY tanggal_pengajuan DESC
    ";

    return $conn->query($sql);
}

if (isset($_POST['hapus_terpilih']) && !empty($_POST['pilih'])) {
    $ids = $_POST['pilih'];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conn->prepare("DELETE FROM berkas_internal WHERE id_berkas_internal IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();

    echo "<script>alert('Data berhasil dihapus.'); window.location.href='detail_berkas_internal.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['download_excel']) || isset($_POST['download_belum']))) {
    $isKosong = isset($_POST['download_belum']);
    $result_berkas = getGabunganBerkas($conn, $isKosong);

    if (!$result_berkas || $result_berkas->num_rows == 0) {
        echo "<script>alert('Tidak ada data untuk diekspor.'); window.history.back();</script>";
        exit();
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = ['No', 'ID Berkas', 'NIM', 'Nama Mahasiswa', 'Nama Kegiatan', 'Tanggal Kegiatan', 'Partisipasi',
                'Kategori Kegiatan', 'Tingkat', 'Poin SKKM', 'Nomor Sertifikat', 'Tanggal Pengajuan', 'Tanggal Dikeluarkan', 'Nama Ormawa', 'Nama BEM'];
    $sheet->fromArray([$headers], null, 'A1');

    $rowNumber = 2;
    $no = 1;
    while ($row = $result_berkas->fetch_assoc()) {
        $sheet->fromArray([
            $no++, $row['id'], $row['nim'], $row['nama_mahasiswa'],
            $row['nama_kegiatan'], $row['tanggal_kegiatan'], $row['partisipasi'], $row['kategori_kegiatan'],
            $row['tingkat'], $row['poin_skkm'], $row['nomor_sertifikat_internal'],
            $row['tanggal_pengajuan'], $row['tanggal_dikeluarkan'], $row['nama_ormawa'], $row['nama_bem']
        ], null, "A$rowNumber");
        $rowNumber++;
    }

    $filename = $isKosong 
        ? "Berkas_Internal_Tanpa_Nomor_Sertifikat.xlsx" 
        : "Seluruh_Berkas_Internal.xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit();
}

$result_berkas = getGabunganBerkas($conn);
$data_berkas = [];
while ($row = $result_berkas->fetch_assoc()) {
    $data_berkas[] = $row;
}

$sqlCountBem = "
    SELECT COUNT(*) as jumlah 
    FROM berkas_bem 
    WHERE (nomor_sertifikat_internal IS NULL OR nomor_sertifikat_internal = '' OR nomor_sertifikat_internal = '0') 
    AND id_kemahasiswaan IS NOT NULL
";
$countBerkasKosongBem = 0;
$resultCountBem = $conn->query($sqlCountBem);
if ($resultCountBem && $row = $resultCountBem->fetch_assoc()) {
    $countBerkasKosongBem = $row['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Seluruh Berkas Internal - Validasi BEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Seluruh Berkas Internal yang Sudah Divalidasi oleh BEM</h2>

    <form method="POST">
        <div class="mb-3 d-flex justify-content-between">
            <div>
                <a href="../index.php" class="btn btn-secondary">Kembali</a>
                <a href="upload_batch_berkas_internal.php" class="btn btn-primary">✏️ Update Sertifikat</a>
            </div>
            <div>
                <a href="export_semua_sertifikat.php" class="btn btn-success me-2">📥 Download Semua</a>
                <button type="submit" name="download_belum" class="btn btn-warning">⚠️ Belum Ada Sertifikat</button>
                <a href="kegiatan_wajib/detail_kegiatan_wajib.php" class="btn btn-primary">Kegiatan Wajib</a>
                <a href="kegiatan_bem/detail_kegiatan_bem.php" class="btn btn-primary">
                    Kegiatan BEM 
                    <?php if ($countBerkasKosongBem > 0): ?>
                        <span class="badge bg-danger"><?= $countBerkasKosongBem ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <input type="text" id="searchInput" class="form-control mb-3" placeholder="🔍 Cari NIM, Nama, Kegiatan...">
        <!-- Tambahkan tombol hapus di bagian atas tabel -->
<div class="mb-3">
    <button type="submit" name="hapus_terpilih" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data terpilih?')">
        🗑️ Hapus Terpilih
    </button>
</div>

        <?php
        if (!empty($data_berkas)) {
            $no = 1;
            $duplikatTracker = [];
            $nomorSertifikatCounter = [];
            $duplikatNomorSertifikat = [];

            foreach ($data_berkas as $row) {
                $key = implode('|', [
                    $row['nim'],
                    strtolower(trim($row['nama_mahasiswa'])),
                    strtolower(trim($row['nama_kegiatan'])),
                    strtolower(trim($row['partisipasi']))
                ]);

                $duplikatTracker[$key][] = $row;

                $nomor = trim($row['nomor_sertifikat_internal']);
                if ($nomor !== '' && $nomor !== '0') {
                    $nomorSertifikatCounter[$nomor] = ($nomorSertifikatCounter[$nomor] ?? 0) + 1;
                    if ($nomorSertifikatCounter[$nomor] > 1) {
                        $duplikatNomorSertifikat[$nomor] = true;
                    }
                }
            }

            if (!empty($duplikatNomorSertifikat)) {
                echo '<div class="alert alert-warning">⚠️ Duplikasi Nomor Sertifikat: <strong>' . implode(', ', array_keys($duplikatNomorSertifikat)) . '</strong></div>';
            }

            echo '<table class="table table-bordered">';
            echo '<thead class="table-dark text-center">
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>No</th>
                        <th>Nama Ormawa</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Nama Kegiatan</th>
                        <th>Tanggal Kegiatan</th>
                        <th>Partisipasi</th>
                        <th>Kategori</th>
                        <th>Tingkat</th>
                        <th>Poin</th>
                        <th>No Sertifikat</th>
                        <th>Pengajuan</th>
                        <th>Dikeluarkan</th>
                    </tr>
                </thead><tbody>';

            foreach ($duplikatTracker as $entries) {
                $isDupe = count($entries) > 1;
                foreach ($entries as $entry) {
                    $isDupeSertifikat = isset($duplikatNomorSertifikat[$entry['nomor_sertifikat_internal']]);
                    echo '<tr class="' . ($isDupe ? 'table-danger' : '') . '">';
                    echo '<td class="text-center"><input type="checkbox" name="pilih[]" value="' . $entry['id'] . '"></td>';
                    echo '<td class="text-center">' . $no++ . '</td>';
                    echo '<td>' . htmlspecialchars($entry['nama_ormawa']) . '</td>';
                    echo '<td>' . htmlspecialchars($entry['nim']) . '</td>';
                    echo '<td>' . htmlspecialchars($entry['nama_mahasiswa']) . '</td>';
                    echo '<td>' . htmlspecialchars($entry['nama_kegiatan']) . '</td>';
                    echo '<td>' . htmlspecialchars($entry['tanggal_kegiatan']) . '</td>';
                    echo '<td>' . htmlspecialchars($entry['partisipasi']) . '</td>';
                    echo '<td>' . htmlspecialchars($entry['kategori_kegiatan']) . '</td>';
                    echo '<td>' . htmlspecialchars($entry['tingkat']) . '</td>';
                    echo '<td>' . htmlspecialchars($entry['poin_skkm']) . '</td>';
                    echo '<td class="' . ($isDupeSertifikat ? 'table-warning' : '') . '">' . htmlspecialchars($entry['nomor_sertifikat_internal']) . '</td>';
                    echo '<td>' . htmlspecialchars($entry['tanggal_pengajuan']) . '</td>';
                    echo '<td>' . htmlspecialchars($entry['tanggal_dikeluarkan']) . '</td>';
                    echo '</tr>';
                }
            }

            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-info text-center">Tidak ada data ditemukan.</div>';
        }
        ?>
    </form>
</div>

<script>
document.getElementById('searchInput').addEventListener('input', function () {
    const keyword = this.value.toLowerCase();
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(keyword) ? '' : 'none';
    });
});

document.getElementById('checkAll').addEventListener('click', function () {
    const checkboxes = document.querySelectorAll('input[name="pilih[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>

</body>
</html>

<?php $conn->close(); ?>
