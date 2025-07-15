<?php
session_start();
require '../../koneksi.php';

// Cek apakah pengguna login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='../index.php';</script>";
    exit();
}

// Cek apakah form dikirim via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? null;
    $email   = $_POST['email'] ?? '';

    if (!$id_user || !$email) {
        echo "<script>alert('Data tidak lengkap!'); window.location.href='edit_profile_mahasiswa.php';</script>";
        exit();
    }

    // Update email di tabel user
    $query = "UPDATE user SET email = ? WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $email, $id_user);

    if ($stmt->execute()) {
        echo "<script>alert('Email berhasil diperbarui!'); window.location.href='profile_mahasiswa.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui email!'); window.location.href='edit_profile_mahasiswa.php';</script>";
    }
} else {
    echo "<script>alert('Akses tidak valid!'); window.location.href='profile_mahasiswa.php';</script>";
    exit();
}
