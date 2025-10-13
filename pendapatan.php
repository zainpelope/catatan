<?php
date_default_timezone_set('Asia/Jakarta'); // Tambahkan baris ini
?>

<?php
include 'koneksi.php';

// Mendapatkan tanggal saat ini dalam format YYYY-MM-DD
$today = date('Y-m-d');

// Simpan data
if (isset($_POST['submit'])) {
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    // Using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO pendapatan (tanggal, jumlah, keterangan) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $tanggal, $jumlah, $keterangan); // s for string, i for integer
    $stmt->execute();
    $stmt->close();
    header("Location: pendapatan.php");
    exit(); // Always exit after a header redirect
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM pendapatan WHERE id = ?");
    $stmt->bind_param("i", $id); // i for integer
    $stmt->execute();
    $stmt->close();
    header("Location: pendapatan.php");
    exit(); // Always exit after a header redirect
}

// Tampilkan data
$result = $conn->query("SELECT * FROM pendapatan ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendapatan Perhari</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            margin-top: 30px;
            margin-bottom: 30px;
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .table-responsive {
            margin-top: 20px;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
        }

        .btn-action {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="mb-4 text-center text-primary">Input Pendapatan Harian</h2>
        <form method="post" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $today ?>" required>
                    <div class="invalid-feedback">
                        Harap pilih tanggal.
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                    <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Masukkan jumlah pendapatan." required>
                    <div class="invalid-feedback">
                        Masukkan jumlah pendapatan.
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan">
                </div>


            </div>
            <br>

            <div class="row g-2">
                <div class="col-12">
                    <button type="submit" name="submit" class="btn btn-primary w-100">Simpan Data</button>
                </div>
                <div class="col-12">
                    <a href="konter.php" class="btn btn-secondary w-100">← Kembali ke Dashboard</a>
                </div>
            </div>




        </form>

        <hr class="my-5">

        <h3 class="mb-4 text-center text-success">Daftar Pendapatan</h3>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                <td>Rp <?= number_format(htmlspecialchars($row['jumlah']), 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td>
                                    <a href="edit_pendapatan.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-warning btn-action">Edit</a>
                                    <a href="pendapatan.php?hapus=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data pendapatan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>

</html>