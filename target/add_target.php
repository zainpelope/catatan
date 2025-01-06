<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_target = $_POST['nama_target'];
    $target_selesai = $_POST['target_selesai'];
    $status = $_POST['status'];

    $insertQuery = "INSERT INTO target (nama_target, target_selesai, status) VALUES ('$nama_target', '$target_selesai', '$status')";
    if ($conn->query($insertQuery)) {
        header("Location: target.php");
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
    <title>Add Target</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Tambah Target</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nama_target" class="form-label">Nama Target</label>
                        <input type="text" class="form-control" id="nama_target" name="nama_target" required>
                    </div>
                    <div class="mb-3">
                        <label for="target_selesai" class="form-label">Target Selesai</label>
                        <input type="date" class="form-control" id="target_selesai" name="target_selesai" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Selesai">Selesai</option>
                            <option value="Belum Selesai">Belum Selesai</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="target.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>