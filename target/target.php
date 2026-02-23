<?php
include '../koneksi.php';

// =========================
// HAPUS DATA
// =========================
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($conn->query("DELETE FROM target WHERE id='$id'")) {
        echo "<script>alert('Target berhasil dihapus'); window.location='target.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus target'); window.location='target.php';</script>";
    }
    exit;
}

// =========================
// PAGINATION
// =========================
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = ($page < 1) ? 1 : $page;
$offset = ($page - 1) * $limit;

// =========================
// SEARCH
// =========================
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = "";

if ($search != '') {
    $where = "WHERE nama_target LIKE '%$search%' 
              OR status LIKE '%$search%' 
              OR target_selesai LIKE '%$search%'";
}

// =========================
// TOTAL DATA
// =========================
$totalQuery = "SELECT COUNT(*) AS total FROM target $where";
$totalResult = $conn->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// =========================
// AMBIL DATA
// =========================
$query = "SELECT * FROM target 
          $where 
          ORDER BY id DESC 
          LIMIT $limit OFFSET $offset";
$targetResult = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Target</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Target</h3>
            </div>

            <div class="card-body">

                <!-- BUTTON & SEARCH -->
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <a href="add_target.php" class="btn btn-success">Tambah Target</a>
                        <a href="../index.php" class="btn btn-secondary">Kembali</a>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2"
                                placeholder="Cari nama / status / tanggal"
                                value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-primary">Cari</button>
                        </form>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Target</th>
                                <th width="15%">Target Selesai</th>
                                <th width="15%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($targetResult->num_rows > 0) {
                                $no = $offset + 1;
                                while ($row = $targetResult->fetch_assoc()) {

                                    $tanggalSelesai = date("d-m-Y", strtotime($row['target_selesai']));

                                    if (strtolower($row['status']) === 'selesai') {
                                        $statusText = "<span class='text-success'>Selesai</span>";
                                    } else {
                                        $statusText = "<span class='text-danger'>Belum Selesai</span>";
                                    }

                                    echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['nama_target']}</td>
                                    <td>{$tanggalSelesai}</td>
                                    <td>{$statusText}</td>
                                    <td>
                                        <a href='edit_target.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                        <a href='?delete={$row['id']}' 
                                           class='btn btn-danger btn-sm'
                                           onclick='return confirm(\"Apakah Anda yakin ingin menghapus data?\")'>Hapus</a>
                                    </td>
                                </tr>";
                                    $no++;
                                }
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
                        <ul class="pagination justify-content-center mt-3">
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

<?php $conn->close(); ?>