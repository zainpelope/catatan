<?php
// Set timezone to Asia/Jakarta for correct date handling
date_default_timezone_set('Asia/Jakarta');

include 'koneksi.php';

// Initialize variables to prevent undefined variable notices
$tanggal = date('Y-m-d'); // Default to current date
$kategori = '';
$jumlah = 0;
$keterangan = '';
$id = 0; // Default ID to 0

// --- Handle fetching existing data for editing ---
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the existing data for the given ID from 'pengeluaran_tabungan' table using a prepared statement
    $stmt = $conn->prepare("SELECT tanggal, kategori, jumlah, keterangan FROM pengeluaran_tabungan WHERE id = ?");
    $stmt->bind_param("i", $id); // 'i' for integer type
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tanggal = $row['tanggal'];
        $kategori = $row['kategori'];
        $jumlah = $row['jumlah'];
        $keterangan = $row['keterangan'];
    } else {
        // Redirect if ID is not found in the database
        header("Location: tabungan.php");
        exit();
    }
    $stmt->close();
} else {
    // Redirect if no ID is provided in the URL
    header("Location: tabungan.php");
    exit();
}

// --- Handle form submission for updating data ---
if (isset($_POST['submit'])) {
    // Re-get ID from POST to ensure it's available after the form submission
    $id = $_POST['id'];
    $new_tanggal = $_POST['tanggal'];
    $new_kategori = $_POST['kategori'];
    $new_jumlah = $_POST['jumlah'];
    $new_keterangan = $_POST['keterangan'];

    // Update the database record in 'pengeluaran_tabungan' table using a prepared statement
    $stmt = $conn->prepare("UPDATE pengeluaran_tabungan SET tanggal = ?, kategori = ?, jumlah = ?, keterangan = ? WHERE id = ?");
    // 'ssisi' -> s for string (tanggal, kategori, keterangan), i for integer (jumlah, id)
    $stmt->bind_param("ssisi", $new_tanggal, $new_kategori, $new_jumlah, $new_keterangan, $id);

    if ($stmt->execute()) {
        header("Location: tabungan.php?status=pengeluaran_updated"); // Add a status message for feedback
        exit();
    } else {
        // Basic error handling for database update failure
        echo "<script>alert('Gagal mengupdate data: " . $conn->error . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Pengeluaran Tabungan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            /* Light grey background */
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .container {
            margin-top: 50px;
            margin-bottom: 50px;
            background-color: #ffffff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #dc3545;
            /* Bootstrap danger red for headings */
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .form-label {
            font-weight: 600;
            color: #343a40;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Data Pengeluaran Tabungan</h2>

        <form method="post" class="needs-validation" novalidate>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal:</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" required>
                <div class="invalid-feedback">Harap pilih tanggal.</div>
            </div>

            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori:</label>
                <select class="form-select" id="kategori" name="kategori" required>
                    <option value="">Pilih Kategori</option>
                    <option value="fauzan" <?= ($kategori == 'fauzan') ? 'selected' : '' ?>>Fauzan</option>
                    <option value="pln" <?= ($kategori == 'pln') ? 'selected' : '' ?>>PLN</option>
                    <option value="pribadi" <?= ($kategori == 'pribadi') ? 'selected' : '' ?>>Pribadi</option>
                </select>
                <div class="invalid-feedback">Harap pilih kategori.</div>
            </div>

            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah (Rp):</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= htmlspecialchars($jumlah) ?>" placeholder="Jumlah Pengeluaran" required>
                <div class="invalid-feedback">Harap masukkan jumlah pengeluaran.</div>
            </div>

            <div class="mb-4">
                <label for="keterangan" class="form-label">Keterangan:</label>
                <input type="text" class="form-control" id="keterangan" name="keterangan" value="<?= htmlspecialchars($keterangan) ?>" placeholder="Contoh: Bayar listrik, Jajan">
            </div>

            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <button type="submit" name="submit" class="btn btn-danger w-100">Update Pengeluaran</button>
                </div>
                <div class="col-12 col-md-6">
                    <a href="tabungan.php" class="btn btn-secondary w-100">← Kembali ke Tabungan</a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bootstrap form validation script
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