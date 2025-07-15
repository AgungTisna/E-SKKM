<?php
session_start(); // Mulai sesi untuk menyimpan pesan

include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Tidak dienkripsi
    $nip = $_POST['nip'];
    $jabatan = "Verifikator"; // Jabatan tetap
    $role = "Kemahasiswaan"; // Role tetap

    // Cek apakah username atau email sudah digunakan
    $cek = $conn->prepare("SELECT * FROM user WHERE username = ? OR email = ?");
    if (!$cek) {
        die("Query Error (Cek username/email): " . $conn->error);
    }
    
    $cek->bind_param("ss", $username, $email);
    $cek->execute();
    $result = $cek->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username atau Email sudah digunakan!";
        header("Location: tambah_kemahasiswaan.php");
        exit();
    }

    // Insert ke tabel user tanpa enkripsi password
    $stmt = $conn->prepare("INSERT INTO user (nama, email, username, password, role) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Query Error (Insert User): " . $conn->error);
    }

    $stmt->bind_param("sssss", $nama, $email, $username, $password, $role);
    
    if (!$stmt->execute()) {
        $_SESSION['error'] = "Gagal menambahkan user: " . $stmt->error;
        header("Location: tambah_kemahasiswaan.php");
        exit();
    }

    // Ambil ID user yang baru saja dibuat
    $id_user = $conn->insert_id;

    // Insert ke tabel user_detail_kemahasiswaan
    $stmt2 = $conn->prepare("INSERT INTO user_detail_kemahasiswaan (id_user, nip, jabatan) VALUES (?, ?, ?)");
    if (!$stmt2) {
        die("Query Error (Insert Detail): " . $conn->error);
    }

    $stmt2->bind_param("iss", $id_user, $nip, $jabatan);
    
    if ($stmt2->execute()) {
        $_SESSION['success'] = "Data berhasil ditambahkan!";
        header("Location: detail_kemahasiswaan.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal menambahkan detail kemahasiswaan: " . $stmt2->error;
        header("Location: tambah_kemahasiswaan.php");
        exit();
    }

    // Tutup statement
    $stmt->close();
    $stmt2->close();
    $conn->close();
}
?>
