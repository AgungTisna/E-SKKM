<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Simpan data setelah konfirmasi
if (isset($_POST['konfirmasi']) && isset($_SESSION['excel_preview']) && isset($_POST['id_ormawa'])) {
    $id_ormawa = $_POST['id_ormawa'];
    $rows = $_SESSION['excel_preview'];

    $berhasil = 0;
    $gagal = 0;

    foreach ($rows as $row) {
        $nama_kegiatan = $conn->real_escape_string(trim($row[0] ?? ''));
        $nama_peserta  = $conn->real_escape_string(trim($row[1] ?? ''));
        $tanggal_kegiatan = $conn->real_escape_string(trim($row[2] ?? ''));
        $keterangan    = $conn->real_escape_string(trim($row[3] ?? ''));

        $tanggal_pengajuan     = date('Y-m-d');
        $tanggal_dikeluarkan   = '0000-00-00';
        $nomor_sertifikat      = '';
        $id_kemahasiswaan      = 0;

        if (empty($nama_kegiatan) || empty($nama_peserta) || empty($tanggal_kegiatan)) {
            $gagal++;
            continue;
        }

        $insert = $conn->query("
            INSERT INTO berkas_eksternal (
                id_ormawa, nama_kegiatan, id_kemahasiswaan, nama_peserta,
                tanggal_kegiatan, nomor_sertifikat_eksternal, tanggal_pengajuan,
                tanggal_dikeluarkan, keterangan
            ) VALUES (
                '$id_ormawa', '$nama_kegiatan', '$id_kemahasiswaan', '$nama_peserta',
                '$tanggal_kegiatan', '$nomor_sertifikat', '$tanggal_pengajuan',
                '$tanggal_dikeluarkan', '$keterangan'
            )
        ");

        $insert ? $berhasil++ : $gagal++;
    }

    unset($_SESSION['excel_preview']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Simpan Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <?php if ($berhasil > 0): ?>
        <div class="alert alert-success">✅ <strong><?= $berhasil ?></strong> data berhasil disimpan.</div>
    <?php endif; ?>
    <?php if ($gagal > 0): ?>
        <div class="alert alert-danger">⚠️ <strong><?= $gagal ?></strong> data gagal disimpan.</div>
    <?php endif; ?>
    <a href="detail_berkas_eksternal.php" class="btn btn-primary">← Kembali ke Daftar</a>
</div>
</body>
</html>
<?php
    exit;
}

// Tahap awal: upload dan tampilkan preview
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
    $id_ormawa = $_POST['id_ormawa'] ?? null;
    $file = $_FILES['file_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Buang header
        $header = array_shift($rows);

        if (!$id_ormawa) {
            die("❌ ID Ormawa tidak valid.");
        }

        $_SESSION['excel_preview'] = $rows;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Preview Data Excel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4">📋 Preview Data Excel</h3>
    <form method="POST">
        <input type="hidden" name="id_ormawa" value="<?= $id_ormawa ?>">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Kegiatan</th>
                    <th>Nama Peserta</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $i => $row): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($row[0]) ?></td>
                        <td><?= htmlspecialchars($row[1]) ?></td>
                        <td><?= htmlspecialchars($row[2]) ?></td>
                        <td><?= htmlspecialchars($row[3]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-between mt-4">
            <a href="upload_batch_berkas_eksternal.php" class="btn btn-secondary">← Kembali</a>
            <button type="submit" name="konfirmasi" class="btn btn-success">Konfirmasi & Simpan</button>
        </div>
    </form>
</div>
</body>
</html>
<?php
        exit;

    } catch (Exception $e) {
        echo "<div style='padding:2rem; color:red'>❌ Gagal membaca file: " . $e->getMessage() . "</div>";
    }
}
