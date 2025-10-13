<?php
require_once 'koneksi.php'; // Sertakan file konfigurasi database

// Ambil total pendapatan dan pengeluaran untuk dashboard
$total_pendapatan = 0;
$total_pengeluaran = 0;

$sql_summary = "SELECT kategori, SUM(jumlah) AS total_jumlah FROM minuman GROUP BY kategori";
$result_summary = $conn->query($sql_summary);

if ($result_summary->num_rows > 0) {
    while ($row_summary = $result_summary->fetch_assoc()) {
        if ($row_summary['kategori'] == 'Pendapatan per Hari') {
            $total_pendapatan += $row_summary['total_jumlah'];
        } else {
            $total_pengeluaran += $row_summary['total_jumlah'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard minuman Keuangan</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Dashboard minuman Keuangan</h1>

        <nav class="main-nav">
            <a href="menu_beli_barang.php">Beli Barang</a>
            <a href="menu_pendapatan.php">Pendapatan per Hari</a>
            <a href="menu_pengeluaran.php">Pengeluaran Umum</a>
            <a href="menu_pln.php">PLN</a>
            <a href="menu_fauzan.php">Fauzan</a>
            <a href="menu_pribadi.php">Pribadi</a>
        </nav>

        <div class="summary">
            <h2>Ringkasan Keuangan</h2>
            <p>Total Pendapatan: <span style="color: green;">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></span></p>
            <p>Total Pengeluaran: <span style="color: red;">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></span></p>
            <p>Saldo Bersih: <span style="color: blue; font-weight: bold;">Rp <?php echo number_format($total_pendapatan - $total_pengeluaran, 0, ',', '.'); ?></span></p>
        </div>

        <h2>Riwayat minuman Terbaru</h2>
        <table class="notes-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ambil 10 minuman terbaru untuk dashboard
                $sql_latest = "SELECT id, tanggal, kategori, jumlah FROM minuman ORDER BY tanggal DESC LIMIT 10";
                $result_latest = $conn->query($sql_latest);

                if ($result_latest->num_rows > 0) {
                    while ($row_latest = $result_latest->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . date('d F Y H:i:s', strtotime($row_latest["tanggal"])) . "</td>";
                        echo "<td>" . htmlspecialchars($row_latest["kategori"]) . "</td>";
                        echo "<td>Rp " . number_format($row_latest["jumlah"], 0, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Belum ada minuman.</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </div>
</body>

</html>

<?php
$conn->close(); // Tutup koneksi database
?>