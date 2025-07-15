<?php
session_start();
include '../../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    header("Location: ../index.php");
    exit();
}

function bulanRomawi($bulan) {
    $romawi = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
        5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
        9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];
    return $romawi[intval($bulan)];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tanggal_dikeluarkan'])) {
    $tanggal = $_POST['tanggal_dikeluarkan'];
    $bulan = date('n', strtotime($tanggal));
    $tahun = date('Y', strtotime($tanggal));
    $romawi = bulanRomawi($bulan);

    $query = mysqli_query($conn, "
        SELECT id_berkas_eksternal 
        FROM berkas_eksternal 
        WHERE nomor_sertifikat_eksternal IS NULL 
        OR nomor_sertifikat_eksternal = '' 
        OR nomor_sertifikat_eksternal = '0'
    ");

    while ($data = mysqli_fetch_assoc($query)) {
        $id = $data['id_berkas_eksternal'];
        $nomor = $id . "/Srtf.eks/KMHS/" . $romawi . "/" . $tahun;

        mysqli_query($conn, "
            UPDATE berkas_eksternal SET 
                nomor_sertifikat_eksternal = '$nomor',
                tanggal_dikeluarkan = '$tanggal'
            WHERE id_berkas_eksternal = '$id'
        ");
    }

    $pesan_sukses = "✅ Nomor sertifikat berhasil dibuat untuk entri eksternal yang belum memiliki sertifikat.";
}

$data = mysqli_query($conn, "
    SELECT * FROM berkas_eksternal 
    WHERE nomor_sertifikat_eksternal IS NULL 
       OR nomor_sertifikat_eksternal = '' 
       OR nomor_sertifikat_eksternal = '0'
    ORDER BY tanggal_kegiatan DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generate Nomor Sertifikat Eksternal</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .form-container {
            width: 95%;
            margin: 20px auto;
            padding: 15px;
            border: 1px solid #ccc;
        }
        .form-container input[type="date"],
        .form-container input[type="submit"] {
            padding: 8px;
            margin-right: 10px;
        }
        .form-container input[type="submit"] {
            background-color: green;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: black;
            color: white;
        }
        .success {
            width: 95%;
            margin: 10px auto;
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
        .btn-back {
            margin: 20px;
            padding: 10px 16px;
            background: #bbb;
            text-decoration: none;
            border-radius: 6px;
            color: black;
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<h2 style="text-align:center;">Data Eksternal yang Belum Memiliki Nomor Sertifikat</h2>
<a href="detail_berkas_eksternal.php" class="btn-back">← Kembali ke Detail Berkas Eksternal</a>

<div class="form-container">
    <form method="post">
        <label for="tanggal_dikeluarkan">Tanggal Dikeluarkan:</label>
        <input type="date" name="tanggal_dikeluarkan" required>
        <input type="submit" value="Generate Sertifikat">
    </form>
</div>

<?php if (isset($pesan_sukses)): ?>
    <div class="success"><?= $pesan_sukses ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kegiatan</th>
            <th>Nama Peserta</th>
            <th>Tanggal Kegiatan</th>
            <th>Nomor Sertifikat</th>
            <th>Tanggal Dikeluarkan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($data)) {
            echo "<tr>";
            echo "<td>{$no}</td>";
            echo "<td>" . htmlspecialchars($row['nama_kegiatan']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_peserta']) . "</td>";
            echo "<td>" . htmlspecialchars($row['tanggal_kegiatan']) . "</td>";
            echo "<td>" . ($row['nomor_sertifikat_eksternal'] ?? '-') . "</td>";
            echo "<td>" . ($row['tanggal_dikeluarkan'] ?? '-') . "</td>";
            echo "</tr>";
            $no++;
        }
        ?>
    </tbody>
</table>

</body>
</html>
