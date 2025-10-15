<?php
include '../koneksi.php';

// ===================================
// 1. PENGATURAN UMUM
// ===================================
$limit = 10; // Jumlah data per halaman

// Fungsi Format Rupiah
function formatRupiah($angka)
{
    if (!is_numeric($angka)) {
        return "Rp 0,00";
    }
    // Pastikan hasil adalah selisih hutang (Beli - Bayar)
    return "Rp " . number_format($angka, 2, ',', '.');
}

// Fungsi Format Tanggal
function formatTanggal($tanggal)
{
    if (strtotime($tanggal) === false) {
        return "Tgl Invalid";
    }
    return date('d-m-Y', strtotime($tanggal));
}

// ===================================
// 2. LOGIKA PENCARIAN
// ===================================
$search_query = isset($_GET['cari']) ? $_GET['cari'] : '';
$where_clause = '';
if (!empty($search_query)) {
    $safe_search_query = $conn->real_escape_string($search_query);
    $where_clause = " AND nama LIKE '%" . $safe_search_query . "%'";
}

// ===================================
// 3. LOGIKA PAGINATION & QUERY PULSA
// ===================================
$pagePulsa = isset($_GET['halaman_pulsa']) ? max(1, (int)$_GET['halaman_pulsa']) : 1;
$startPulsa = ($pagePulsa * $limit) - $limit;

// Total Pulsa
$totalQueryPulsa = "SELECT COUNT(id) AS total FROM pulsa WHERE status = 'Hutang'" . $where_clause;
$totalResultPulsa = $conn->query($totalQueryPulsa);
$totalRowsPulsa = $totalResultPulsa->fetch_assoc()['total'];
$totalPagesPulsa = ceil($totalRowsPulsa / $limit);
// Penyesuaian halaman
if ($pagePulsa > $totalPagesPulsa && $totalPagesPulsa > 0) {
    $pagePulsa = $totalPagesPulsa;
    $startPulsa = ($pagePulsa * $limit) - $limit;
}

$queryPulsa = "SELECT * FROM pulsa WHERE status = 'Hutang'" . $where_clause . " ORDER BY tanggal DESC LIMIT $startPulsa, $limit";
$resultPulsa = $conn->query($queryPulsa);

$totalBeliPulsa = 0;
$totalBayarPulsa = 0;

// ===================================
// 4. LOGIKA PAGINATION & QUERY DOKUMEN
// ===================================
$pageDokumen = isset($_GET['halaman_dokumen']) ? max(1, (int)$_GET['halaman_dokumen']) : 1;
$startDokumen = ($pageDokumen * $limit) - $limit;

// Total Dokumen
$totalQueryDokumen = "SELECT COUNT(id) AS total FROM dokumen WHERE status = 'Hutang'" . $where_clause;
$totalResultDokumen = $conn->query($totalQueryDokumen);
$totalRowsDokumen = $totalResultDokumen->fetch_assoc()['total'];
$totalPagesDokumen = ceil($totalRowsDokumen / $limit);
// Penyesuaian halaman
if ($pageDokumen > $totalPagesDokumen && $totalPagesDokumen > 0) {
    $pageDokumen = $totalPagesDokumen;
    $startDokumen = ($pageDokumen * $limit) - $limit;
}

$queryDokumen = "SELECT * FROM dokumen WHERE status = 'Hutang'" . $where_clause . " ORDER BY tanggal DESC LIMIT $startDokumen, $limit";
$resultDokumen = $conn->query($queryDokumen);

$totalBayarDokumen = 0; // Untuk dokumen, kolom 'bayar' sudah mewakili jumlah yang harus dilunasi
$totalHutangPulsaGlobal = 0;
$totalHutangDokumenGlobal = 0;

// Re-query untuk menghitung total hutang global (tidak dipengaruhi LIMIT/OFFSET)
$globalQueryPulsa = "SELECT SUM(beli) AS total_beli, SUM(bayar) AS total_bayar FROM pulsa WHERE status = 'Hutang'";
$globalResultPulsa = $conn->query($globalQueryPulsa)->fetch_assoc();
$totalHutangPulsaGlobal = $globalResultPulsa['total_beli'] - $globalResultPulsa['total_bayar'];

$globalQueryDokumen = "SELECT SUM(bayar) AS total_bayar FROM dokumen WHERE status = 'Hutang'";
$globalResultDokumen = $conn->query($globalQueryDokumen)->fetch_assoc();
$totalHutangDokumenGlobal = $globalResultDokumen['total_bayar'];

$totalHutangGlobal = $totalHutangPulsaGlobal + $totalHutangDokumenGlobal;

/**
 * Fungsi untuk membuat tautan pagination
 * @param int $target_page Halaman yang dituju
 * @param string $current_page_param Parameter halaman saat ini (e.g., 'halaman_pulsa')
 * @param string $other_page_param Parameter halaman lain (e.g., 'halaman_dokumen')
 * @param int $other_page_value Nilai halaman lain
 * @param string $search_query Kata kunci pencarian
 * @return string URL yang sudah dienkode
 */
function createPaginationLink($target_page, $current_page_param, $other_page_param, $other_page_value, $search_query)
{
    $url = "?{$current_page_param}={$target_page}";
    // Pertahankan parameter pencarian jika ada
    if (!empty($search_query)) {
        $url .= "&cari=" . urlencode($search_query);
    }
    // Pertahankan parameter pagination tabel lain
    $url .= "&{$other_page_param}={$other_page_value}";
    return $url;
}

/**
 * Fungsi untuk menampilkan link pagination yang ramping
 */
function displayPagination($currentPage, $totalPages, $currentPageParam, $otherPageParam, $otherPageValue, $searchQuery)
{
    if ($totalPages <= 1) return;

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
    $prev_link = createPaginationLink($currentPage - 1, $currentPageParam, $otherPageParam, $otherPageValue, $searchQuery);
    $disabled = ($currentPage <= 1) ? 'disabled' : '';
    echo "<li class=\"page-item {$disabled}\"><a class=\"page-link\" href=\"{$prev_link}\">Previous</a></li>";

    // Tautan ke Halaman Pertama
    if ($start_display > $start_page) {
        $link = createPaginationLink(1, $currentPageParam, $otherPageParam, $otherPageValue, $searchQuery);
        echo "<li class=\"page-item\"><a class=\"page-link\" href=\"{$link}\">1</a></li>";
        if ($start_display > $start_page + 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Tautan di sekitar Halaman Aktif
    for ($i = $start_display; $i <= $end_display; $i++) {
        if ($i > $totalPages) break;
        $link = createPaginationLink($i, $currentPageParam, $otherPageParam, $otherPageValue, $searchQuery);
        $active = ($currentPage == $i) ? 'active' : '';
        echo "<li class=\"page-item {$active}\"><a class=\"page-link\" href=\"{$link}\">{$i}</a></li>";
    }

    // Tautan ke Halaman Terakhir
    if ($end_display < $end_page) {
        if ($end_display < $end_page - 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $link = createPaginationLink($totalPages, $currentPageParam, $otherPageParam, $otherPageValue, $searchQuery);
        echo "<li class=\"page-item\"><a class=\"page-link\" href=\"{$link}\">{$totalPages}</a></li>";
    }

    // Tombol Next
    $next_link = createPaginationLink($currentPage + 1, $currentPageParam, $otherPageParam, $otherPageValue, $searchQuery);
    $disabled = ($currentPage >= $totalPages) ? 'disabled' : '';
    echo "<li class=\"page-item {$disabled}\"><a class=\"page-link\" href=\"{$next_link}\">Next</a></li>";

    echo '</ul>';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Hutang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
        }

        h2,
        h4 {
            font-weight: 600;
        }

        .card {
            border-radius: 1rem;
        }

        .btn-sm {
            font-size: 0.8rem;
        }

        @media (max-width: 576px) {
            .btn-sm {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h2><i class="bi bi-cash-coin"></i> Data Hutang</h2>
            </div>
            <div class="card-body">

                <form action="" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="hidden" name="halaman_pulsa" value="<?= $pagePulsa ?>">
                        <input type="hidden" name="halaman_dokumen" value="<?= $pageDokumen ?>">

                        <input type="text" class="form-control" placeholder="Cari berdasarkan Nama..." name="cari"
                            value="<?= htmlspecialchars($search_query) ?>">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <?php if (!empty($search_query)): ?>
                            <a href="data_hutang.php?halaman_pulsa=<?= $pagePulsa ?>&halaman_dokumen=<?= $pageDokumen ?>" class="btn btn-outline-danger">
                                <i class="bi bi-x-lg"></i> Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </form>


                <h4 class="mt-3 text-primary"><i class="bi bi-phone"></i> Pulsa</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-striped align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama</th>
                                <th>Beli</th>
                                <th>Bayar</th>
                                <th>Sisa Hutang</th>
                                <th>Tanggal</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = $startPulsa + 1;
                            if ($resultPulsa->num_rows > 0) :
                                while ($row = $resultPulsa->fetch_assoc()) {
                                    $totalBeliPulsa += $row['beli'];
                                    $totalBayarPulsa += $row['bayar'];
                                    $sisaHutang = $row['beli'] - $row['bayar'];
                            ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td><?= formatRupiah($row['beli']) ?></td>
                                        <td><?= formatRupiah($row['bayar']) ?></td>
                                        <td class="fw-bold text-danger"><?= formatRupiah($sisaHutang) ?></td>
                                        <td><?= formatTanggal($row['tanggal']) ?></td>
                                        <td class="text-center">
                                            <form action="../hutang/update_status.php" method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="type" value="pulsa">
                                                <button type="submit" class="btn btn-success btn-sm"
                                                    onclick="return confirm('Yakin ingin melunasi hutang pulsa dari <?= htmlspecialchars($row['nama']) ?>?')">
                                                    <i class="bi bi-check-circle"></i> Lunasi
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php }
                            else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data hutang pulsa.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="2" class="text-end">Total (Halaman Ini)</td>
                                <td><?= formatRupiah($totalBeliPulsa) ?></td>
                                <td><?= formatRupiah($totalBayarPulsa) ?></td>
                                <td class="text-danger"><?= formatRupiah($totalBeliPulsa - $totalBayarPulsa) ?></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-5">
                    <p class="mb-0 text-muted">
                        Menampilkan **<?= $resultPulsa->num_rows ?>** dari **<?= $totalRowsPulsa ?>** data.
                    </p>
                    <nav aria-label="Pulsa page navigation">
                        <?php displayPagination($pagePulsa, $totalPagesPulsa, 'halaman_pulsa', 'halaman_dokumen', $pageDokumen, $search_query); ?>
                    </nav>
                </div>


                <h4 class="text-primary"><i class="bi bi-file-earmark-text"></i> Dokumen</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-striped align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Bayar</th>
                                <th>Keterangan</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = $startDokumen + 1;
                            if ($resultDokumen->num_rows > 0) :
                                while ($row = $resultDokumen->fetch_assoc()) {
                                    $totalBayarDokumen += $row['bayar'];
                            ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td><?= formatTanggal($row['tanggal']) ?></td>
                                        <td class="fw-bold text-danger"><?= formatRupiah($row['bayar']) ?></td>
                                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                        <td class="text-center">
                                            <form action="../hutang/update_status.php" method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="type" value="dokumen">
                                                <button type="submit" class="btn btn-success btn-sm"
                                                    onclick="return confirm('Yakin ingin melunasi hutang dokumen dari <?= htmlspecialchars($row['nama']) ?>?')">
                                                    <i class="bi bi-check-circle"></i> Lunasi
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php }
                            else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data hutang dokumen.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Total (Halaman Ini)</td>
                                <td class="text-danger"><?= formatRupiah($totalBayarDokumen) ?></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="mb-0 text-muted">
                        Menampilkan **<?= $resultDokumen->num_rows ?>** dari **<?= $totalRowsDokumen ?>** data.
                    </p>
                    <nav aria-label="Dokumen page navigation">
                        <?php displayPagination($pageDokumen, $totalPagesDokumen, 'halaman_dokumen', 'halaman_pulsa', $pagePulsa, $search_query); ?>
                    </nav>
                </div>

                <div class="alert alert-info fw-bold text-center">
                    TOTAL KESELURUHAN HUTANG: <?= formatRupiah($totalHutangGlobal) ?>
                    (Pulsa: <?= formatRupiah($totalHutangPulsaGlobal) ?> | Dokumen: <?= formatRupiah($totalHutangDokumenGlobal) ?>)
                </div>
                <div class="text-center">
                    <a href="../index.php" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-left-circle"></i> Kembali
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>