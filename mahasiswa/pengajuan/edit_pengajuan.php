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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID tidak valid.'); window.location.href='detail_pengajuan.php';</script>";
    exit();
}

$id_pengajuan = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM pengajuan_skkm WHERE id_pengajuan = ? AND nim = ?");
$stmt->bind_param("is", $id_pengajuan, $nim);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "<script>alert('Data tidak ditemukan.'); window.location.href='detail_pengajuan.php';</script>";
    exit();
}

$data = $result->fetch_assoc();

if ($data['status_verifikasi_bem'] !== 'Invalid' && $data['status_verifikasi_kemahasiswaan'] !== 'Invalid') {
    echo "<script>alert('Pengajuan ini tidak dapat diedit karena telah tervalidasi.'); window.location.href='detail_pengajuan.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kegiatan = trim($_POST['nama_kegiatan']);
    $file = $_FILES['file_bukti'];
    $bukti_keikutsertaan = $_FILES['bukti_keikutsertaan'];

    if (empty($nama_kegiatan)) {
        $errors[] = "Nama kegiatan wajib diisi.";
    }

    $newFileName = $data['file_bukti'];
    $newBuktiKeikutsertaan = $data['bukti_keikutsertaan'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            $errors[] = "Format file harus JPG, JPEG, atau PNG.";
        } else {
            $newFileName = uniqid('bukti_') . '.' . $ext;
            $uploadPath = "../../asset/upload/" . $newFileName;

            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $errors[] = "Gagal mengunggah file baru.";
            } else {
                $oldPath = "../../asset/upload/" . $data['file_bukti'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
        }
    }

    if ($bukti_keikutsertaan['error'] === UPLOAD_ERR_OK) {
        $ext2 = strtolower(pathinfo($bukti_keikutsertaan['name'], PATHINFO_EXTENSION));
        $allowed2 = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($ext2, $allowed2)) {
            $errors[] = "Format bukti keikutsertaan harus JPG, JPEG, PNG, atau PDF.";
        } else {
            $newBuktiKeikutsertaan = uniqid('keikutsertaan_') . '.' . $ext2;
            $uploadPath2 = "../../asset/upload/" . $newBuktiKeikutsertaan;

            if (!move_uploaded_file($bukti_keikutsertaan['tmp_name'], $uploadPath2)) {
                $errors[] = "Gagal mengunggah bukti keikutsertaan.";
            } else {
                $oldPath2 = "../../asset/upload/" . $data['bukti_keikutsertaan'];
                if (file_exists($oldPath2)) {
                    unlink($oldPath2);
                }
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE pengajuan_skkm 
            SET nama_kegiatan = ?, file_bukti = ?, bukti_keikutsertaan = ?, 
                status_verifikasi_bem = 'Pending', 
                status_verifikasi_kemahasiswaan = 'Pending', 
                catatan_bem = '', catatan_kemahasiswaan = '', 
                tanggal_verifikasi_bem = NULL, tanggal_verifikasi_kemahasiswaan = NULL 
            WHERE id_pengajuan = ?");
        $stmt->bind_param("sssi", $nama_kegiatan, $newFileName, $newBuktiKeikutsertaan, $id_pengajuan);
        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Gagal menyimpan perubahan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengajuan SKKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../navbar.php'; ?>
<div class="container mt-5">
    <h3 class="mb-4 text-center">✏️ Edit Pengajuan SKKM</h3>
    <a href="detail_pengajuan.php" class="btn btn-secondary mb-4">← Kembali</a>

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
            Perubahan berhasil disimpan dan akan diverifikasi ulang.
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow">
        <div class="mb-3">
            <input type="text" class="form-control" value="<?= htmlspecialchars($nim) ?>" hidden>
        </div>
        <div class="mb-3">
            <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
            <input type="text" name="nama_kegiatan" class="form-control" required value="<?= htmlspecialchars($data['nama_kegiatan']) ?>">
        </div>
        <div class="mb-3">
            <label for="file_bukti" class="form-label">File Bukti Baru (Opsional)</label>
            <input type="file" name="file_bukti" class="form-control" accept=".jpg,.jpeg,.png">
            <small class="text-muted">Biarkan kosong jika tidak ingin mengganti file bukti.</small><br>
            <a href="../../asset/upload/<?= htmlspecialchars($data['file_bukti']) ?>" target="_blank">📎 Lihat file sebelumnya</a>
        </div>
        <div class="mb-3">
            <label for="bukti_keikutsertaan" class="form-label">Bukti Keikutsertaan Baru (Opsional)</label>
            <input type="file" name="bukti_keikutsertaan" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti bukti keikutsertaan.</small><br>
            <?php if (!empty($data['bukti_keikutsertaan'])): ?>
                <a href="../../asset/upload/<?= htmlspecialchars($data['bukti_keikutsertaan']) ?>" target="_blank">📎 Lihat bukti keikutsertaan sebelumnya</a>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
</body>
</html>
