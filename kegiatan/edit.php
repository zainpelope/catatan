<?php
// Tampilkan error untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database
include('../koneksi.php'); // Sesuaikan dengan file koneksi Anda

// Periksa apakah parameter 'id' ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk mengambil data kegiatan berdasarkan ID
    $query = "SELECT * FROM kegiatan WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Periksa apakah data ditemukan
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        echo "Data tidak ditemukan.";
        exit;
    }
} else {
    echo "ID tidak disediakan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kegiatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Edit Kegiatan
            </div>
            <div class="card-body">
                <form action="update.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="mb-3">
                        <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                        <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan"
                            value="<?php echo htmlspecialchars($data['nama_kegiatan'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="jam" class="form-label">Jam</label>
                        <input type="text" class="form-control" id="jam" name="jam"
                            value="<?php echo htmlspecialchars($data['jam'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>