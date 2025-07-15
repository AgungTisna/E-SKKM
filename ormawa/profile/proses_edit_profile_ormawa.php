<?php
session_start();
require '../../koneksi.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='../index.php';</script>";
    exit();
}

// Cek data POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user      = $_POST['id_user'] ?? null;
    $nama         = $_POST['nama'] ?? '';
    $email        = $_POST['email'] ?? '';
    $nama_ormawa  = $_POST['nama_ormawa'] ?? '';

    if (!$id_user || !$nama || !$email || !$nama_ormawa) {
        echo "<script>alert('Semua field wajib diisi!'); window.location.href='edit_profile_ormawa.php';</script>";
        exit();
    }

    // Update nama dan email di tabel user
    $query_user = "UPDATE user SET nama = ?, email = ? WHERE id_user = ?";
    $stmt_user = $conn->prepare($query_user);
    $stmt_user->bind_param("ssi", $nama, $email, $id_user);

    // Update nama ormawa di tabel user_detail_ormawa
    $query_ormawa = "UPDATE user_detail_ormawa SET nama_ormawa = ? WHERE id_user = ?";
    $stmt_ormawa = $conn->prepare($query_ormawa);
    $stmt_ormawa->bind_param("si", $nama_ormawa, $id_user);

    // Eksekusi update
    if ($stmt_user->execute() && $stmt_ormawa->execute()) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='profile_ormawa.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan perubahan.'); window.location.href='edit_profile_ormawa.php';</script>";
    }
} else {
    echo "<script>alert('Akses tidak valid.'); window.location.href='profile_ormawa.php';</script>";
}
