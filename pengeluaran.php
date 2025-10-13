<?php
date_default_timezone_set('Asia/Jakarta'); // Tambahkan baris ini
?>
<?php include 'koneksi.php'; ?>

<?php
// Simpan data
if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    $stmt = $conn->prepare("INSERT INTO pengeluaran (tanggal, jumlah, keterangan) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $tanggal, $jumlah, $keterangan);
    $stmt->execute();
    $stmt->close();

    header("Location: pengeluaran.php");
    exit();
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    $stmt = $conn->prepare("DELETE FROM pengeluaran WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: pengeluaran.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Pengeluaran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 40px;
            margin-bottom: 40px;
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-action {
            margin-right: 5px;
        }

        .table thead th {
            background-color: #343a40;
            color: white;
        }

        .table tfoot td {
            background-color: #e9ecef;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center text-danger mb-4">Input Pengeluaran Harian</h2>

        <!-- Form Input -->
        <form method="post" class="needs-validation" novalidate>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                    <div class="invalid-feedback">Harap pilih tanggal.</div>
                </div>
                <div class="col-md-4">
                    <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                    <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Masukkan jumlah pengeluaran" required>
                    <div class="invalid-feedback">Harap masukkan jumlah pengeluaran.</div>
                </div>
                <div class="col-md-4">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan">
                </div>
            </div>
            <div class="row g-2">
                <div class="col-12">
                    <button type="submit" name="simpan" class="btn btn-danger w-100">Simpan Pengeluaran</button>
                </div>
                <div class="col-12">
                    <a href="konter.php" class="btn btn-secondary w-100">← Kembali ke Dashboard</a>
                </div>
            </div>


        </form>

        <hr class="my-5">

        <!-- Tabel Data -->
        <h3 class="text-center text-dark mb-4">Daftar Pengeluaran</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_pengeluaran = 0;
                    $data = $conn->query("SELECT * FROM pengeluaran ORDER BY tanggal DESC");

                    if ($data->num_rows > 0) :
                        while ($row = $data->fetch_assoc()) :
                            $total_pengeluaran += $row['jumlah'];
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td>
                                    <a href="edit_pengeluaran.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-action">Edit</a>
                                    <a href="pengeluaran.php?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data pengeluaran.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">Total Pengeluaran</td>
                        <td>Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
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