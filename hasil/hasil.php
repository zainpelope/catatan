<?php
include '../koneksi.php';

// Mendapatkan nilai filter untuk Pulsa
$bulan_pulsa = isset($_GET['bulan_pulsa']) ? $_GET['bulan_pulsa'] : 'all';
$tahun_pulsa = isset($_GET['tahun_pulsa']) ? $_GET['tahun_pulsa'] : 'all';

// Mendapatkan nilai filter untuk Dokumen
$bulan_dokumen = isset($_GET['bulan_dokumen']) ? $_GET['bulan_dokumen'] : 'all';
$tahun_dokumen = isset($_GET['tahun_dokumen']) ? $_GET['tahun_dokumen'] : 'all';

// Query untuk data Pulsa
$query_pulsa = "SELECT * FROM pulsa WHERE status = 'Lunas'";
if ($bulan_pulsa != 'all') $query_pulsa .= " AND MONTH(tanggal) = '$bulan_pulsa'";
if ($tahun_pulsa != 'all') $query_pulsa .= " AND YEAR(tanggal) = '$tahun_pulsa'";
$result_pulsa = $conn->query($query_pulsa);

// Query untuk data Dokumen
$query_dokumen = "SELECT * FROM dokumen WHERE status = 'Lunas'";
if ($bulan_dokumen != 'all') $query_dokumen .= " AND MONTH(tanggal) = '$bulan_dokumen'";
if ($tahun_dokumen != 'all') $query_dokumen .= " AND YEAR(tanggal) = '$tahun_dokumen'";
$result_dokumen = $conn->query($query_dokumen);

// Perhitungan total Pulsa
$total_beli_pulsa = 0;
$total_bayar_pulsa = 0;
while ($row = $result_pulsa->fetch_assoc()) {
    $total_beli_pulsa += $row['beli'];
    $total_bayar_pulsa += $row['bayar'];
}
$selisih_bayar_pulsa = $total_bayar_pulsa - $total_beli_pulsa;

// Perhitungan total Dokumen
$total_bayar_dokumen = 0;
while ($row = $result_dokumen->fetch_assoc()) {
    $total_bayar_dokumen += $row['bayar'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pulsa dan Dokumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Hasil Pulsa dan Dokumen</h2>

        <!-- Filter untuk Pulsa -->
        <div class="mb-5">
            <h4>Filter Pulsa</h4>
            <form action="" method="get" class="d-flex align-items-center gap-3">
                <select name="bulan_pulsa" class="form-select w-auto">
                    <option value="all" <?= ($bulan_pulsa == 'all') ? 'selected' : '' ?>>Semua Bulan</option>
                    <?php
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
                    foreach ($bulan_nama as $key => $value) {
                        $selected = ($key == $bulan_pulsa) ? 'selected' : '';
                        echo "<option value='$key' $selected>$value</option>";
                    }
                    ?>
                </select>

                <select name="tahun_pulsa" class="form-select w-auto">
                    <option value="all" <?= ($tahun_pulsa == 'all') ? 'selected' : '' ?>>Semua Tahun</option>
                    <?php
                    $tahun_sekarang = date('Y');
                    for ($tahun = 2024; $tahun <= $tahun_sekarang + 5; $tahun++) {
                        $selected = ($tahun == $tahun_pulsa) ? 'selected' : '';
                        echo "<option value='$tahun' $selected>$tahun</option>";
                    }
                    ?>
                </select>


                <button type="submit" class="btn btn-primary ms-auto">Tampilkan</button>
            </form>

            <div class="table-responsive mt-4">
                <h4 class="text-center">Hasil Pulsa</h4>
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>Keterangan</th>
                            <th>Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Beli Pulsa</td>
                            <td><?= number_format($total_beli_pulsa, 2) ?></td>
                        </tr>
                        <tr>
                            <td>Total Bayar Pulsa</td>
                            <td><?= number_format($total_bayar_pulsa, 2) ?></td>
                        </tr>
                        <tr>
                            <td>Total Hasil Pulsa</td>
                            <td><?= number_format($selisih_bayar_pulsa, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Filter untuk Dokumen -->
        <div>
            <h4>Filter Dokumen</h4>
            <form action="" method="get" class="d-flex align-items-center gap-3">
                <select name="bulan_dokumen" class="form-select w-auto">
                    <option value="all" <?= ($bulan_dokumen == 'all') ? 'selected' : '' ?>>Semua Bulan</option>
                    <?php
                    foreach ($bulan_nama as $key => $value) {
                        $selected = ($key == $bulan_dokumen) ? 'selected' : '';
                        echo "<option value='$key' $selected>$value</option>";
                    }
                    ?>
                </select>

                <select name="tahun_dokumen" class="form-select w-auto">
                    <option value="all" <?= ($tahun_dokumen == 'all') ? 'selected' : '' ?>>Semua Tahun</option>
                    <?php
                    for ($tahun = $tahun_sekarang - 5; $tahun <= $tahun_sekarang + 5; $tahun++) {
                        $selected = ($tahun == $tahun_dokumen) ? 'selected' : '';
                        echo "<option value='$tahun' $selected>$tahun</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="btn btn-primary ms-auto">Tampilkan</button>
            </form>

            <div class="table-responsive mt-4">
                <h4 class="text-center">Hasil Dokumen</h4>
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>Keterangan</th>
                            <th>Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Hasil Dokumen</td>
                            <td><?= number_format($total_bayar_dokumen, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tombol Kembali -->
        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-secondary btn-lg w-100">Kembali</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>