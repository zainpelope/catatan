<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Set timezone to Asia/Jakarta for correct date
date_default_timezone_set('Asia/Jakarta');

include 'koneksi.php';

// --- Handle Form Submissions ---

// Simpan data tabungan
if (isset($_POST['simpan_tabungan'])) {
    $tanggal = $_POST['tanggal'];
    $fauzan = $_POST['fauzan'];
    $pln = $_POST['pln'];
    $pribadi = $_POST['pribadi'];

    // Use prepared statement for INSERT
    $stmt = $conn->prepare("INSERT INTO tabungan (tanggal, fauzan, pln, pribadi) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siii", $tanggal, $fauzan, $pln, $pribadi); // s = string, i = integer
    $stmt->execute();
    $stmt->close();

    header("Location: tabungan.php");
    exit();
}

// Simpan data pengeluaran tabungan
if (isset($_POST['simpan_pengeluaran'])) {
    $tanggal = $_POST['tanggal'];
    $kategori = $_POST['kategori'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    // Use prepared statement for INSERT
    $stmt = $conn->prepare("INSERT INTO pengeluaran_tabungan (tanggal, kategori, jumlah, keterangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $tanggal, $kategori, $jumlah, $keterangan); // s = string, i = integer
    $stmt->execute();
    $stmt->close();

    header("Location: tabungan.php");
    exit();
}

// Hapus data tabungan
if (isset($_GET['hapus_tabungan'])) {
    $id = $_GET['hapus_tabungan'];

    // Use prepared statement for DELETE
    $stmt = $conn->prepare("DELETE FROM tabungan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: tabungan.php");
    exit();
}

// Hapus data pengeluaran tabungan
if (isset($_GET['hapus_keluar'])) {
    $id = $_GET['hapus_keluar'];

    // Use prepared statement for DELETE
    $stmt = $conn->prepare("DELETE FROM pengeluaran_tabungan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: tabungan.php");
    exit();
}

// --- Data Retrieval for Display ---

// Ambil total tabungan
// Use prepared statement (though not strictly necessary for simple SELECT without WHERE clause, good practice)
$stmt_total = $conn->prepare("SELECT SUM(fauzan) AS fauzan, SUM(pln) AS pln, SUM(pribadi) AS pribadi FROM tabungan");
$stmt_total->execute();
$total = $stmt_total->get_result()->fetch_assoc();
$stmt_total->close();

// Ambil total pengeluaran
$stmt_keluar = $conn->prepare("SELECT kategori, SUM(jumlah) AS total FROM pengeluaran_tabungan GROUP BY kategori");
$stmt_keluar->execute();
$keluar_result = $stmt_keluar->get_result();
$keluarData = ['fauzan' => 0, 'pln' => 0, 'pribadi' => 0];
while ($row = $keluar_result->fetch_assoc()) {
    $keluarData[$row['kategori']] = $row['total'];
}
$stmt_keluar->close();

// Hitung sisa tabungan
$sisa = [
    'fauzan' => ($total['fauzan'] ?? 0) - ($keluarData['fauzan'] ?? 0),
    'pln' => ($total['pln'] ?? 0) - ($keluarData['pln'] ?? 0),
    'pribadi' => ($total['pribadi'] ?? 0) - ($keluarData['pribadi'] ?? 0),
];

// Hitung total keseluruhan
$jumlah_sebelum = ($total['fauzan'] ?? 0) + ($total['pln'] ?? 0) + ($total['pribadi'] ?? 0);
$jumlah_sesudah = $sisa['fauzan'] + $sisa['pln'] + $sisa['pribadi'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tabungan Saya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9f5f9;
            /* Light blueish background */
        }

        .container {
            margin-top: 40px;
            margin-bottom: 40px;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            color: #2c3e50;
            /* Dark blue/grey */
            margin-bottom: 25px;
            font-weight: 600;
        }

        .form-section {
            padding: 25px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 30px;
            background-color: #f8fafd;
            /* Lighter background for form sections */
        }

        .table-responsive {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .table thead th {
            background-color: #3498db;
            /* Blue header */
            color: white;
            border-color: #3498db;
        }

        .table tbody tr:hover {
            background-color: #eaf6fa;
            /* Light hover effect */
        }

        .table tfoot td {
            background-color: #d1ecf1;
            /* Light blue footer */
            font-weight: bold;
            color: #0c5460;
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
        <h1 class="text-center mb-5">Tabungan</h1>

        <div class="form-section">
            <h2 class="text-primary">Input Tabungan</h2>
            <form method="post" class="needs-validation" novalidate>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="tanggal_tabungan" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal_tabungan" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                        <div class="invalid-feedback">Harap pilih tanggal.</div>
                    </div>
                    <div class="col-md-8">
                        <label for="fauzan" class="form-label">Tabungan Fauzan (Rp)</label>
                        <input type="number" class="form-control" id="fauzan" name="fauzan" placeholder="Tabungan Fauzan" value="" required>
                        <div class="invalid-feedback">Harap masukkan jumlah tabungan Fauzan.</div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="pln" class="form-label">Tabungan PLN (Rp)</label>
                        <input type="number" class="form-control" id="pln" name="pln" placeholder="Tabungan PLN" value="" required>
                        <div class="invalid-feedback">Harap masukkan jumlah tabungan PLN.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="pribadi" class="form-label">Tabungan Pribadi (Rp)</label>
                        <input type="number" class="form-control" id="pribadi" name="pribadi" placeholder="Tabungan Pribadi" value="" required>
                        <div class="invalid-feedback">Harap masukkan jumlah tabungan Pribadi.</div>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-12">
                        <button type="submit" name="simpan_tabungan" class="btn btn-primary w-100">Simpan Tabungan</button>
                    </div>
                    <div class="col-12">
                        <a href="konter.php" class="btn btn-secondary w-100">← Kembali ke Dashboard</a>
                    </div>
                </div>
            </form>
        </div>

        <hr class="my-5">

        <div class="form-section">
            <h2 class="text-danger">Input Pengeluaran Tabungan</h2>
            <form method="post" class="needs-validation" novalidate>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="tanggal_pengeluaran" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal_pengeluaran" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                        <div class="invalid-feedback">Harap pilih tanggal.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select class="form-select" id="kategori" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="fauzan">Fauzan</option>
                            <option value="pln">PLN</option>
                            <option value="pribadi">Pribadi</option>
                        </select>
                        <div class="invalid-feedback">Harap pilih kategori.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="jumlah_keluar" class="form-label">Jumlah (Rp)</label>
                        <input type="number" class="form-control" id="jumlah_keluar" name="jumlah" placeholder="Jumlah Pengeluaran" required>
                        <div class="invalid-feedback">Harap masukkan jumlah pengeluaran.</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="keterangan_keluar" class="form-label">Keterangan</label>
                    <input type="text" class="form-control" id="keterangan_keluar" name="keterangan" placeholder="Contoh: Bayar listrik, Jajan">
                </div>
                <button type="submit" name="simpan_pengeluaran" class="btn btn-danger w-100">Simpan Pengeluaran</button>
            </form>
        </div>

        <hr class="my-5">

        <h2 class="text-info">Rangkuman Saldo Tabungan</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Total Pemasukan</th>
                        <th>Total Pengeluaran</th>
                        <th>Sisa Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Fauzan</td>
                        <td>Rp <?= number_format($total['fauzan'] ?? 0, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($keluarData['fauzan'] ?? 0, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($sisa['fauzan'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>PLN</td>
                        <td>Rp <?= number_format($total['pln'] ?? 0, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($keluarData['pln'] ?? 0, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($sisa['pln'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Pribadi</td>
                        <td>Rp <?= number_format($total['pribadi'] ?? 0, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($keluarData['pribadi'] ?? 0, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($sisa['pribadi'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <td>Total Keseluruhan</td>
                        <td>Rp <?= number_format($jumlah_sebelum ?? 0, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format(array_sum($keluarData) ?? 0, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($jumlah_sesudah ?? 0, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <hr class="my-5">

        <h2 class="text-dark">Riwayat Pemasukan Tabungan</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Fauzan (Rp)</th>
                        <th>PLN (Rp)</th>
                        <th>Pribadi (Rp)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt_data_tabungan = $conn->prepare("SELECT * FROM tabungan ORDER BY tanggal DESC");
                    $stmt_data_tabungan->execute();
                    $data_tabungan = $stmt_data_tabungan->get_result();

                    if ($data_tabungan->num_rows > 0) :
                        while ($row = $data_tabungan->fetch_assoc()) :
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                <td><?= number_format($row['fauzan'], 0, ',', '.') ?></td>
                                <td><?= number_format($row['pln'], 0, ',', '.') ?></td>
                                <td><?= number_format($row['pribadi'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="edit_tabungan.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-action">Edit</a>
                                    <a href="?hapus_tabungan=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data pemasukan tabungan.</td>
                        </tr>
                    <?php endif;
                    $stmt_data_tabungan->close();
                    ?>
                </tbody>
            </table>
        </div>

        <hr class="my-5">

        <h2 class="text-dark">Riwayat Pengeluaran Tabungan</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Jumlah (Rp)</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt_data_keluar = $conn->prepare("SELECT * FROM pengeluaran_tabungan ORDER BY tanggal DESC");
                    $stmt_data_keluar->execute();
                    $data_keluar = $stmt_data_keluar->get_result();

                    if ($data_keluar->num_rows > 0) :
                        while ($row = $data_keluar->fetch_assoc()) :
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($row['kategori'])) ?></td>
                                <td><?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td>
                                    <a href="edit_pengeluaran_tabungan.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-action">Edit</a>
                                    <a href="?hapus_keluar=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data pengeluaran tabungan.</td>
                        </tr>
                    <?php endif;
                    $stmt_data_keluar->close();
                    ?>
                </tbody>
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