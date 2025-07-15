<?php
session_start();
include '../../../koneksi.php';


// Cek role login
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../../index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Kegiatan Wajib</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../navbar.php'; ?>

<div class="container py-4">
    <h2>Upload Data Kegiatan Wajib (Excel)</h2>
    <form action="confirm_upload.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Pilih file Excel (.xlsx)</label>
            <input type="file" name="excel_file" class="form-control" accept=".xlsx" required>
        </div>
        <button type="submit" name="preview" class="btn btn-primary mb-3">Lanjutkan ke Konfirmasi</button>
        <a href="detail_kegiatan_wajib.php" class="btn btn-secondary mb-3"> Kembali</a>

    </form>
</div>
</body>
</html>
