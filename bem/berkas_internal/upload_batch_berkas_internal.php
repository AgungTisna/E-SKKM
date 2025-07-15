<?php
session_start();
include '../../koneksi.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$id_ormawa = $_GET['id_ormawa'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
    $file = $_FILES['file_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (empty($rows) || count($rows) < 2) {
            echo "<script>alert('❌ File kosong atau format tidak sesuai.'); window.location.href='upload_batch_berkas_internal.php?id_ormawa={$id_ormawa}';</script>";
            exit;
        }

        $importedData = [];
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header

            if (count($row) < 8) continue;

            $nim               = trim($row[2] ?? '');
            $nama_mahasiswa    = trim($row[3] ?? '');
            $nama_kegiatan     = trim($row[4] ?? '');
            $partisipasi       = trim($row[5] ?? '');
            $kategori_kegiatan = trim($row[6] ?? '');
            $tingkat           = trim($row[7] ?? '');
            $poin_skkm         = trim($row[8] ?? '');
            $tanggal_kegiatan  = trim($row[10] ?? '');

            if (!empty($nim) && !empty($nama_kegiatan)) {
                $importedData[] = [
                    'nim'               => $nim,
                    'nama_mahasiswa'    => $nama_mahasiswa,
                    'nama_kegiatan'     => $nama_kegiatan,
                    'partisipasi'       => $partisipasi,
                    'kategori_kegiatan' => $kategori_kegiatan,
                    'tingkat'           => $tingkat,
                    'poin_skkm'         => $poin_skkm,
                    'tanggal_kegiatan'  => $tanggal_kegiatan,
                ];
            }
        }

        if (empty($importedData)) {
            echo "<script>alert('❌ Tidak ada data valid untuk diimpor.'); window.history.back();</script>";
            exit;
        }

        $_SESSION['imported_data'] = $importedData;
        $_SESSION['id_ormawa_upload'] = $id_ormawa;

        header("Location: confirm_upload.php?id_ormawa=" . urlencode($id_ormawa));
        exit;

    } catch (Exception $e) {
        echo "<script>alert('Gagal membaca file Excel!\\n" . $e->getMessage() . "'); window.history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Batch Berkas Internal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-5">
    <h3 class="text-center mb-4">📤 Upload Batch Berkas Internal</h3>

    <form method="POST" enctype="multipart/form-data" action="upload_batch_berkas_internal.php?id_ormawa=<?= htmlspecialchars($id_ormawa) ?>">
        <div class="mb-3">
            <label for="file_excel" class="form-label">Pilih File Excel (.xlsx)</label>
            <input type="file" class="form-control" name="file_excel" accept=".xlsx" required>
        </div>
        <div class="d-flex justify-content-between">
            <a href="detail_berkas_internal.php?id_ormawa=<?= htmlspecialchars($id_ormawa) ?>" class="btn btn-secondary">← Kembali</a>
            <button type="submit" class="btn btn-success">📤 Upload & Preview</button>
        </div>
    </form>

    <div class="mt-4">
        <p><strong>📌 Format Excel yang diperlukan (kolom):</strong></p>
        <ol>
            <li>NIM</li>
            <li>Nama Mahasiswa</li>
            <li>Nama Kegiatan</li>
            <li>Partisipasi</li>
            <li>Kategori Kegiatan : Kegiatan Wajib / Bidang Akademik & Ilmiah / Bidang Minat Bakat Seni & Olahraga / Bidang Organisasi & Sosial</li>
            <li>Tingkat</li>
            <li>Poin SKKM</li>
            <li>Tanggal Kegiatan (YYYY-MM-DD)</li>
        </ol>
    </div>
</div>

</body>
</html>
