<?php
include '../koneksi.php';

$query = "SELECT * FROM pulsa";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pulsa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Data Pulsa</h2>
        <a href="tambah_pulsa.php" class="btn btn-primary mb-3">Add Pulsa</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Beli</th>
                    <th>Bayar</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['nama'] ?></td>
                        <td><?= number_format($row['beli'], 2) ?></td>
                        <td><?= number_format($row['bayar'], 2) ?></td>
                        <td><?= $row['tanggal'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>
                            <a href="edit_pulsa.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_pulsa.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="../index.php" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</body>

</html>