<?php
include '../koneksi.php';

// Menangani Create (Tambah Data)
if (isset($_POST['submit'])) {
    $nominal = $_POST['nominal'];
    $keterangan = $_POST['keterangan'];
    $tanggal = $_POST['tanggal'];

    $insertQuery = "INSERT INTO catatan (nominal, keterangan, tanggal) 
                    VALUES ('$nominal', '$keterangan', '$tanggal')";
    if ($conn->query($insertQuery)) {
        echo "Data berhasil ditambahkan.";
        header("Location: catatan.php"); // Arahkan kembali ke halaman utama setelah berhasil menambahkan
        exit();
    } else {
        echo "Gagal menambahkan data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Catatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Tambah Catatan</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="nominal" class="form-label">Nominal</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="nominal" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" required
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" name="submit" class="btn btn-primary">Tambah Catatan</button>
                        <a href="catatan.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-wpNFUeIJENdJtJ6H/0IrmyQX+RZsDdMfo5WPKNBoBo1Or4+CrDOxIxtNkz0nEJG4" crossorigin="anonymous"></script>
</body>

</html>

<?php
// Menutup koneksi database
$conn->close();
?>