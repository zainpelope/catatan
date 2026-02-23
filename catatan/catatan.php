<?php
include '../koneksi.php';

// ==========================
// HAPUS DATA
// ==========================
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM catatan WHERE id='$id'");
    echo "<script>alert('Catatan berhasil dihapus'); window.location='catatan.php';</script>";
    exit;
}

// ==========================
// KONFIGURASI PAGINATION
// ==========================
$limit = 10; // 10 data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = ($page < 1) ? 1 : $page;
$offset = ($page - 1) * $limit;

// ==========================
// SEARCH
// ==========================
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = "";

if ($search != '') {
    $where = "WHERE nominal LIKE '%$search%' 
              OR keterangan LIKE '%$search%' 
              OR tanggal LIKE '%$search%'";
}

// ==========================
// TOTAL DATA
// ==========================
$totalQuery = "SELECT COUNT(*) AS total FROM catatan $where";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalData = $totalRow['total'];
$totalPages = ceil($totalData / $limit);

// ==========================
// AMBIL DATA
// ==========================
$query = "SELECT * FROM catatan 
          $where 
          ORDER BY id DESC 
          LIMIT $limit OFFSET $offset";

$result = $conn->query($query);

// ==========================
// TOTAL NOMINAL (FILTER SEARCH)
// ==========================
$totalNominalQuery = "SELECT SUM(nominal) AS total_nominal FROM catatan $where";
$totalNominalResult = $conn->query($totalNominalQuery);
$totalNominal = $totalNominalResult->fetch_assoc()['total_nominal'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Catatan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Catatan</h3>
            </div>

            <div class="card-body">

                <!-- BUTTON & SEARCH -->
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <a href="add_catatan.php" class="btn btn-success">Tambah Catatan</a>
                        <a href="../index.php" class="btn btn-secondary">Kembali</a>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2"
                                placeholder="Cari nominal / keterangan / tanggal"
                                value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-primary">Cari</button>
                        </form>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Nominal</th>
                                <th>Keterangan</th>
                                <th width="15%">Tanggal</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                $no = $offset + 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                <td>{$no}</td>
                                <td>Rp " . number_format($row['nominal'], 2, ',', '.') . "</td>
                                <td>{$row['keterangan']}</td>
                                <td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>
                                <td>
                                    <a href='edit_catatan.php?edit={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='?delete={$row['id']}' class='btn btn-danger btn-sm'
                                       onclick='return confirm(\"Yakin hapus data?\")'>Hapus</a>
                                </td>
                            </tr>";
                                    $no++;
                                }

                                echo "<tr>
                            <td class='text-end'><strong>Total</strong></td>
                            <td><strong>Rp " . number_format($totalNominal, 2, ',', '.') . "</strong></td>
                            <td colspan='3'></td>
                        </tr>";
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Data tidak ditemukan</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>