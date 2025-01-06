<?php
include '../koneksi.php';

$query = "SELECT * FROM pulsa";
$result = $conn->query($query);

function formatRupiah($angka)
{
    return "Rp " . number_format($angka, 2, ',', '.');
}

function formatTanggal($tanggal)
{
    return date("d-m-Y", strtotime($tanggal));
}

// Variabel untuk menghitung total
$total_beli = 0;
$total_bayar = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pulsa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Data Pulsa</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-4">
                    <a href="tambah_pulsa.php" class="btn btn-success">Tambah Pulsa</a>
                    <a href="../index.php" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
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
                            <?php
                            $no = 1;
                            while ($row = $result->fetch_assoc()):
                                // Menambahkan total beli dan bayar
                                $total_beli += $row['beli'];
                                $total_bayar += $row['bayar'];
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= formatRupiah($row['beli']) ?></td>
                                    <td><?= formatRupiah($row['bayar']) ?></td>
                                    <td><?= formatTanggal($row['tanggal']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $row['status'] == 'Lunas' ? 'success' : 'danger' ?>">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="edit_pulsa.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm me-2">Edit</a>
                                            <a href="delete_pulsa.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <tr>
                                <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                <th><?= formatRupiah($total_beli) ?></th>
                                <th><?= formatRupiah($total_bayar) ?></th>
                                <td colspan="3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>