<?php
include '../koneksi.php';

// Query untuk mendapatkan data Pulsa
$queryPulsa = "SELECT * FROM pulsa WHERE status = 'Hutang' ORDER BY tanggal";
$resultPulsa = $conn->query($queryPulsa);

// Query untuk mendapatkan data Dokumen
$queryDokumen = "SELECT * FROM dokumen WHERE status = 'Hutang' ORDER BY tanggal";
$resultDokumen = $conn->query($queryDokumen);

// Fungsi untuk format tanggal
function formatTanggal($tanggal)
{
    return date('d-m-Y', strtotime($tanggal));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Hutang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Data Hutang</h2>

        <!-- Data Pulsa Section -->
        <h4 class="mt-4">Pulsa</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Beli</th>
                        <th>Bayar</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($row = $resultPulsa->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td>Rp<?= number_format($row['beli'], 2) ?></td>
                            <td>Rp<?= number_format($row['bayar'], 2) ?></td>
                            <td><?= formatTanggal($row['tanggal']) ?></td>
                            <td>
                                <form action="../hutang/update_status.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="type" value="pulsa">
                                    <button type="submit" class="btn btn-success btn-sm w-100">Lunasi</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Data Dokumen Section -->
        <h4 class="mt-4">Dokumen</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Bayar</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($row = $resultDokumen->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= formatTanggal($row['tanggal']) ?></td>
                            <td>Rp<?= number_format($row['bayar'], 2) ?></td>
                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                            <td>
                                <form action="../hutang/update_status.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="type" value="dokumen">
                                    <button type="submit" class="btn btn-success btn-sm w-100">Lunasi</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Back Button -->
        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-secondary w-100">Kembali</a>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>