<?php
include '../koneksi.php';

function deleteRecord($id, $conn)
{
    $deleteQuery = "DELETE FROM catatan WHERE id = '$id'";
    if ($conn->query($deleteQuery)) {
        echo "<script>alert('Catatan berhasil dihapus'); window.location='catatan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus catatan'); window.location='catatan.php';</script>";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    deleteRecord($id, $conn);
}

function getRecords($conn)
{
    $catatanQuery = "SELECT * FROM catatan ORDER BY id DESC";
    $catatanResult = $conn->query($catatanQuery);
    return $catatanResult;
}


function calculateTotalNominal($conn)
{
    $totalQuery = "SELECT SUM(nominal) AS total_nominal FROM catatan";
    $totalResult = $conn->query($totalQuery);
    $row = $totalResult->fetch_assoc();
    return $row['total_nominal'];
}

$catatanResult = getRecords($conn);
$totalNominal = calculateTotalNominal($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Catatan</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-4">
                    <a href="add_catatan.php" class="btn btn-success">Tambah Catatan</a>
                    <a href="../index.php" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 20%;">Nominal</th>
                                <th>Keterangan</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if ($catatanResult->num_rows > 0) {
                                while ($row = $catatanResult->fetch_assoc()) {
                                    // Mengubah format tanggal
                                    $tanggal = date("d-m-Y", strtotime($row['tanggal']));
                                    echo "<tr>
                                        <td>{$no}</td>
                                        <td>Rp " . number_format($row['nominal'], 2, ',', '.') . "</td>
                                        <td>{$row['keterangan']}</td>
                                        <td>{$tanggal}</td>
                                        <td>
                                            <div class='btn-group' role='group'>
                                                <a href='edit_catatan.php?edit={$row['id']}' class='btn btn-warning btn-sm me-2'>Edit</a>
                                                <a href='?delete={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data?\")'>Hapus</a>
                                            </div>
                                        </td>
                                    </tr>";
                                    $no++;
                                }
                                // Menambahkan total nominal di bawah kolom nominal
                                echo "<tr>
                                    <td colspan='1' class='text-end'><strong>Total:</strong></td>
                                    <td><strong>Rp " . number_format($totalNominal, 2, ',', '.') . "</strong></td>
                                    <td colspan='3'></td>
                                </tr>";
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Tidak ada data tersedia</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-wEmeIV1mKuiNp12KWhlzzFWs20XEVwGhxCZpHCzJyZJNqefcf8mI4Da1l6EsmNBk"
        crossorigin="anonymous"></script>
</body>

</html>