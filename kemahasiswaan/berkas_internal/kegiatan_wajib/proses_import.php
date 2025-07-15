<?php
include '../../../koneksi.php';

if (isset($_POST['import']) && isset($_POST['data'])) {
    $data = unserialize(base64_decode($_POST['data']));
    $berhasil = 0;

    // Cari user role Kemahasiswaan
    $result_user = $conn->query("SELECT id_user FROM user WHERE role = 'Kemahasiswaan' LIMIT 1");
    if ($result_user->num_rows == 0) {
        die("User dengan role 'Kemahasiswaan' tidak ditemukan.");
    }
    $id_user_kemahasiswaan = $result_user->fetch_assoc()['id_user'];

    // Cari id_ormawa dari user_detail_ormawa
    $result_ormawa = $conn->query("SELECT id_ormawa FROM user_detail_ormawa WHERE id_user = '$id_user_kemahasiswaan' LIMIT 1");
    if ($result_ormawa->num_rows == 0) {
        die("User 'Kemahasiswaan' tidak memiliki data di user_detail_ormawa.");
    }
    $id_ormawa_kemahasiswaan = $result_ormawa->fetch_assoc()['id_ormawa'];

    // Loop data dari Excel
    foreach ($data as $row) {
        if (count($row) < 9) continue; // Pastikan semua kolom tersedia

        $nim = $conn->real_escape_string((string) $row[1]);
        $nama_mahasiswa = $conn->real_escape_string((string) $row[2]); // Tidak disimpan
        $nama_kegiatan = $conn->real_escape_string((string) $row[3]);
        $partisipasi = $conn->real_escape_string((string) $row[4]);
        $tanggal_kegiatan = is_object($row[5]) ? $row[5]->format('Y-m-d') : (string) $row[5];
        $tanggal_pengajuan = is_object($row[6]) ? $row[6]->format('Y-m-d') : (string) $row[6];
        $nomor_sertifikat = $conn->real_escape_string((string) $row[7]); // hasil formula -> treat as text
        $tanggal_dikeluarkan = is_object($row[8]) ? $row[8]->format('Y-m-d') : (string) $row[8];

        $sql = "INSERT INTO berkas_internal (
                    id_ormawa, nim, nama_kegiatan, partisipasi, tanggal_kegiatan,
                    tanggal_pengajuan, kategori_kegiatan, nomor_sertifikat_internal, tanggal_dikeluarkan
                ) VALUES (
                    '$id_ormawa_kemahasiswaan', '$nim', '$nama_kegiatan', '$partisipasi', '$tanggal_kegiatan',
                    '$tanggal_pengajuan', 'Kegiatan Wajib', '$nomor_sertifikat', '$tanggal_dikeluarkan'
                )";

        if ($conn->query($sql)) {
            $berhasil++;
        }
    }

    echo "<div style='padding: 20px; font-family: Arial'>";
    echo "<h3>$berhasil data berhasil disimpan ke <code>berkas_internal</code> dengan tanggal pengajuan.</h3>";
    echo "<a href='detail_kegiatan_wajib.php'>← Kembali ke Daftar Kegiatan</a>";
    echo "</div>";
} else {
    echo "Akses tidak sah.";
}
?>
