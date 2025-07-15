<?php
session_start();
require_once('../../koneksi.php');

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Akses ditolak.'); window.location.href='../index.php';</script>";
    exit;
}

$tahun_filter = isset($_GET['tahun']) ? intval($_GET['tahun']) : null;

$query = "
    SELECT a.*, o.nama_ormawa
    FROM arsip_piagam a
    JOIN user_detail_ormawa o ON a.id_ormawa = o.id_ormawa
";

if ($tahun_filter) {
    $query .= " WHERE a.tahun_arsip = $tahun_filter";
}

$query .= " ORDER BY a.tahun_arsip DESC, a.tanggal_dikeluarkan DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Arsip Sertifikat Piagam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h3 class="mb-4 text-center">📦 Arsip Sertifikat Piagam</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">⬅️ Kembali</a>

<form method="GET" class="row g-3 mb-3">
    <div class="col-auto">
        <select name="tahun" class="form-select" required>
            <option value="">-- Pilih Tahun Arsip --</option>
            <?php
            $tahun_sql = "SELECT DISTINCT tahun_arsip FROM arsip_piagam ORDER BY tahun_arsip DESC";
            $tahun_result = $conn->query($tahun_sql);
            while ($tahun = $tahun_result->fetch_assoc()):
            ?>
                <option value="<?= $tahun['tahun_arsip'] ?>" <?= isset($_GET['tahun']) && $_GET['tahun'] == $tahun['tahun_arsip'] ? 'selected' : '' ?>>
                    <?= $tahun['tahun_arsip'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </div>
    <?php if (isset($_GET['tahun'])): ?>
    <div class="col-auto">
        <a href="export_piagam_excel.php?tahun=<?= $_GET['tahun'] ?>" class="btn btn-success">Export Excel</a>
    </div>
    <?php endif; ?>
</form>

    <table class="table table-bordered table-hover">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>Tahun Arsip</th>
                <th>Nama Ormawa</th>
                <th>Nama Kegiatan</th>
                <th>Nama Penerima</th>
                <th>Tanggal Kegiatan</th>
                <th>Nomor Sertifikat</th>
                <th>Tanggal Pengajuan</th>
                <th>Tanggal Dikeluarkan</th>
                <th>Keterangan</th>
                <th>Tahun Arsip</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): 
                $no = 1;
                while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['tahun_arsip']) ?></td>
                    <td><?= htmlspecialchars($row['nama_ormawa']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                    <td><?= htmlspecialchars($row['nama_penerima']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_kegiatan']) ?></td>
                    <td><?= htmlspecialchars($row['nomor_sertifikat_piagam']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_dikeluarkan']) ?></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td class="text-center"><?= $row['tahun_arsip'] ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="10" class="text-center">Tidak ada data arsip ditemukan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    document.getElementById('searchInput').addEventListener('input', function () {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');
        rows.forEach(row => {
            const content = row.textContent.toLowerCase();
            row.style.display = content.includes(keyword) ? '' : 'none';
        });
    });
</script>

</body>
</html>
