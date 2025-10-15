<?php
// Pastikan file koneksi.php sudah tersedia dan berfungsi
include '../koneksi.php';

// ===================================
// 1. PENGATURAN PAGINATION
// ===================================
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
// Pastikan halaman tidak kurang dari 1
$page = max(1, $page);
$start = ($page * $limit) - $limit; // Menghitung OFFSET

// ===================================
// 2. LOGIKA PENCARIAN
// ===================================
$search_query = isset($_GET['cari']) ? $_GET['cari'] : '';
$where_clause = '';

if (!empty($search_query)) {
    // Membersihkan input untuk keamanan
    $safe_search_query = $conn->real_escape_string($search_query);
    $where_clause = " WHERE nama LIKE '%" . $safe_search_query . "%'";
    // Reset start/page jika pencarian baru dilakukan (walaupun hidden input sudah disetel ke 1)
    if (!isset($_GET['halaman'])) {
        $start = 0;
        $page = 1;
    }
}

// ===================================
// 3. MENGHITUNG TOTAL BARIS (UNTUK PAGINATION)
// ===================================
$total_query = "SELECT COUNT(id) AS total FROM pulsa" . $where_clause;
$total_result = $conn->query($total_query);
$total_rows = $total_result->fetch_assoc()['total'];

// Pastikan total_rows tidak negatif
$total_rows = max(0, $total_rows);
$total_pages = ceil($total_rows / $limit); // Total halaman

// Jika halaman yang diminta melebihi total halaman, kembalikan ke halaman terakhir
if ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
    $start = ($page * $limit) - $limit;
}

// ===================================
// 4. QUERY DATA DENGAN LIMIT DAN OFFSET
// ===================================
// NOTE: Kita SELECT * untuk mendapatkan kolom 'tanggal_lunas' yang baru
$query = "SELECT * FROM pulsa" . $where_clause . " ORDER BY id DESC LIMIT $start, $limit";
$result = $conn->query($query);

// Cek jika query gagal (untuk debugging)
if ($result === false) {
    die("Error executing query: " . $conn->error);
}

// ===================================
// FUNGSI FORMAT
// ===================================
function formatRupiah($angka)
{
    if (!is_numeric($angka)) {
        return "Rp 0,00";
    }
    return "Rp " . number_format($angka, 2, ',', '.');
}

function formatTanggal($tanggal, $include_time = false)
{
    // Cek jika tanggal kosong, NULL, atau '0000-00-00 00:00:00'
    if (empty($tanggal) || $tanggal === '0000-00-00 00:00:00' || strtotime($tanggal) === false) {
        return "-"; // Tampilkan strip jika tidak ada tanggal
    }

    // Format standar: d-m-Y
    $format = "d-m-Y";

    // Jika include_time true, tambahkan format jam dan menit
    if ($include_time) {
        $format = "d-m-Y H:i";
    }

    return date($format, strtotime($tanggal));
}

$total_beli = 0;
$total_bayar = 0; // Variabel ini akan menghitung total dari data yang **ditampilkan** di halaman saat ini.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Pulsa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
        }

        .card {
            border-radius: 1rem;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-sm {
            font-size: 0.8rem;
        }

        @media (max-width: 576px) {
            .btn-group {
                flex-direction: column;
                gap: 0.3rem;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3><i class="bi bi-phone"></i> Data Pulsa</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between mb-4 gap-2">
                    <a href="tambah_pulsa.php" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah Pulsa
                    </a>
                    <a href="../index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Kembali
                    </a>
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
                            <a href="pulsa.php" class="btn btn-outline-danger">
                                <i class="bi bi-x-lg"></i> Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama</th>
                                <th>Beli</th>
                                <th>Bayar</th>
                                <th>Tanggal Transaksi</th>
                                <th>Status</th>
                                <th style="width: 15%;">Tanggal Lunas</th> <!-- KOLOM BARU -->
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = $start + 1; // Nomor urut disesuaikan dengan halaman 
                            ?>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <?php
                                    $total_beli += $row['beli'];
                                    $total_bayar += $row['bayar'];

                                    // Ambil tanggal lunas dari kolom baru
                                    $tanggal_lunas = $row['tanggal_lunas'] ?? null;
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td><?= formatRupiah($row['beli']) ?></td>
                                        <td><?= formatRupiah($row['bayar']) ?></td>
                                        <td><?= formatTanggal($row['tanggal']) ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $row['status'] === 'Lunas' ? 'success' : 'danger' ?>">
                                                <i class="bi bi-<?= $row['status'] === 'Lunas' ? 'check-circle' : 'x-circle' ?>"></i>
                                                <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <!-- TAMPILKAN TANGGAL LUNAS DENGAN WAKTU -->
                                            <?= formatTanggal($tanggal_lunas, true) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group d-flex justify-content-center" role="group">
                                                <a href="edit_pulsa.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm me-1">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                                <a href="delete_pulsa.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </a>
                                                <!-- Tombol Lunasi (Hanya muncul jika status Belum Lunas) -->
                                                <?php if ($row['status'] !== 'Lunas'): ?>
                                                    <form method="POST" action="../hutang/update_status.php" style="display:inline;"
                                                        onsubmit="return confirm('Yakin ingin melunasi transaksi ini?');">
                                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                        <input type="hidden" name="type" value="pulsa">
                                                        <button type="submit" class="btn btn-primary btn-sm ms-1">
                                                            <i class="bi bi-cash"></i> Lunasi
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <tr class="table-light fw-bold">
                                    <td colspan="2" class="text-end">Total (Halaman Ini)</td>
                                    <td><?= formatRupiah($total_beli) ?></td>
                                    <td><?= formatRupiah($total_bayar) ?></td>
                                    <td colspan="4"></td> <!-- Tambahan 1 kolom colspan untuk Tanggal Lunas -->
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center"> <!-- Tambahan 1 kolom colspan -->
                                        <?php if (!empty($search_query)): ?>
                                            Data **<?= htmlspecialchars($search_query) ?>** tidak ditemukan.
                                        <?php else: ?>
                                            Tidak ada data pulsa.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): // Tampilkan pagination jika lebih dari 1 halaman 
                ?>
                    <div class="d-flex justify-content-between align-items-center mt-4">

                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <?php
                                // Logika untuk menampilkan navigasi halaman yang ramping
                                $current_page = $page;
                                $start_page = 1;
                                $end_page = $total_pages;
                                $max_links = 5; // Jumlah maksimal link nomor halaman di sekitar halaman aktif
                                $range = floor($max_links / 2);

                                $start_display = $current_page - $range;
                                $end_display = $current_page + $range;

                                // Penyesuaian jika di dekat awal
                                if ($start_display < $start_page) {
                                    $end_display += ($start_page - $start_display);
                                    $start_display = $start_page;
                                }

                                // Penyesuaian jika di dekat akhir
                                if ($end_display > $end_page) {
                                    $start_display -= ($end_display - $end_page);
                                    $end_display = $end_page;
                                }

                                // Pastikan tidak di bawah 1 (setelah penyesuaian)
                                if ($start_display < $start_page) {
                                    $start_display = $start_page;
                                }

                                // Fungsi helper untuk membuat URL dengan parameter cari
                                $url_params = !empty($search_query) ? '&cari=' . urlencode($search_query) : '';
                                ?>

                                <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link"
                                        href="?halaman=<?= $current_page - 1 ?><?= $url_params ?>">
                                        Previous
                                    </a>
                                </li>

                                <?php
                                // 1. Tautan ke Halaman Pertama (jika halaman aktif jauh dari awal)
                                if ($start_display > $start_page) {
                                    // Tautan ke halaman 1
                                    echo '<li class="page-item"><a class="page-link" href="?halaman=1' . $url_params . '">1</a></li>';
                                    // Ellipsis (titik-titik)
                                    if ($start_display > $start_page + 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }

                                // 2. Tautan di sekitar Halaman Aktif
                                for ($i = $start_display; $i <= $end_display; $i++) {
                                    if ($i > $total_pages) break;
                                ?>
                                    <li class="page-item <?= ($current_page == $i) ? 'active' : '' ?>">
                                        <a class="page-link"
                                            href="?halaman=<?= $i ?><?= $url_params ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php
                                }

                                // 3. Tautan ke Halaman Terakhir (jika halaman aktif jauh dari akhir)
                                if ($end_display < $end_page) {
                                    // Ellipsis (titik-titik)
                                    if ($end_display < $end_page - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    // Tautan ke halaman terakhir
                                    echo '<li class="page-item"><a class="page-link" href="?halaman=' . $total_pages . $url_params . '">' . $total_pages . '</a></li>';
                                }
                                ?>

                                <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                                    <a class="page-link"
                                        href="?halaman=<?= $current_page + 1 ?><?= $url_params ?>">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>