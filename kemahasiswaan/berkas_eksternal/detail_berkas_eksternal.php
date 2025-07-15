<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// 🔐 Cek role Kemahasiswaan
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    header("Location: ../index.php");
    exit();
}
function countEksternalTanpaSertifikat($conn) {
    $sql = "
        SELECT COUNT(*) AS jumlah 
        FROM berkas_eksternal 
        WHERE 
            (nomor_sertifikat_eksternal IS NULL 
            OR nomor_sertifikat_eksternal = '' 
            OR nomor_sertifikat_eksternal = '0')
    ";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return (int) $row['jumlah'];
    }
    return 0;
}

// ✅ Fungsi untuk ambil data (semua / kosong)
function getDataEksternal($conn, $onlyKosong = false) {
    $sql = "
        SELECT 
            b.id_berkas_eksternal, b.nama_kegiatan, b.nama_peserta,
            b.tanggal_kegiatan,
            b.nomor_sertifikat_eksternal, b.tanggal_pengajuan,
            b.tanggal_dikeluarkan, b.keterangan,
            uo.nama_ormawa
        FROM berkas_eksternal b
        JOIN user_detail_ormawa uo ON b.id_ormawa = uo.id_ormawa
    ";

    if ($onlyKosong) {
        $sql .= " WHERE b.nomor_sertifikat_eksternal IS NULL OR b.nomor_sertifikat_eksternal = '' OR b.nomor_sertifikat_eksternal = '0' ";
    }

    $sql .= " ORDER BY b.id_berkas_eksternal ASC";

    return $conn->query($sql);
}

// ✅ Hapus data eksternal terpilih
if (isset($_POST['hapus_terpilih']) && !empty($_POST['pilih'])) {
    $ids = $_POST['pilih'];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conn->prepare("DELETE FROM berkas_eksternal WHERE id_berkas_eksternal IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();

    echo "<script>alert('Data berhasil dihapus.'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    exit();
}

// ✅ Export Excel jika tombol ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['export_all']) || isset($_POST['export_kosong']))) {
    $isKosong = isset($_POST['export_kosong']);
    $result = getDataEksternal($conn, $isKosong);

    if (!$result || $result->num_rows == 0) {
        echo "<script>alert('Tidak ada data untuk diekspor.'); window.history.back();</script>";
        exit();
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = ['No', 'ID Berkas', 'Nama Ormawa', 'Nama Kegiatan', 'Nama Peserta', 'Tanggal Kegiatan', 'Nomor Sertifikat', 'Tanggal Pengajuan', 'Tanggal Dikeluarkan', 'Keterangan'];
    $sheet->fromArray([$headers], null, 'A1');
    $sheet->getStyle('I2:I5')->getNumberFormat()->setFormatCode('yyyy-mm-dd');

    $no = 1;
    $rowNum = 2;
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue("A$rowNum", $no++);
$sheet->setCellValue("B$rowNum", $row['id_berkas_eksternal']);
$sheet->setCellValue("C$rowNum", $row['nama_ormawa']);
$sheet->setCellValue("D$rowNum", $row['nama_kegiatan']);
$sheet->setCellValue("E$rowNum", $row['nama_peserta']);
$sheet->setCellValue("F$rowNum", $row['tanggal_kegiatan']);

// Kolom G → isi dengan formula Excel
$sheet->setCellValue("G$rowNum", "=B$rowNum&\"/Srtf.eks/\"&\"KMHS/\"&ROMAN(MONTH(I$rowNum))&\"/\"&YEAR(I$rowNum)");

$sheet->setCellValue("H$rowNum", $row['tanggal_pengajuan']);
$sheet->setCellValue("I$rowNum", $row['tanggal_dikeluarkan']);
$sheet->setCellValue("J$rowNum", $row['keterangan']);

        $rowNum++;
    }

    $filename = $isKosong 
        ? "Berkas_Eksternal_Tanpa_Nomor_Sertifikat.xlsx" 
        : "Semua_Berkas_Eksternal.xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit();
}
$countKosong = countEksternalTanpaSertifikat($conn);

// ✅ Data untuk tampilan
$result = getDataEksternal($conn, false);

// 🔁 Reset AUTO_INCREMENT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_auto_increment_eksternal'])) {
    $result = $conn->query("SELECT MAX(id_berkas_eksternal) AS max_id FROM berkas_eksternal");
    $maxId = 0;
    if ($result && $row = $result->fetch_assoc()) {
        $maxId = (int)$row['max_id'];
    }
    $nextAutoIncrement = $maxId + 1;

    $conn->query("ALTER TABLE berkas_eksternal AUTO_INCREMENT = $nextAutoIncrement");

    echo "<script>alert('AUTO_INCREMENT berhasil direset ke $nextAutoIncrement'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    exit();
}

// 🧨 Truncate Semua Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['truncate_eksternal'])) {
    $conn->query("TRUNCATE TABLE berkas_eksternal");
    echo "<script>alert('Semua data berhasil dihapus.'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    exit();
}
// 🔁 Arsip dan truncate data eksternal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['arsip_dan_truncate'])) {
    $tahunArsip = date('Y');

    $sqlInsert = "
        INSERT INTO arsip_eksternal (
            id_berkas_eksternal, id_ormawa, nama_kegiatan, id_kemahasiswaan,
            nama_peserta, tanggal_kegiatan, nomor_sertifikat_eksternal,
            tanggal_pengajuan, tanggal_dikeluarkan, keterangan, tahun_arsip
        )
        SELECT 
            id_berkas_eksternal, id_ormawa, nama_kegiatan, id_kemahasiswaan,
            nama_peserta, tanggal_kegiatan, nomor_sertifikat_eksternal,
            tanggal_pengajuan, tanggal_dikeluarkan, keterangan, $tahunArsip
        FROM berkas_eksternal
    ";

    if ($conn->query($sqlInsert)) {
        if ($conn->query("TRUNCATE TABLE berkas_eksternal")) {
            echo "<script>alert('✅ Data berhasil diarsipkan dan semua data berkas_eksternal dihapus.'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
        } else {
            echo "<script>alert('❌ Arsip berhasil, tapi gagal menghapus data dari berkas_eksternal.');</script>";
        }
    } else {
        echo "<script>alert('❌ Gagal mengarsipkan data: " . addslashes($conn->error) . "');</script>";
    }
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Semua Berkas Eksternal - Kemahasiswaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h3 class="mb-4 text-center">📋 Semua Berkas Peserta Eksternal</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">Kembali</a>
    <!-- <a href="upload_batch_berkas_eksternal.php" class="btn btn-primary mb-3">✏️ Update Data Sertifikat</a> -->
    <a href="input_berkas_eksternal.php" class="btn btn-primary mb-3"> Input Data Sertifikat</a>

    <!-- Form utama -->
    <form method="POST">
        <div class="d-flex justify-content-between mb-3">
            <button type="submit" name="hapus_terpilih" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data terpilih?')">
                🗑️ Hapus Terpilih
            </button>
            <div>
                <button type="submit" name="export_all" class="btn btn-success me-2">📥 Export Semua</button>
                <a href="generate_sertifikat_eksternal.php" class="btn btn-info">
    📝 Generate Sertifikat Eksternal
    <?php if ($countKosong > 0): ?>
        <span class="badge bg-danger"><?= $countKosong ?></span>
    <?php endif; ?>
</a>

</button>
            </div>
        </div>

        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari Ormawa, Kegiatan, Peserta, Sertifikat...">
        </div>
<?php if (date('m') === '06'): ?>
    <div class="mb-3 d-flex gap-2">
        <button type="submit" name="arsip_dan_truncate" class="btn btn-warning"
            onclick="return confirm('⚠️ Semua data akan dipindahkan ke arsip dan tabel akan dihapus. Anda yakin?')">
            📦 Arsipkan & Hapus Semua
        </button>
    </div>
<?php endif; ?>


        <table class="table table-bordered table-hover">
            <thead class="table-dark text-center">
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>No</th>
                    <th>Nama Ormawa</th>
                    <th>Nama Kegiatan</th>
                    <th>Nama Peserta</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Nomor Sertifikat</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Tanggal Dikeluarkan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td class='text-center'><input type='checkbox' name='pilih[]' value='{$row['id_berkas_eksternal']}'></td>
                        <td class='text-center'>{$no}</td>
                        <td>" . htmlspecialchars($row['nama_ormawa']) . "</td>
                        <td>" . htmlspecialchars($row['nama_kegiatan']) . "</td>
                        <td>" . htmlspecialchars($row['nama_peserta']) . "</td>
                        <td>" . htmlspecialchars($row['tanggal_kegiatan']) . "</td>
                        <td>" . htmlspecialchars($row['nomor_sertifikat_eksternal']) . "</td>
                        <td>" . htmlspecialchars($row['tanggal_pengajuan']) . "</td>
                        <td>" . htmlspecialchars($row['tanggal_dikeluarkan']) . "</td>
                        <td>" . htmlspecialchars($row['keterangan']) . "</td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='10' class='text-center'>Tidak ada data ditemukan.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </form>
</div>

<script>
    // Live search
    document.getElementById('searchInput').addEventListener('input', function () {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(keyword) ? '' : 'none';
        });
    });

    // Checkbox "Pilih Semua"
    document.getElementById('checkAll').addEventListener('click', function () {
        const checkboxes = document.querySelectorAll('input[name="pilih[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>

</body>
</html>

<?php $conn->close(); ?>
