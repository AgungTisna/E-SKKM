<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Akses ditolak!'); window.location.href='../index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Berkas Piagam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h3>Upload Berkas Piagam (Excel)</h3>
    <form action="confirm_input_berkas_piagam.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="file" class="form-label">Pilih File Excel</label>
            <input type="file" name="file" id="file" class="form-control" accept=".xls,.xlsx" required>
        </div>
        <button type="submit" class="btn btn-primary">Lanjutkan ke Konfirmasi</button>
        <a href="detail_berkas_piagam.php" class="btn btn-secondary">← Kembali</a>
    </form>
</div>
</body>
</html>
