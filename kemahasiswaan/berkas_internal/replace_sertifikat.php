<?php
session_start();
include '../../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../index.php';</script>";
    exit();
}

// Ambil semua nomor sertifikat duplikat
$sqlDuplikat = "
    SELECT nomor_sertifikat_internal 
    FROM (
        SELECT nomor_sertifikat_internal FROM berkas_internal WHERE nomor_sertifikat_internal IS NOT NULL AND nomor_sertifikat_internal != ''
        UNION ALL
        SELECT nomor_sertifikat_internal FROM berkas_bem WHERE nomor_sertifikat_internal IS NOT NULL AND nomor_sertifikat_internal != ''
    ) AS combined
    GROUP BY nomor_sertifikat_internal
    HAVING COUNT(*) > 1
";

$duplikat = [];
$result = $conn->query($sqlDuplikat);
while ($row = $result->fetch_assoc()) {
    $duplikat[] = $row['nomor_sertifikat_internal'];
}

// Ambil semua entri yang mengandung nomor duplikat
$data = [];
if (!empty($duplikat)) {
    $inClause = "'" . implode("','", array_map([$conn, 'real_escape_string'], $duplikat)) . "'";

    $sqlGabungan = "
        SELECT 'internal' AS sumber, id_berkas_internal AS id, nim, nama_kegiatan, partisipasi, nomor_sertifikat_internal
        FROM berkas_internal
        WHERE nomor_sertifikat_internal IN ($inClause)
        UNION ALL
        SELECT 'bem' AS sumber, id_berkas_bem AS id, nim, nama_kegiatan, partisipasi, nomor_sertifikat_internal
        FROM berkas_bem
        WHERE nomor_sertifikat_internal IN ($inClause)
    ";

    $res = $conn->query($sqlGabungan);
    while ($r = $res->fetch_assoc()) {
        $data[] = $r;
    }
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['nomor'] as $key => $nomor_baru) {
        $id = intval($_POST['id'][$key]);
        $sumber = $_POST['sumber'][$key];

        $tabel = ($sumber === 'internal') ? 'berkas_internal' : 'berkas_bem';
        $conn->query("UPDATE $tabel SET nomor_sertifikat_internal = '" . $conn->real_escape_string($nomor_baru) . "' WHERE id_berkas_$sumber = $id");
    }

    echo "<script>alert('Nomor duplikat berhasil diganti.'); window.location.href='detail_berkas_internal.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Nomor Sertifikat Duplikat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h3 class="mb-4">Ganti Nomor Sertifikat Duplikat</h3>
    <form method="post">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Sumber</th>
                    <th>NIM</th>
                    <th>Nama Kegiatan</th>
                    <th>Partisipasi</th>
                    <th>Nomor Sertifikat (Ganti jika perlu)</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($data)): ?>
                <?php foreach ($data as $i => $row): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= strtoupper($row['sumber']) ?></td>
                        <td><?= htmlspecialchars($row['nim']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['partisipasi']) ?></td>
                        <td>
                            <input type="hidden" name="id[]" value="<?= $row['id'] ?>">
                            <input type="hidden" name="sumber[]" value="<?= $row['sumber'] ?>">
                            <input type="text" name="nomor[]" value="<?= htmlspecialchars($row['nomor_sertifikat_internal']) ?>" class="form-control" required>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">Tidak ada duplikasi ditemukan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php if (!empty($data)): ?>
            <button type="submit" name="update" class="btn btn-success">💾 Simpan Perubahan</button>
        <?php endif; ?>
        <a href="detail_berkas_internal.php" class="btn btn-secondary">← Kembali</a>
    </form>
</div>
</body>
</html>
