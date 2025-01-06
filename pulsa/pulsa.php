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
    <style>
        body {
            background-color: #f8f9fa;
        }

        .table-container {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h2 {
            font-weight: bold;
            color: #343a40;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-warning,
        .btn-danger {
            transition: transform 0.2s ease-in-out;
        }

        .btn-warning:hover,
        .btn-danger:hover {
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="table-container">
            <h2 class="text-center">Data Pulsa</h2>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="tambah_pulsa.php" class="btn btn-primary">Add Pulsa</a>
                <a href="../index.php" class="btn btn-secondary">Kembali</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Beli</th>
                            <th>Bayar</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1; // Inisialisasi nomor urut
                        while ($row = $result->fetch_assoc()):
                            // Menambahkan total beli dan bayar
                            $total_beli += $row['beli'];
                            $total_bayar += $row['bayar'];
                        ?>
                            <tr>
                                <td><?= $no++ ?></td> <!-- Menampilkan nomor urut -->
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= formatRupiah($row['beli']) ?></td> <!-- Format Rupiah -->
                                <td><?= formatRupiah($row['bayar']) ?></td> <!-- Format Rupiah -->
                                <td><?= formatTanggal($row['tanggal']) ?></td> <!-- Format Tanggal -->
                                <td>
                                    <span class="badge bg-<?= $row['status'] == 'Lunas' ? 'success' : 'danger' ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit_pulsa.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_pulsa.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="2">Total</th>
                            <th><?= formatRupiah($total_beli) ?></th>
                            <th><?= formatRupiah($total_bayar) ?></th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>