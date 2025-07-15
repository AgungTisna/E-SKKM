<?php
session_start();
require_once('../../koneksi.php');

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    header("Location: ../../index.php");
    exit();
}

$id_user = $_SESSION['id_user'];

$query = "
    SELECT p.*, u.nama AS nama_mahasiswa
    FROM pengajuan_skkm p
    JOIN user_detail_mahasiswa udm ON p.nim = udm.nim
    JOIN user u ON udm.id_user = u.id_user
    WHERE p.status_verifikasi_bem = 'Valid'
    ORDER BY p.id_pengajuan DESC
";
$result = $conn->query($query);

function getColor($status) {
    if ($status === 'Valid') return 'success';
    if ($status === 'Invalid') return 'danger';
    if ($status === 'Pending') return 'secondary';
    return 'light';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pengajuan SKKM - Kemahasiswaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php include '../navbar.php'; ?>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="text-center mb-4">📄 Verifikasi Pengajuan Poin SKKM Mahasiswa</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

    <div class="card shadow">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Bukti Sertifikat</th>
                        <th>Bukti Keikutsertaan</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Nama Kegiatan</th>
                        <th>Kategori</th>
                        <th>Tingkat</th>
                        <th>Partisipasi</th>
                        <th>Poin</th>
                        <th>Status BEM</th>
                        <th>Status KMHS</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>

                    <!-- Bukti sertifikat -->
                    <td class="text-center">
                        <a href="../../asset/upload/<?= $row['file_bukti'] ?>" target="_blank">
                            <img src="../../asset/upload/<?= $row['file_bukti'] ?>" width="50">
                        </a>
                    </td>

                    <!-- Bukti keikutsertaan -->
                    <td class="text-center">
                        <?php if (!empty($row['bukti_keikutsertaan'])): ?>
                            <?php
                            $ext = strtolower(pathinfo($row['bukti_keikutsertaan'], PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg', 'jpeg', 'png'])):
                            ?>
                                <a href="../../asset/upload/<?= $row['bukti_keikutsertaan'] ?>" target="_blank">
                                    <img src="../../asset/upload/<?= $row['bukti_keikutsertaan'] ?>" width="50">
                                </a>
                            <?php else: ?>
                                <a href="../../asset/upload/<?= $row['bukti_keikutsertaan'] ?>" target="_blank">📎 Dokumen</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <td class="text-center"><?= $row['nim'] ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['kategori_kegiatan']) ?></td>
                    <td class="text-center"><?= $row['tingkat'] ?></td>
                    <td class="text-center"><?= $row['partisipasi'] ?></td>
                    <td class="text-center"><?= $row['poin_skkm'] ?></td>
                    <td class="text-center">
                        <span class="badge bg-<?= getColor($row['status_verifikasi_bem']) ?>">
                            <?= $row['status_verifikasi_bem'] ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-<?= getColor($row['status_verifikasi_kemahasiswaan']) ?>">
                            <?= $row['status_verifikasi_kemahasiswaan'] ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <?php if ($row['status_verifikasi_kemahasiswaan'] !== 'Valid'): ?>
                            <form method="POST" action="pengajuan_skkm.php">
                                <input type="hidden" name="id_pengajuan" value="<?= $row['id_pengajuan'] ?>">
                                <input type="hidden" name="nim" value="<?= $row['nim'] ?>">
                                <button type="submit" class="btn btn-sm btn-warning">✔️ Verifikasi</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">Terverifikasi</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr class="table-light">
                    <td colspan="13">
                        <strong>Catatan BEM:</strong> <?= htmlspecialchars($row['catatan_bem']) ?><br>
                        <strong>Catatan Kemahasiswaan:</strong> <?= htmlspecialchars($row['catatan_kemahasiswaan']) ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
