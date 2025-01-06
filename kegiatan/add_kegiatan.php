<?php
include '../koneksi.php';

// Handle Create operation
if (isset($_POST['create'])) {
    $namaKegiatan = $_POST['nama_kegiatan'];
    $jam = $_POST['jam'];

    // Insert the new kegiatan into the database
    $insertQuery = "INSERT INTO kegiatan (nama_kegiatan, jam) VALUES ('$namaKegiatan', '$jam')";
    if ($conn->query($insertQuery) === TRUE) {
        // Redirect back to the main page (index.php) after successful creation
        header('Location: ../kegiatan/kegiatan.php');
        exit();
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
    <title>Tambah Kegiatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Tambah Kegiatan</h3>
            </div>
            <div class="card-body">
                <!-- Form to add new kegiatan -->
                <form method="POST">
                    <div class="mb-3">
                        <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                        <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" required>
                    </div>
                    <div class="mb-3">
                        <label for="jam" class="form-label">Jam (WIB)</label>
                        <div class="input-group">
                            <input type="time" class="form-control" id="jam" name="jam" required>
                            <span class="input-group-text">WIB</span>
                        </div>
                    </div>
                    <button type="submit" name="create" class="btn btn-primary">Simpan</button>
                    <a href="../kegiatan/kegiatan.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-wpNFUeIJENdJtJ6H/0IrmyQX+RZsDdMfo5WPKNBoBo1Or4+CrDOxIxtNkz0nEJG4" crossorigin="anonymous"></script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>