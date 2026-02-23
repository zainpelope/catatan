<?php
include '../koneksi.php';

// Logika Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM transaksi_konter WHERE id = $id");
    header("location:pengeluaran.php");
}

// Logika Tambah
if (isset($_POST['simpan'])) {
    $jml = $_POST['jumlah'];
    $tgl = $_POST['tanggal'];
    $ket = $_POST['keterangan'];
    $conn->query("INSERT INTO transaksi_konter (tanggal, keterangan, jumlah_pengeluaran, tipe) VALUES ('$tgl', '$ket', '$jml', 'keluar')");
    header("location:pengeluaran.php");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Data Pengeluaran</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background: #dc3545;
            color: white;
        }

        .btn-edit {
            color: blue;
            text-decoration: none;
            margin-right: 10px;
        }

        .btn-hapus {
            color: red;
            text-decoration: none;
        }

        .form-box {
            background: #fff5f5;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <a href="index.php">⬅ Dashboard</a>
    <h2>Data Pengeluaran Konter</h2>

    <div class="form-box">
        <form method="POST">
            <input type="number" name="jumlah" placeholder="Jumlah (Rp)" required>
            <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>">
            <input type="text" name="keterangan" placeholder="Keterangan (Beli sparepart, dll)" required>
            <button type="submit" name="simpan">Simpan</button>
        </form>
    </div>

    <table>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Jumlah</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        $data = $conn->query("SELECT * FROM transaksi_konter WHERE tipe='keluar' ORDER BY id DESC");
        while ($row = $data->fetch_assoc()) { ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                <td><?= $row['keterangan']; ?></td>
                <td>Rp <?= number_format($row['jumlah_pengeluaran'], 0, ',', '.'); ?></td>
                <td>
                    <a href="edit_keluar.php?id=<?= $row['id']; ?>" class="btn-edit">Edit</a>
                    <a href="pengeluaran.php?hapus=<?= $row['id']; ?>" class="btn-hapus" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>

</html>