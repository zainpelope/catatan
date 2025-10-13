<?php
// Set timezone to Asia/Jakarta for correct date handling
date_default_timezone_set('Asia/Jakarta');

include 'koneksi.php';

// --- Handle Form Submissions ---

// Simpan data
if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $nama = $_POST['nama'];
    $jumlah = $_POST['jumlah'];

    // Use prepared statement for INSERT to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO biaya (tanggal, nama, jumlah) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $tanggal, $nama, $jumlah); // s = string, i = integer
    $stmt->execute();
    $stmt->close();

    header("Location: biaya.php");
    exit();
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Use prepared statement for DELETE to prevent SQL Injection
    $stmt = $conn->prepare("DELETE FROM biaya WHERE id = ?");
    $stmt->bind_param("i", $id); // i = integer
    $stmt->execute();
    $stmt->close();

    header("Location: biaya.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Biaya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
            /* Light grey background */
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        .container {
            margin-top: 40px;
            margin-bottom: 40px;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            color: #28a745;
            /* Bootstrap green for headings */
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .form-section {
            padding: 25px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 40px;
            background-color: #fdfefe;
            /* Slightly lighter background for form */
        }

        .table-responsive {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .table thead th {
            background-color: #28a745;
            /* Green header */
            color: white;
            border-color: #28a745;
        }

        .table tbody tr:hover {
            background-color: #e6f7ed;
            /* Light green hover effect */
        }

        .table tfoot td {
            background-color: #d4edda;
            /* Light green footer */
            font-weight: bold;
            color: #155724;
        }

        .btn-action {
            margin-right: 5px;
        }

        .btn-back {
            margin-top: 30px;
            display: block;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="form-section">
            <h2 class="text-success mb-4">Input Data Biaya Baru</h2>
            <form method="post" class="needs-validation" novalidate>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                        <div class="invalid-feedback">Harap pilih tanggal.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="nama" class="form-label">Nama Biaya</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Keterangan Biaya" required>
                        <div class="invalid-feedback">Harap masukkan nama biaya.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Masukkan jumlah biaya" required>
                        <div class="invalid-feedback">Harap masukkan jumlah biaya.</div>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-12">
                        <button type="submit" name="simpan" class="btn btn-success w-100 mt-3">Simpan Biaya</button>
                    </div>
                    <div class="col-12">
                        <a href="konter.php" class="btn btn-secondary w-100">← Kembali ke Dashboard</a>
                    </div>
                </div>

            </form>
        </div>

        <hr class="my-5">

        <h2 class="text-dark mb-4">Daftar Biaya</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Biaya</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_biaya = 0;
                    // Use prepared statement for SELECT
                    $stmt_data = $conn->prepare("SELECT id, tanggal, nama, jumlah FROM biaya ORDER BY tanggal DESC");
                    $stmt_data->execute();
                    $data = $stmt_data->get_result();

                    if ($data->num_rows > 0) :
                        while ($row = $data->fetch_assoc()) :
                            $total_biaya += $row['jumlah'];
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="edit_biaya.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-action">Edit</a>
                                    <a href="biaya.php?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data biaya.</td>
                        </tr>
                    <?php endif;
                    $stmt_data->close();
                    ?>
                </tbody>
                <tfoot>
                    <tr class="table-success">
                        <td colspan="2">Total Biaya</td>
                        <td colspan="2">Rp <?= number_format($total_biaya, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>


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