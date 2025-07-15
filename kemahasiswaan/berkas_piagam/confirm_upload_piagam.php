<?php
session_start();
include '../../koneksi.php';

// 🔐 Cek login & role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    header("Location: ../index.php");
    exit();
}

// 🔄 Ambil data dari session
$data = $_SESSION['excel_preview_piagam'] ?? null;

if (!$data) {
    echo "<script>alert('Data tidak ditemukan. Silakan upload ulang.'); window.location.href='upload_batch_berkas_piagam.php';</script>";
    exit();
}

// ✅ Proses simpan jika dikonfirmasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi'])) {
    $berhasil = 0;
    $gagal = 0;

    foreach ($data as $row) {
        $id_berkas           = (int)($row['id_berkas'] ?? 0);
        $nomor_sertifikat    = trim($row['nomor_sertifikat'] ?? '');
        $tanggal_dikeluarkan = trim($row['tanggal_dikeluarkan'] ?? '');

        if (!$id_berkas || !$nomor_sertifikat || !$tanggal_dikeluarkan) {
            $gagal++;
            continue;
        }

        $stmt = $conn->prepare("UPDATE berkas_piagam SET 
            nomor_sertifikat_piagam = ?, 
            tanggal_dikeluarkan = ?, 
            id_kemahasiswaan = ?
            WHERE id_berkas_piagam = ?");

        if ($stmt) {
            $stmt->bind_param("ssii", $nomor_sertifikat, $tanggal_dikeluarkan, $_SESSION['id_user'], $id_berkas);
            if ($stmt->execute()) {
                $berhasil++;
            } else {
                $gagal++;
            }
            $stmt->close();
        } else {
            $gagal++;
        }
    }

    unset($_SESSION['excel_preview_piagam']);

    echo "<script>
        alert('✅ $berhasil data berhasil diperbarui.\\n❌ $gagal data gagal diperbarui.');
        window.location.href='detail_berkas_piagam.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Update Piagam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="text-center mb-4">📋 Konfirmasi Update Data Piagam</h3>
    <form method="POST">
        <input type="hidden" name="konfirmasi" value="1">

        <table class="table table-bordered table-hover">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>ID Berkas</th>
                    <th>Nama Ormawa</th>
                    <th>Nama Kegiatan</th>
                    <th>Nama Penerima</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Nomor Sertifikat (Baru)</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Tanggal Dikeluarkan (Baru)</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($data as $row): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['id_berkas'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['nama_ormawa'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['nama_kegiatan'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['nama_penerima'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['tanggal_kegiatan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nomor_sertifikat'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['tanggal_pengajuan'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['tanggal_dikeluarkan'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-between mt-4">
            <a href="upload_batch_berkas_piagam.php" class="btn btn-secondary">← Kembali</a>
            <button type="submit" class="btn btn-success">✅ Konfirmasi & Simpan</button>
        </div>
    </form>
</div>
</body>
</html>
