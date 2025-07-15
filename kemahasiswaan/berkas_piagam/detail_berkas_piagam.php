<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// 🔒 Cek login
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    header("Location: ../index.php");
    exit();
}

// 🔁 Fungsi Ambil Data
function getPiagam($conn, $filterKosong = false) {
    $sql = "
        SELECT 
            p.id_berkas_piagam,
            p.nama_kegiatan,
            p.nama_penerima,
            p.tanggal_kegiatan,
            p.nomor_sertifikat_piagam,
            p.tanggal_pengajuan,
            p.tanggal_dikeluarkan,
            p.keterangan,
            o.nama_ormawa
        FROM berkas_piagam p
        JOIN user_detail_ormawa o ON p.id_ormawa = o.id_ormawa
    ";

    if ($filterKosong) {
        $sql .= " WHERE (p.nomor_sertifikat_piagam IS NULL OR p.nomor_sertifikat_piagam = '' OR p.nomor_sertifikat_piagam = '0')";
    }

    $sql .= " ORDER BY p.id_berkas_piagam ASC";

    return $conn->query($sql);
}
function countPiagamTanpaNomor($conn) {
    $sql = "
        SELECT COUNT(*) AS jumlah 
        FROM berkas_piagam 
        WHERE 
            (nomor_sertifikat_piagam IS NULL 
             OR nomor_sertifikat_piagam = '' 
             OR nomor_sertifikat_piagam = '0')
    ";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return (int) $row['jumlah'];
    }
    return 0;
}


// ✅ Hapus data terpilih
if (isset($_POST['hapus_terpilih']) && !empty($_POST['pilih'])) {
    $ids = $_POST['pilih'];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    
    $stmt = $conn->prepare("DELETE FROM berkas_piagam WHERE id_berkas_piagam IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    
    echo "<script>alert('Data berhasil dihapus.'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    exit;
}

// ✅ Ekspor Excel jika diminta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['export_all']) || isset($_POST['export_belum']))) {
    $isKosong = isset($_POST['export_belum']);
    $result = getPiagam($conn, $isKosong);

    if (!$result || $result->num_rows === 0) {
        echo "<script>alert('Tidak ada data untuk diekspor.'); window.history.back();</script>";
        exit;
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = ['No', 'ID Berkas', 'Nama Ormawa', 'Nama Kegiatan', 'Nama Penerima', 'Tanggal Kegiatan', 'Nomor Sertifikat', 'Tanggal Pengajuan', 'Tanggal Dikeluarkan', 'Keterangan'];
    $sheet->fromArray([$headers], null, 'A1');
    $sheet->getStyle('I2:I5')->getNumberFormat()->setFormatCode('yyyy-mm-dd');


    $no = 1;
$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue("A$rowNumber", $no++);
    $sheet->setCellValue("B$rowNumber", $row['id_berkas_piagam']);
    $sheet->setCellValue("C$rowNumber", $row['nama_ormawa']);
    $sheet->setCellValue("D$rowNumber", $row['nama_kegiatan']);
    $sheet->setCellValue("E$rowNumber", $row['nama_penerima']);
    $sheet->setCellValue("F$rowNumber", $row['tanggal_kegiatan']);

    // 🧮 Kolom G - Gunakan formula Excel untuk Nomor Sertifikat
    $sheet->setCellValue("G$rowNumber", "=B$rowNumber&\"/Piagam/\"&\"KMHS/\"&ROMAN(MONTH(I$rowNumber))&\"/\"&YEAR(I$rowNumber)");

    $sheet->setCellValue("H$rowNumber", $row['tanggal_pengajuan']);
    $sheet->setCellValue("I$rowNumber", $row['tanggal_dikeluarkan']);
    $sheet->setCellValue("J$rowNumber", $row['keterangan']);

    $rowNumber++;
}


    $filename = $isKosong 
        ? "Berkas_Piagam_Tanpa_Nomor_Sertifikat.xlsx"
        : "Semua_Berkas_Piagam_Valid.xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}
$countKosongPiagam = countPiagamTanpaNomor($conn);

// ✅ Ambil data untuk ditampilkan
$result = getPiagam($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['arsipkan_piagam'])) {
    $tahun = date('Y');

    $sql = "
        INSERT INTO arsip_piagam (
            id_berkas_piagam, nama_kegiatan, id_ormawa, id_kemahasiswaan,
            nama_penerima, tanggal_kegiatan, tanggal_pengajuan,
            tanggal_dikeluarkan, nomor_sertifikat_piagam, keterangan, tahun_arsip
        )
        SELECT 
            id_berkas_piagam, nama_kegiatan, id_ormawa, id_kemahasiswaan,
            nama_penerima, tanggal_kegiatan, tanggal_pengajuan,
            tanggal_dikeluarkan, nomor_sertifikat_piagam, keterangan, $tahun
        FROM berkas_piagam
    ";

    if ($conn->query($sql)) {
        $conn->query("TRUNCATE TABLE berkas_piagam");
        echo "<script>alert('✅ Semua data berhasil diarsipkan dan dihapus.'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
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
    <title>Semua Berkas Piagam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h3 class="text-center mb-4">📜 Semua Berkas Piagam</h3>

    <a href="../index.php" class="btn btn-secondary">Kembali</a>
    <!-- <a href="upload_batch_berkas_piagam.php" class="btn btn-primary">✏️ Update Data Sertifikat</a> -->
    <a href="input_berkas_piagam.php" class="btn btn-primary">Input Data Sertifikat</a>

    <form method="POST" class="mb-3">
        <div class="d-flex justify-content-between mb-3">
            <button type="submit" name="hapus_terpilih" class="btn btn-danger mt-3" onclick="return confirm('Yakin ingin menghapus data terpilih?')">
                🗑️ Hapus Terpilih
            </button>
            <div>
                <button type="submit" name="export_all" class="btn btn-success me-2">⬇️ Export Semua Data</button>
                <a href="generate_sertifikat_piagam.php" class="btn btn-info">
    📝 Generate Sertifikat Piagam
    <?php if ($countKosongPiagam > 0): ?>
        <span class="badge bg-danger"><?= $countKosongPiagam ?></span>
    <?php endif; ?>
</a>

            </div>
        </div>

        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari Ormawa, Kegiatan, Penerima, Sertifikat...">
        </div>
<?php if (date('m') === '06'): ?>
    <div class="mb-3">
        <button type="submit" name="arsipkan_piagam" class="btn btn-warning"
            onclick="return confirm('⚠️ Semua data akan dipindahkan ke arsip lalu dihapus dari tabel utama. Lanjutkan?')">
            📦 Arsipkan & Hapus Semua Data
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
                    <th>Nama Penerima</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Nomor Sertifikat</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Tanggal Dikeluarkan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): 
                    $no = 1;
                    while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="text-center"><input type="checkbox" name="pilih[]" value="<?= $row['id_berkas_piagam'] ?>"></td>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_ormawa']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['nama_penerima']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['nomor_sertifikat_piagam']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_dikeluarkan']) ?></td>
                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="10" class="text-center">Tidak ada data piagam ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
</div>

<script>
    // Pencarian live
    document.getElementById('searchInput').addEventListener('input', function () {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(keyword) ? '' : 'none';
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
