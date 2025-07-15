<?php
session_start();
require_once('../../koneksi.php');

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'BEM') {
    header("Location: ../../index.php");
    exit();
}

$id_bem = $_SESSION['id_user'];
$id_pengajuan = $_POST['id_pengajuan'] ?? null;
$nim = $_POST['nim'] ?? null;

if (!$id_pengajuan || !$nim) {
    echo "<script>alert('Data tidak lengkap!'); window.location.href='detail_pengajuan.php';</script>";
    exit();
}

$query = "
    SELECT p.*, u.nama AS nama_mahasiswa
    FROM pengajuan_skkm p
    JOIN user_detail_mahasiswa udm ON p.nim = udm.nim
    JOIN user u ON udm.id_user = u.id_user
    WHERE p.id_pengajuan = ? AND p.nim = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $id_pengajuan, $nim);
$stmt->execute();
$result = $stmt->get_result();
$pengajuan = $result->fetch_assoc();

if (!$pengajuan) {
    echo "<script>alert('Pengajuan tidak ditemukan!'); window.location.href='detail_pengajuan.php';</script>";
    exit();
}

$kategori_options = [];
$res_kategori = $conn->query("SELECT DISTINCT kategori_kegiatan FROM tabel_ketentuan_skkm");
while ($row = $res_kategori->fetch_assoc()) {
    $kategori_options[] = $row['kategori_kegiatan'];
}

$tingkat_options = [];
$res_tingkat = $conn->query("SELECT nama_tingkat FROM tingkat");
while ($row = $res_tingkat->fetch_assoc()) {
    $tingkat_options[] = $row['nama_tingkat'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_validasi'])) {
    $kategori = $_POST['kategori_kegiatan'];
    $tingkat = $_POST['tingkat'];
    $partisipasi = $_POST['partisipasi'];
    $poin_skkm = $_POST['poin_skkm'];
    $status = $_POST['status_verifikasi_bem'];
    $tanggal = $_POST['tanggal_verifikasi_bem'];
    $catatan = $_POST['catatan_bem'];

    $stmt = $conn->prepare("UPDATE pengajuan_skkm 
        SET id_bem=?, kategori_kegiatan=?, tingkat=?, partisipasi=?, poin_skkm=?, status_verifikasi_bem=?, tanggal_verifikasi_bem=?, catatan_bem=? 
        WHERE id_pengajuan=?");
    $stmt->bind_param("isssssssi", $id_bem, $kategori, $tingkat, $partisipasi, $poin_skkm, $status, $tanggal, $catatan, $id_pengajuan);
    if ($stmt->execute()) {
        header("Location: detail_pengajuan.php");
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Validasi Pengajuan SKKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
<?php include '../navbar.php'; ?>
<div class="container mt-4">
    <h3 class="text-center mb-4">🛠️ Validasi Pengajuan SKKM</h3>
    <a href="detail_pengajuan.php" class="btn btn-outline-secondary mb-3">← Kembali</a>

    <form method="POST" class="row g-4 align-items-start">
        <input type="hidden" name="id_pengajuan" value="<?= $pengajuan['id_pengajuan'] ?>">
        <input type="hidden" name="nim" value="<?= $pengajuan['nim'] ?>">

        <div class="col-md-5 text-center">
            <a href="../../asset/upload/<?= $pengajuan['file_bukti'] ?>" target="_blank">
                <img src="../../asset/upload/<?= $pengajuan['file_bukti'] ?>" class="border mb-2" style="width: 100%; max-height: 400px; object-fit: contain;">
            </a>
            <div class="mt-3">
                <strong>Bukti Keikutsertaan:</strong><br>
                <?php if (!empty($pengajuan['bukti_keikutsertaan'])):
                    $ext = pathinfo($pengajuan['bukti_keikutsertaan'], PATHINFO_EXTENSION);
                    if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])): ?>
                        <a href="../../asset/upload/<?= $pengajuan['bukti_keikutsertaan'] ?>" target="_blank">
                            <img src="../../asset/upload/<?= $pengajuan['bukti_keikutsertaan'] ?>" class="border" style="width: 100%; max-height: 300px; object-fit: contain;">
                        </a>
                    <?php else: ?>
                        <a href="../../asset/upload/<?= $pengajuan['bukti_keikutsertaan'] ?>" target="_blank">📎 Lihat Dokumen</a>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="text-muted">Tidak tersedia</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-7">
            <label class="form-label mt-2">NIM</label>
            <input type="text" class="form-control" readonly value="<?= $pengajuan['nim'] ?>">

            <label class="form-label mt-2">Nama Mahasiswa</label>
            <input type="text" class="form-control" readonly value="<?= $pengajuan['nama_mahasiswa'] ?>">

            <label class="form-label mt-2">Nama Kegiatan</label>
            <input type="text" class="form-control" readonly value="<?= $pengajuan['nama_kegiatan'] ?>">


            <label class="form-label">Kategori Kegiatan</label>
            <select name="kategori_kegiatan" id="kategori" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategori_options as $kat): ?>
                    <option value="<?= $kat ?>" <?= $pengajuan['kategori_kegiatan'] === $kat ? 'selected' : '' ?>><?= $kat ?></option>
                <?php endforeach; ?>
            </select>

            <label class="form-label mt-2">Tingkat</label>
            <select name="tingkat" class="form-select" required>
                <option value="">-- Pilih Tingkat --</option>
                <?php foreach ($tingkat_options as $tk): ?>
                    <option value="<?= $tk ?>" <?= $pengajuan['tingkat'] === $tk ? 'selected' : '' ?>><?= $tk ?></option>
                <?php endforeach; ?>
            </select>

            <label class="form-label mt-2">Partisipasi</label>
            <select name="partisipasi" id="partisipasi" class="form-select" required></select>

            <label class="form-label mt-2">Poin SKKM</label>
            <input type="number" class="form-control" name="poin_skkm" required value="<?= $pengajuan['poin_skkm'] ?>">

            <label class="form-label mt-2">Status</label>
            <select name="status_verifikasi_bem" class="form-select" required>
                <option value="Pending" <?= $pengajuan['status_verifikasi_bem'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Valid" <?= $pengajuan['status_verifikasi_bem'] === 'Valid' ? 'selected' : '' ?>>Valid</option>
                <option value="Invalid" <?= $pengajuan['status_verifikasi_bem'] === 'Invalid' ? 'selected' : '' ?>>Invalid</option>
            </select>

            <label class="form-label mt-2">Tanggal Verifikasi</label>
            <input type="date" class="form-control" name="tanggal_verifikasi_bem" value="<?= date('Y-m-d') ?>" required>

            <label class="form-label mt-2">Catatan</label>
            <input type="text" class="form-control" name="catatan_bem" value="<?= htmlspecialchars($pengajuan['catatan_bem']) ?>">

            <button type="submit" name="submit_validasi" class="btn btn-success mt-4">✔️ Simpan Validasi</button>
        </div>
    </form>
</div>


<script>
// Objek poin SKKM
const poinSKKM = {
  "Bidang Akademik & Ilmiah": {
    "Konfrensi - Penulis Utama": { "P.T.": 3, "Reg.": 4, "Nas.": 5, "Inter.": 6 },
    "Konfrensi - Penyaji": { "P.T.": 3, "Reg.": 4, "Nas.": 5, "Inter.": 6 },
    "Konfrensi - Moderator": { "P.T.": 3, "Reg.": 4, "Nas.": 5, "Inter.": 6 },
    "Penelitian/Karya Tulis - Perorangan": { "P.T.": 4, "Reg.": 5, "Nas.": 5, "Inter.": 6 },
    "Penelitian/Karya Tulis - Kelompok - Ketua": { "P.T.": 0, "Reg": 0, "Nas": 0, "Inter.": 0 },
    "Penelitian/Karya Tulis - Kelompok - Anggota": { "P.T.": 3, "Reg.": 4, "Nas.": 4, "Inter.": 5 },
    "Seminar/Workshop/Talk show - Peserta": { "P.T.": 2, "Reg.": 3, "Nas.": 3, "Inter.": 4 },
    "Kuliah Industri - Peserta": { "P.T.": 2, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "Kepanitiaan - Ketua": { "P.T.": 4, "Reg.": 5, "Nas.": 6, "Inter.": 8 },
    "Kepanitiaan - Anggota": { "P.T.": 3, "Reg.": 3, "Nas.": 4, "Inter.": 6 }
  },
  "Bidang Minat Bakat Seni & Olahraga": {
    "Lomba - Juara 1": { "P.T.": 4, "Reg.": 5, "Nas.": 6, "Inter.": 7 },
    "Lomba - Juara 2": { "P.T.": 3, "Reg.": 4, "Nas.": 5, "Inter.": 6 },
    "Lomba - Juara 3": { "P.T.": 3, "Reg.": 4, "Nas.": 5, "Inter.": 6 },
    "Lomba - Harapan/Favorit": { "P.T.": 3, "Reg.": 4, "Nas.": 5, "Inter.": 6 },
    "Lomba - Peserta": { "P.T.": 2, "Reg.": 3, "Nas.": 4, "Inter.": 5 }
  },
  "Bidang Organisasi & Sosial": {
    "BEM/BALMA - Ketua": { "P.T.": 8, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "PM/BAKSOS - Peserta": { "P.T.": 2, "Reg.": 2, "Nas.": 3, "Inter.": 4 }
  },
  "Kegiatan Wajib": {
    "Peserta - G.M.T.I": { "P.T.": 2, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "Peserta - Jalan Santai": { "P.T.": 2, "Reg.": 0, "Nas.": 0, "Inter.": 0 }
  }
};

function updatePoinSKKM() {
    const kategori = $('#kategori').val();
    const partisipasi = $('#partisipasi').val();
    const tingkat = $('select[name="tingkat"]').val();

    if (poinSKKM[kategori] && poinSKKM[kategori][partisipasi] && poinSKKM[kategori][partisipasi][tingkat]) {
        const poin = poinSKKM[kategori][partisipasi][tingkat];
        $('input[name="poin_skkm"]').val(poin);
    } else {
        $('input[name="poin_skkm"]').val('');
    }
}

$(document).ready(function() {
    function loadPartisipasi(kategori, selected = '') {
        if (!kategori) return;
        $.ajax({
            url: 'get_partisipasi.php',
            method: 'POST',
            data: { kategori: kategori },
            success: function(response) {
                $('#partisipasi').html(response);
                if (selected) {
                    $('#partisipasi').val(selected);
                }
                updatePoinSKKM();
            }
        });
    }

    const initialKategori = $('#kategori').val();
    const selectedPartisipasi = "<?= $pengajuan['partisipasi'] ?>";
    if (initialKategori) {
        loadPartisipasi(initialKategori, selectedPartisipasi);
    }

    $('#kategori').on('change', function() {
        const kategori = $(this).val();
        loadPartisipasi(kategori);
    });

    $('#kategori, #partisipasi, select[name="tingkat"]').on('change', function() {
        updatePoinSKKM();
    });

    updatePoinSKKM();
});
</script>
</body>
</html>
