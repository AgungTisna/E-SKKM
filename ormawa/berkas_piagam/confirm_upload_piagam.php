<?php
session_start();
include '../../koneksi.php';

if (!isset($_SESSION["imported_piagam"]) || empty($_SESSION["imported_piagam"])) {
    header("Location: upload_batch_berkas_piagam.php");
    exit();
}

$importedData = $_SESSION["imported_piagam"];

// Ambil id_ormawa dari session user Ormawa
$id_user = $_SESSION['id_user'];
$query_ormawa = "SELECT id_ormawa FROM user_detail_ormawa WHERE id_user = ?";
$stmt_ormawa = $conn->prepare($query_ormawa);
$stmt_ormawa->bind_param("i", $id_user);
$stmt_ormawa->execute();
$result_ormawa = $stmt_ormawa->get_result();
$row_ormawa = $result_ormawa->fetch_assoc();
$id_ormawa = $row_ormawa['id_ormawa'] ?? null;

if (!$id_ormawa) {
    die("❌ Gagal mendapatkan ID Ormawa.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $successCount = 0;
    $failedCount = 0;
    $errorMessages = [];

    foreach ($importedData as $data) {
        $nama_kegiatan     = $data["nama_kegiatan"] ?? '';
        $nama_penerima     = $data["nama_penerima"] ?? '';
        $tanggal_kegiatan  = $data["tanggal_kegiatan"] ?? '';
        $keterangan        = $data["keterangan"] ?? '';

        if (!empty($nama_kegiatan) && !empty($nama_penerima) && !empty($tanggal_kegiatan) && !empty($keterangan)) {
            $query = "INSERT INTO berkas_piagam 
                      (id_ormawa, nama_kegiatan, nama_penerima, tanggal_kegiatan, keterangan, tanggal_pengajuan)
                      VALUES (?, ?, ?, ?, ?, NOW())";

            $stmt = $conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param(
                    "issss",
                    $id_ormawa,
                    $nama_kegiatan,
                    $nama_penerima,
                    $tanggal_kegiatan,
                    $keterangan
                );

                if ($stmt->execute()) {
                    $successCount++;
                } else {
                    $failedCount++;
                    $errorMessages[] = "❌ Gagal simpan data: " . $stmt->error;
                }
            } else {
                $failedCount++;
                $errorMessages[] = "❌ Prepare gagal: " . $conn->error;
            }
        } else {
            $failedCount++;
            $errorMessages[] = "❌ Data tidak lengkap.";
        }
    }

    unset($_SESSION["imported_piagam"]);

    if ($failedCount == 0) {
        echo "<script>alert('✅ Semua data berhasil disimpan: $successCount'); window.location.href='detail_berkas_piagam.php';</script>";
    } else {
        echo "<script>alert('✅ $successCount berhasil \\n❌ $failedCount gagal'); window.location.href='upload_batch_berkas_piagam.php';</script>";
    }

    foreach ($errorMessages as $msg) {
        error_log($msg);
    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Data Piagam</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h3 class="text-center">Konfirmasi Data Berkas Piagam</h3>
    <p class="text-center">Periksa data berikut sebelum disimpan.</p>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Kegiatan</th>
                <th>Nama Penerima</th>
                <th>Tanggal Kegiatan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($importedData as $row) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>" . htmlspecialchars($row['nama_kegiatan']) . "</td>
                        <td>" . htmlspecialchars($row['nama_penerima']) . "</td>
                        <td>" . htmlspecialchars($row['tanggal_kegiatan']) . "</td>
                        <td>" . htmlspecialchars($row['keterangan']) . "</td>
                    </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>

    <form action="confirm_upload_piagam.php" method="POST">
        <button type="submit" class="btn btn-success">✅ Simpan ke Database</button>
        <a href="upload_batch_berkas_piagam.php" class="btn btn-danger">❌ Batalkan</a>
    </form>
</div>

</body>
</html>
