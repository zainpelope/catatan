<?php
include '../koneksi.php';

// Query untuk data pulsa dan dokumen
$query_pulsa = "SELECT * FROM pulsa WHERE status = 'Lunas'";
$result_pulsa = $conn->query($query_pulsa);

$query_dokumen = "SELECT * FROM dokumen WHERE status = 'Lunas'";
$result_dokumen = $conn->query($query_dokumen);

// Perhitungan total
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
        <h2 class="text-center mb-4">Hasil Pulsa dan Dokumen</h2>

        <!-- Tabel Pulsa -->
        <div class="table-responsive">
            <h4>Hasil Pulsa</h4>
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Keterangan</th>
                        <th>Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Beli Pulsa</td>
                        <td><?= number_format($total_beli_pulsa, 2) ?></td>
                    </tr>
                    <tr>
                        <td>Total Bayar Pulsa</td>
                        <td><?= number_format($total_bayar_pulsa, 2) ?></td>
                    </tr>
                    <tr>
                        <td>Total Hasil Pulsa</td>
                        <td><?= number_format($selisih_bayar_pulsa, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tabel Dokumen -->
        <div class="table-responsive mt-4">
            <h4>Hasil Dokumen</h4>
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Keterangan</th>
                        <th>Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Hasil Dokumen</td>
                        <td><?= number_format($total_bayar_dokumen, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tombol Kembali -->
        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-secondary btn-lg w-100">Kembali</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>