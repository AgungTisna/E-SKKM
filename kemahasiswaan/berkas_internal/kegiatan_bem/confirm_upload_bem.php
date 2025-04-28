<?php
session_start();
include '../../../koneksi.php';
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../index.php';</script>";
    exit();
}

$id_kemahasiswaan = $_SESSION['id_user'];

// Langkah 2: Konfirmasi dan Simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi'])) {
    $berhasil = 0;
    $gagal = 0;

    $conn->begin_transaction();

    try {
        foreach ($_POST['update_data'] as $data) {
            list($id_berkas, $nomor_sertifikat, $tanggal_dikeluarkan) = explode('|', $data);

            $stmt = $conn->prepare("
                UPDATE berkas_bem
                SET id_kemahasiswaan = ?, 
                    nomor_sertifikat_internal = ?, 
                    tanggal_dikeluarkan = ?
                WHERE id_berkas_bem = ?
            ");
            $stmt->bind_param("issi", $id_kemahasiswaan, $nomor_sertifikat, $tanggal_dikeluarkan, $id_berkas);

            if ($stmt->execute()) {
                $berhasil++;
            } else {
                $gagal++;
            }
        }

        $conn->commit();
        echo "<script>alert('Update selesai. Berhasil: $berhasil, Gagal: $gagal'); window.location.href='detail_kegiatan_bem.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Gagal memproses data.'); window.location.href='upload_kegiatan_bem.php';</script>";
    }

    exit();
}

// Langkah 1: Upload dan Preview
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo "<script>alert('Gagal mengunggah file.'); window.location.href='upload_kegiatan_bem.php';</script>";
    exit();
}

$tmpFile = $_FILES['file']['tmp_name'];
$spreadsheet = IOFactory::load($tmpFile);
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray(null, true, true, true);

if (count($data) <= 1) {
    echo "<script>alert('File kosong atau tidak valid.'); window.location.href='upload_kegiatan_bem.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Update Sertifikat BEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4">📋 Pratinjau Data Sertifikat yang Akan Diperbarui</h3>
    <form method="post">
        <input type="hidden" name="konfirmasi" value="1">
        <table class="table table-bordered table-hover">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>ID Berkas</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Nama Kegiatan</th>
                    <th>Partisipasi</th>
                    <th>Kategori</th>
                    <th>Tingkat</th>
                    <th>Poin SKKM</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Nomor Sertifikat (Baru)</th>
                    <th>Tanggal Dikeluarkan (Baru)</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $no = 1;
            foreach ($data as $index => $row) {
                if ($index == 1) continue;

                $id_berkas = trim($row['A']);
                $nomor_sertifikat = trim($row['K']);
                $tanggal_dikeluarkan = trim($row['L']);

                if (empty($id_berkas) || empty($nomor_sertifikat) || empty($tanggal_dikeluarkan)) continue;

                // Ambil info pendukung
                $stmt = $conn->prepare("
                    SELECT bb.nim, u.nama AS nama_mahasiswa, bb.nama_kegiatan, bb.partisipasi,
                        bb.kategori_kegiatan, bb.tingkat, bb.poin_skkm, bb.tanggal_kegiatan, bb.tanggal_pengajuan
                    FROM berkas_bem bb
                    LEFT JOIN user_detail_mahasiswa udm ON bb.nim = udm.nim
                    LEFT JOIN user u ON udm.id_user = u.id_user
                    WHERE bb.id_berkas_bem = ?
                ");
                $stmt->bind_param("i", $id_berkas);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $rowData = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='text-center'>{$no}</td>";
                    echo "<td>{$id_berkas}</td>";
                    echo "<td>" . htmlspecialchars($rowData['nim']) . "</td>";
                    echo "<td>" . htmlspecialchars($rowData['nama_mahasiswa']) . "</td>";
                    echo "<td>" . htmlspecialchars($rowData['nama_kegiatan']) . "</td>";
                    echo "<td>" . htmlspecialchars($rowData['partisipasi']) . "</td>";
                    echo "<td>" . htmlspecialchars($rowData['kategori_kegiatan']) . "</td>";
                    echo "<td>" . htmlspecialchars($rowData['tingkat']) . "</td>";
                    echo "<td class='text-center'>" . htmlspecialchars($rowData['poin_skkm']) . "</td>";
                    echo "<td>" . htmlspecialchars($rowData['tanggal_kegiatan']) . "</td>";
                    echo "<td>" . htmlspecialchars($rowData['tanggal_pengajuan']) . "</td>";
                    echo "<td><strong>" . htmlspecialchars($nomor_sertifikat) . "</strong></td>";
                    echo "<td>" . htmlspecialchars($tanggal_dikeluarkan) . "</td>";
                    echo "<input type='hidden' name='update_data[]' value='{$id_berkas}|{$nomor_sertifikat}|{$tanggal_dikeluarkan}'>";
                    echo "</tr>";
                    $no++;
                }
            }
            ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-between mt-4">
            <a href="upload_kegiatan_bem.php" class="btn btn-secondary">← Kembali</a>
            <button type="submit" class="btn btn-success">✅ Konfirmasi dan Simpan</button>
        </div>
    </form>
</div>
</body>
</html>
