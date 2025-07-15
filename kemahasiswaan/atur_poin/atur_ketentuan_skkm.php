<?php
session_start();
include '../../koneksi.php';

// Cek login & role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    header("Location: ../index.php");
    exit();
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['minimal'] as $kategori => $poin) {
        $poin = max(0, (int)$poin); // pastikan minimal 0

        $stmt = $conn->prepare("
            INSERT INTO tabel_ketentuan_skkm (kategori_kegiatan, minimal_poin)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE minimal_poin = VALUES(minimal_poin)
        ");
        $stmt->bind_param("si", $kategori, $poin);
        $stmt->execute();
        $stmt->close();
    }

    echo "<script>alert('✅ Ketentuan berhasil diperbarui!'); window.location.href='atur_ketentuan_skkm.php';</script>";
    exit();
}

// Ambil data dari tabel
$kategori_list = [
    'Kegiatan Wajib',
    'Bidang Organisasi & Sosial',
    'Bidang Minat Bakat Seni & Olahraga',
    'Bidang Akademik & Ilmiah'
];

$ketentuan = [];
$sql = "SELECT * FROM tabel_ketentuan_skkm";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $ketentuan[$row['kategori_kegiatan']] = $row['minimal_poin'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Atur Ketentuan SKKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h3 class="mb-4 text-center">🛠️ Pengaturan Minimal Poin SKKM</h3>

    <form method="POST" class="card shadow p-4">
        <?php foreach ($kategori_list as $kategori): ?>
            <div class="mb-3 row">
                <label class="col-sm-5 col-form-label"><?= htmlspecialchars($kategori) ?></label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" name="minimal[<?= $kategori ?>]"
                           value="<?= htmlspecialchars($ketentuan[$kategori] ?? 0) ?>" min="0" required>
                </div>
                <div class="col-sm-3 d-flex align-items-center">
                    <span class="text-muted">poin</span>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="d-flex justify-content-between mt-4">
            <a href="../index.php" class="btn btn-secondary">← Kembali</a>
            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
        </div>
    </form>
</div>

</body>
</html>

<?php $conn->close(); ?>
