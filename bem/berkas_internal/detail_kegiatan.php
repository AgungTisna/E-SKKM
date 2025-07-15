<?php
session_start();
include '../../koneksi.php';

if (!isset($_GET['nama_kegiatan'])) {
    echo "Kegiatan tidak ditemukan.";
    exit;
}

$nama_kegiatan = urldecode($_GET['nama_kegiatan']);

// Ambil salah satu id_berkas_internal dan id_ormawa untuk keperluan navigasi/verifikasi
$get_id_query = mysqli_query($conn, "
    SELECT id_berkas_internal, id_ormawa 
    FROM berkas_internal 
    WHERE nama_kegiatan = '" . mysqli_real_escape_string($conn, $nama_kegiatan) . "' 
    LIMIT 1
");

$id_row = mysqli_fetch_assoc($get_id_query);
$id_berkas = $id_row['id_berkas_internal'];
$id_ormawa = $id_row['id_ormawa']; // Ambil id_ormawa
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Kegiatan - <?= htmlspecialchars($nama_kegiatan) ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }
        th {
            background-color: black;
            color: white;
        }
        .btn {
            padding: 8px 16px;
            background-color: #00aa00;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #008800;
        }
        .back-btn {
            margin: 20px 50px;
            display: inline-block;
            background-color: #bbb;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
        }
        h2, .center-btn {
            text-align: center;
        }
        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<!-- Tombol Kembali dengan id_ormawa -->
<a href="list_kegiatan.php?id_ormawa=<?= urlencode($id_ormawa) ?>" class="back-btn">← Kembali</a>

<h2>Detail Peserta Kegiatan: <?= htmlspecialchars($nama_kegiatan) ?></h2>

<div class="center-btn">
    <a class="btn" href="verifikasi_form.php?id=<?= $id_berkas ?>">Verifikasi Kegiatan Ini</a>
</div>

<br>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Partisipasi</th>
            <th>Tanggal Kegiatan</th>
            <th>Kategori</th>
            <th>Jenis</th>
            <th>Tingkat</th>
            <th>Poin SKKM</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $query = mysqli_query($conn, "
            SELECT nim, partisipasi, tanggal_kegiatan, kategori_kegiatan, jenis_kegiatan, tingkat, poin_skkm 
            FROM berkas_internal 
            WHERE nama_kegiatan = '" . mysqli_real_escape_string($conn, $nama_kegiatan) . "'
        ");

        while ($row = mysqli_fetch_assoc($query)) {
            echo "<tr>";
            echo "<td>{$no}</td>";
            echo "<td>{$row['nim']}</td>";
            echo "<td>{$row['partisipasi']}</td>";
            echo "<td>{$row['tanggal_kegiatan']}</td>";
            echo "<td>{$row['kategori_kegiatan']}</td>";
            echo "<td>{$row['jenis_kegiatan']}</td>";
            echo "<td>{$row['tingkat']}</td>";
            echo "<td>{$row['poin_skkm']}</td>";
            echo "</tr>";
            $no++;
        }
        ?>
    </tbody>
</table>

<footer>© Agung Tisna</footer>
</body>
</html>
