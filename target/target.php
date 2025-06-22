<?php
// Include the database connection
include '../koneksi.php';

// Function to delete a target
function deleteTarget($id, $conn)
{
    $deleteQuery = "DELETE FROM target WHERE id = '$id'";
    if ($conn->query($deleteQuery)) {
        echo "<script>alert('Target berhasil dihapus'); window.location='target.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus target'); window.location='target.php';</script>";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    deleteTarget($id, $conn);
}

// Query to get Target data
function getTargets($conn)
{
    $targetQuery = "SELECT * FROM target ORDER BY id DESC";
    return $conn->query($targetQuery);
}

$targetResult = getTargets($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Target</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Target</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-4">
                    <a href="add_target.php" class="btn btn-success">Tambah Target</a>
                    <a href="../index.php" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama Target</th>
                                <th style="width: 15%;">Target Selesai</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if ($targetResult->num_rows > 0) {
                                while ($row = $targetResult->fetch_assoc()) {
                                    // Format tanggal menjadi d-m-Y
                                    $tanggalSelesai = date("d-m-Y", strtotime($row['target_selesai']));

                                    // Tampilkan teks "Selesai" (Hijau) atau "Belum Selesai" (Merah)
                                    // Tampilkan teks "Selesai" (Hijau) atau "Belum Selesai" (Merah)
                                    if (strtolower($row['status']) === 'selesai') {
                                        $statusText = "<span class='text-success'>Selesai</span>";
                                    } else {
                                        $statusText = "<span class='text-danger'>Belum Selesai</span>";
                                    }


                                    echo "<tr>
                                        <td>{$no}</td>
                                        <td>{$row['nama_target']}</td>
                                        <td>{$tanggalSelesai}</td>
                                        <td>{$statusText}</td>
                                        <td>
                                            <div class='btn-group' role='group'>
                                                <a href='edit_target.php?id={$row['id']}' class='btn btn-warning btn-sm me-2'>Edit</a>
                                                <a href='?delete={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data?\")'>Hapus</a>
                                            </div>
                                        </td>
                                    </tr>";
                                    $no++;
                                }
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

<?php
// Close the database connection
$conn->close();
?>