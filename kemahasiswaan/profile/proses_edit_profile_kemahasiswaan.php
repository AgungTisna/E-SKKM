<?php
session_start();
require '../../koneksi.php';

// Cek apakah user login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='../index.php';</script>";
    exit();
}

// Cek data POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? null;
    $email   = $_POST['email'] ?? '';
    $nip     = $_POST['nip'] ?? '';

    if (!$id_user || !$email || !$nip) {
        echo "<script>alert('Data tidak lengkap!'); window.location.href='edit_profile_kemahasiswaan.php';</script>";
        exit();
    }

    // Update email di tabel user
    $query1 = "UPDATE user SET email = ? WHERE id_user = ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("si", $email, $id_user);

    // Update NIP di tabel user_detail_kemahasiswaan
    $query2 = "UPDATE user_detail_kemahasiswaan SET nip = ? WHERE id_user = ?";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("si", $nip, $id_user);

    // Eksekusi keduanya
    if ($stmt1->execute() && $stmt2->execute()) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='profile_kemahasiswaan.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan perubahan!'); window.location.href='edit_profile_kemahasiswaan.php';</script>";
    }
} else {
    echo "<script>alert('Akses tidak sah!'); window.location.href='profile_kemahasiswaan.php';</script>";
}
