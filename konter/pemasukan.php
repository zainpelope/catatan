<?php
include '../koneksi.php';

// Logika Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM transaksi_konter WHERE id = $id");
    header("location:pemasukan.php");
}

// Logika Tambah
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $tgl = $_POST['tanggal'];
    $bayar = $_POST['bayar'];
    $status = $_POST['status'];
    $conn->query("INSERT INTO transaksi_konter (nama, tanggal, bayar, status, tipe) VALUES ('$nama', '$tgl', '$bayar', '$status', 'masuk')");
    header("location:pemasukan.php");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Data Pemasukan</title>
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
            background: #28a745;
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
            background: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <a href="index.php">⬅ Dashboard</a>
    <h2>Data Pemasukan (Servis/Barang)</h2>

    <div class="form-box">
        <form method="POST">
            <input type="text" name="nama" placeholder="Nama Pelanggan" required>
            <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>">
            <input type="number" name="bayar" placeholder="Harga (Rp)" required>
            <select name="status">
                <option>Lunas</option>
                <option>Proses</option>
            </select>
            <button type="submit" name="simpan">Simpan</button>
        </form>
    </div>

    <table>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Tanggal</th>
            <th>Bayar</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        $data = $conn->query("SELECT * FROM transaksi_konter WHERE tipe='masuk' ORDER BY id DESC");
        while ($row = $data->fetch_assoc()) { ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['nama']; ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                <td>Rp <?= number_format($row['bayar'], 0, ',', '.'); ?></td>
                <td><?= $row['status']; ?></td>
                <td>
                    <a href="edit_masuk.php?id=<?= $row['id']; ?>" class="btn-edit">Edit</a>
                    <a href="pemasukan.php?hapus=<?= $row['id']; ?>" class="btn-hapus" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>

</html>