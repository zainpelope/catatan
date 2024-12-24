<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $beli = $_POST['beli'];
    $bayar = $_POST['bayar'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    // Update data pulsa
    $query = "UPDATE pulsa SET nama = '$nama', beli = '$beli', bayar = '$bayar', tanggal = '$tanggal', status = '$status' WHERE id = '$id'";
    if ($conn->query($query)) {
        header('Location: pulsa.php');  // Redirect kembali ke halaman pulsa
    } else {
        echo "Error: " . $conn->error;
    }
}

// Ambil data pulsa berdasarkan ID
$id = $_GET['id'];
$query = "SELECT * FROM pulsa WHERE id = '$id'";
$result = $conn->query($query);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pulsa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Pulsa</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?= $row['nama'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="beli" class="form-label">Beli</label>
                <input type="number" class="form-control" id="beli" name="beli" value="<?= $row['beli'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="bayar" class="form-label">Bayar</label>
                <input type="number" class="form-control" id="bayar" name="bayar" value="<?= $row['bayar'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $row['tanggal'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" required>
                    <option value="Lunas" <?= $row['status'] == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                    <option value="Hutang" <?= $row['status'] == 'Hutang' ? 'selected' : '' ?>>Hutang</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="pulsa.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>

</html>