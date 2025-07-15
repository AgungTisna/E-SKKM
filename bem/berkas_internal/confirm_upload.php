<?php
session_start();
include '../../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'BEM') {
    echo "<script>alert('Anda harus login sebagai BEM!'); window.location.href='../index.php';</script>";
    exit();
}

$id_bem = $_SESSION['id_user'];
$id_ormawa = $_GET['id_ormawa'] ?? ($_SESSION['id_ormawa_upload'] ?? null);

if (!isset($_SESSION['imported_data']) || empty($_SESSION['imported_data'])) {
    echo "<script>alert('Tidak ada data yang diimpor.'); window.location.href='upload_batch_berkas_internal.php';</script>";
    exit;
}

$data = $_SESSION['imported_data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = 0;
    $fail = 0;

    foreach ($data as $item) {
        $nim = $item['nim'];
        $nama_kegiatan = $item['nama_kegiatan'];
        $kategori = $item['kategori_kegiatan'];
        $tingkat = $item['tingkat'];
        $poin = $item['poin_skkm'];

        $query = "UPDATE berkas_internal 
                  SET kategori_kegiatan = ?, tingkat = ?, poin_skkm = ?, id_bem = ?
                  WHERE nim = ? AND nama_kegiatan = ?";

        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("sssiss", $kategori, $tingkat, $poin, $id_bem, $nim, $nama_kegiatan);
            if ($stmt->execute()) {
                $success++;
            } else {
                $fail++;
            }
        } else {
            $fail++;
        }
    }

    unset($_SESSION['imported_data']);

    if ($fail === 0) {
        echo "<script>
            alert('✅ $success data berhasil diperbarui.');
            window.location.href = 'detail_berkas_internal.php?id_ormawa=" . urlencode($id_ormawa) . "';
        </script>";
    } else {
        echo "<script>
            alert('✅ Berhasil: $success \\n❌ Gagal: $fail');
            window.location.href = 'upload_batch_berkas_internal.php?id_ormawa=" . urlencode($id_ormawa) . "';
        </script>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Data SKKM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h3 class="text-center mb-4">📋 Konfirmasi Data SKKM</h3>
    <p class="text-center">Periksa kembali data sebelum disimpan ke database.</p>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm text-center">
            <thead class="table-dark">
                <tr>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Nama Kegiatan</th>
                    <th>Partisipasi</th>
                    <th>Kategori Kegiatan</th>
                    <th>Tingkat</th>
                    <th>Poin SKKM</th>
                    <th>Tanggal Kegiatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nim']) ?></td>
                        <td><?= htmlspecialchars($row['nama_mahasiswa'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['partisipasi']) ?></td>
                        <td><?= htmlspecialchars($row['kategori_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['tingkat']) ?></td>
                        <td><?= htmlspecialchars($row['poin_skkm']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_kegiatan']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <form method="POST">
        <div class="d-flex justify-content-between mt-4">
            <a href="upload_batch_berkas_internal.php?id_ormawa=<?= urlencode($id_ormawa) ?>" class="btn btn-secondary">← Batalkan</a>
            <button type="submit" class="btn btn-success">✅ Simpan ke Database</button>
        </div>
    </form>
</div>

</body>
</html>
