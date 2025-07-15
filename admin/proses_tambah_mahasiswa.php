<?php
session_start(); // Mulai sesi untuk menyimpan pesan
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $prodi = $_POST['prodi'];
    $angkatan = $_POST['angkatan'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Tidak dienkripsi
    $role = "Mahasiswa"; // Role tetap

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
        header("Location: tambah_mahasiswa.php");
        exit();
    }

    // Insert ke tabel user
    $stmt = $conn->prepare("INSERT INTO user (nama, email, username, password, role) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Query Error (Insert User): " . $conn->error);
    }

    $stmt->bind_param("sssss", $nama, $email, $username, $password, $role);
    
    if (!$stmt->execute()) {
        $_SESSION['error'] = "Gagal menambahkan user: " . $stmt->error;
        header("Location: tambah_mahasiswa.php");
        exit();
    }

    // Ambil ID user yang baru saja dibuat
    $id_user = $conn->insert_id;

    // Insert ke tabel user_detail_mahasiswa
    $stmt2 = $conn->prepare("INSERT INTO user_detail_mahasiswa (nim, id_user, prodi, angkatan) VALUES (?, ?, ?, ?)");
    if (!$stmt2) {
        die("Query Error (Insert Detail Mahasiswa): " . $conn->error);
    }

    $stmt2->bind_param("siss", $nim, $id_user, $prodi, $angkatan);
    
    if ($stmt2->execute()) {
        $_SESSION['success'] = "Data Mahasiswa berhasil ditambahkan!";
        header("Location: detail_mahasiswa.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal menambahkan detail mahasiswa: " . $stmt2->error;
        header("Location: tambah_mahasiswa.php");
        exit();
    }

    // Tutup statement
    $stmt->close();
    $stmt2->close();
    $conn->close();
}
?>
