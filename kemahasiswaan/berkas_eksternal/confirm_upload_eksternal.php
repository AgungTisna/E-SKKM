<?php
session_start();
include '../../koneksi.php';

// 🔒 Cek login & role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    header("Location: ../index.php");
    exit();
}

$data = $_SESSION['excel_preview_eksternal'] ?? null;

if (!$data) {
    echo "<script>alert('Data tidak ditemukan. Silakan upload ulang.'); window.location.href='upload_batch_berkas_eksternal.php';</script>";
    exit();
}

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

        $stmt = $conn->prepare("UPDATE berkas_eksternal SET 
            nomor_sertifikat_eksternal = ?, 
            tanggal_dikeluarkan = ?, 
            id_kemahasiswaan = ?
            WHERE id_berkas_eksternal = ?");

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

    unset($_SESSION['excel_preview_eksternal']);

    echo "<script>alert('✅ $berhasil data berhasil diperbarui.\\n❌ $gagal data gagal diperbarui.'); 
          window.location.href='detail_berkas_eksternal.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Update Sertifikat Eksternal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-5">
    <h3 class="mb-4 text-center">📋 Konfirmasi Update Sertifikat Eksternal</h3>
    <form method="POST">
        <input type="hidden" name="konfirmasi" value="1">
        <table class="table table-bordered table-hover">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>ID Berkas</th>
                    <th>Nama Ormawa</th>
                    <th>Nama Kegiatan</th>
                    <th>Nama Peserta</th>
                    <th><strong>Tanggal Kegiatan</strong></th>
                    <th>Nomor Sertifikat (Baru)</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Tanggal Dikeluarkan (Baru)</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach ($data as $row):
                    $id_berkas = (int)($row['id_berkas'] ?? 0);
                    $nomor_sertifikat = htmlspecialchars($row['nomor_sertifikat'] ?? '');
                    $tanggal_dikeluarkan = htmlspecialchars($row['tanggal_dikeluarkan'] ?? '');

                    // Ambil data lengkap dari database
                    $stmt = $conn->prepare("
                        SELECT b.nama_kegiatan, b.nama_peserta, b.tanggal_kegiatan, b.tanggal_pengajuan, b.keterangan, o.nama_ormawa 
                        FROM berkas_eksternal b 
                        JOIN user_detail_ormawa o ON b.id_ormawa = o.id_ormawa 
                        WHERE b.id_berkas_eksternal = ?
                    ");
                    $stmt->bind_param("i", $id_berkas);
                    $stmt->execute();
                    $detail = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $id_berkas ?></td>
                    <td><?= htmlspecialchars($detail['nama_ormawa'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($detail['nama_kegiatan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($detail['nama_peserta'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($detail['tanggal_kegiatan'] ?? '-') ?></td>
                    <td><?= $nomor_sertifikat ?></td>
                    <td><?= htmlspecialchars($detail['tanggal_pengajuan'] ?? '-') ?></td>
                    <td><?= $tanggal_dikeluarkan ?></td>
                    <td><?= htmlspecialchars($detail['keterangan'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-between mt-4">
            <a href="upload_batch_berkas_eksternal.php" class="btn btn-secondary">← Kembali</a>
            <button type="submit" class="btn btn-success">✅ Konfirmasi & Simpan</button>
        </div>
    </form>
</div>
</body>
</html>
