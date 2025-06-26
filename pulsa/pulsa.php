<?php
include '../koneksi.php';

$query = "SELECT * FROM pulsa ORDER BY id DESC";
$result = $conn->query($query);

function formatRupiah($angka)
{
    return "Rp " . number_format($angka, 2, ',', '.');
}

function formatTanggal($tanggal)
{
    return date("d-m-Y", strtotime($tanggal));
}

$total_beli = 0;
$total_bayar = 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Pulsa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
        }

        .card {
            border-radius: 1rem;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-sm {
            font-size: 0.8rem;
        }

        @media (max-width: 576px) {
            .btn-group {
                flex-direction: column;
                gap: 0.3rem;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3><i class="bi bi-phone"></i> Data Pulsa</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between mb-4 gap-2">
                    <a href="tambah_pulsa.php" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah Pulsa
                    </a>
                    <a href="../index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Kembali
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama</th>
                                <th>Beli</th>
                                <th>Bayar</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                $total_beli += $row['beli'];
                                $total_bayar += $row['bayar'];
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= formatRupiah($row['beli']) ?></td>
                                    <td><?= formatRupiah($row['bayar']) ?></td>
                                    <td><?= formatTanggal($row['tanggal']) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $row['status'] === 'Lunas' ? 'success' : 'danger' ?>">
                                            <i class="bi bi-<?= $row['status'] === 'Lunas' ? 'check-circle' : 'x-circle' ?>"></i>
                                            <?= htmlspecialchars($row['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group d-flex justify-content-center" role="group">
                                            <a href="edit_pulsa.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm me-1">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <a href="delete_pulsa.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <tr class="table-light fw-bold">
                                <td colspan="2" class="text-end">Total</td>
                                <td><?= formatRupiah($total_beli) ?></td>
                                <td><?= formatRupiah($total_bayar) ?></td>
                                <td colspan="3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>