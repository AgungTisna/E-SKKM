<?php
session_start();
require_once('../../koneksi.php');

if (!isset($_SESSION['nim'])) {
    header("Location: ../../login.php");
    exit();
}

$nim = $_SESSION['nim'];
$errors = [];
$success = false;

// Saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kegiatan = isset($_POST['nama_kegiatan']) ? trim($_POST['nama_kegiatan']) : '';
    $file = $_FILES['file_bukti'] ?? null;

    // Validasi
    if (empty($nama_kegiatan)) {
        $errors[] = "Nama kegiatan wajib diisi.";
    }

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        $maxSize = 10 * 1024 * 1024; // 10 MB

        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file tidak valid. Hanya JPG, JPEG, dan PNG yang diperbolehkan.";
        } elseif ($file['size'] > $maxSize) {
            $errors[] = "Ukuran file maksimal 10MB.";
        } else {
            $newFileName = uniqid('bukti_') . '.' . $ext;
            $uploadPath = '../../asset/upload/' . $newFileName;
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $errors[] = "Gagal mengunggah file bukti.";
            }
        }
    } else {
        $errors[] = "File bukti wajib diunggah.";
    }


    // Simpan ke database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO pengajuan_skkm 
            (nim, file_bukti, nama_kegiatan, status_verifikasi_bem, status_verifikasi_kemahasiswaan)
            VALUES (?, ?, ?, 'Pending', 'Pending')");
        $stmt->bind_param("sss", $nim, $newFileName, $nama_kegiatan);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Gagal menyimpan data ke database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengajuan SKKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../navbar.php'; ?>
<div class="container mt-5">
    <h3 class="mb-4 text-center">📝 Pengajuan Berkas SKKM</h3>
    <a href="detail_pengajuan.php" class="btn btn-secondary mb-4">← Kembali ke Riwayat</a>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($success): ?>
        <div class="alert alert-success">
            Pengajuan berhasil dikirim! Silakan tunggu proses verifikasi BEM dan Kemahasiswaan.
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow">
        <div class="mb-3">
            <input type="text" class="form-control" value="<?= htmlspecialchars($nim) ?>" hidden>
        </div>

        <div class="mb-3">
            <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
            <input type="text" name="nama_kegiatan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="file_bukti" class="form-label">Upload Bukti (JPG/JPEG/PNG)</label>
            <input type="file" name="file_bukti" class="form-control" accept=".jpg,.jpeg,.png" required>
        </div>

        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
    </form>
</div>
</body>
</html>
