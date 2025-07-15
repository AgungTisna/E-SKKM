<?php
include "../koneksi.php";

// Ambil data ormawa
$sql = "SELECT o.nama_ormawa, u.username, u.password 
        FROM user u
        JOIN user_detail_ormawa o ON u.id_user = o.id_user";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export PDF - Data Ormawa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .no-print {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h2>Data Ormawa - E-SKKM</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Ormawa</th>
                <th>Username</th>
                <th>Password</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['nama_ormawa']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['password']}</td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='4'>Tidak ada data Ormawa</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="no-print">
        <button onclick="window.print()">🖨 Cetak / Simpan PDF</button>
        <button onclick="window.close()">❌ Tutup</button>
    </div>

</body>
</html>
