<?php
include '../koneksi.php';

$query = "SELECT * FROM pulsa WHERE status = 'Lunas'";
$result_pulsa = $conn->query($query);

$query_dokumen = "SELECT * FROM dokumen WHERE status = 'Lunas'";
$result_dokumen = $conn->query($query_dokumen);

$total_beli_pulsa = 0;
$total_bayar_pulsa = 0;
while ($row = $result_pulsa->fetch_assoc()) {
    $total_beli_pulsa += $row['beli'];
    $total_bayar_pulsa += $row['bayar'];
}

$total_bayar_dokumen = 0;
while ($row = $result_dokumen->fetch_assoc()) {
    $total_bayar_dokumen += $row['bayar'];
}

$selisih_bayar_pulsa = $total_bayar_pulsa - $total_beli_pulsa;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perhitungan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Hasil Perhitungan Pulsa dan Dokumen</h2>

        <h4>Hasil Pulsa</h4>
        <p>Total Beli Pulsa: Rp<?= number_format($total_beli_pulsa, 2) ?></p>
        <p>Total Bayar Pulsa: Rp<?= number_format($total_bayar_pulsa, 2) ?></p>
        <p>Selisih Bayar Pulsa: Rp<?= number_format($selisih_bayar_pulsa, 2) ?></p>

        <h4>Hasil Dokumen</h4>
        <p>Total Bayar Dokumen: Rp<?= number_format($total_bayar_dokumen, 2) ?></p>

        <a href="../index.php" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</body>

</html>