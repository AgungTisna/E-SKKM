<?php
$host = "localhost"; // Ganti jika database tidak di localhost
$user = "root"; // Ganti dengan username database Anda
$pass = ""; // Ganti dengan password database Anda
$dbname = "e-skkm"; // Ganti dengan nama database yang benar jika berbeda

// Membuat koneksi
$conn = new mysqli($host, $user, $pass, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
} 

// Jika koneksi berhasil
//echo "Koneksi berhasil";
?>
