<?php
session_start();
include '../../koneksi.php';

// Pastikan parameter id_ormawa dikirim
if (!isset($_GET['id_ormawa'])) {
    echo "<script>alert('ID Ormawa tidak ditemukan.'); window.location.href='../index.php';</script>";
    exit;
}

$id_ormawa = intval($_GET['id_ormawa']); // Hindari SQL injection dengan casting ke integer

// Ambil nama Ormawa
$nama_ormawa = 'Ormawa Tidak Dikenal';
$getNama = mysqli_query($conn, "SELECT nama_ormawa FROM user_detail_ormawa WHERE id_ormawa = $id_ormawa");
if ($getNama && $rowOrmawa = mysqli_fetch_assoc($getNama)) {
    $nama_ormawa = $rowOrmawa['nama_ormawa'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>List Kegiatan - <?= htmlspecialchars($nama_ormawa) ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        table {
            width: 80%;
            margin: 30px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid black;
        }
        th {
            background-color: black;
            color: white;
        }
        .btn-detail {
            background-color: #00aaff;
            border: none;
            padding: 8px 14px;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn-detail:hover {
            background-color: #0077aa;
        }
        .back-btn {
            background-color: #bbb;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            margin: 20px 50px;
            display: inline-block;
        }
        h2 {
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
<a href="../index.php" class="back-btn">← Kembali</a>
<h2>Detail Berkas Internal - <?= htmlspecialchars($nama_ormawa) ?></h2>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kegiatan</th>
            <th>Jumlah Peserta</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $no = 1;

    // Ambil daftar kegiatan berdasarkan id_ormawa
    $query = mysqli_query($conn, "
        SELECT nama_kegiatan, COUNT(*) AS jumlah_peserta 
        FROM berkas_internal 
        WHERE id_ormawa = $id_ormawa 
        GROUP BY nama_kegiatan
        ORDER BY nama_kegiatan ASC
    ");

    while ($row = mysqli_fetch_assoc($query)) {
        $nama_kegiatan = $row['nama_kegiatan'];
        $nama_kegiatan_encoded = urlencode($nama_kegiatan);

        // Cek berapa yang sudah terverifikasi (poin > 0)
        $cek = mysqli_query($conn, "
            SELECT COUNT(*) as sudah 
            FROM berkas_internal 
            WHERE nama_kegiatan = '" . mysqli_real_escape_string($conn, $nama_kegiatan) . "' 
              AND id_ormawa = $id_ormawa 
              AND poin_skkm > 0
        ");
        $sudah = mysqli_fetch_assoc($cek)['sudah'];

        $status = ($sudah == $row['jumlah_peserta']) ? "✅ Terverifikasi" : "❌ Belum Terverifikasi";

        echo "<tr>";
        echo "<td>{$no}</td>";
        echo "<td>" . htmlspecialchars($nama_kegiatan) . "</td>";
        echo "<td>{$row['jumlah_peserta']} Peserta</td>";
        echo "<td>{$status}</td>";
        echo "<td><a class='btn-detail' href='detail_kegiatan.php?nama_kegiatan={$nama_kegiatan_encoded}&id_ormawa={$id_ormawa}'>Lihat Detail</a></td>";
        echo "</tr>";
        $no++;
    }
    ?>
    </tbody>
</table>

<footer>© Agung Tisna</footer>
</body>
</html>
