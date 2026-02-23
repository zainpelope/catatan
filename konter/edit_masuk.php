<?php
include '../koneksi.php';

// Ambil ID dari URL
$id = $_GET['id'];
$query = $conn->query("SELECT * FROM transaksi_konter WHERE id = $id");
$data = $query->fetch_assoc();

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $tgl = $_POST['tanggal'];
    $bayar = $_POST['bayar'];
    $status = $_POST['status'];
    // Menangkap input keterangan baru
    $keterangan = $_POST['keterangan'];

    // Update query termasuk kolom keterangan
    $conn->query("UPDATE transaksi_konter SET 
        nama='$nama', 
        tanggal='$tgl', 
        bayar='$bayar', 
        status='$status', 
        keterangan='$keterangan' 
        WHERE id=$id");

    header("location:index.php?halaman=pemasukan");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Pemasukan</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f4f7f6;
            display: flex;
            justify-content: center;
            padding-top: 50px;
        }

        .form-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        input,
        select,
        textarea,
        /* Tambahkan textarea di style */
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        textarea {
            height: 80px;
            resize: vertical;
        }

        button {
            background: #ffc107;
            color: black;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .back {
            text-align: center;
            display: block;
            text-decoration: none;
            color: #666;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="form-card">
        <h3>Edit Pemasukan Servis</h3>
        <form method="POST">
            <label>Nama Pelanggan:</label>
            <input type="text" name="nama" value="<?= $data['nama'] ?>" required>

            <label>Tanggal:</label>
            <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" required>

            <label>Jumlah Bayar (Rp):</label>
            <input type="number" name="bayar" value="<?= $data['bayar'] ?>" required>

            <label>Status:</label>
            <select name="status">
                <option value="Lunas" <?= ($data['status'] == 'Lunas') ? 'selected' : '' ?>>Lunas</option>
                <option value="Hutang" <?= ($data['status'] == 'Hutang') ? 'selected' : '' ?>>Hutang</option>
            </select>

            <label>Keterangan:</label>
            <textarea name="keterangan" placeholder="Contoh: Ganti LCD atau Service IC Power"><?= $data['keterangan'] ?></textarea>

            <button type="submit" name="update">Update Data</button>
            <a href="index.php?halaman=pemasukan" class="back">Batal</a>
        </form>
    </div>
</body>

</html>