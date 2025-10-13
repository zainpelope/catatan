<?php
// Ensure the timezone is set correctly before any date functions are used
date_default_timezone_set('Asia/Jakarta');

include 'koneksi.php';

// Initialize variables to prevent undefined variable notices
$tanggal = '';
$jumlah = '';
$keterangan = '';
$id = 0; // Default ID to 0 or null

// Check if an ID is provided for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the existing data for the given ID using prepared statement for security
    $stmt = $conn->prepare("SELECT tanggal, jumlah, keterangan FROM pengeluaran WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tanggal = $row['tanggal'];
        $jumlah = $row['jumlah'];
        $keterangan = $row['keterangan'];
    } else {
        // Redirect if ID is not found
        header("Location: pengeluaran.php");
        exit();
    }
    $stmt->close();
} else {
    // Redirect if no ID is provided
    header("Location: pengeluaran.php");
    exit();
}

// Handle form submission for updating data
if (isset($_POST['submit'])) {
    // Re-get ID from POST to ensure it's available after initial GET request
    $id = $_POST['id'];
    $new_tanggal = $_POST['tanggal'];
    $new_jumlah = $_POST['jumlah'];
    $new_keterangan = $_POST['keterangan'];

    // Update the database record using prepared statement for security
    $stmt = $conn->prepare("UPDATE pengeluaran SET tanggal = ?, jumlah = ?, keterangan = ? WHERE id = ?");
    $stmt->bind_param("sisi", $new_tanggal, $new_jumlah, $new_keterangan, $id);

    if ($stmt->execute()) {
        header("Location: pengeluaran.php?status=updated");
        exit();
    } else {
        // Handle error, e.g., display an error message
        echo "<script>alert('Gagal mengupdate data: " . $conn->error . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Pengeluaran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
            margin-bottom: 50px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 500;
        }

        .btn-action {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center text-primary mb-4">Edit Data Pengeluaran</h2>

        <form method="post" class="needs-validation" novalidate>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" required>
                <div class="invalid-feedback">Harap pilih tanggal.</div>
            </div>

            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= htmlspecialchars($jumlah) ?>" placeholder="Contoh: 50000" required>
                <div class="invalid-feedback">Harap masukkan jumlah pengeluaran.</div>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <input type="text" class="form-control" id="keterangan" name="keterangan" value="<?= htmlspecialchars($keterangan) ?>" placeholder="Contoh: Beli alat tulis">
            </div>



            <div class="row">
                <div class="col-12 col-md-6 mb-2">
                    <button type="submit" name="submit" class="btn btn-primary w-100 btn-action">Update Pengeluaran</button>
                </div>
                <div class="col-12 col-md-6">
                    <a href="pengeluaran.php" class="btn btn-secondary w-100 btn-action">← Kembali ke Daftar Pengeluaran</a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>

</html>