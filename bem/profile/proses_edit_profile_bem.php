<?php
session_start();
include '../../koneksi.php'; // Koneksi ke database

// Pastikan pengguna login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='../index.php';</script>";
    exit();
}

// Ambil data dari form
$id_user = $_POST['id_user'];
$nama = $_POST['nama'];
$email = $_POST['email'];

// 🔹 Validasi hanya cek keberadaan '@'
if (strpos($email, '@') === false) {
    echo "<script>alert('Email harus mengandung \'@\'!'); window.history.back();</script>";
    exit();
}

// 🔹 Cek apakah email sudah digunakan oleh user lain
$query_check = "SELECT id_user FROM user WHERE email = ? AND id_user != ?";
$stmt_check = $conn->prepare($query_check);
$stmt_check->bind_param("si", $email, $id_user);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "<script>alert('Email sudah digunakan oleh pengguna lain!'); window.history.back();</script>";
    exit();
}

// 🔹 Update tabel `user` (Hanya Nama dan Email)
$query1 = "UPDATE user SET nama = ?, email = ? WHERE id_user = ?";
$stmt1 = $conn->prepare($query1);
$stmt1->bind_param("ssi", $nama, $email, $id_user);
$success1 = $stmt1->execute();

if ($success1) {
    echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='profile_bem.php';</script>";
} else {
    echo "<script>alert('Gagal memperbarui profil!'); window.history.back();</script>";
}
?>
