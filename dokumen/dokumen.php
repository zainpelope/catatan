<?php
include '../koneksi.php';

$query = "SELECT * FROM dokumen";
$result = $conn->query($query);

// Variabel untuk menghitung total bayar
$total_bayar = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dokumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .table-container {
            overflow-x: auto;
        }

        h2 {
            color: #343a40;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Data Dokumen</h2>
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="tambah_dokumen.php" class="btn btn-primary">Add Dokumen</a>
            <a href="../perlengkapan/perlengkapan.php" class="btn btn-success">Biaya Perlengkapan Dokumen</a>
            <a href="../index.php" class="btn btn-secondary ms-auto">Kembali</a>
        </div>


        <div class="table-container">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Bayar</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()):
                        // Menambahkan total bayar
                        $total_bayar += $row['bayar'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td><?= 'Rp ' . number_format($row['bayar'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                            <td>
                                <span class="badge bg-<?= $row['status'] == 'Lunas' ? 'success' : 'danger' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_dokumen.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_dokumen.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="2">Total</th>
                        <th><?= 'Rp ' . number_format($total_bayar, 2, ',', '.') ?></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>