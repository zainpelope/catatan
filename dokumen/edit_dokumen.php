<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    $bayar = $_POST['bayar'];
    $keterangan = $_POST['keterangan'];
    $status = $_POST['status'];

    // Update data dokumen
    $query = "UPDATE dokumen SET nama = '$nama', tanggal = '$tanggal', bayar = '$bayar', keterangan = '$keterangan', status = '$status' WHERE id = '$id'";
    if ($conn->query($query)) {
        header('Location: ../dokumen/dokumen.php'); // Redirect kembali ke halaman dokumen
    } else {
        echo "Error: " . $conn->error;
    }
}

// Ambil data dokumen berdasarkan ID
$id = $_GET['id'];
$query = "SELECT * FROM dokumen WHERE id = '$id'";
$result = $conn->query($query);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dokumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Edit Dokumen</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?= $row['nama'] ?>" placeholder="Masukkan nama dokumen" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $row['tanggal'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="bayar" class="form-label">Bayar</label>
                                <input type="number" class="form-control" id="bayar" name="bayar" value="<?= $row['bayar'] ?>" placeholder="Masukkan jumlah bayar" required>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Masukkan keterangan dokumen" required><?= $row['keterangan'] ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Lunas" <?= $row['status'] == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                                    <option value="Hutang" <?= $row['status'] == 'Hutang' ? 'selected' : '' ?>>Hutang</option>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="../dokumen/dokumen.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>