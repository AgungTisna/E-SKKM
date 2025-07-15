<?php
session_start();
require_once('../../koneksi.php');

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    header("Location: ../../index.php");
    exit();
}

$id_kemahasiswaan = $_SESSION['id_user'];
$id_pengajuan = $_POST['id_pengajuan'] ?? null;
$nim = $_POST['nim'] ?? null;

if (!$id_pengajuan || !$nim) {
    echo "<script>alert('Data pengajuan tidak lengkap!'); window.location.href='detail_pengajuan.php';</script>";
    exit();
}

$query = "
    SELECT p.*, u.nama AS nama_mahasiswa
    FROM pengajuan_skkm p
    JOIN user_detail_mahasiswa udm ON p.nim = udm.nim
    JOIN user u ON udm.id_user = u.id_user
    WHERE p.id_pengajuan = ? AND p.nim = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $id_pengajuan, $nim);
$stmt->execute();
$pengajuan = $stmt->get_result()->fetch_assoc();

if (!$pengajuan) {
    echo "<script>alert('Pengajuan tidak ditemukan!'); window.location.href='detail_pengajuan.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_validasi'])) {
    $status = $_POST['status_verifikasi_kemahasiswaan'];
    $tanggal = $_POST['tanggal_verifikasi_kemahasiswaan'];
    $catatan = $_POST['catatan_kemahasiswaan'];

    $stmt = $conn->prepare("UPDATE pengajuan_skkm 
        SET id_kemahasiswaan = ?, status_verifikasi_kemahasiswaan = ?, tanggal_verifikasi_kemahasiswaan = ?, catatan_kemahasiswaan = ? 
        WHERE id_pengajuan = ?");
    $stmt->bind_param("isssi", $id_kemahasiswaan, $status, $tanggal, $catatan, $id_pengajuan);
    if ($stmt->execute()) {
        echo "<script>alert('Verifikasi berhasil disimpan!'); window.location.href='detail_pengajuan.php';</script>";
        exit();
    } else {
        echo "<script>alert('Terjadi kesalahan saat menyimpan verifikasi.'); window.location.href='detail_pengajuan.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Validasi SKKM oleh Kemahasiswaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../navbar.php'; ?>

<div class="container mt-4">
    <h3 class="text-center mb-4">🛠️ Verifikasi Pengajuan SKKM</h3>
    <a href="detail_pengajuan.php" class="btn btn-outline-secondary mb-3">← Kembali</a>

    <form method="POST" class="row g-4">
        <input type="hidden" name="id_pengajuan" value="<?= $pengajuan['id_pengajuan'] ?>">
        <input type="hidden" name="nim" value="<?= $pengajuan['nim'] ?>">

        <div class="col-md-5 text-center">
            <p><strong>Bukti Sertifikat:</strong></p>
            <a href="../../asset/upload/<?= $pengajuan['file_bukti'] ?>" target="_blank">
                <img src="../../asset/upload/<?= $pengajuan['file_bukti'] ?>" class="border mb-2" style="width: 100%; max-height: 400px; object-fit: contain;">
            </a>

            <p class="mt-3"><strong>Bukti Keikutsertaan:</strong></p>
            <?php if (!empty($pengajuan['bukti_keikutsertaan'])):
                $ext = strtolower(pathinfo($pengajuan['bukti_keikutsertaan'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])): ?>
                    <a href="../../asset/upload/<?= $pengajuan['bukti_keikutsertaan'] ?>" target="_blank">
                        <img src="../../asset/upload/<?= $pengajuan['bukti_keikutsertaan'] ?>" class="border" style="width: 100%; max-height: 300px; object-fit: contain;">
                    </a>
                <?php else: ?>
                    <a href="../../asset/upload/<?= $pengajuan['bukti_keikutsertaan'] ?>" target="_blank">📎 Lihat Dokumen</a>
                <?php endif; ?>
            <?php else: ?>
                <span class="text-muted">Tidak tersedia</span>
            <?php endif; ?>
        </div>

        <div class="col-md-7">
            <label class="form-label">NIM</label>
            <input type="text" class="form-control" value="<?= $pengajuan['nim'] ?>" readonly>

            <label class="form-label mt-2">Nama Mahasiswa</label>
            <input type="text" class="form-control" value="<?= $pengajuan['nama_mahasiswa'] ?>" readonly>

            <label class="form-label mt-2">Nama Kegiatan</label>
            <input type="text" class="form-control" value="<?= $pengajuan['nama_kegiatan'] ?>" readonly>

            <label class="form-label mt-2">Tingkat</label>
            <input type="text" class="form-control" value="<?= $pengajuan['tingkat'] ?>" readonly>

            <label class="form-label mt-2">Partisipasi</label>
            <input type="text" class="form-control" value="<?= $pengajuan['partisipasi'] ?>" readonly>

            <label class="form-label mt-2">Poin SKKM</label>
            <input type="text" class="form-control" value="<?= $pengajuan['poin_skkm'] ?>" readonly>

            <label class="form-label mt-2">Status Verifikasi</label>
            <select name="status_verifikasi_kemahasiswaan" class="form-select" required>
                <option value="Pending" <?= $pengajuan['status_verifikasi_kemahasiswaan'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Valid" <?= $pengajuan['status_verifikasi_kemahasiswaan'] === 'Valid' ? 'selected' : '' ?>>Valid</option>
                <option value="Invalid" <?= $pengajuan['status_verifikasi_kemahasiswaan'] === 'Invalid' ? 'selected' : '' ?>>Invalid</option>
            </select>

            <label class="form-label mt-2">Tanggal Verifikasi</label>
            <input type="date" name="tanggal_verifikasi_kemahasiswaan" class="form-control" value="<?= date('Y-m-d') ?>" required>

            <label class="form-label mt-2">Catatan</label>
            <input type="text" name="catatan_kemahasiswaan" class="form-control" placeholder="Catatan (opsional)">

            <div class="mt-4 d-grid">
                <button type="submit" name="submit_validasi" class="btn btn-success">✔️ Simpan Verifikasi</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>
