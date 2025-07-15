<?php
session_start();
include 'koneksi.php'; // Hubungkan ke database

// Cek apakah form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // Password dalam teks biasa

    // 🔹 Ambil data dari tabel `user` termasuk semua role
    $query = "SELECT u.id_user, u.username, u.password, u.role, u.email, u.nama, 
                     m.nim, m.prodi, m.angkatan, 
                     b.jabatan AS jabatan_bem, 
                     k.jabatan AS jabatan_kemahasiswaan, 
                     o.nama_ormawa
              FROM user u
              LEFT JOIN user_detail_mahasiswa m ON u.id_user = m.id_user
              LEFT JOIN user_detail_bem b ON u.id_user = b.id_user
              LEFT JOIN user_detail_kemahasiswaan k ON u.id_user = k.id_user
              LEFT JOIN user_detail_ormawa o ON u.id_user = o.id_user
              WHERE u.username = ? AND u.password = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("❌ Error pada query: " . $conn->error);
    }

    // 🔹 Bind parameter dan eksekusi
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // 🔹 Cek apakah username dan password cocok
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // ✅ Simpan data ke session
        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['nim'] = $row['nim'];
        $_SESSION['prodi'] = $row['prodi'];
        $_SESSION['angkatan'] = $row['angkatan'];
        $_SESSION['jabatan_bem'] = $row['jabatan_bem'];
        $_SESSION['jabatan_kemahasiswaan'] = $row['jabatan_kemahasiswaan'];
        $_SESSION['nama_ormawa'] = $row['nama_ormawa']; // Nama Ormawa

        // ✅ Redirect berdasarkan role
        switch ($row['role']) {
            case 'Mahasiswa':
                header("Location: mahasiswa/index.php");
                break;
            case 'BEM':
                header("Location: bem/index.php");
                break;
            case 'Kemahasiswaan':
                header("Location: kemahasiswaan/index.php");
                break;
            case 'Administrator':
                header("Location: admin/index.php");
                break;
            case 'Ormawa':
                header("Location: ormawa/index.php");
                break;
            default:
                header("Location: index.php");
                break;
        }
        exit();
    } else {
        echo "<script>alert('❌ Username atau Password salah!'); window.location.href='index.php';</script>";
    }
}
?>
