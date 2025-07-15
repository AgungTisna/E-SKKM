<?php
session_start();
require_once('../../koneksi.php');

if (!isset($_SESSION['nim'])) {
    header("Location: ../../index.php");
    exit();
}

$nim = $_SESSION['nim'];

// Proses penghapusan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pengajuan'])) {
    $id_pengajuan = intval($_POST['id_pengajuan']);

    // Cek apakah data milik mahasiswa ini dan masih boleh dihapus
    $stmt = $conn->prepare("SELECT file_bukti, status_verifikasi_bem, status_verifikasi_kemahasiswaan FROM pengajuan_skkm WHERE id_pengajuan = ? AND nim = ?");
    $stmt->bind_param("is", $id_pengajuan, $nim);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        $status_bem = $row['status_verifikasi_bem'];
        $status_kemahasiswaan = $row['status_verifikasi_kemahasiswaan'];
        $file_bukti = $row['file_bukti'];

        $bolehHapus = in_array($status_bem, ['Pending', 'Invalid']) || in_array($status_kemahasiswaan, ['Pending', 'Invalid']);

        if ($bolehHapus) {
            // Hapus dari database
            $delete = $conn->prepare("DELETE FROM pengajuan_skkm WHERE id_pengajuan = ?");
            $delete->bind_param("i", $id_pengajuan);
            $delete->execute();

            // Hapus file jika ada
            $file_path = "../../asset/upload/" . $file_bukti;
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            header("Location: detail_pengajuan.php?status=deleted");
            exit();
        } else {
            echo "<script>alert('Data tidak dapat dihapus karena sudah divalidasi.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Data tidak ditemukan.'); window.history.back();</script>";
        exit();
    }
} else {
    header("Location: ../detail_pengajuan.php");
    exit();
}
?>
