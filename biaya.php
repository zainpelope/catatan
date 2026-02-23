<?php
// Set timezone to Asia/Jakarta for correct date handling
date_default_timezone_set('Asia/Jakarta');
include 'koneksi.php';

// --- Ambil Parameter Filter ---
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Bangun string WHERE untuk filter
$where_clauses = [];
if ($bulan != '') $where_clauses[] = "MONTH(tanggal) = '$bulan'";
if ($tahun != '') $where_clauses[] = "YEAR(tanggal) = '$tahun'";

$where = (count($where_clauses) > 0) ? "WHERE " . implode(' AND ', $where_clauses) : "";

// --- Handle Form Submissions ---

// Simpan data
if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $nama = $_POST['nama'];
    $jumlah = $_POST['jumlah'];

    $stmt = $conn->prepare("INSERT INTO biaya (tanggal, nama, jumlah) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $tanggal, $nama, $jumlah);
    $stmt->execute();
    $stmt->close();

    header("Location: biaya.php");
    exit();
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM biaya WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: biaya.php?bulan=$bulan&tahun=$tahun");
    exit();
}

// --- Pagination Logic ---
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung Total Biaya & Total Data untuk Filter Saat Ini
$totalQuery = "SELECT COUNT(*) AS total, SUM(jumlah) AS total_biaya FROM biaya $where";
$totalResult = $conn->query($totalQuery);
$rowTotal = $totalResult->fetch_assoc();
$totalData = $rowTotal['total'];
$sumBiaya = $rowTotal['total_biaya'] ?? 0;
$totalPages = ceil($totalData / $limit);

// Ambil Data Biaya
$query = "SELECT id, tanggal, nama, jumlah FROM biaya $where ORDER BY tanggal DESC LIMIT $limit OFFSET $offset";
$data = $conn->query($query);
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
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            margin-top: 30px;
            background-color: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.05);
        }

        .stats-card {
            background: linear-gradient(45deg, #198754, #20c997);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .table thead th {
            background-color: #198754;
            color: white;
            border: none;
        }
    </style>
</head>

<body>
    <div class="container shadow-sm">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success fw-bold mb-0">Manajemen Biaya</h2>
            <div>
                <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalBiaya">
                    Input Biaya
                </button>
                <a href="minuman.php" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>

        <div class="stats-card">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 opacity-75">Total Biaya (Periode Terpilih):</p>
                    <h2 class="fw-bold">Rp <?= number_format($sumBiaya, 0, ',', '.') ?></h2>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 opacity-75">Jumlah Transaksi:</p>
                    <h4 class="fw-bold"><?= $totalData ?> Data</h4>
                </div>
            </div>
        </div>

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <select name="bulan" class="form-select">
                    <option value="">-- Semua Bulan --</option>
                    <?php
                    $months = [1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                    foreach ($months as $num => $name) {
                        echo "<option value='$num' " . ($bulan == $num ? 'selected' : '') . ">$name</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="tahun" class="form-select">
                    <?php
                    for ($y = date('Y'); $y >= 2025; $y--) {
                        echo "<option value='$y' " . ($tahun == $y ? 'selected' : '') . ">$y</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-5 d-flex gap-1">
                <button type="submit" class="btn btn-dark w-100">Filter</button>
                <a href="biaya.php" class="btn btn-light border">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle border">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan Biaya</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data->num_rows > 0) : ?>
                        <?php while ($row = $data->fetch_assoc()) : ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td class="text-end fw-bold">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <a href="edit_biaya.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning btn-sm">Edit</a>
                                    <a href="biaya.php?hapus=<?= $row['id'] ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>

    </div>

    <div class="modal fade" id="modalBiaya" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Input Biaya Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" class="needs-validation" novalidate>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>" min="2025-01-01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Biaya</label>
                            <input type="text" class="form-control" name="nama" placeholder="Contoh: Bayar Keamanan" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jumlah (Rp)</label>
                            <input type="number" class="form-control" name="jumlah" placeholder="Masukkan angka saja" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="simpan" class="btn btn-success px-4">Simpan Data</button>
                    </div>
                </form>
            </div>
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