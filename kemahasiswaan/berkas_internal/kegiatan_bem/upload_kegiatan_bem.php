<?php
session_start();
include '../../../koneksi.php';
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Kegiatan BEM (Kemahasiswaan)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-5">
    <h2 class="text-center mb-4">📥 Upload Data Sertifikat Kegiatan BEM</h2>

    <form action="confirm_upload_bem.php" method="post" enctype="multipart/form-data" class="border p-4 rounded">
        <div class="mb-3">
            <label for="file" class="form-label">Pilih File Excel (.xlsx)</label>
            <input type="file" name="file" id="file" class="form-control" accept=".xlsx" required>
        </div>
        <button type="submit" class="btn btn-primary">🚀 Upload & Lanjutkan</button>
        <a href="detail_kegiatan_bem.php" class="btn btn-secondary">← Kembali</a>
    </form>
</div>
</body>
</html>
