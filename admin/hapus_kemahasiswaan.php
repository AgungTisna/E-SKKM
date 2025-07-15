<?php
include '../koneksi.php';

if (isset($_GET['id'])) {
    $id_user = $_GET['id'];

    // Hapus dari tabel user_detail_kemahasiswaan terlebih dahulu
    $delete_detail = $conn->prepare("DELETE FROM user_detail_kemahasiswaan WHERE id_user = ?");
    $delete_detail->bind_param("i", $id_user);
    $delete_detail->execute();

    // Hapus dari tabel user
    $delete_user = $conn->prepare("DELETE FROM user WHERE id_user = ?");
    $delete_user->bind_param("i", $id_user);

    if ($delete_user->execute()) {
        echo "<script>alert('Data berhasil dihapus!'); window.location.href='detail_kemahasiswaan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location.href='detail_kemahasiswaan.php';</script>";
    }

    $delete_detail->close();
    $delete_user->close();
}

$conn->close();
?>
