<?php
date_default_timezone_set('Asia/Jakarta');
include 'koneksi.php';

// ========================
// TANGGAL HARI INI
// ========================
$today = date('Y-m-d');

// ========================
// SIMPAN DATA
// ========================
if (isset($_POST['submit'])) {
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    $stmt = $conn->prepare("INSERT INTO pendapatan (tanggal, jumlah, keterangan) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $tanggal, $jumlah, $keterangan);
    $stmt->execute();
    $stmt->close();

    header("Location: pendapatan.php");
    exit();
}

// ========================
// HAPUS DATA
// ========================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    $stmt = $conn->prepare("DELETE FROM pendapatan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: pendapatan.php");
    exit();
}

// ========================
// FILTER & SEARCH LOGIC
// ========================
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$bulan  = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun  = isset($_GET['tahun']) ? $_GET['tahun'] : '';

$where_clauses = [];
if ($search != '') $where_clauses[] = "(keterangan LIKE '%$search%' OR tanggal LIKE '%$search%')";
if ($bulan != '') $where_clauses[] = "MONTH(tanggal) = '$bulan'";
if ($tahun != '') $where_clauses[] = "YEAR(tanggal) = '$tahun'";

$where = (count($where_clauses) > 0) ? "WHERE " . implode(' AND ', $where_clauses) : "";

// ========================
// PAGINATION
// ========================
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// TOTAL DATA & JUMLAH UANG
$totalQuery = "SELECT COUNT(*) AS total, SUM(jumlah) AS total_duit FROM pendapatan $where";
$totalResult = $conn->query($totalQuery);
$rowTotal = $totalResult->fetch_assoc();
$totalData = $rowTotal['total'];
$totalDuit = $rowTotal['total_duit'] ?? 0;
$totalPages = ceil($totalData / $limit);

// AMBIL DATA
$query = "SELECT * FROM pendapatan $where ORDER BY tanggal DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manajemen Pendapatan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 30px;
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stats-card {
            background: linear-gradient(45deg, #0d6efd, #0099ff);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
    </style>
</head>

<body>

    <div class="container shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold mb-0">Pendapatan</h2>
            <div>
                <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalInput">
                    Tambah Data
                </button>
                <a href="minuman.php" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>

        <div class="stats-card">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="mb-0 opacity-75">Total Pendapatan (Filter):</p>
                    <h2 class="fw-bold">Rp <?= number_format($totalDuit, 0, ',', '.') ?></h2>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 opacity-75">Jumlah Transaksi:</p>
                    <h4 class="fw-bold"><?= $totalData ?> Data</h4>
                </div>
            </div>
        </div>

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-3">
                <select name="bulan" class="form-select text-center">
                    <option value="">-- Semua Bulan --</option>
                    <?php
                    $bln_nama = [1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                    foreach ($bln_nama as $val => $nama) {
                        echo "<option value='$val' " . ($bulan == $val ? 'selected' : '') . ">$nama</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="tahun" class="form-select text-center">
                    <option value="">-- Tahun --</option>
                    <?php
                    $thn_mulai = 2025; // Mulai dari tahun 2025
                    $thn_skrg  = date('Y'); // Tahun saat ini (misal 2025, 2026, dst)

                    // Jika tahun sekarang lebih kecil dari 2025 (untuk jaga-jaga dev), set ke 2025
                    $loop_tahun = ($thn_skrg < $thn_mulai) ? $thn_mulai : $thn_skrg;

                    for ($i = $loop_tahun; $i >= $thn_mulai; $i--) {
                        echo "<option value='$i' " . ($tahun == $i ? 'selected' : '') . ">$i</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-1 ms-auto">
                <button type="submit" class="btn btn-success w-100">Filter</button>
                <a href="pendapatan.php" class="btn btn-light border">Reset</a>
            </div>

        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle border">
                <thead class="table-light">
                    <tr>
                        <th width="15%">Tanggal</th>
                        <th>Keterangan</th>
                        <th class="text-end" width="20%">Jumlah</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td class="text-end fw-bold text-dark">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="edit_pendapatan.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning btn-sm">Edit</a>
                                        <a href="?hapus=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus data?')">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Belum ada data pendapatan untuk periode ini.</td>
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
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="modalInput" tabindex="-1" aria-labelledby="modalInputLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalInputLabel">Input Pendapatan Harian</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" value="<?= $today ?>" min="2025-01-01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jumlah Uang (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="jumlah" placeholder="Contoh: 50000" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="3" placeholder="Keterangan . . . . . ."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="submit" class="btn btn-primary px-4">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php $conn->close(); ?>