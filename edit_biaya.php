<?php
// Set timezone to Asia/Jakarta for correct date handling
date_default_timezone_set('Asia/Jakarta');

include 'koneksi.php';

// Initialize variables to prevent undefined variable notices
$tanggal = date('Y-m-d'); // Default to current date
$nama = '';
$jumlah = 0;
$id = 0; // Default ID to 0

// --- Handle fetching existing data for editing ---
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the existing data for the given ID from the 'biaya' table using a prepared statement for security
    $stmt = $conn->prepare("SELECT tanggal, nama, jumlah FROM biaya WHERE id = ?");
    $stmt->bind_param("i", $id); // 'i' for integer type
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tanggal = $row['tanggal'];
        $nama = $row['nama'];
        $jumlah = $row['jumlah'];
    } else {
        // Redirect if ID is not found in the database
        header("Location: biaya.php");
        exit();
    }
    $stmt->close();
} else {
    // Redirect if no ID is provided in the URL
    header("Location: biaya.php");
    exit();
}

// --- Handle form submission for updating data ---
if (isset($_POST['submit'])) {
    // Re-get ID from POST to ensure it's available after the form submission
    $id = $_POST['id'];
    $new_tanggal = $_POST['tanggal'];
    $new_nama = $_POST['nama'];
    $new_jumlah = $_POST['jumlah'];

    // Update the database record in the 'biaya' table using a prepared statement for security
    $stmt = $conn->prepare("UPDATE biaya SET tanggal = ?, nama = ?, jumlah = ? WHERE id = ?");
    // 'ssii' -> s for string (tanggal, nama), i for integer (jumlah, id)
    $stmt->bind_param("ssii", $new_tanggal, $new_nama, $new_jumlah, $id);

    if ($stmt->execute()) {
        header("Location: biaya.php?status=updated"); // Add a status message for feedback
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
    <title>Edit Data Biaya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
            /* Light grey background, consistent with biaya.php */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            color: #28a745;
            /* Bootstrap success green for headings, consistent with biaya.php */
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .form-label {
            font-weight: 600;
            color: #343a40;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
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
        <h2>Edit Data Biaya</h2>

        <form method="post" class="needs-validation" novalidate>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal:</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" required>
                <div class="invalid-feedback">Harap pilih tanggal.</div>
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Biaya:</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>" placeholder="Contoh: Transportasi, Makan" required>
                <div class="invalid-feedback">Harap masukkan nama biaya.</div>
            </div>

            <div class="mb-4">
                <label for="jumlah" class="form-label">Jumlah Biaya (Rp):</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= htmlspecialchars($jumlah) ?>" placeholder="Contoh: 25000" required>
                <div class="invalid-feedback">Harap masukkan jumlah biaya.</div>
            </div>

            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <button type="submit" name="submit" class="btn btn-success w-100">Update Biaya</button>
                </div>
                <div class="col-12 col-md-6">
                    <a href="biaya.php" class="btn btn-secondary w-100">← Kembali ke Daftar Biaya</a>
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