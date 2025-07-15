<?php
session_start();
require_once('../../koneksi.php');
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Cek akses
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Ormawa') {
    echo "<script>alert('Akses ditolak'); window.location.href='../index.php';</script>";
    exit();
}

// Ambil id_ormawa berdasarkan id_user
$id_user = $_SESSION['id_user'];
$stmt = $conn->prepare("SELECT id_ormawa FROM user_detail_ormawa WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$id_ormawa = $data['id_ormawa'] ?? null;

if (!$id_ormawa) {
    die("Ormawa tidak ditemukan.");
}

// Ambil tahun filter jika ada
$tahun_filter = isset($_GET['tahun']) ? intval($_GET['tahun']) : null;

// Query data arsip_skkm
$sql = "SELECT * FROM arsip_skkm WHERE id_ormawa = ?";
$params = [$id_ormawa];
$types = "i";

if ($tahun_filter) {
    $sql .= " AND tahun_arsip = ?";
    $params[] = $tahun_filter;
    $types .= "i";
}

$sql .= " ORDER BY tanggal_kegiatan DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$data_result = $stmt->get_result();

// Export Excel jika tombol diklik
if (isset($_GET['export']) && $tahun_filter) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Arsip SKKM $tahun_filter");

    $headers = ['No', 'NIM', 'Nama Kegiatan', 'Partisipasi', 'Kategori', 'Tingkat', 'Poin', 'Nomor Sertifikat', 'Tanggal Kegiatan', 'Tanggal Pengajuan', 'Tanggal Dikeluarkan', 'Tahun Arsip'];
    $col = 'A';
    foreach ($headers as $h) {
        $sheet->setCellValue($col . '1', $h);
        $col++;
    }

    $rowNum = 2;
    $no = 1;
    $data_result->data_seek(0); // Reset result pointer
    while ($row = $data_result->fetch_assoc()) {
        $sheet->setCellValue("A$rowNum", $no++);
        $sheet->setCellValue("B$rowNum", $row['nim']);
        $sheet->setCellValue("C$rowNum", $row['nama_kegiatan']);
        $sheet->setCellValue("D$rowNum", $row['partisipasi']);
        $sheet->setCellValue("E$rowNum", $row['kategori_kegiatan']);
        $sheet->setCellValue("F$rowNum", $row['tingkat']);
        $sheet->setCellValue("G$rowNum", $row['poin_skkm']);
        $sheet->setCellValue("H$rowNum", $row['nomor_sertifikat_internal']);
        $sheet->setCellValue("I$rowNum", $row['tanggal_kegiatan']);
        $sheet->setCellValue("J$rowNum", $row['tanggal_pengajuan']);
        $sheet->setCellValue("K$rowNum", $row['tanggal_dikeluarkan']);
        $sheet->setCellValue("L$rowNum", $row['tahun_arsip']);
        $rowNum++;
    }

    // Output Excel
    $filename = "arsip_internal_$tahun_filter.xlsx";
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
    <title>Arsip Berkas Internal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
<div class="container mt-5">
    <h3 class="text-center mb-4">📦 Arsip Berkas Internal</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">← Kembali</a>

    <!-- Filter Tahun & Export -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-auto">
            <select name="tahun" class="form-select" required>
                <option value="">-- Pilih Tahun Arsip --</option>
                <?php
                $tahun_q = "SELECT DISTINCT tahun_arsip FROM arsip_skkm WHERE id_ormawa = ? ORDER BY tahun_arsip DESC";
                $stmt_tahun = $conn->prepare($tahun_q);
                $stmt_tahun->bind_param("i", $id_ormawa);
                $stmt_tahun->execute();
                $res_tahun = $stmt_tahun->get_result();
                while ($t = $res_tahun->fetch_assoc()):
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
                    <th>NIM</th>
                    <th>Nama Kegiatan</th>
                    <th>Partisipasi</th>
                    <th>Kategori</th>
                    <th>Tingkat</th>
                    <th>Poin</th>
                    <th>Nomor Sertifikat</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Dikeluarkan</th>
                    <th>Tahun Arsip</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($data_result && $data_result->num_rows > 0): 
                    $no = 1;
                    while ($row = $data_result->fetch_assoc()): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nim']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['partisipasi']) ?></td>
                        <td><?= htmlspecialchars($row['kategori_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['tingkat']) ?></td>
                        <td class="text-center"><?= $row['poin_skkm'] ?></td>
                        <td><?= htmlspecialchars($row['nomor_sertifikat_internal']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_dikeluarkan']) ?></td>
                        <td class="text-center"><?= $row['tahun_arsip'] ?></td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="12" class="text-center">Tidak ada arsip ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
