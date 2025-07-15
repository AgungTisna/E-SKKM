<?php
session_start();
require_once('../../koneksi.php');

// Cek jika user belum login
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan'); window.location.href='../index.php';</script>";
    exit();
}

// Ambil data arsip dari arsip_skkm
$tahun_filter = isset($_GET['tahun']) ? intval($_GET['tahun']) : null;
$query = "SELECT id_berkas_internal, nim, nama_kegiatan, partisipasi, kategori_kegiatan, tingkat,
           poin_skkm, nomor_sertifikat_internal, tanggal_kegiatan, tanggal_pengajuan,
           tanggal_dikeluarkan, tahun_arsip
          FROM arsip_skkm";

if ($tahun_filter) {
    $query .= " WHERE tahun_arsip = $tahun_filter";
}

$query .= " ORDER BY tanggal_kegiatan DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Arsip SKKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h3 class="mb-4 text-center">Daftar Arsip SKKM</h3>
    <a href="../" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

<form method="GET" class="row g-3 mb-3">
    <div class="col-auto">
        <select name="tahun" class="form-select" required>
            <option value="">-- Pilih Tahun Arsip --</option>
            <?php
            $tahun_query = "SELECT DISTINCT tahun_arsip FROM arsip_skkm ORDER BY tahun_arsip DESC";
            $tahun_result = $conn->query($tahun_query);
            while ($t = $tahun_result->fetch_assoc()):
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
            <a href="export_excel.php?tahun=<?= $_GET['tahun'] ?>" class="btn btn-success">Export Excel</a>
        </div>
    <?php endif; ?>
</form>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Kegiatan</th>
                        <th>Partisipasi</th>
                        <th>Kategori</th>
                        <th>Tingkat</th>
                        <th>Poin</th>
                        <th>No. Sertifikat</th>
                        <th>Tanggal Kegiatan</th>
                        <th>Pengajuan</th>
                        <th>Dikeluarkan</th>
                        <th>Tahun Arsip</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
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
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">Tidak ada data arsip ditemukan.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
