<?php
session_start();
include '../../../koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'Kemahasiswaan') {
    echo "<script>alert('Anda harus login sebagai Kemahasiswaan!'); window.location.href='../index.php';</script>";
    exit();
}

// Handle hapus massal
if (isset($_POST['hapus_terpilih']) && !empty($_POST['pilih'])) {
    $ids = $_POST['pilih'];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conn->prepare("DELETE FROM berkas_kemahasiswaan WHERE id_berkas_kemahasiswaan IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();

    echo "<script>alert('Data berhasil dihapus.'); window.location.href='detail_kegiatan_wajib.php';</script>";
    exit();
}

// Ambil data
$sql = "
    SELECT 
        bk.id_berkas_kemahasiswaan AS id,
        bk.nim,
        u.nama AS nama_mahasiswa,
        bk.nama_kegiatan,
        bk.partisipasi,
        bk.kategori_kegiatan,
        bk.tingkat,
        bk.poin_skkm,
        bk.tanggal_kegiatan
    FROM berkas_kemahasiswaan bk
    JOIN user_detail_mahasiswa m ON bk.nim = m.nim
    JOIN user u ON m.id_user = u.id_user
    WHERE bk.kategori_kegiatan = 'Kegiatan Wajib'
    ORDER BY bk.tanggal_kegiatan DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Kegiatan Wajib</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-warning {
            background-color: #fff3cd !important;
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Detail Kegiatan Wajib Mahasiswa</h2>
    <a href="../detail_berkas_internal.php" class="btn btn-secondary mb-3">Kembali</a>
    <a href="upload_kegiatan_wajib.php" class="btn btn-primary mb-3">⬆️ Upload Batch Kegiatan Wajib</a>
    <a href="download_template_kegiatan_wajib.php" class="btn btn-success mb-3">📥 Download Template</a>
    <form method="POST" class="mb-3">
        <div>
            <input type="text" id="searchInput" class="form-control mb-3" placeholder="🔍 Cari NIM, Nama Mahasiswa, atau Kegiatan...">
        </div>
        <div class="d-flex justify-content-between mb-3">
            <div>
                <button type="submit" name="hapus_terpilih" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data terpilih?')">
                    🗑️ Hapus Terpilih
                </button>
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark text-center">
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Nama Kegiatan</th>
                    <th>Partisipasi</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Kategori</th>
                    <th>Tingkat</th>
                    <th>Poin</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    $no = 1;
                    $tracker = [];

                    while ($row = $result->fetch_assoc()) {
                        $key = strtolower(trim($row['nim'] . '|' . $row['nama_kegiatan'] . '|' . $row['partisipasi']));
                        $tracker[$key][] = $row;
                    }

                    foreach ($tracker as $rows) {
                        $isDuplicate = count($rows) > 1;
                        foreach ($rows as $row) {
                            $rowClass = $isDuplicate ? 'table-danger' : '';
                            echo "<tr class='{$rowClass}'>";
                            echo "<td class='text-center'><input type='checkbox' name='pilih[]' value='" . $row['id'] . "'></td>";
                            echo "<td class='text-center'>{$no}</td>";
                            echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_mahasiswa']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_kegiatan']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['partisipasi']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tanggal_kegiatan']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['kategori_kegiatan']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tingkat']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['poin_skkm']) . "</td>";
                            echo "</tr>";
                            $no++;
                        }
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center'>Tidak ada data ditemukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </form>
</div>

<script>
    // Live search
    document.getElementById('searchInput').addEventListener('input', function () {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(keyword) ? '' : 'none';
        });
    });

    // Pilih Semua
    document.getElementById('checkAll').addEventListener('click', function () {
        const checkboxes = document.querySelectorAll('input[name="pilih[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>

</body>
</html>

<?php $conn->close(); ?>
