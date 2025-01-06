<?php
include '../koneksi.php';

// Fungsi untuk menghapus data perlengkapan
function deletePerlengkapan($id, $conn)
{
    $deleteQuery = "DELETE FROM perlengkapan WHERE id = '$id'";
    if ($conn->query($deleteQuery)) {
        echo "<script>alert('Perlengkapan berhasil dihapus'); window.location='perlengkapan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus perlengkapan'); window.location='perlengkapan.php';</script>";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    deletePerlengkapan($id, $conn);
}

// Query untuk mendapatkan data perlengkapan
function getPerlengkapan($conn)
{
    $query = "SELECT * FROM perlengkapan";
    return $conn->query($query);
}

$perlengkapanResult = getPerlengkapan($conn);

// Variabel untuk menghitung total harga perlengkapan
$totalHarga = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perlengkapan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Perlengkapan</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-4">
                    <a href="../perlengkapan/tambah.php" class="btn btn-success">Tambah Perlengkapan</a>
                    <a href="../dokumen/dokumen.php" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama</th>
                                <th style="width: 15%;">Harga</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if ($perlengkapanResult->num_rows > 0) {
                                while ($row = $perlengkapanResult->fetch_assoc()) {
                                    // Format harga dengan format Rupiah
                                    $formattedHarga = "Rp " . number_format($row['harga'], 2, ',', '.');
                                    $totalHarga += $row['harga']; // Menambahkan harga ke total
                                    $tanggalBuat = date("d-m-Y", strtotime($row['tanggal_buat']));

                                    echo "<tr>
                                        <td>{$no}</td>
                                        <td>{$row['nama']}</td>
                                        <td>{$formattedHarga}</td>
                                        <td>{$tanggalBuat}</td>
                                        <td>
                                            <div class='btn-group' role='group'>
                                                <a href='../perlengkapan/edit.php?id={$row['id']}' class='btn btn-warning btn-sm me-2'>Edit</a>
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
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="2">Total Harga</th>
                                <th><?= 'Rp ' . number_format($totalHarga, 2, ',', '.') ?></th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
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
// Tutup koneksi database
$conn->close();
?>