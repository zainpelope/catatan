<?php
include '../koneksi.php';

// ===================================
// FUNGSI FORMAT
// ===================================
function formatRupiah($angka)
{
    if (!is_numeric($angka)) {
        return "Rp 0,00";
    }
    return 'Rp ' . number_format($angka, 2, ',', '.');
}

function formatTanggal($tanggal)
{
    if (strtotime($tanggal) === false) {
        return "Tgl Invalid";
    }
    return date('d-m-Y', strtotime($tanggal));
}

// ===================================
// 1. PENGATURAN PAGINATION
// ===================================
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$page = max(1, $page);
$start = ($page * $limit) - $limit;

// ===================================
// 2. LOGIKA PENCARIAN
// ===================================
$search_query = isset($_GET['cari']) ? $_GET['cari'] : '';
$where_clause = '';

if (!empty($search_query)) {
    $safe_search_query = $conn->real_escape_string($search_query);
    $where_clause = " WHERE nama LIKE '%" . $safe_search_query . "%'";
    if (!isset($_GET['halaman'])) {
        $start = 0;
        $page = 1;
    }
}

// ===================================
// 3. MENGHITUNG TOTAL BARIS
// ===================================
$total_query = "SELECT COUNT(id) AS total FROM dokumen" . $where_clause;
$total_result = $conn->query($total_query);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// *** DEBUGGING: Hapus baris ini setelah pagination berfungsi ***
// echo "<div class='container mt-3 alert alert-warning'>DEBUG: Total Data: {$total_rows}, Halaman: {$page}, Total Halaman: {$total_pages}</div>"; 
// *** DEBUGGING END ***

// Penyesuaian halaman
if ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
    $start = ($page * $limit) - $limit;
}

// ===================================
// 4. QUERY DATA DENGAN LIMIT DAN OFFSET
// ===================================
$query = "SELECT * FROM dokumen" . $where_clause . " ORDER BY id DESC LIMIT $start, $limit";
$result = $conn->query($query);

$total_bayar_halaman = 0;

// ===================================
// FUNGSI UNTUK MENAMPILKAN PAGINATION YANG RAMPING
// ===================================
function displayPagination($currentPage, $totalPages, $searchQuery)
{
    if ($totalPages <= 1) return;

    $url_params = !empty($searchQuery) ? '&cari=' . urlencode($searchQuery) : '';
    $link_base = "?halaman=";

    $max_links = 5;
    $range = floor($max_links / 2);

    $start_display = $currentPage - $range;
    $end_display = $currentPage + $range;

    $start_page = 1;
    $end_page = $totalPages;

    if ($start_display < $start_page) {
        $end_display += ($start_page - $start_display);
        $start_display = $start_page;
    }
    if ($end_display > $end_page) {
        $start_display -= ($end_display - $end_page);
        $end_display = $end_page;
    }
    if ($start_display < $start_page) {
        $start_display = $start_page;
    }

    echo '<ul class="pagination pagination-sm mb-0">';

    // Tombol Previous
    $disabled = ($currentPage <= 1) ? 'disabled' : '';
    echo "<li class=\"page-item {$disabled}\"><a class=\"page-link\" href=\"{$link_base}" . ($currentPage - 1) . $url_params . "\">Previous</a></li>";

    // Halaman Pertama + Ellipsis
    if ($start_display > $start_page) {
        echo '<li class="page-item"><a class="page-link" href="' . $link_base . '1' . $url_params . '">1</a></li>';
        if ($start_display > $start_page + 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Tautan di sekitar Halaman Aktif
    for ($i = $start_display; $i <= $end_display; $i++) {
        if ($i > $totalPages) break;
        $active = ($currentPage == $i) ? 'active' : '';
        echo "<li class=\"page-item {$active}\"><a class=\"page-link\" href=\"{$link_base}{$i}{$url_params}\">{$i}</a></li>";
    }

    // Halaman Terakhir + Ellipsis
    if ($end_display < $end_page) {
        if ($end_display < $end_page - 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        echo '<li class="page-item"><a class="page-link" href="' . $link_base . $totalPages . $url_params . '">' . $totalPages . '</a></li>';
    }

    // Tombol Next
    $disabled = ($currentPage >= $totalPages) ? 'disabled' : '';
    echo "<li class=\"page-item {$disabled}\"><a class=\"page-link\" href=\"{$link_base}" . ($currentPage + 1) . $url_params . "\">Next</a></li>";

    echo '</ul>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dokumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .table-container {
            overflow-x: auto;
        }

        h2 {
            color: #343a40;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Data Dokumen</h2>
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="tambah_dokumen.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Dokumen</a>
            <a href="../perlengkapan/perlengkapan.php" class="btn btn-success"><i class="bi bi-gear"></i> Biaya Perlengkapan Dokumen</a>
            <a href="../index.php" class="btn btn-secondary ms-auto"><i class="bi bi-arrow-left-circle"></i> Kembali</a>
        </div>

        <form action="" method="GET" class="mb-4">
            <div class="input-group">
                <input type="hidden" name="halaman" value="1">
                <input type="text" class="form-control" placeholder="Cari berdasarkan Nama..." name="cari"
                    value="<?= htmlspecialchars($search_query) ?>">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-search"></i> Cari
                </button>
                <?php if (!empty($search_query)): ?>
                    <a href="dokumen.php" class="btn btn-outline-danger">
                        <i class="bi bi-x-lg"></i> Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
        <div class="table-container">
            <table class="table table-striped table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Bayar</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = $start + 1;
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            $total_bayar_halaman += $row['bayar'];
                    ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= formatTanggal($row['tanggal']) ?></td>
                                <td><?= formatRupiah($row['bayar']) ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $row['status'] == 'Lunas' ? 'success' : 'danger' ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="edit_dokumen.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm me-1"><i class="bi bi-pencil-square"></i> Edit</a>
                                    <a href="delete_dokumen.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr>
                            <td colspan="7" class="text-center">
                                <?php if (!empty($search_query)): ?>
                                    Data **<?= htmlspecialchars($search_query) ?>** tidak ditemukan.
                                <?php else: ?>
                                    Tidak ada data dokumen.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="3" class="text-end">Total (Halaman Ini)</th>
                        <th><?= formatRupiah($total_bayar_halaman) ?></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <?php if ($total_pages > 1 || !empty($search_query)): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <p class="mb-0 text-muted">
                    Menampilkan **<?= $result->num_rows ?>** dari **<?= $total_rows ?>** total data.
                </p>
                <nav aria-label="Page navigation">
                    <?php displayPagination($page, $total_pages, $search_query); ?>
                </nav>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>