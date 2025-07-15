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
    <title>Upload Berkas Internal</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Upload Berkas Internal (Excel)</h4>
        </div>
        <div class="card-body">
            <form action="confirm_input_berkas_internal.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="file" class="form-label">Pilih File Excel (.xls / .xlsx)</label>
                    <input type="file" class="form-control" name="file" id="file" accept=".xls,.xlsx" required>
                </div>
                <button type="submit" name="submit" class="btn btn-success">Lanjutkan ke Konfirmasi</button>
                <a href="detail_berkas_internal.php" class="btn btn-secondary ms-2">Kembali</a>
            </form>
        </div>
    </div>
</div>

<!-- Optional: Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
