<?php
include '../../koneksi.php';

$kategori = $_GET['kategori'];
$data = [];

$query = mysqli_query($conn, "SELECT jenis_kegiatan FROM jenis_kegiatan WHERE kategori_kegiatan = '$kategori'");
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row['jenis_kegiatan'];
}

echo json_encode($data);
?>
