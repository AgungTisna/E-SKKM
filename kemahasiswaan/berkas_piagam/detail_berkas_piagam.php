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

    $rowNumber = 2;
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $sheet->fromArray([
            $no++,
            $row['id_berkas_piagam'],
            $row['nama_ormawa'],
            $row['nama_kegiatan'],
            $row['nama_penerima'],
            $row['tanggal_kegiatan'],
            $row['nomor_sertifikat_piagam'],
            $row['tanggal_pengajuan'],
            $row['tanggal_dikeluarkan'],
            $row['keterangan']
        ], null, "A$rowNumber");
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

// ✅ Ambil data untuk ditampilkan
$result = getPiagam($conn);
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

    <a href="../index.php" class="btn btn-secondary">← Kembali</a>
    <a href="upload_batch_berkas_piagam.php" class="btn btn-primary">✏️ Update Data Sertifikat</a>

    <form method="POST" class="mb-3">
        <div class="d-flex justify-content-between mb-3">
            <button type="submit" name="hapus_terpilih" class="btn btn-danger mt-3" onclick="return confirm('Yakin ingin menghapus data terpilih?')">
                🗑️ Hapus Terpilih
            </button>
            <div>
                <button type="submit" name="export_all" class="btn btn-success me-2">⬇️ Export Semua Data</button>
                <button type="submit" name="export_belum" class="btn btn-warning">⚠️ Export Tanpa Nomor Sertifikat</button>
            </div>
        </div>

        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari Ormawa, Kegiatan, Penerima, Sertifikat...">
        </div>

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
