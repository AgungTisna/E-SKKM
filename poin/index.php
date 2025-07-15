<?php
require_once('../koneksi.php');

$nim = '';
$data_poin = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = trim($_POST['nim']);

    if ($nim !== '') {
        // Cek apakah NIM ada di salah satu tabel
        $cek_query = $conn->prepare("
            SELECT COUNT(*) as total FROM (
                SELECT nim FROM berkas_internal WHERE nim = ?
                UNION
                SELECT nim FROM arsip_skkm WHERE nim = ?
                UNION
                SELECT nim FROM pengajuan_skkm WHERE nim = ?
            ) AS combined
        ");
        $cek_query->bind_param("sss", $nim, $nim, $nim);
        $cek_query->execute();
        $cek_result = $cek_query->get_result()->fetch_assoc();

        if ((int)$cek_result['total'] === 0) {
            $data_poin = null;
        } else {
            // Ambil ketentuan poin
            $query_ketentuan = "SELECT kategori_kegiatan, minimal_poin FROM tabel_ketentuan_skkm";
            $res_ketentuan = $conn->query($query_ketentuan);
            while ($row = $res_ketentuan->fetch_assoc()) {
                $data_poin[$row['kategori_kegiatan']] = [
                    'poin' => 0,
                    'minimal' => $row['minimal_poin'],
                    'detail' => []
                ];
            }

            // Query dari 3 tabel
            $tables = [
                'berkas_internal' => [
                    'tanggal_field' => 'tanggal_kegiatan',
                    'where' => "WHERE nim = ? AND nomor_sertifikat_internal IS NOT NULL AND nomor_sertifikat_internal != ''"
                ],
                'arsip_skkm' => [
                    'tanggal_field' => 'tanggal_kegiatan',
                    'where' => "WHERE nim = ? AND nomor_sertifikat_internal IS NOT NULL AND nomor_sertifikat_internal != ''"
                ],
                'pengajuan_skkm' => [
                    'tanggal_field' => 'tanggal_verifikasi_kemahasiswaan',
                    'where' => "WHERE nim = ? AND status_verifikasi_bem = 'Valid' AND status_verifikasi_kemahasiswaan = 'Valid'"
                ]
            ];

            foreach ($tables as $table => $conf) {
                $extra_column = ($table !== 'pengajuan_skkm') ? ", nomor_sertifikat_internal" : "";

                $query = "
                    SELECT kategori_kegiatan, nama_kegiatan, partisipasi, tingkat, poin_skkm, {$conf['tanggal_field']} AS tanggal_kegiatan $extra_column
                    FROM $table
                    {$conf['where']}
                ";

                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $nim);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $kategori = trim($row['kategori_kegiatan']);
                    if ($kategori === '') continue;

                    if (!isset($data_poin[$kategori])) {
                        $data_poin[$kategori] = [
                            'poin' => 0,
                            'minimal' => 0,
                            'detail' => []
                        ];
                    }

                    $data_poin[$kategori]['poin'] += (int)$row['poin_skkm'];
                    $data_poin[$kategori]['detail'][] = $row;
                }
            }

            // Urutkan 'Kegiatan Wajib' di atas
            uksort($data_poin, function ($a, $b) {
                if ($a === 'Kegiatan Wajib') return -1;
                if ($b === 'Kegiatan Wajib') return 1;
                return strcmp($a, $b);
            });
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cari Poin Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            border: 1px solid black;
            background-color: white;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            width: 60px;
            height: 60px;
        }

        .logo img {
            width: 50px;
        }

        .navbar-title {
            font-size: 18px;
            font-weight: bold;
        }

        .profile-container {
            position: relative;
        }

        .profile-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: black;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .profile-icon img {
            width: 35px;
        }

        .dropdown {
            position: absolute;
            top: 45px;
            right: 0;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 220px;
            display: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            z-index: 10;
        }

        .dropdown div, .dropdown a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: black;
            border-bottom: 1px solid #eee;
        }

        .dropdown div:last-child {
            border-bottom: none;
        }

        .dropdown a:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body class="bg-light">
<div class="navbar">
    <div class="navbar-left">
        <div class="logo">
            <a href="index.php"><img src="../asset/img/itb.png" alt="Logo ITB STIKOM"></a>
        </div>
        <div class="navbar-title">E-SKKM</div>
    </div>

    <div class="profile-container" onclick="toggleDropdown()">
        <div class="profile-icon"><img src="../asset/img/profile.png" alt="Profile"></div>
        <div id="profileDropdown" class="dropdown">
            <a href="../index.php">Login</a>
        </div>
    </div>
</div>

<div class="container py-5">
    <h2 class="mb-4 text-primary">Cari Poin Mahasiswa Berdasarkan NIM</h2>
    <form method="POST" class="mb-4">
        <div class="input-group">
            <input type="text" name="nim" class="form-control" placeholder="Masukkan NIM..." required value="<?= htmlspecialchars($nim) ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    <?php if (is_array($data_poin) && !empty($data_poin)): ?>
        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title">Hasil Pencarian</h5>
                <table class="table table-bordered table-hover">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Kategori Kegiatan</th>
                            <th>Poin Saat Ini</th>
                            <th>Minimal Poin</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_poin as $kategori => $info): ?>
                        <tr>
                            <td><?= htmlspecialchars($kategori) ?></td>
                            <td class="text-center"><?= $info['poin'] ?></td>
                            <td class="text-center"><?= $info['minimal'] ?></td>
                            <td class="text-center">
                                <span class="badge <?= $info['poin'] >= $info['minimal'] ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $info['poin'] >= $info['minimal'] ? 'Terpenuhi' : 'Belum Terpenuhi' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#detail<?= md5($kategori) ?>">Lihat Detail</button>
                            </td>
                        </tr>
                        <tr class="collapse" id="detail<?= md5($kategori) ?>">
                            <td colspan="5">
                                <?php if (!empty($info['detail'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-secondary text-center">
                                                <tr>
                                                    <th>Nama Kegiatan</th>
                                                    <th>Partisipasi</th>
                                                    <th>Tingkat</th>
                                                    <th>Poin</th>
                                                    <th>Tanggal Kegiatan</th>
                                                    <th>Nomor Sertifikat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($info['detail'] as $detail): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($detail['nama_kegiatan']) ?></td>
                                                    <td><?= htmlspecialchars($detail['partisipasi']) ?></td>
                                                    <td><?= htmlspecialchars($detail['tingkat']) ?></td>
                                                    <td class="text-center"><?= $detail['poin_skkm'] ?></td>
                                                    <td class="text-center"><?= htmlspecialchars($detail['tanggal_kegiatan']) ?></td>
                                                    <td class="text-center"><?= htmlspecialchars($detail['nomor_sertifikat_internal'] ?? '-') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <em>Belum ada kegiatan</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-warning mt-4">Data tidak ditemukan untuk NIM: <?= htmlspecialchars($nim) ?></div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById("profileDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    document.addEventListener("click", function(event) {
        const profileContainer = document.querySelector(".profile-container");
        const dropdown = document.getElementById("profileDropdown");
        if (!profileContainer.contains(event.target)) {
            dropdown.style.display = "none";
        }
    });
</script>
</body>
</html>
