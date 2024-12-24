<?php
include '../koneksi.php';

$queryPulsa = "SELECT * FROM pulsa WHERE status = 'Hutang'";
$resultPulsa = $conn->query($queryPulsa);

$queryDokumen = "SELECT * FROM dokumen WHERE status = 'Hutang'";
$resultDokumen = $conn->query($queryDokumen);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hutang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Data Hutang</h2>
        <h4>Pulsa</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Beli</th>
                    <th>Bayar</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultPulsa->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['nama'] ?></td>
                        <td>Rp<?= number_format($row['beli'], 2) ?></td>
                        <td>Rp<?= number_format($row['bayar'], 2) ?></td>
                        <td><?= $row['tanggal'] ?></td>
                        <td>
                            <form action="update_status.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="type" value="pulsa">
                                <button type="submit" class="btn btn-success">Lunasi</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h4>Dokumen</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Bayar</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultDokumen->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['tanggal'] ?></td>
                        <td>Rp<?= number_format($row['bayar'], 2) ?></td>
                        <td><?= $row['keterangan'] ?></td>
                        <td>
                            <form action="update_status.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="type" value="dokumen">
                                <button type="submit" class="btn btn-success">Lunasi</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="../index.php" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</body>

</html>