<?php
session_start();
require_once('../../koneksi.php');

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'BEM') {
    header("Location: ../../index.php");
    exit();
}

$id_bem = $_SESSION['id_user'];
$id_pengajuan = $_POST['id_pengajuan'] ?? null;
$nim = $_POST['nim'] ?? null;

if (!$id_pengajuan || !$nim) {
    echo "<script>alert('Data tidak lengkap!'); window.location.href='detail_pengajuan.php';</script>";
    exit();
}

// Ambil data pengajuan
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

// 🔹 Ambil daftar kategori dari tabel_ketentuan_skkm
$kategori_options = [];
$res_kategori = $conn->query("SELECT kategori_kegiatan FROM tabel_ketentuan_skkm");
while ($row = $res_kategori->fetch_assoc()) {
    $kategori_options[] = $row['kategori_kegiatan'];
}

// Handle update
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_validasi'])) {
    $kategori = $_POST['kategori_kegiatan'];
    $tingkat = $_POST['tingkat'];
    $partisipasi = $_POST['partisipasi'];
    $poin_skkm = $_POST['poin_skkm'];
    $status = $_POST['status_verifikasi_bem'];
    $tanggal = $_POST['tanggal_verifikasi_bem'];
    $catatan = $_POST['catatan_bem'];

    $stmt = $conn->prepare("UPDATE pengajuan_skkm 
        SET id_bem=?, kategori_kegiatan=?, tingkat=?, partisipasi=?, poin_skkm=?, status_verifikasi_bem=?, tanggal_verifikasi_bem=?, catatan_bem=? 
        WHERE id_pengajuan=?");
    $stmt->bind_param("isssssssi", $id_bem, $kategori, $tingkat, $partisipasi, $poin_skkm, $status, $tanggal, $catatan, $id_pengajuan);
    
    if ($stmt->execute()) {
        header("Location: detail_pengajuan.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Validasi Pengajuan SKKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../navbar.php'; ?>

<div class="container mt-4">
    <h3 class="text-center mb-4">🛠️ Validasi Pengajuan SKKM</h3>

    <div class="d-flex justify-content-between mb-3">
        <a href="detail_pengajuan.php" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">✅ Validasi berhasil disimpan.</div>
    <?php endif; ?>

    <form method="POST" class="row g-4 align-items-start">
        <input type="hidden" name="id_pengajuan" value="<?= $pengajuan['id_pengajuan'] ?>">
        <input type="hidden" name="nim" value="<?= $pengajuan['nim'] ?>">

        <!-- Kiri: Gambar -->
        <div class="col-md-5 text-center">
            <a href="../../asset/upload/<?= $pengajuan['file_bukti'] ?>" target="_blank">
                <img src="../../asset/upload/<?= $pengajuan['file_bukti'] ?>" class="border" style="width: 100%; max-height: 400px; object-fit: contain;">
            </a>
        </div>

        <!-- Kanan: Form -->
        <div class="col-md-7">
            <label class="form-label mt-2">NIM</label>
            <input type="text" class="form-control" readonly value="<?= $pengajuan['nim'] ?>">

            <label class="form-label mt-2">Nama Mahasiswa</label>
            <input type="text" class="form-control" readonly value="<?= $pengajuan['nama_mahasiswa'] ?>">

            <label class="form-label mt-2">Nama Kegiatan</label>
            <input type="text" class="form-control" readonly value="<?= $pengajuan['nama_kegiatan'] ?>">

            <label class="form-label mt-2">Kategori Kegiatan</label>
            <select name="kategori_kegiatan" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategori_options as $kat): ?>
                    <option value="<?= $kat ?>" <?= $pengajuan['kategori_kegiatan'] === $kat ? 'selected' : '' ?>>
                        <?= $kat ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label class="form-label mt-2">Tingkat</label>
            <input type="text" class="form-control" name="tingkat" required>

            <label class="form-label mt-2">Partisipasi</label>
            <input type="text" class="form-control" name="partisipasi" required>

            <label class="form-label mt-2">Poin SKKM</label>
            <input type="number" class="form-control" name="poin_skkm" min="1" required>

            <label class="form-label mt-2">Status</label>
            <select name="status_verifikasi_bem" class="form-select" required>
                <option value="Pending" <?= $pengajuan['status_verifikasi_bem'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Valid">Valid</option>
                <option value="Invalid">Invalid</option>
            </select>

            <label class="form-label mt-2">Tanggal Verifikasi</label>
            <input type="date" class="form-control" name="tanggal_verifikasi_bem" value="<?= date('Y-m-d') ?>" required>

            <label class="form-label mt-2">Catatan</label>
            <input type="text" class="form-control" name="catatan_bem">

            <div class="d-flex justify-content-between mt-4">
                <button type="submit" name="submit_validasi" class="btn btn-success">✔️ Validasi</button>
            </div>
        </div>
    </form>

    <footer class="text-center mt-5 text-muted small">© Agung Tisna</footer>
</div>
</body>
</html>
