<?php
// Set timezone to Asia/Jakarta for correct date handling
date_default_timezone_set('Asia/Jakarta');

include 'koneksi.php';

// Initialize variables to prevent undefined variable notices
$tanggal = date('Y-m-d'); // Default to current date
$fauzan = 0;
$pln = 0;
$pribadi = 0;
$id = 0; // Default ID to 0

// --- Handle fetching existing data for editing ---
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the existing data for the given ID using a prepared statement for security
    $stmt = $conn->prepare("SELECT tanggal, fauzan, pln, pribadi FROM tabungan WHERE id = ?");
    $stmt->bind_param("i", $id); // 'i' for integer type
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tanggal = $row['tanggal'];
        $fauzan = $row['fauzan'];
        $pln = $row['pln'];
        $pribadi = $row['pribadi'];
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
    $new_fauzan = $_POST['fauzan'];
    $new_pln = $_POST['pln'];
    $new_pribadi = $_POST['pribadi'];

    // Update the database record using a prepared statement for security
    $stmt = $conn->prepare("UPDATE tabungan SET tanggal = ?, fauzan = ?, pln = ?, pribadi = ? WHERE id = ?");
    // 'siiii' -> s for string (tanggal), i for integer (fauzan, pln, pribadi, id)
    $stmt->bind_param("siiii", $new_tanggal, $new_fauzan, $new_pln, $new_pribadi, $id);

    if ($stmt->execute()) {
        header("Location: tabungan.php?status=updated"); // Add a status message for feedback
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
    <title>Edit Data Tabungan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            /* Light grey background */
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
            color: #007bff;
            /* Primary blue for headings */
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .form-label {
            font-weight: 600;
            color: #343a40;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
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
        <h2>Edit Data Tabungan</h2>

        <form method="post" class="needs-validation" novalidate>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal:</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" required>
                <div class="invalid-feedback">Harap pilih tanggal.</div>
            </div>

            <div class="mb-3">
                <label for="fauzan" class="form-label">Tabungan Fauzan (Rp):</label>
                <input type="number" class="form-control" id="fauzan" name="fauzan" value="<?= htmlspecialchars($fauzan) ?>" placeholder="Jumlah Tabungan Fauzan" required>
                <div class="invalid-feedback">Harap masukkan jumlah tabungan Fauzan.</div>
            </div>

            <div class="mb-3">
                <label for="pln" class="form-label">Tabungan PLN (Rp):</label>
                <input type="number" class="form-control" id="pln" name="pln" value="<?= htmlspecialchars($pln) ?>" placeholder="Jumlah Tabungan PLN" required>
                <div class="invalid-feedback">Harap masukkan jumlah tabungan PLN.</div>
            </div>

            <div class="mb-4">
                <label for="pribadi" class="form-label">Tabungan Pribadi (Rp):</label>
                <input type="number" class="form-control" id="pribadi" name="pribadi" value="<?= htmlspecialchars($pribadi) ?>" placeholder="Jumlah Tabungan Pribadi" required>
                <div class="invalid-feedback">Harap masukkan jumlah tabungan Pribadi.</div>
            </div>

            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <button type="submit" name="submit" class="btn btn-primary w-100">Update Tabungan</button>
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