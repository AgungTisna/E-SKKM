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
function countBerkasInternalTanpaSertifikatDenganPoin($conn) {
    $sql = "
        SELECT COUNT(*) as jumlah 
        FROM berkas_internal 
        WHERE 
            (nomor_sertifikat_internal IS NULL 
            OR nomor_sertifikat_internal = '' 
            OR nomor_sertifikat_internal = '0')
            AND id_bem IS NOT NULL
            AND poin_skkm IS NOT NULL
            AND poin_skkm > 0
    ";

    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return (int) $row['jumlah'];
    }
    return 0;
}

function getBerkasInternalOnly($conn, $filterKosong = false) {
    $whereKosong = "";

    if ($filterKosong) {
        $whereKosong = "AND (
            (bi.nomor_sertifikat_internal IS NULL OR bi.nomor_sertifikat_internal = '' OR bi.nomor_sertifikat_internal = '0')
            AND bi.kategori_kegiatan IS NOT NULL AND bi.kategori_kegiatan != ''
            AND bi.tingkat IS NOT NULL AND bi.tingkat != ''
            AND bi.poin_skkm IS NOT NULL AND bi.poin_skkm > 0
        )";
    }

    $sql = "
        SELECT 
            bi.id_berkas_internal AS id,
            bi.nim,
            COALESCE(u.nama, 'Mahasiswa belum ada di sistem') AS nama_mahasiswa,
            bi.nama_kegiatan,
            bi.partisipasi,
            bi.kategori_kegiatan,
            bi.tingkat,
            bi.poin_skkm,
            bi.nomor_sertifikat_internal,
            bi.tanggal_kegiatan,
            bi.tanggal_pengajuan,
            bi.tanggal_dikeluarkan,
            o.nama_ormawa
        FROM berkas_internal bi
        LEFT JOIN user_detail_mahasiswa m ON bi.nim = m.nim
        LEFT JOIN user u ON m.id_user = u.id_user
        JOIN user_detail_ormawa o ON bi.id_ormawa = o.id_ormawa
        WHERE bi.id_bem IS NOT NULL $whereKosong
        ORDER BY bi.nama_kegiatan DESC
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
    $result_berkas = getBerkasInternalOnly($conn, $isKosong);

    if (!$result_berkas || $result_berkas->num_rows == 0) {
        echo "<script>alert('Tidak ada data untuk diekspor.'); window.history.back();</script>";
        exit();
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = ['No', 'ID Berkas', 'NIM', 'Nama Mahasiswa', 'Nama Kegiatan', 'Tanggal Kegiatan', 'Partisipasi',
                'Kategori Kegiatan', 'Tingkat', 'Poin SKKM', 'Nomor Sertifikat', 'Tanggal Pengajuan', 'Tanggal Dikeluarkan', 'Nama Ormawa'];
    $sheet->fromArray([$headers], null, 'A1');
    $sheet->getStyle('M2:M5')->getNumberFormat()->setFormatCode('yyyy-mm-dd');

    $rowNumber = 2;
    $no = 1;
    while ($row = $result_berkas->fetch_assoc()) {
        $sheet->setCellValue("A$rowNumber", $no++);
$sheet->setCellValue("B$rowNumber", $row['id']);
$sheet->setCellValue("C$rowNumber", $row['nim']);
$sheet->setCellValue("D$rowNumber", $row['nama_mahasiswa']);
$sheet->setCellValue("E$rowNumber", $row['nama_kegiatan']);
$sheet->setCellValue("F$rowNumber", $row['tanggal_kegiatan']);
$sheet->setCellValue("G$rowNumber", $row['partisipasi']);
$sheet->setCellValue("H$rowNumber", $row['kategori_kegiatan']);
$sheet->setCellValue("I$rowNumber", $row['tingkat']);
$sheet->setCellValue("J$rowNumber", $row['poin_skkm']);

// Sisipkan formula Excel di kolom K
$sheet->setCellValue("K$rowNumber", "=B$rowNumber&\"/Srtf/\"&\"KMHS/\"&ROMAN(MONTH(M$rowNumber))&\"/\"&YEAR(M$rowNumber)");

 
$sheet->setCellValue("L$rowNumber", $row['tanggal_pengajuan']);
$sheet->setCellValue("M$rowNumber", $row['tanggal_dikeluarkan']);
$sheet->setCellValue("N$rowNumber", $row['nama_ormawa']);


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

$result_berkas = getBerkasInternalOnly($conn);
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
$countBerkasKosongInternal = countBerkasInternalTanpaSertifikatDenganPoin($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['arsipkan_dan_hapus'])) {
    $tahunArsip = date('Y');

    $sqlInsert = "
        INSERT INTO arsip_skkm (
            id_berkas_internal, id_ormawa, nim, nama_kegiatan, partisipasi,
            tanggal_kegiatan, tanggal_pengajuan, id_bem, kategori_kegiatan,
            tingkat, poin_skkm, id_kemahasiswaan, nomor_sertifikat_internal,
            tanggal_dikeluarkan, tahun_arsip
        )
        SELECT
            id_berkas_internal, id_ormawa, nim, nama_kegiatan, partisipasi,
            tanggal_kegiatan, tanggal_pengajuan, id_bem, kategori_kegiatan,
            tingkat, poin_skkm, id_kemahasiswaan, nomor_sertifikat_internal,
            tanggal_dikeluarkan, $tahunArsip
        FROM berkas_internal
    ";

    if ($conn->query($sqlInsert)) {
        if ($conn->query("TRUNCATE TABLE berkas_internal")) {
            echo "<script>alert('✅ Data berhasil diarsipkan dan tabel di-reset.'); window.location.href='detail_berkas_internal.php';</script>";
        } else {
            echo "<script>alert('❌ Arsip berhasil, tapi gagal TRUNCATE tabel.');</script>";
        }
    } else {
        echo "<script>alert('❌ Gagal mengarsipkan: " . addslashes($conn->error) . "');</script>";
    }
    exit();
}

// Hitung entri valid poin_skkm tapi belum punya nomor sertifikat
$sqlJumlahPoinTanpaSertifikat = "
    SELECT COUNT(*) as jumlah 
    FROM berkas_internal 
    WHERE poin_skkm > 0 
      AND (nomor_sertifikat_internal IS NULL OR nomor_sertifikat_internal = '' OR nomor_sertifikat_internal = '0')
";
$countPoinTanpaSertifikat = 0;
$resultJumlah = $conn->query($sqlJumlahPoinTanpaSertifikat);
if ($resultJumlah && $row = $resultJumlah->fetch_assoc()) {
    $countPoinTanpaSertifikat = $row['jumlah'];
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
    <h2 class="text-center mb-4">Seluruh Berkas Peserta Internal yang Sudah Divalidasi oleh BEM</h2>

    <form method="POST">
        <div class="mb-3 d-flex justify-content-between">
            <div>
                <a href="../index.php" class="btn btn-secondary">Kembali</a>
                <!-- <a href="upload_batch_berkas_internal.php" class="btn btn-primary">✏️ Update Sertifikat</a> -->
                <a href="input_berkas_internal.php" class="btn btn-primary">Upload Berkas</a>
            </div>
            <div>
                <a href="export_semua_sertifikat.php" class="btn btn-success me-2">📥 Download Semua</a>
<a href="generate_sertifikat.php" class="btn btn-info position-relative">
    📝 Generate Nomor Sertifikat
    <?php if ($countPoinTanpaSertifikat > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $countPoinTanpaSertifikat ?>
        </span>
    <?php endif; ?>
</a>

                <!-- <button type="submit" name="download_belum" class="btn btn-warning">
    ⚠️ Belum Ada Sertifikat
    <?php if ($countBerkasKosongInternal > 0): ?>
        <span class="badge bg-danger"><?= $countBerkasKosongInternal ?></span>
    <?php endif; ?>
</button> -->
                <!-- <a href="kegiatan_wajib/detail_kegiatan_wajib.php" class="btn btn-primary">Kegiatan Wajib</a> -->
            </div>
        </div>

        <input type="text" id="searchInput" class="form-control mb-3" placeholder="🔍 Cari NIM, Nama, Kegiatan...">
        <!-- Tambahkan tombol hapus di bagian atas tabel -->
<div class="mb-3">
    <button type="submit" name="hapus_terpilih" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data terpilih?')">
        🗑️ Hapus Terpilih
    </button>
</div>
<?php if (date('m') === '06'): ?>
    <button type="submit" name="arsipkan_dan_hapus" class="btn btn-warning mb-3"
        onclick="return confirm('Semua data akan dipindahkan ke arsip dan dihapus. Lanjutkan?')">
        📦 Arsip & Hapus Semua
    </button>
<?php endif; ?>


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
                $daftarDuplikat = implode(', ', array_keys($duplikatNomorSertifikat));
                echo <<<HTML
                <div class="alert alert-warning d-flex justify-content-between align-items-center">
                    <div>
                        ⚠️ Duplikasi Nomor Sertifikat ditemukan: 
                        <strong>{$daftarDuplikat}</strong>
                    </div>
                    <a href="replace_sertifikat.php" class="btn btn-danger btn-sm">
                        🔁 Replace Nomor Duplikat
                    </a>
                </div>
                HTML;
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
                    echo '<td>' . ($entry['nama_mahasiswa'] ? htmlspecialchars($entry['nama_mahasiswa']) : '<em class="text-danger">Mahasiswa belum ditambahkan ke sistem</em>') . '</td>';
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
