<?php
session_start();
include '../../koneksi.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

// Ambil id_bem dari session login
$id_user = $_SESSION['id_user'];
$bem_q = mysqli_query($conn, "SELECT id_bem FROM user_detail_bem WHERE id_user = '$id_user'");
$bem_row = mysqli_fetch_assoc($bem_q);
$id_bem = $bem_row['id_bem'] ?? 0;

// Ambil data dari form
$ids        = $_POST['id'];
$kategori   = $_POST['kategori_kegiatan'];
$jenis      = $_POST['jenis_kegiatan'];
$tingkat    = $_POST['tingkat'];
$poin       = $_POST['poin_skkm'];

// Ambil nama kegiatan untuk redirect
$first_id = $ids[0];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_kegiatan FROM berkas_internal WHERE id_berkas_internal = '$first_id'"));
$nama_kegiatan = $data['nama_kegiatan'];

// Update setiap baris
for ($i = 0; $i < count($ids); $i++) {
    $id  = intval($ids[$i]);
    $kat = mysqli_real_escape_string($conn, $kategori[$i]);
    $jen = mysqli_real_escape_string($conn, $jenis[$i]);
    $tin = mysqli_real_escape_string($conn, $tingkat[$i]);
    $poi = intval($poin[$i]);

    $update = mysqli_query($conn, "UPDATE berkas_internal SET 
        kategori_kegiatan = '$kat',
        jenis_kegiatan = '$jen',
        tingkat = '$tin',
        poin_skkm = '$poi',
        id_bem = '$id_bem'
        WHERE id_berkas_internal = '$id'");
}

// Redirect ke halaman detail kegiatan dengan notifikasi
header("Location: detail_kegiatan.php?nama_kegiatan=" . urlencode($nama_kegiatan) . "&success=1");
exit;
