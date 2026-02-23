<?php
include '../koneksi.php';

// 1. LOGIKA HAPUS
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $hal = $_GET['halaman'];
    $conn->query("DELETE FROM transaksi_konter WHERE id = $id");
    header("location:index.php?halaman=$hal");
    exit;
}

// 2. LOGIKA LUNASI (Mengubah status Hutang menjadi Lunas)
if (isset($_GET['lunasi'])) {
    $id = $_GET['lunasi'];
    $conn->query("UPDATE transaksi_konter SET status = 'Lunas' WHERE id = $id");
    header("location:index.php?halaman=pemasukan");
    exit;
}

// 3. HITUNG RINGKASAN (Tampil Terus)
$total_p = $conn->query("SELECT SUM(bayar) as total FROM transaksi_konter WHERE tipe='masuk'")->fetch_assoc()['total'] ?? 0;
$total_k = $conn->query("SELECT SUM(jumlah_pengeluaran) as total FROM transaksi_konter WHERE tipe='keluar'")->fetch_assoc()['total'] ?? 0;
$sisa = $total_p - $total_k;

// 4. TENTUKAN HALAMAN YANG AKTIF
$halaman_aktif = isset($_GET['halaman']) ? $_GET['halaman'] : 'pemasukan';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background-color: #f4f4f4; border-bottom: 2px solid #ddd; font-family: sans-serif;">
        <h2 style="margin: 0; color: #333;">Manajemen Keuangan Konter</h2>

        <a href="../index.php" style="text-decoration: none; color: #333; font-size: 2rem; font-weight: bold; line-height: 0.8; transition: 0.3s;">
            &times;
        </a>
    </div>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 30px;
            background: #f4f7f6;
            color: #333;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #007bff;
        }

        .card h3 {
            margin: 0;
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
        }

        .card p {
            margin: 10px 0 0;
            font-size: 22px;
            font-weight: bold;
        }

        .nav-tabs {
            margin-bottom: 20px;
        }

        .btn-tab {
            padding: 12px 25px;
            text-decoration: none;
            background: #e0e0e0;
            color: #555;
            border-radius: 5px;
            margin-right: 10px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-tab.active {
            background: #007bff;
            color: white;
        }

        .container {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #eee;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #f8f9fa;
            color: #555;
        }

        .btn-tambah {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .btn-aksi {
            text-decoration: none;
            font-size: 11px;
            padding: 6px 10px;
            border-radius: 4px;
            margin-right: 5px;
            font-weight: bold;
            display: inline-block;
        }

        .edit {
            background: #ffc107;
            color: #212529;
        }

        .hapus {
            background: #dc3545;
            color: white;
        }

        .lunasi {
            background: #17a2b8;
            color: white;
        }

        .status-lunas {
            color: #28a745;
            font-weight: bold;
        }

        .status-hutang {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>

<body>


    <div class="summary-grid">
        <div class="card" style="border-top-color: #28a745;">
            <h3>Total Pendapatan</h3>
            <p>Rp <?= number_format($total_p, 0, ',', '.'); ?></p>
        </div>
        <div class="card" style="border-top-color: #dc3545;">
            <h3>Total Pengeluaran</h3>
            <p>Rp <?= number_format($total_k, 0, ',', '.'); ?></p>
        </div>
        <div class="card" style="border-top-color: #007bff;">
            <h3>Sisa Saldo (Profit)</h3>
            <p>Rp <?= number_format($sisa, 0, ',', '.'); ?></p>
        </div>
    </div>

    <div class="nav-tabs">
        <a href="index.php?halaman=pemasukan" class="btn-tab <?= ($halaman_aktif == 'pemasukan') ? 'active' : ''; ?>">Data Pemasukan</a>
        <a href="index.php?halaman=pengeluaran" class="btn-tab <?= ($halaman_aktif == 'pengeluaran') ? 'active' : ''; ?>">Data Pengeluaran</a>
    </div>

    <div class="container">

        <?php if ($halaman_aktif == 'pemasukan'): ?>
            <h3>📋 Daftar Pemasukan (Servis & Barang)</h3>
            <a href="tambah_masuk.php" class="btn-tambah">+ Tambah Pemasukan</a>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $data = $conn->query("SELECT * FROM transaksi_konter WHERE tipe='masuk' ORDER BY id DESC");
                    if ($data->num_rows > 0) {
                        while ($row = $data->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $row['nama']; ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                <td><?= $row['keterangan']; ?></td>
                                <td>Rp <?= number_format($row['bayar'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="<?= ($row['status'] == 'Hutang') ? 'status-hutang' : 'status-lunas'; ?>">
                                        <?= $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'Hutang'): ?>
                                        <a href="index.php?lunasi=<?= $row['id']; ?>" class="btn-aksi lunasi" onclick="return confirm('Konfirmasi pelunasan untuk pelanggan ini?')">Lunasi</a>
                                    <?php endif; ?>
                                    <a href="edit_masuk.php?id=<?= $row['id']; ?>" class="btn-aksi edit">Edit</a>



                                    <a href="index.php?halaman=pemasukan&hapus=<?= $row['id']; ?>" class="btn-aksi hapus" onclick="return confirm('Hapus data transaksi ini?')">Hapus</a>
                                </td>
                            </tr>
                    <?php }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center;'>Belum ada data pemasukan.</td></tr>";
                    } ?>
                </tbody>
            </table>

        <?php else: ?>
            <h3>💸 Daftar Pengeluaran</h3>
            <a href="tambah_keluar.php" class="btn-tambah" style="background:#dc3545;">+ Catat Pengeluaran</a>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Jumlah Pengeluaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $data = $conn->query("SELECT * FROM transaksi_konter WHERE tipe='keluar' ORDER BY id DESC");
                    if ($data->num_rows > 0) {
                        while ($row = $data->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                <td><?= $row['keterangan']; ?></td>
                                <td>Rp <?= number_format($row['jumlah_pengeluaran'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="edit_keluar.php?id=<?= $row['id']; ?>" class="btn-aksi edit">Edit</a>
                                    <a href="index.php?halaman=pengeluaran&hapus=<?= $row['id']; ?>" class="btn-aksi hapus" onclick="return confirm('Hapus data pengeluaran ini?')">Hapus</a>
                                </td>
                            </tr>
                    <?php }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center;'>Belum ada data pengeluaran.</td></tr>";
                    } ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>

</body>

</html>