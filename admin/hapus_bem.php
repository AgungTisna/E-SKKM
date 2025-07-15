<?php
session_start();
include '../koneksi.php';

if (isset($_GET['id'])) {
    $id_user = $_GET['id'];

    // Hapus data dari tabel user_detail_bem terlebih dahulu
    $delete_detail = $conn->prepare("DELETE FROM user_detail_bem WHERE id_user = ?");
    if (!$delete_detail) {
        die("Query Error (Hapus Detail BEM): " . $conn->error);
    }
    $delete_detail->bind_param("i", $id_user);
    $delete_detail->execute();
    $delete_detail->close();

    // Hapus data dari tabel user
    $delete_user = $conn->prepare("DELETE FROM user WHERE id_user = ?");
    if (!$delete_user) {
        die("Query Error (Hapus User): " . $conn->error);
    }
    $delete_user->bind_param("i", $id_user);

    if ($delete_user->execute()) {
        $_SESSION['success'] = "Data BEM berhasil dihapus!";
        header("Location: detail_bem.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal menghapus data BEM: " . $delete_user->error;
        header("Location: detail_bem.php");
        exit();
    }

    $delete_user->close();
}

$conn->close();
?>
