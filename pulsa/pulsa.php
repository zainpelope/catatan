<?php
include '../koneksi.php';

// ===================================
// 1. PENGATURAN PAGINATION
// ===================================
$limit = 10;
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$page = max(1, $page);
$start = ($page * $limit) - $limit;

// ===================================
// 2. LOGIKA FILTER & PENCARIAN
// ===================================
$search_query = isset($_GET['cari']) ? $_GET['cari'] : '';
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$conditions = [];
if (!empty($search_query)) {
    $safe_search_query = $conn->real_escape_string($search_query);
    $conditions[] = "nama LIKE '%$safe_search_query%'";
}
if (!empty($filter_bulan)) {
    $safe_bulan = $conn->real_escape_string($filter_bulan);
    $conditions[] = "MONTH(tanggal) = '$safe_bulan'";
}
if (!empty($filter_tahun)) {
    $safe_tahun = $conn->real_escape_string($filter_tahun);
    $conditions[] = "YEAR(tanggal) = '$safe_tahun'";
}

$where_clause = "";
if (count($conditions) > 0) {
    $where_clause = " WHERE " . implode(' AND ', $conditions);
}

// Variabel untuk mengecek apakah filter sedang aktif agar otomatis terbuka
$is_filtering = (!empty($search_query) || !empty($filter_bulan)) ? 'show' : '';

// ===================================
// 3. HITUNG TOTAL KESELURUHAN & SELISIH
// ===================================
$sql_grand_total = "SELECT SUM(beli) AS total_beli_all, SUM(bayar) AS total_bayar_all FROM pulsa" . $where_clause;
$res_grand_total = $conn->query($sql_grand_total);
$data_grand_total = $res_grand_total->fetch_assoc();

$grand_total_beli = $data_grand_total['total_beli_all'] ?? 0;
$grand_total_bayar = $data_grand_total['total_bayar_all'] ?? 0;
$selisih = $grand_total_bayar - $grand_total_beli;

// ===================================
// 4. PAGINATION DATA
// ===================================
$total_query = "SELECT COUNT(id) AS total FROM pulsa" . $where_clause;
$total_result = $conn->query($total_query);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// ===================================
// 5. QUERY DATA UTAMA
// ===================================
$query = "SELECT * FROM pulsa" . $where_clause . " ORDER BY tanggal DESC, id DESC LIMIT $start, $limit";
$result = $conn->query($query);

function formatRupiah($angka)
{
    $prefix = $angka < 0 ? "-Rp " : "Rp ";
    return $prefix . number_format(abs($angka), 0, ',', '.');
}

function formatTanggal($tanggal, $include_time = false)
{
    if (empty($tanggal) || $tanggal === '0000-00-00 00:00:00' || strtotime($tanggal) === false) return "-";
    return date($include_time ? "d-m-Y H:i" : "d-m-Y", strtotime($tanggal));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pulsa Lengkap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
        }

        .card {
            border-radius: 1rem;
            border: none;
        }

        .summary-box {
            background: #fff;
            border-left: 5px solid #0d6efd;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            height: 100%;
        }
    </style>
</head>

<body>

    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white py-3 text-center">
                <h3 class="mb-0"><i class="bi bi-phone"></i> Manajemen Transaksi Pulsa</h3>
            </div>

            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-4">
                    <div class="d-flex gap-2">
                        <a href="tambah_pulsa.php" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Tambah Pulsa
                        </a>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                    </div>
                    <a href="../index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Kembali
                    </a>
                </div>

                <div class="collapse <?= $is_filtering ?>" id="filterCollapse">
                    <form action="" method="GET" class="row g-3 mb-4 bg-light p-3 rounded border">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Cari Nama</label>
                            <input type="text" name="cari" class="form-control" placeholder="Nama..." value="<?= htmlspecialchars($search_query) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Bulan</label>
                            <select name="bulan" class="form-select">
                                <option value="">-- Semua Bulan --</option>
                                <?php
                                $bulan_list = ["01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"];
                                foreach ($bulan_list as $k => $v) {
                                    $s = ($filter_bulan == $k) ? 'selected' : '';
                                    echo "<option value='$k' $s>$v</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Tahun</label>
                            <select name="tahun" class="form-select">
                                <?php
                                for ($t = date('Y'); $t >= 2025; $t--) {
                                    $s = ($filter_tahun == $t) ? 'selected' : '';
                                    echo "<option value='$t' $s>$t</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                            <a href="pulsa.php" class="btn btn-outline-danger"><i class="bi bi-arrow-clockwise"></i></a>
                        </div>
                    </form>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="summary-box" style="border-left-color: #0d6efd;">
                            <small class="text-muted d-block fw-bold">TOTAL BELI</small>
                            <h4 class="text-primary mb-0"><?= formatRupiah($grand_total_beli) ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="summary-box" style="border-left-color: #198754;">
                            <small class="text-muted d-block fw-bold">TOTAL BAYAR</small>
                            <h4 class="text-success mb-0"><?= formatRupiah($grand_total_bayar) ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="summary-box" style="border-left-color: <?= $selisih < 0 ? '#dc3545' : '#fd7e14' ?>;">
                            <small class="text-muted d-block fw-bold">HASIL</small>
                            <h4 class="<?= $selisih < 0 ? 'text-danger' : 'text-success' ?> mb-0">
                                <?= formatRupiah($selisih) ?>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Beli</th>
                                <th>Bayar</th>
                                <th>Tgl Transaksi</th>
                                <th>Status</th>
                                <th>Tgl Lunas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = $start + 1;
                            if ($result->num_rows > 0):
                                while ($row = $result->fetch_assoc()):
                            ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($row['nama']) ?></td>
                                        <td><?= formatRupiah($row['beli']) ?></td>
                                        <td><?= formatRupiah($row['bayar']) ?></td>
                                        <td class="text-center small"><?= formatTanggal($row['tanggal'], true) ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $row['status'] === 'Lunas' ? 'success' : 'danger' ?>">
                                                <?= $row['status'] ?>
                                            </span>
                                        </td>
                                        <td class="text-center small"><?= formatTanggal($row['tanggal_lunas'], true) ?></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="edit_pulsa.php?id=<?= $row['id'] ?>" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
                                                <a href="delete_pulsa.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
                                                <?php if ($row['status'] !== 'Lunas'): ?>
                                                    <form method="POST" action="../hutang/update_status.php" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                        <input type="hidden" name="type" value="pulsa">
                                                        <button type="submit" class="btn btn-primary btn-sm ms-1"><i class="bi bi-cash"></i></button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <tr class="table-secondary fw-bold text-center">
                                    <td colspan="2">TOTAL</td>
                                    <td class="text-primary"><?= formatRupiah($grand_total_beli) ?></td>
                                    <td class="text-success"><?= formatRupiah($grand_total_bayar) ?></td>
                                    <td colspan="4" class="text-end">
                                        SISA: <span class="<?= $selisih < 0 ? 'text-danger' : 'text-success' ?>"><?= formatRupiah($selisih) ?></span>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">Data tidak ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1):
                    $url_params = "&cari=" . urlencode($search_query) . "&bulan=" . $filter_bulan . "&tahun=" . $filter_tahun;
                ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?halaman=<?= ($page - 1) . $url_params ?>">Prev</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?halaman=<?= $i . $url_params ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?halaman=<?= ($page + 1) . $url_params ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>