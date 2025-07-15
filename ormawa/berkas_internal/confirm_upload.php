<?php
session_start();
include '../../koneksi.php';

if (!isset($_SESSION["imported_data"]) || empty($_SESSION["imported_data"])) {
    header("Location: upload_batch_berkas_internal.php");
    exit();
}

$importedData = $_SESSION["imported_data"];

// Ambil id_ormawa dari user
$id_user = $_SESSION['id_user'];
$stmt_ormawa = $conn->prepare("SELECT id_ormawa FROM user_detail_ormawa WHERE id_user = ?");
$stmt_ormawa->bind_param("i", $id_user);
$stmt_ormawa->execute();
$result_ormawa = $stmt_ormawa->get_result();
$id_ormawa = $result_ormawa->fetch_assoc()['id_ormawa'] ?? null;

if (!$id_ormawa) {
    die("❌ ID Ormawa tidak ditemukan.");
}

// Proses simpan ke database
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $berhasil = 0;
    $gagal = 0;

    foreach ($importedData as $data) {
        if (!empty($data["nim"]) && !empty($data["nama_kegiatan"]) && !empty($data["partisipasi"])) {

            $stmt = $conn->prepare("
                INSERT INTO berkas_internal 
                (nim, nama_kegiatan, partisipasi, tanggal_kegiatan, id_ormawa, tanggal_pengajuan) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");

            if ($stmt) {
                $stmt->bind_param(
                    "ssssi",
                    $data["nim"],
                    $data["nama_kegiatan"],
                    $data["partisipasi"],
                    $data["tanggal_kegiatan"],
                    $id_ormawa
                );

                if ($stmt->execute()) {
                    $berhasil++;
                } else {
                    $gagal++;
                }

                $stmt->close();
            } else {
                $gagal++;
            }
        } else {
            $gagal++;
        }
    }

    unset($_SESSION["imported_data"]);

    echo "<script>
        alert('✅ Berhasil: {$berhasil}\\n❌ Gagal: {$gagal}');
        window.location.href='detail_berkas_internal.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Data Pengajuan SKKM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h3 class="text-center mb-4">📋 Konfirmasi Data Pengajuan SKKM</h3>
    <p class="text-center">Periksa data sebelum dikirim ke database:</p>

    <table class="table table-bordered">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Nama Kegiatan</th>
                <th>Partisipasi</th>
                <th>Tanggal Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($importedData as $row) {
                $stmt_user = $conn->prepare("
                    SELECT u.nama 
                    FROM user u 
                    JOIN user_detail_mahasiswa m ON u.id_user = m.id_user 
                    WHERE m.nim = ?
                ");
                $stmt_user->bind_param("s", $row["nim"]);
                $stmt_user->execute();
                $result_user = $stmt_user->get_result();
                $nama_mahasiswa = $result_user->fetch_assoc()['nama'] ?? 'Tidak Diketahui';

                echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['nim']}</td>
                    <td>{$nama_mahasiswa}</td>
                    <td>{$row['nama_kegiatan']}</td>
                    <td>{$row['partisipasi']}</td>
                    <td>{$row['tanggal_kegiatan']}</td>
                </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>

    <form method="POST">
        <div class="d-flex justify-content-between">
            <a href="upload_batch_berkas_internal.php" class="btn btn-danger">← Kembali</a>
            <button type="submit" class="btn btn-success">✅ Simpan ke Database</button>
        </div>
    </form>
</div>

</body>
</html>
