<?php
include '../koneksi.php';

$queryPulsa = "SELECT * FROM pulsa WHERE status = 'Hutang' ORDER BY tanggal DESC";
$resultPulsa = $conn->query($queryPulsa);

$queryDokumen = "SELECT * FROM dokumen WHERE status = 'Hutang' ORDER BY tanggal DESC";
$resultDokumen = $conn->query($queryDokumen);

$totalBeliPulsa = 0;
$totalBayarPulsa = 0;
$totalBayarDokumen = 0;

function formatTanggal($tanggal)
{
    return date('d-m-Y', strtotime($tanggal));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Hutang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
        }

        h2,
        h4 {
            font-weight: 600;
        }

        .card {
            border-radius: 1rem;
        }

        .btn-sm {
            font-size: 0.8rem;
        }

        @media (max-width: 576px) {
            .btn-sm {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h2><i class="bi bi-cash-coin"></i> Data Hutang</h2>
            </div>
            <div class="card-body">

                <!-- Data Pulsa -->
                <h4 class="mt-3 text-primary"><i class="bi bi-phone"></i> Pulsa</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-striped align-middle">
                        <thead class="table-primary text-center">
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
                            while ($row = $resultPulsa->fetch_assoc()) {
                                $totalBeliPulsa += $row['beli'];
                                $totalBayarPulsa += $row['bayar'];
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td>Rp<?= number_format($row['beli'], 2, ',', '.') ?></td>
                                    <td>Rp<?= number_format($row['bayar'], 2, ',', '.') ?></td>
                                    <td><?= formatTanggal($row['tanggal']) ?></td>
                                    <td class="text-center">
                                        <form action="../hutang/update_status.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="type" value="pulsa">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="bi bi-check-circle"></i> Lunasi
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="2" class="text-end">Total</td>
                                <td>Rp<?= number_format($totalBeliPulsa, 2, ',', '.') ?></td>
                                <td>Rp<?= number_format($totalBayarPulsa, 2, ',', '.') ?></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Data Dokumen -->
                <h4 class="text-primary"><i class="bi bi-file-earmark-text"></i> Dokumen</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-striped align-middle">
                        <thead class="table-primary text-center">
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
                            while ($row = $resultDokumen->fetch_assoc()) {
                                $totalBayarDokumen += $row['bayar'];
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= formatTanggal($row['tanggal']) ?></td>
                                    <td>Rp<?= number_format($row['bayar'], 2, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                    <td class="text-center">
                                        <form action="../hutang/update_status.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="type" value="dokumen">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="bi bi-check-circle"></i> Lunasi
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Total</td>
                                <td>Rp<?= number_format($totalBayarDokumen, 2, ',', '.') ?></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Tombol Kembali -->
                <div class="text-center">
                    <a href="../index.php" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-left-circle"></i> Kembali
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>