<?php
session_start();
include '../../koneksi.php';

if (!isset($_GET['id'])) {
    echo "ID kegiatan tidak ditemukan.";
    exit;
}

$id_berkas = $_GET['id'];

// Ambil nama kegiatan berdasarkan ID
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_kegiatan FROM berkas_internal WHERE id_berkas_internal = '$id_berkas'"));
$nama_kegiatan = $data['nama_kegiatan'];

// Ambil semua peserta dari kegiatan tersebut
$query = mysqli_query($conn, "SELECT * FROM berkas_internal WHERE nama_kegiatan = '" . mysqli_real_escape_string($conn, $nama_kegiatan) . "'");
?>
<script>
const poinSKKM = {
  "Bidang Akademik & Ilmiah": {
    "Konfrensi - Penulis Utama": { "P.T.": 3, "Reg.": 4, "Nas.": 5, "Inter.": 6 },
    "Konfrensi - Penyaji": { "P.T.": 3, "Reg.": 4, "Nas.": 5, "Inter.": 6 },
    "Konfrensi - Moderator": { "P.T.": 3, "Reg.": 4, "Nas.": 5, "Inter.": 6 },
    "Penelitian/Karya Tulis - Perorangan": { "P.T.": 4, "Reg.": 5, "Nas.": 5, "Inter.": 6 },
    "Penelitian/Karya Tulis - Kelompok - Ketua": { "P.T.": 0, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
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
    "Lomba - Peserta": { "P.T.": 2, "Reg.": 3, "Nas.": 4, "Inter.": 5 },
    "Festival/Pentas Seni/Musik/Olahraga - Peserta": { "P.T.": 2, "Reg.": 3, "Nas.": 4, "Inter.": 5 },
    "Festival/Pentas Seni/Musik/Olahraga - Juri": { "P.T.": 2, "Reg.": 3, "Nas.": 4, "Inter.": 5 },
    "Festival/Pentas Seni/Musik/Olahraga - Pengisi Acara": { "P.T.": 2, "Reg.": 2, "Nas.": 3, "Inter.": 4 },
    "Festival/Pentas Seni/Musik/Olahraga - Partisipasi": { "P.T.": 1, "Reg.": 1, "Nas.": 1, "Inter.": 2 },
    "Kepanitiaan - Ketua": { "P.T.": 4, "Reg.": 5, "Nas.": 6, "Inter.": 8 },
    "Kepanitiaan - Anggota": { "P.T.": 3, "Reg.": 3, "Nas.": 4, "Inter.": 6 }
  },
  "Bidang Organisasi & Sosial": {
    "BEM/BALMA - Ketua": { "P.T.": 8, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "BEM/BALMA - Wakil": { "P.T.": 6, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "BEM/BALMA - Sekretaris": { "P.T.": 5, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "BEM/BALMA - Bendahara": { "P.T.": 5, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "BEM/BALMA - Koordinator": { "P.T.": 5, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "BEM/BALMA - Anggota": { "P.T.": 4, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "HIMAPRODI/HIMAS/PKM/UKM - Ketua": { "P.T.": 5, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "HIMAPRODI/HIMAS/PKM/UKM - Wakil": { "P.T.": 4, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "HIMAPRODI/HIMAS/PKM/UKM - Sekretaris": { "P.T.": 3, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "Bendahara": { "P.T.": 3, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "Koordinator": { "P.T.": 3, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "Anggota": { "P.T.": 3, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "PM/BAKSOS - Peserta": { "P.T.": 2, "Reg.": 2, "Nas.": 3, "Inter.": 4 },
    "Kepanitiaan - Ketua": { "P.T.": 4, "Reg.": 5, "Nas.": 6, "Inter.": 8 },
    "Kepanitiaan - Anggota": { "P.T.": 3, "Reg.": 3, "Nas.": 4, "Inter.": 6 }
  },
  "Kegiatan Wajib": {
    "Peserta - G.M.T.I": { "P.T.": 2, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "Peserta - Jalan Santai": { "P.T.": 2, "Reg.": 0, "Nas.": 0, "Inter.": 0 },
    "Peserta - Pentas Musik": { "P.T.": 2, "Reg.": 0, "Nas.": 0, "Inter.": 0 }
  }
};

</script>

<!DOCTYPE html>
<html>
<head>
    <title>Proses Verifikasi Kegiatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: black;
            color: white;
        }

        select, input[type='number'] {
            padding: 5px;
        }

        .submit-btn {
            margin: 20px auto;
            display: block;
            padding: 10px 20px;
            background: green;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .submit-btn:hover {
            background: darkgreen;
        }

        .back-btn {
            margin: 20px 50px;
            display: inline-block;
            background-color: #bbb;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            color: black;
            font-weight: bold;
        }

        h2 {
            text-align: center;
        }
    </style>

    <script>
    function updatePoin(index) {
    const kategori = document.getElementById(`kategori_${index}`).value;
    const jenis = document.getElementById(`jenis_${index}`).value;
    const tingkat = document.getElementById(`tingkat_${index}`).value;

    const poin = (poinSKKM[kategori] &&
                  poinSKKM[kategori][jenis] &&
                  poinSKKM[kategori][jenis][tingkat]) || 0;

    document.getElementById(`poin_${index}`).value = poin;
}


    function loadJenis(index) {
        const kategori = document.getElementById(`kategori_${index}`).value;
        const jenisSelect = document.getElementById(`jenis_${index}`);
        jenisSelect.innerHTML = "<option>Loading...</option>";

        fetch("load_jenis.php?kategori=" + encodeURIComponent(kategori))
            .then(res => res.json())
            .then(data => {
                jenisSelect.innerHTML = "";
                data.forEach(jenis => {
                    const opt = document.createElement("option");
                    opt.value = jenis;
                    opt.text = jenis;
                    jenisSelect.appendChild(opt);
                });
                updatePoin(index);
            });
    }
    function loadJenisGlobal() {
    const kategori = document.getElementById("semua_kategori").value;
    const jenisSelect = document.getElementById("semua_jenis");
    jenisSelect.innerHTML = "<option>Loading...</option>";

    fetch("load_jenis.php?kategori=" + encodeURIComponent(kategori))
        .then(res => res.json())
        .then(data => {
            jenisSelect.innerHTML = "";
            data.forEach(jenis => {
                const opt = document.createElement("option");
                opt.value = jenis;
                opt.text = jenis;
                jenisSelect.appendChild(opt);
            });
        });
}

function terapkanKeSemua() {
    const kategori = document.getElementById("semua_kategori").value;
    const jenis = document.getElementById("semua_jenis").value;
    const tingkat = document.getElementById("semua_tingkat").value;

    const total = document.querySelectorAll("select[id^='kategori_']").length;

    for (let i = 0; i < total; i++) {
        document.getElementById(`kategori_${i}`).value = kategori;
        loadJenis(i); // ini akan muat jenis dan panggil updatePoin otomatis setelah async
        setTimeout(() => {
            document.getElementById(`jenis_${i}`).value = jenis;
            document.getElementById(`tingkat_${i}`).value = tingkat;
            updatePoin(i);
        }, 300); // sedikit delay agar opsi jenis tersedia
    }
}
if (poin === 0) {
    console.warn(`Poin tidak ditemukan untuk kombinasi: ${kategori} > ${jenis} > ${tingkat}`);
}


    </script>
</head>
<body>
    <?php include '../navbar.php'; ?>
    <a href="javascript:history.back()" class="back-btn">← Kembali</a>
    <h2>Verifikasi Kegiatan: <?php echo htmlspecialchars($nama_kegiatan); ?></h2>

    <form method="post" action="proses_verifikasi_lengkap.php">
            <div style="width: 95%; margin: 20px auto; padding: 10px; border: 1px solid #ccc;">
        <strong>Isi Semua Baris Sekaligus:</strong><br><br>

        <label>Kategori Kegiatan:</label>
        <select id="semua_kategori" onchange="loadJenisGlobal()" style="margin-right: 20px;">
            <option value="">--Pilih--</option>
            <?php
            $kategori_q = mysqli_query($conn, "SELECT * FROM tabel_ketentuan_skkm");
            while ($k = mysqli_fetch_assoc($kategori_q)) {
                echo "<option value='{$k['kategori_kegiatan']}'>{$k['kategori_kegiatan']}</option>";
            }
            ?>
        </select>

        <label>Jenis Kegiatan:</label>
        <select id="semua_jenis" style="margin-right: 20px;">
            <option value="">--Pilih Jenis--</option>
        </select>

        <label>Tingkat:</label>
        <select id="semua_tingkat" style="margin-right: 20px;">
            <option value="">--Pilih--</option>
            <?php
            $tingkat_q = mysqli_query($conn, "SELECT * FROM tingkat");
            while ($t = mysqli_fetch_assoc($tingkat_q)) {
                echo "<option value='{$t['nama_tingkat']}'>{$t['nama_tingkat']}</option>";
            }
            ?>
        </select>

        <button type="button" onclick="terapkanKeSemua()">Terapkan ke Semua</button>
    </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Partisipasi</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Kategori</th>
                    <th>Jenis</th>
                    <th>Tingkat</th>
                    <th>Poin</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                while ($row = mysqli_fetch_assoc($query)) {
                    echo "<tr>";
                    echo "<td>" . ($i+1) . "</td>";
                    echo "<td>{$row['nim']}</td>";
                    echo "<td>{$row['partisipasi']}</td>";
                    echo "<td>{$row['tanggal_kegiatan']}</td>";
                    echo "<td>
                            <input type='hidden' name='id[]' value='{$row['id_berkas_internal']}'>
                            <select name='kategori_kegiatan[]' id='kategori_{$i}' onchange='loadJenis({$i})' required>
                                <option value=''>--Pilih--</option>";
                                $kategori_q = mysqli_query($conn, "SELECT * FROM tabel_ketentuan_skkm");
                                while ($k = mysqli_fetch_assoc($kategori_q)) {
                                    echo "<option value='{$k['kategori_kegiatan']}'>{$k['kategori_kegiatan']}</option>";
                                }
                    echo    "</select>
                          </td>";
                    echo "<td>
                            <select name='jenis_kegiatan[]' id='jenis_{$i}' onchange='updatePoin({$i})' required>
                                <option value=''>--Pilih Jenis--</option>
                            </select>
                          </td>";
                    echo "<td>
                            <select name='tingkat[]' id='tingkat_{$i}' onchange='updatePoin({$i})' required>";
                            $tingkat_q = mysqli_query($conn, "SELECT * FROM tingkat");
                            echo "<option value=''>--Pilih Tingkat--</option>";
                            while ($t = mysqli_fetch_assoc($tingkat_q)) {
                                echo "<option value='{$t['nama_tingkat']}'>{$t['nama_tingkat']}</option>";
                            }
                    echo    "</select>
                          </td>";
                    echo "<td>
                            <input type='number' name='poin_skkm[]' id='poin_{$i}' readonly>
                          </td>";
                    echo "</tr>";
                    $i++;
                }
                ?>
            </tbody>
        </table>

        <button type="submit" class="submit-btn">Simpan Verifikasi</button>
    </form>
</body>
</html>
