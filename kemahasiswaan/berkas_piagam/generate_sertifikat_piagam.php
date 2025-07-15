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
        SELECT id_berkas_piagam 
        FROM berkas_piagam 
        WHERE nomor_sertifikat_piagam IS NULL 
           OR nomor_sertifikat_piagam = '' 
           OR nomor_sertifikat_piagam = '0'
    ");

    while ($data = mysqli_fetch_assoc($query)) {
        $id = $data['id_berkas_piagam'];
        $nomor = $id . "/Piagam/KMHS/" . $romawi . "/" . $tahun;

        mysqli_query($conn, "
            UPDATE berkas_piagam SET 
                nomor_sertifikat_piagam = '$nomor',
                tanggal_dikeluarkan = '$tanggal'
            WHERE id_berkas_piagam = '$id'
        ");
    }

    $pesan_sukses = "✅ Nomor sertifikat piagam berhasil dibuat.";
}

// Ambil data piagam yang belum punya nomor sertifikat
$data = mysqli_query($conn, "
    SELECT bp.*, uo.nama_ormawa
    FROM berkas_piagam bp
    JOIN user_detail_ormawa uo ON bp.id_ormawa = uo.id_ormawa
    WHERE bp.nomor_sertifikat_piagam IS NULL 
       OR bp.nomor_sertifikat_piagam = '' 
       OR bp.nomor_sertifikat_piagam = '0'
    ORDER BY bp.tanggal_kegiatan DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generate Sertifikat Piagam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
        .success {
            width: 95%;
            margin: 10px auto;
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
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

<h2 class="text-center mt-3">📝 Generate Sertifikat Piagam</h2>
<a href="detail_berkas_piagam.php" class="btn-back">← Kembali ke Detail Berkas Piagam</a>

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
            <th>Nama Ormawa</th>
            <th>Nama Kegiatan</th>
            <th>Nama Penerima</th>
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
            echo "<td>" . htmlspecialchars($row['nama_ormawa']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_kegiatan']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama_penerima']) . "</td>";
            echo "<td>" . htmlspecialchars($row['tanggal_kegiatan']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nomor_sertifikat_piagam']) . "</td>";
            echo "<td>" . htmlspecialchars($row['tanggal_dikeluarkan']) . "</td>";
            echo "</tr>";
            $no++;
        }
        ?>
    </tbody>
</table>

</body>
</html>
