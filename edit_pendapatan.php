<?php
include 'koneksi.php';
$id = $_GET['id'];

// Hindari SQL injection dengan prepared statement
$stmt = $conn->prepare("SELECT * FROM pendapatan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (isset($_POST['update'])) {
    $tgl = $_POST['tanggal'];
    $jml = $_POST['jumlah'];
    $ket = $_POST['keterangan'];

    $stmt = $conn->prepare("UPDATE pendapatan SET tanggal=?, jumlah=?, keterangan=? WHERE id=?");
    $stmt->bind_param("sisi", $tgl, $jml, $ket, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: pendapatan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pendapatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="container col-md-6">
        <h3 class="text-center text-primary mb-4">Edit Pendapatan</h3>
        <form method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= htmlspecialchars($data['tanggal']) ?>" required>
                <div class="invalid-feedback">Tanggal tidak boleh kosong.</div>
            </div>

            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= htmlspecialchars($data['jumlah']) ?>" required>
                <div class="invalid-feedback">Jumlah tidak boleh kosong.</div>
            </div>

            <div class="mb-4">
                <label for="keterangan" class="form-label">Keterangan</label>
                <input type="text" class="form-control" id="keterangan" name="keterangan" value="<?= htmlspecialchars($data['keterangan']) ?>">
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mb-2">
                    <button type="submit" name="update" class="btn btn-success w-100">Update Data</button>
                </div>
                <div class="col-12 col-md-6">
                    <a href="pendapatan.php" class="btn btn-secondary w-100">← Kembali</a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Bootstrap form validation
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
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