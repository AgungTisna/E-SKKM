<?php
require '../../../vendor/autoload.php';
include '../../../koneksi.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$previewData = [];

if (isset($_POST['preview']) && isset($_FILES['excel_file'])) {
    $reader = new Xlsx();
    $spreadsheet = $reader->load($_FILES['excel_file']['tmp_name']);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
    $headers = $rows[0];
    $previewData = array_slice($rows, 1);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2>Konfirmasi Data dari Excel</h2>

    <?php if (count($previewData) > 0): ?>
        <form action="proses_import.php" method="POST">
            <table class="table table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <?php foreach ($headers as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($previewData as $row): ?>
                    <tr>
                        <?php foreach ($row as $i => $cell): ?>
                            <td><?= is_object($cell) ? $cell->format('Y-m-d') : htmlspecialchars($cell) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <input type="hidden" name="data" value='<?= base64_encode(serialize($previewData)) ?>'>
            <button type="submit" name="import" class="btn btn-success">Simpan ke Database</button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">File kosong atau tidak sesuai format.</div>
    <?php endif; ?>
</div>
</body>
</html>
