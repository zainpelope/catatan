<?php
include '../koneksi.php';

$id = $_GET['id'];
$targetQuery = "SELECT * FROM target WHERE id = $id";
$targetResult = $conn->query($targetQuery);
$targetData = $targetResult->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_target = $_POST['nama_target'];
    $target_selesai = $_POST['target_selesai'];
    $status = $_POST['status'];

    $updateQuery = "UPDATE target SET nama_target='$nama_target', target_selesai='$target_selesai', status='$status' WHERE id=$id";
    if ($conn->query($updateQuery)) {
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
    <title>Edit Target</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Edit Target</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nama_target" class="form-label">Nama Target</label>
                        <input type="text" class="form-control" id="nama_target" name="nama_target" value="<?= $targetData['nama_target'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="target_selesai" class="form-label">Target Selesai</label>
                        <input type="date" class="form-control" id="target_selesai" name="target_selesai" value="<?= $targetData['target_selesai'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Selesai" <?= $targetData['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="Belum Selesai" <?= $targetData['status'] == 'Belum Selesai' ? 'selected' : '' ?>>Belum Selesai</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="target.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>