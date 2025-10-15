<?php
include '../koneksi.php';

// ===================================
// FUNGSI UTILITY
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

// Fungsi untuk menampilkan link pagination yang ramping
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


// ===================================
// LOGIKA PENGHAPUSAN
// ===================================

function deletePerlengkapan($id, $conn)
{
    // Menggunakan prepared statement lebih aman, tapi saya tetap menggunakan style Anda
    $deleteQuery = "DELETE FROM perlengkapan WHERE id = '$id'";
    if ($conn->query($deleteQuery)) {
        // Alihkan kembali dengan status sukses
        echo "<script>alert('Perlengkapan berhasil dihapus'); window.location='perlengkapan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus perlengkapan: " . $conn->error . "'); window.location='perlengkapan.php';</script>";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    deletePerlengkapan($id, $conn);
    exit(); // Penting untuk menghentikan eksekusi setelah pengalihan
}

// ===================================
// PENGATURAN QUERY & PAGINATION
// ===================================
$limit = 10;
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$page = max(1, $page);
$start = ($page * $limit) - $limit;

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

// 1. MENGHITUNG TOTAL BARIS (Untuk Pagination)
$total_query = "SELECT COUNT(id) AS total FROM perlengkapan" . $where_clause;
$total_result = $conn->query($total_query);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Penyesuaian halaman
if ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
    $start = ($page * $limit) - $limit;
}

// 2. QUERY DATA DENGAN LIMIT DAN OFFSET
$query = "SELECT * FROM perlengkapan" . $where_clause . " ORDER BY tanggal_buat DESC LIMIT $start, $limit";
$perlengkapanResult = $conn->query($query);

// 3. Menghitung Total Harga Global (Tidak terpengaruh oleh LIMIT)
$globalTotalQuery = "SELECT SUM(harga) AS total FROM perlengkapan";
if (!empty($search_query)) {
    // Jika ada pencarian, total harga yang ditampilkan juga harus yang dicari
    $globalTotalQuery = "SELECT SUM(harga) AS total FROM perlengkapan" . $where_clause;
}
$globalTotalResult = $conn->query($globalTotalQuery);
$totalHargaGlobal = $globalTotalResult->fetch_assoc()['total'] ?? 0;

$totalHargaHalaman = 0; // Variabel untuk total harga di halaman yang sedang aktif
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perlengkapan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 1rem;
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3><i class="bi bi-box-seam"></i> Data Perlengkapan Dokumen</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-2">
                    <a href="../perlengkapan/tambah.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Tambah Perlengkapan</a>
                    <a href="../dokumen/dokumen.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle"></i> Kembali ke Data Dokumen</a>
                </div>

                <form action="" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="hidden" name="halaman" value="1">
                        <input type="text" class="form-control" placeholder="Cari berdasarkan Nama Perlengkapan..." name="cari"
                            value="<?= htmlspecialchars($search_query) ?>">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <?php if (!empty($search_query)): ?>
                            <a href="perlengkapan.php" class="btn btn-outline-danger">
                                <i class="bi bi-x-lg"></i> Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </form>


                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama</th>
                                <th style="width: 15%;">Harga</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = $start + 1;
                            if ($perlengkapanResult->num_rows > 0) {
                                while ($row = $perlengkapanResult->fetch_assoc()) {
                                    $totalHargaHalaman += $row['harga'];
                                    $formattedHarga = formatRupiah($row['harga']);
                                    $tanggalBuat = formatTanggal($row['tanggal_buat']);
                            ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td><?= $formattedHarga ?></td>
                                        <td><?= $tanggalBuat ?></td>
                                        <td class="text-center">
                                            <div class='btn-group' role='group'>
                                                <a href='../perlengkapan/edit.php?id=<?= $row['id'] ?>' class='btn btn-warning btn-sm me-2'><i class="bi bi-pencil-square"></i> Edit</a>
                                                <a href='?delete=<?= $row['id'] ?>' class='btn btn-danger btn-sm' onclick='return confirm("Apakah Anda yakin ingin menghapus data ini? Aksi ini tidak dapat dibatalkan.")'><i class="bi bi-trash"></i> Hapus</a>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Tidak ada data perlengkapan.</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="2" class="text-end">Total Harga (Halaman Ini)</th>
                                <th><?= formatRupiah($totalHargaHalaman) ?></th>
                                <th colspan="2"></th>
                            </tr>
                            <div class="alert alert-info fw-bold text-center">
                                TOTAL KESELURUHAN BIAYA PERLENGKAPAN: <?= formatRupiah($totalHargaGlobal) ?>
                            </div>
                        </tfoot>

                    </table>
                </div>

                <?php if ($total_pages > 1 || !empty($search_query)): ?>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <p class="mb-0 text-muted">
                            Menampilkan **<?= $perlengkapanResult->num_rows ?>** dari **<?= $total_rows ?>** total data.
                        </p>
                        <nav aria-label="Page navigation">
                            <?php displayPagination($page, $total_pages, $search_query); ?>
                        </nav>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Tutup koneksi database
$conn->close();
?>