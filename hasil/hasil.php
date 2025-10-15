<?php
// Pastikan koneksi.php sudah di-include
include '../koneksi.php';

// Fungsi untuk menghapus perlengkapan (logika yang sudah ada)
function deletePerlengkapan($id, $conn)
{
    $deleteQuery = "DELETE FROM perlengkapan WHERE id = '$id'";
    if ($conn->query($deleteQuery)) {
        echo "<script>alert('Perlengkapan berhasil dihapus'); window.location='perlengkapan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus perlengkapan'); window.location='perlengkapan.php';</script>";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    deletePerlengkapan($id, $conn);
}

// ----------------------------------------------------
// 1. Dapatkan Nilai Filter
// ----------------------------------------------------

// Mendapatkan nilai filter untuk Pulsa
$bulan_pulsa = isset($_GET['bulan_pulsa']) ? $_GET['bulan_pulsa'] : 'all';
$tahun_pulsa = isset($_GET['tahun_pulsa']) ? $_GET['tahun_pulsa'] : 'all';

// Mendapatkan nilai filter untuk Dokumen
$bulan_dokumen = isset($_GET['bulan_dokumen']) ? $_GET['bulan_dokumen'] : 'all';
$tahun_dokumen = isset($_GET['tahun_dokumen']) ? $_GET['tahun_dokumen'] : 'all';

// ----------------------------------------------------
// 2. Query dan Perhitungan untuk Pulsa (Filter Berdasarkan tanggal_lunas)
// ----------------------------------------------------

$query_pulsa = "SELECT * FROM pulsa WHERE status = 'Lunas'";
// MEMPERBARUI FILTER: Menggunakan kolom tanggal_lunas
if ($bulan_pulsa != 'all') $query_pulsa .= " AND MONTH(tanggal_lunas) = '$bulan_pulsa'";
if ($tahun_pulsa != 'all') $query_pulsa .= " AND YEAR(tanggal_lunas) = '$tahun_pulsa'";
$result_pulsa = $conn->query($query_pulsa);

// Perhitungan total Pulsa
$total_beli_pulsa = 0;
$total_bayar_pulsa = 0;
// Reset result pointer for new loop if needed, but in this structure, it's fine.
if ($result_pulsa) {
    while ($row = $result_pulsa->fetch_assoc()) {
        $total_beli_pulsa += $row['beli'];
        $total_bayar_pulsa += $row['bayar'];
    }
}
$selisih_bayar_pulsa = $total_bayar_pulsa - $total_beli_pulsa;

// ----------------------------------------------------
// 3. Query dan Perhitungan untuk Dokumen (Filter Berdasarkan tanggal_lunas)
// ----------------------------------------------------

$query_dokumen = "SELECT * FROM dokumen WHERE status = 'Lunas'";
// MEMPERBARUI FILTER: Menggunakan kolom tanggal_lunas
if ($bulan_dokumen != 'all') $query_dokumen .= " AND MONTH(tanggal_lunas) = '$bulan_dokumen'";
if ($tahun_dokumen != 'all') $query_dokumen .= " AND YEAR(tanggal_lunas) = '$tahun_dokumen'";
$result_dokumen = $conn->query($query_dokumen);

// Perhitungan total Dokumen
$total_bayar_dokumen = 0;
if ($result_dokumen) {
    while ($row = $result_dokumen->fetch_assoc()) {
        $total_bayar_dokumen += $row['bayar'];
    }
}

// ----------------------------------------------------
// 4. Query dan Perhitungan untuk Perlengkapan (Disinkronkan dengan Filter Dokumen)
// ----------------------------------------------------

// Fungsi untuk mendapatkan data perlengkapan dengan filter
function getPerlengkapanFiltered($conn, $bulan, $tahun)
{
    $query = "SELECT * FROM perlengkapan";
    // Filter perlengkapan (pengeluaran) berdasarkan tanggal_buat
    if ($bulan != 'all' && $tahun != 'all') {
        $query .= " WHERE MONTH(tanggal_buat) = '$bulan' AND YEAR(tanggal_buat) = '$tahun'";
    } else if ($bulan != 'all') {
        $query .= " WHERE MONTH(tanggal_buat) = '$bulan'";
    } else if ($tahun != 'all') {
        $query .= " WHERE YEAR(tanggal_buat) = '$tahun'";
    }
    return $conn->query($query);
}

$perlengkapanResult = getPerlengkapanFiltered($conn, $bulan_dokumen, $tahun_dokumen);

// Variabel untuk menghitung total harga perlengkapan
$totalHarga = 0;
$no = 1;

if ($perlengkapanResult && $perlengkapanResult->num_rows > 0) {
    while ($row = $perlengkapanResult->fetch_assoc()) {
        // Format harga dengan format Rupiah (meski tidak ditampilkan, variabel ini harus dihitung)
        $formattedHarga = "Rp " . number_format($row['harga'], 2, ',', '.');
        $totalHarga += $row['harga']; // Menambahkan harga ke total
        $tanggalBuat = date("d-m-Y", strtotime($row['tanggal_buat']));
    }
} else {
    // Tidak ada baris yang perlu ditampilkan di sini, hanya perhitungan yang penting
    // Logika HTML untuk "Tidak ada data tersedia" akan dipindahkan ke dalam tabel Dokumen
}

// Daftar nama bulan untuk dropdown
$bulan_nama = [
    1 => "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember"
];
$tahun_sekarang = date('Y');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pulsa dan Dokumen (Tanggal Lunas)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-responsive {
            margin-top: 1.5rem;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table-primary th {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-5 fw-bold text-primary">Laporan Hasil Pulsa dan Dokumen</h2>

        <!-- Filter dan Hasil untuk Pulsa -->
        <div class="mb-5 p-4 border rounded-3 bg-light">
            <h4 class="text-decoration-underline mb-3">Hasil Transaksi Pulsa (Berdasarkan Tanggal Lunas)</h4>

            <form action="" method="get" class="d-flex flex-wrap align-items-center gap-3 mb-4">
                <input type="hidden" name="bulan_dokumen" value="<?= htmlspecialchars($bulan_dokumen) ?>">
                <input type="hidden" name="tahun_dokumen" value="<?= htmlspecialchars($tahun_dokumen) ?>">

                <label class="fw-bold">Periode Lunas Pulsa:</label>
                <select name="bulan_pulsa" class="form-select w-auto rounded-pill">
                    <option value="all" <?= ($bulan_pulsa == 'all') ? 'selected' : '' ?>>Semua Bulan</option>
                    <?php
                    foreach ($bulan_nama as $key => $value) {
                        $selected = ($key == $bulan_pulsa) ? 'selected' : '';
                        echo "<option value='$key' $selected>$value</option>";
                    }
                    ?>
                </select>

                <select name="tahun_pulsa" class="form-select w-auto rounded-pill">
                    <option value="all" <?= ($tahun_pulsa == 'all') ? 'selected' : '' ?>>Semua Tahun</option>
                    <?php
                    for ($tahun = $tahun_sekarang - 5; $tahun <= $tahun_sekarang + 5; $tahun++) {
                        $selected = ($tahun == $tahun_pulsa) ? 'selected' : '';
                        echo "<option value='$tahun' $selected>$tahun</option>";
                    }
                    ?>
                </select>


                <button type="submit" class="btn btn-primary rounded-pill ms-auto">Tampilkan Hasil</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">Keterangan</th>
                            <th class="text-end">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Beli Pulsa (Modal)</td>
                            <td class="text-end text-danger"><?= number_format($total_beli_pulsa, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Total Bayar Pulsa (Penjualan)</td>
                            <td class="text-end text-success"><?= number_format($total_bayar_pulsa, 2, ',', '.') ?></td>
                        </tr>
                        <tr class="<?= ($selisih_bayar_pulsa >= 0) ? 'table-success' : 'table-danger' ?>">
                            <th class="fw-bold">Total Hasil Pulsa (Profit/Loss)</th>
                            <th class="text-end fw-bold"><?= number_format($selisih_bayar_pulsa, 2, ',', '.') ?></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <hr class="my-5">

        <!-- Filter dan Hasil untuk Dokumen -->
        <div class="p-4 border rounded-3 bg-light">
            <h4 class="text-decoration-underline mb-3">Hasil Transaksi Dokumen (Berdasarkan Tanggal Lunas)</h4>

            <form action="" method="get" class="d-flex flex-wrap align-items-center gap-3 mb-4">
                <input type="hidden" name="bulan_pulsa" value="<?= htmlspecialchars($bulan_pulsa) ?>">
                <input type="hidden" name="tahun_pulsa" value="<?= htmlspecialchars($tahun_pulsa) ?>">

                <label class="fw-bold">Periode Lunas Dokumen & Perlengkapan:</label>
                <select name="bulan_dokumen" class="form-select w-auto rounded-pill">
                    <option value="all" <?= ($bulan_dokumen == 'all') ? 'selected' : '' ?>>Semua Bulan</option>
                    <?php
                    foreach ($bulan_nama as $key => $value) {
                        $selected = ($key == $bulan_dokumen) ? 'selected' : '';
                        echo "<option value='$key' $selected>$value</option>";
                    }
                    ?>
                </select>

                <select name="tahun_dokumen" class="form-select w-auto rounded-pill">
                    <option value="all" <?= ($tahun_dokumen == 'all') ? 'selected' : '' ?>>Semua Tahun</option>
                    <?php
                    for ($tahun = $tahun_sekarang - 5; $tahun <= $tahun_sekarang + 5; $tahun++) {
                        $selected = ($tahun == $tahun_dokumen) ? 'selected' : '';
                        echo "<option value='$tahun' $selected>$tahun</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="btn btn-primary rounded-pill ms-auto">Tampilkan Hasil</button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">Keterangan</th>
                            <th class="text-end">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Perlengkapan Dokumen (Pengeluaran)</td>
                            <td class="text-end text-danger"><?= number_format($totalHarga, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Total Pendapatan Dokumen (Pemasukan)</td>
                            <td class="text-end text-success"><?= number_format($total_bayar_dokumen, 2, ',', '.') ?></td>
                        </tr>
                        <?php $hasil_dokumen = $total_bayar_dokumen - $totalHarga; ?>
                        <tr class="<?= ($hasil_dokumen >= 0) ? 'table-success' : 'table-danger' ?>">
                            <th class="fw-bold">Total Hasil Dokumen (Profit/Loss)</th>
                            <th class="text-end fw-bold"><?= number_format($hasil_dokumen, 2, ',', '.') ?></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tombol Kembali -->
        <div class="text-center mt-5 mb-5">
            <a href="../index.php" class="btn btn-secondary btn-lg w-100 rounded-pill shadow">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-left-circle-fill me-2" viewBox="0 0 16 16">
                    <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>