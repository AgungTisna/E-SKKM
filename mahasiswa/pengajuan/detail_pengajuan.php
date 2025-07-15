<?php
session_start();
require_once('../../koneksi.php');

if (!isset($_SESSION['nim'])) {
    header("Location: ../../index.php");
    exit();
}

$nim = $_SESSION['nim'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pengajuan SKKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../navbar.php'; ?>
<div class="container mt-5">
    <h3 class="mb-4 text-center">📋 Detail Pengajuan SKKM</h3>
    <a href="../index.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>
    <div class="mb-4 text-end">
    <a href="pengajuan_skkm.php" class="btn btn-primary mb-3">
        ➕ Ajukan SKKM Baru
    </a>
    <div class="card shadow">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>File Bukti</th>
                        <th>Bukti Keikutsertaan</th>
                        <th>Nama Kegiatan</th>
                        <th>Kategori Kegiatan</th> <!-- Tambahan -->
                        <th>Tingkat</th>
                        <th>Partisipasi</th>
                        <th>Poin</th>
                        <th>Status BEM</th>
                        <th>Tgl Verifikasi BEM</th>
                        <th>Status Kemahasiswaan</th>
                        <th>Tgl Verifikasi Kemahasiswaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM pengajuan_skkm WHERE nim = ? ORDER BY id_pengajuan DESC";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $nim);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            $canDelete = in_array($row['status_verifikasi_bem'], ['Pending', 'Invalid']) ||
                                         in_array($row['status_verifikasi_kemahasiswaan'], ['Pending', 'Invalid']);
                            echo "<tr>
                                <td class='text-center'>{$no}</td>
                                <td class='text-center'>
                                    <a href='../../asset/upload/{$row['file_bukti']}' target='_blank'>
                                        <img src='../../asset/upload/{$row['file_bukti']}' height='50px' width='50px'>
                                    </a>
                                </td>
                                <td class='text-center'>";
                                if (!empty($row['bukti_keikutsertaan'])) {
                                    $ext = pathinfo($row['bukti_keikutsertaan'], PATHINFO_EXTENSION);
                                    if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
                                        echo "<a href='../../asset/upload/{$row['bukti_keikutsertaan']}' target='_blank'>
                                                <img src='../../asset/upload/{$row['bukti_keikutsertaan']}' height='50px' width='50px'>
                                            </a>";
                                    } else {
                                        echo "<a href='../../asset/upload/{$row['bukti_keikutsertaan']}' target='_blank'>📎 Lihat Dokumen</a>";
                                    }
                                }
                                echo "</td>

                                <td class='text-center'>{$row['nama_kegiatan']}</td>
                                <td class='text-center'>{$row['kategori_kegiatan']}</td>
                                <td class='text-center'>{$row['tingkat']}</td>
                                <td class='text-center'>{$row['partisipasi']}</td>
                                <td class='text-center'>{$row['poin_skkm']}</td>
                                <td class='text-center'>
                                    <span class='badge bg-" . getColor($row['status_verifikasi_bem']) . "'>
                                        {$row['status_verifikasi_bem']}
                                    </span>
                                </td>
                                <td class='text-center'>{$row['tanggal_verifikasi_bem']}</td>
                                <td class='text-center'>
                                    <span class='badge bg-" . getColor($row['status_verifikasi_kemahasiswaan']) . "'>
                                        {$row['status_verifikasi_kemahasiswaan']}
                                    </span>
                                </td>
                                <td class='text-center'>{$row['tanggal_verifikasi_kemahasiswaan']}</td>
                                <td class='text-center'>";
                            if ($canDelete) {
                                echo "<div class='d-flex justify-content-center gap-1'>";
                                    if ($row['status_verifikasi_bem'] === 'Invalid' || $row['status_verifikasi_kemahasiswaan'] === 'Invalid') {
                                        echo "<a href='edit_pengajuan.php?id={$row['id_pengajuan']}' class='btn btn-sm btn-warning'>✏️ Edit</a>";
                                    }
                                    echo "<form method='POST' action='hapus_pengajuan.php' onsubmit='return confirm(\"Yakin ingin menghapus pengajuan ini?\")'>
                                            <input type='hidden' name='id_pengajuan' value='{$row['id_pengajuan']}'>
                                            <button type='submit' class='btn btn-sm btn-danger'>🗑️ Hapus</button>
                                          </form>
                                </div>";
                            } else {
                                echo "<span class='text-muted'>Terkunci</span>";
                            }
                            echo "</td>
                            </tr>";

                            // Catatan
                            echo "<tr class='table-light'>
                                <td colspan='13'>
                                    <div class='mb-4 text-start '>
                                        <p class='mb-1'><strong>Catatan BEM:</strong> " . htmlspecialchars($row['catatan_bem']) . "</p>
                                        <p class='mb-0'><strong>Catatan Kemahasiswaan:</strong> " . htmlspecialchars($row['catatan_kemahasiswaan']) . "</p>
                                    </div>
                                </td>
                            </tr>";

                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='12' class='text-center'>Belum ada pengajuan SKKM.</td></tr>";
                    }

                    function getColor($status) {
                        if ($status === 'Valid') return 'success';
                        if ($status === 'Invalid') return 'danger';
                        if ($status === 'Pending') return 'secondary';
                        return 'light';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
