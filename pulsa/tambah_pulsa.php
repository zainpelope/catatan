<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $beli = $_POST['beli'];
    $bayar = $_POST['bayar'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    $query = "INSERT INTO pulsa (nama, beli, bayar, tanggal, status) VALUES ('$nama', '$beli', '$bayar', '$tanggal', '$status')";
    if ($conn->query($query)) {
        header('Location: pulsa.php');
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pulsa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Tambah Pulsa</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="mb-3">
                <label for="beli" class="form-label">Beli</label>
                <input type="number" class="form-control" id="beli" name="beli" required>
            </div>
            <div class="mb-3">
                <label for="bayar" class="form-label">Bayar</label>
                <input type="number" class="form-control" id="bayar" name="bayar" required>
            </div>
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" required>
                    <option value="Lunas">Lunas</option>
                    <option value="Hutang">Hutang</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="pulsa.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
<script>
    // Mendapatkan elemen input tanggal
    const inputTanggal = document.getElementById('tanggal');

    // Mendapatkan tanggal sekarang dalam format YYYY-MM-DD
    const today = new Date().toISOString().split('T')[0];

    // Mengatur nilai input tanggal menjadi tanggal sekarang
    inputTanggal.value = today;
</script>

</html>