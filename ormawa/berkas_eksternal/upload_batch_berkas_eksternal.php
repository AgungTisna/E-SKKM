<?php
session_start();
include '../../koneksi.php';

// 🔐 Cek login sebagai Ormawa
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Ormawa') {
    header("Location: ../index.php");
    exit();
}

// Ambil id_user dan id_ormawa
$id_user = $_SESSION['id_user'];
$stmt = $conn->prepare("SELECT id_ormawa FROM user_detail_ormawa WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$id_ormawa = $result->fetch_assoc()['id_ormawa'] ?? null;

if (!$id_ormawa) {
    die("❌ ID Ormawa tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Batch Berkas Eksternal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-5">
    <h3 class="mb-4 text-center">Upload Batch Berkas Eksternal</h3>

    <div class="card">
        <div class="card-body">
            <form action="confirm_upload.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_ormawa" value="<?= $id_ormawa ?>">

                <div class="mb-3">
                    <label for="file_excel" class="form-label">Pilih File Excel (.xlsx)</label>
                    <input type="file" class="form-control" name="file_excel" accept=".xlsx" required>
                </div>

                <button type="submit" class="btn btn-success">Upload</button>
                <a href="detail_berkas_eksternal.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

    <div class="mt-4">
        <p><strong>📌 Catatan:</strong> Format Excel harus memuat kolom berikut secara berurutan:</p>
        <ul>
            <li>Nama Kegiatan</li>
            <li>Nama Peserta</li>
            <li><strong>Tanggal Kegiatan (format: YYYY-MM-DD)</strong></li>
            <li>Keterangan</li>
        </ul>
        <p class="text-muted">* Pastikan semua kolom diisi dengan format yang benar agar data bisa diproses.</p>
    </div>
</div>

</body>
</html>
