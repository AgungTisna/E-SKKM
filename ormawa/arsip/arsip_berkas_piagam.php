<?php
session_start();
require_once('../../koneksi.php');
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Cek role Ormawa
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Ormawa') {
    echo "<script>alert('Akses ditolak'); window.location.href='../index.php';</script>";
    exit();
}

// Ambil id_ormawa
$id_user = $_SESSION['id_user'];
$stmt = $conn->prepare("SELECT id_ormawa FROM user_detail_ormawa WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$id_ormawa = $data['id_ormawa'] ?? null;

if (!$id_ormawa) {
    die("❌ Ormawa tidak ditemukan.");
}

// Filter tahun
$tahun_filter = isset($_GET['tahun']) ? intval($_GET['tahun']) : null;

// Query arsip_piagam
$sql = "SELECT * FROM arsip_piagam WHERE id_ormawa = ?";
$params = [$id_ormawa];
$types = "i";

$sql = "SELECT * FROM arsip_piagam WHERE id_ormawa = ?";
$params = [$id_ormawa];
$types = "i";

if ($tahun_filter !== null) {
    $sql .= " AND tahun_arsip = ?";
    $params[] = $tahun_filter;
    $types .= "i";
}


$sql .= " ORDER BY tanggal_kegiatan DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result_data = $stmt->get_result();

// Export Excel jika diminta
if (isset($_GET['export']) && $tahun_filter) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Arsip Piagam $tahun_filter");

    $headers = ['No', 'Nama Kegiatan', 'Nama Penerima', 'Nomor Sertifikat', 'Tanggal Kegiatan', 'Pengajuan', 'Dikeluarkan', 'Keterangan', 'Tahun Arsip'];
    $col = 'A';
    foreach ($headers as $h) {
        $sheet->setCellValue($col . '1', $h);
        $col++;
    }

    $rowNum = 2;
    $no = 1;
    $result_data->data_seek(0);
    while ($row = $result_data->fetch_assoc()) {
        $sheet->setCellValue("A$rowNum", $no++);
        $sheet->setCellValue("B$rowNum", $row['nama_kegiatan']);
        $sheet->setCellValue("C$rowNum", $row['nama_penerima']);
        $sheet->setCellValue("D$rowNum", $row['nomor_sertifikat_piagam']);
        $sheet->setCellValue("E$rowNum", $row['tanggal_kegiatan']);
        $sheet->setCellValue("F$rowNum", $row['tanggal_pengajuan']);
        $sheet->setCellValue("G$rowNum", $row['tanggal_dikeluarkan']);
        $sheet->setCellValue("H$rowNum", $row['keterangan']);
        $sheet->setCellValue("I$rowNum", $row['tahun_arsip']);
        $rowNum++;
    }

    $filename = "arsip_piagam_$tahun_filter.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Arsip Berkas Piagam</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
        <?php include '../navbar.php'; ?>
<div class="container mt-5">
    <h3 class="text-center mb-4">📜 Arsip Berkas Piagam</h3>
        <a href="../index.php" class="btn btn-secondary mb-3">← Kembali</a>

    <!-- Filter Tahun dan Export -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-auto">
            <select name="tahun" class="form-select" required>
                <option value="">-- Pilih Tahun Arsip --</option>
                <?php
                $q = "SELECT DISTINCT tahun_arsip FROM arsip_piagam WHERE id_ormawa = ? ORDER BY tahun_arsip DESC";
                $st = $conn->prepare($q);
                $st->bind_param("i", $id_ormawa);
                $st->execute();
                $rs = $st->get_result();
                while ($t = $rs->fetch_assoc()):
                ?>
                    <option value="<?= $t['tahun_arsip'] ?>" <?= $tahun_filter == $t['tahun_arsip'] ? 'selected' : '' ?>>
                        <?= $t['tahun_arsip'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
        <?php if ($tahun_filter): ?>
        <div class="col-auto">
            <a href="?tahun=<?= $tahun_filter ?>&export=1" class="btn btn-success">Export Excel</a>
        </div>
        <?php endif; ?>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>Nama Kegiatan</th>
                    <th>Nama Penerima</th>
                    <th>Nomor Sertifikat</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Dikeluarkan</th>
                    <th>Keterangan</th>
                    <th>Tahun Arsip</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_data && $result_data->num_rows > 0): 
                    $no = 1;
                    while ($row = $result_data->fetch_assoc()): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['nama_penerima']) ?></td>
                        <td><?= htmlspecialchars($row['nomor_sertifikat_piagam']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_dikeluarkan']) ?></td>
                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td class="text-center"><?= $row['tahun_arsip'] ?></td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="9" class="text-center">Tidak ada arsip ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
