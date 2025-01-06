<?php
include '../koneksi.php';

function deleteRecord($id, $conn)
{
    $deleteQuery = "DELETE FROM kegiatan WHERE id = '$id'";
    if ($conn->query($deleteQuery)) {
        echo "<script>alert('Kegiatan berhasil dihapus'); window.location='kegiatan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus kegiatan'); window.location='kegiatan.php';</script>";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    deleteRecord($id, $conn);
}

function getRecords($conn)
{
    $kegiatanQuery = "SELECT * FROM kegiatan";
    $kegiatanResult = $conn->query($kegiatanQuery);
    return $kegiatanResult;
}

$kegiatanResult = getRecords($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kegiatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Kegiatan</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-4">
                    <a href="add_kegiatan.php" class="btn btn-success">Tambah Kegiatan</a>
                    <a href="../index.php" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama Kegiatan</th>
                                <th style="width: 20%;">Jam</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if ($kegiatanResult->num_rows > 0) {
                                while ($row = $kegiatanResult->fetch_assoc()) {
                                    // Menambahkan " WIB" ke jam
                                    $jam = $row['jam'] . " WIB";
                                    echo "<tr>
                                        <td>{$no}</td>
                                        <td>{$row['nama_kegiatan']}</td>
                                        <td>{$jam}</td>
                                        <td>
                                            <div class='btn-group' role='group'>
                                                <a href='edit.php?id={$row['id']}' class='btn btn-warning btn-sm me-2'>Edit</a>
                                                <a href='?delete={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data?\")'>Hapus</a>
                                            </div>
                                        </td>
                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>Tidak ada data tersedia</td></tr>";
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