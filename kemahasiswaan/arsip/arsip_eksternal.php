<?php
session_start();
require_once('../../koneksi.php');

// Pastikan hanya role Kemahasiswaan yang dapat mengakses
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Akses ditolak. Hanya Kemahasiswaan yang dapat mengakses.'); window.location.href='../index.php';</script>";
    exit();
}

// Ambil data dari arsip_eksternal
$tahun_filter = isset($_GET['tahun']) ? intval($_GET['tahun']) : null;

$sql = "
    SELECT 
        id_arsip_eksternal, id_berkas_eksternal, id_ormawa, nama_kegiatan,
        id_kemahasiswaan, nama_peserta, tanggal_kegiatan,
        nomor_sertifikat_eksternal, tanggal_pengajuan, tanggal_dikeluarkan,
        keterangan, tahun_arsip
    FROM arsip_eksternal";

if ($tahun_filter) {
    $sql .= " WHERE tahun_arsip = $tahun_filter";
}

$sql .= " ORDER BY tanggal_kegiatan DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Arsip Sertifikat Eksternal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h3 class="text-center mb-4">📦 Arsip Sertifikat Eksternal</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

    <form method="GET" class="row g-3 mb-4">
    <div class="col-auto">
        <select name="tahun" class="form-select" required>
            <option value="">-- Pilih Tahun Arsip --</option>
            <?php
            $tahun_q = "SELECT DISTINCT tahun_arsip FROM arsip_eksternal ORDER BY tahun_arsip DESC";
            $tahun_r = $conn->query($tahun_q);
            while ($t = $tahun_r->fetch_assoc()):
            ?>
                <option value="<?= $t['tahun_arsip'] ?>" <?= isset($_GET['tahun']) && $_GET['tahun'] == $t['tahun_arsip'] ? 'selected' : '' ?>>
                    <?= $t['tahun_arsip'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </div>
    <?php if (isset($_GET['tahun'])): ?>
    <div class="col-auto">
        <a href="export_eksternal_excel.php?tahun=<?= $_GET['tahun'] ?>" class="btn btn-success">Export Excel</a>
    </div>
    <?php endif; ?>
</form>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>ID Arsip</th>
                        <th>ID Berkas</th>
                        <th>Nama Kegiatan</th>
                        <th>Nama Peserta</th>
                        <th>Tanggal Kegiatan</th>
                        <th>No. Sertifikat</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Tanggal Dikeluarkan</th>
                        <th>Keterangan</th>
                        <th>Tahun Arsip</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center"><?= $row['id_arsip_eksternal'] ?></td>
                            <td class="text-center"><?= $row['id_berkas_eksternal'] ?></td>
                            <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                            <td><?= htmlspecialchars($row['nama_peserta']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_kegiatan']) ?></td>
                            <td><?= htmlspecialchars($row['nomor_sertifikat_eksternal']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_dikeluarkan']) ?></td>
                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                            <td class="text-center"><?= $row['tahun_arsip'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">Belum ada data arsip eksternal yang tersedia.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
