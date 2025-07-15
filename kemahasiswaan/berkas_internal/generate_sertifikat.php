<?php
session_start();
include '../../koneksi.php';

// Fungsi konversi bulan ke angka romawi
function bulanRomawi($bulan) {
    $romawi = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
        5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
        9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];
    return $romawi[intval($bulan)];
}

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tanggal_dikeluarkan'])) {
    $tanggal_dikeluarkan = $_POST['tanggal_dikeluarkan'];

    $bulan = date('n', strtotime($tanggal_dikeluarkan));
    $tahun = date('Y', strtotime($tanggal_dikeluarkan));
    $romawi = bulanRomawi($bulan);

    // Ambil hanya data yang memenuhi syarat
    $query = mysqli_query($conn, "
        SELECT id_berkas_internal 
        FROM berkas_internal 
        WHERE poin_skkm > 0 
          AND (nomor_sertifikat_internal IS NULL OR nomor_sertifikat_internal = '' OR nomor_sertifikat_internal = '0')
    ");

    while ($data = mysqli_fetch_assoc($query)) {
        $id = $data['id_berkas_internal'];
        $nomor = $id . "/srtf/KMHS/" . $romawi . "/" . $tahun;

        mysqli_query($conn, "
            UPDATE berkas_internal SET 
                tanggal_dikeluarkan = '$tanggal_dikeluarkan',
                nomor_sertifikat_internal = '$nomor'
            WHERE id_berkas_internal = '$id'
        ");
    }

    $pesan_sukses = "✅ Nomor sertifikat berhasil dibuat untuk semua entri yang memenuhi syarat.";
}

// Ambil data yang memenuhi syarat untuk ditampilkan
$data = mysqli_query($conn, "
    SELECT * FROM berkas_internal 
    WHERE poin_skkm > 0 
      AND (nomor_sertifikat_internal IS NULL OR nomor_sertifikat_internal = '' OR nomor_sertifikat_internal = '0')
    ORDER BY tanggal_kegiatan DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Nomor Sertifikat</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .form-container {
            width: 95%;
            margin: 20px auto;
            padding: 15px;
            border: 1px solid #ccc;
        }
        .form-container input[type="date"] {
            padding: 8px;
            margin-right: 10px;
        }
        .form-container input[type="submit"] {
            padding: 8px 20px;
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

<h2 style="text-align:center;">Generate Nomor Sertifikat Internal</h2>
<a href="detail_berkas_internal.php" class="btn-back">← Kembali ke Detail Berkas Internal</a>

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
            <th>NIM</th>
            <th>Tanggal Kegiatan</th>
            <th>Poin</th>
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
            echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
            echo "<td>" . htmlspecialchars($row['tanggal_kegiatan']) . "</td>";
            echo "<td>" . htmlspecialchars($row['poin_skkm']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nomor_sertifikat_internal']) . "</td>";
            echo "<td>" . htmlspecialchars($row['tanggal_dikeluarkan']) . "</td>";
            echo "</tr>";
            $no++;
        }
        ?>
    </tbody>
</table>

</body>
</html>
