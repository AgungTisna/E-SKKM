<?php
session_start();
include '../../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    header("Location: ../index.php");
    exit();
}

$data = $_SESSION['excel_preview_internal'] ?? null;

if (!$data) {
    echo "<script>alert('Data tidak ditemukan. Silakan upload ulang.'); window.location.href='upload_batch_berkas_internal.php';</script>";
    exit();
}

$data_lengkap = [];

foreach ($data as $row) {
    $id_berkas = (int)($row['id_berkas_internal'] ?? 0);
    $nomor_sertifikat = trim($row['nomor_sertifikat'] ?? '');
    $tanggal_dikeluarkan = trim($row['tanggal_dikeluarkan'] ?? '');

    if (!$id_berkas) continue;

    $query = $conn->prepare("
        SELECT 
            bi.id_berkas_internal, bi.nim, u.nama AS nama_mahasiswa, bi.nama_kegiatan, 
            bi.tanggal_kegiatan, bi.partisipasi, bi.kategori_kegiatan, bi.tingkat,
            bi.poin_skkm, bi.nomor_sertifikat_internal, bi.tanggal_pengajuan, 
            bi.tanggal_dikeluarkan, o.nama_ormawa
        FROM berkas_internal bi
        JOIN user_detail_mahasiswa m ON bi.nim = m.nim
        JOIN user u ON m.id_user = u.id_user
        JOIN user_detail_ormawa o ON bi.id_ormawa = o.id_ormawa
        WHERE bi.id_berkas_internal = ?
    ");
    $query->bind_param("i", $id_berkas);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $detail = $result->fetch_assoc();
        $detail['nomor_sertifikat_baru'] = $nomor_sertifikat;
        $detail['tanggal_dikeluarkan_baru'] = $tanggal_dikeluarkan;
        $data_lengkap[] = $detail;
    }

    $query->close();
}

// ✅ Saat Simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi'])) {
    $berhasil = 0;
    $gagal = 0;

    foreach ($data as $row) {
        $id_berkas = (int)($row['id_berkas_internal'] ?? 0);
        $nomor_sertifikat = trim($row['nomor_sertifikat'] ?? '');
        $tanggal_dikeluarkan = trim($row['tanggal_dikeluarkan'] ?? '');

        if (!$id_berkas || !$nomor_sertifikat || !$tanggal_dikeluarkan) {
            $gagal++;
            continue;
        }

        $stmt = $conn->prepare("UPDATE berkas_internal SET 
            nomor_sertifikat_internal = ?, 
            tanggal_dikeluarkan = ?, 
            id_kemahasiswaan = ?
            WHERE id_berkas_internal = ?");

        $stmt->bind_param("ssii", $nomor_sertifikat, $tanggal_dikeluarkan, $_SESSION['id_user'], $id_berkas);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $berhasil++;
        } else {
            $gagal++;
        }

        $stmt->close();
    }

    unset($_SESSION['excel_preview_internal']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Simpan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <?php if ($berhasil): ?>
        <div class="alert alert-success">✅ <strong><?= $berhasil ?></strong> data berhasil diperbarui.</div>
    <?php endif; ?>
    <?php if ($gagal): ?>
        <div class="alert alert-danger">⚠️ <strong><?= $gagal ?></strong> data gagal diperbarui.</div>
    <?php endif; ?>
    <a href="detail_berkas_internal.php" class="btn btn-primary">← Kembali</a>
</div>
</body>
</html>
<?php
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Update Sertifikat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4 text-center">📋 Konfirmasi Update Berkas Internal</h3>
    <form method="POST">
        <input type="hidden" name="konfirmasi" value="1">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>ID Berkas</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Nama Kegiatan</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Partisipasi</th>
                    <th>Kategori</th>
                    <th>Tingkat</th>
                    <th>Poin</th>
                    <th>Nomor Sertifikat (baru)</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Tanggal Dikeluarkan (baru)</th>
                    <th>Nama Ormawa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_lengkap as $i => $row): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($row['id_berkas_internal']) ?></td>
                        <td><?= htmlspecialchars($row['nim']) ?></td>
                        <td><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['partisipasi']) ?></td>
                        <td><?= htmlspecialchars($row['kategori_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['tingkat']) ?></td>
                        <td><?= htmlspecialchars($row['poin_skkm']) ?></td>
                        <td><?= htmlspecialchars($row['nomor_sertifikat_baru']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_dikeluarkan_baru']) ?></td>
                        <td><?= htmlspecialchars($row['nama_ormawa']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-between mt-4">
            <a href="upload_batch_berkas_internal.php" class="btn btn-secondary">← Kembali</a>
            <button type="submit" class="btn btn-success">✅ Konfirmasi & Simpan</button>
        </div>
    </form>
</div>
</body>
</html>
