<?php
require_once('../../koneksi.php');

if (isset($_POST['kategori'])) {
    $kategori = $_POST['kategori'];
    $stmt = $conn->prepare("SELECT jenis_kegiatan FROM jenis_kegiatan WHERE kategori_kegiatan = ?");
    $stmt->bind_param("s", $kategori);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">-- Pilih Partisipasi --</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['jenis_kegiatan']) . '">' . htmlspecialchars($row['jenis_kegiatan']) . '</option>';
    }
}
