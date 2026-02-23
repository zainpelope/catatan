<?php
include '../koneksi.php';
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $tgl = $_POST['tanggal'];
    $bayar = $_POST['bayar'];
    $status = $_POST['status'];
    // Menangkap input keterangan dari form
    $keterangan = $_POST['keterangan'];

    // Menambahkan kolom keterangan ke dalam query INSERT
    $conn->query("INSERT INTO transaksi_konter (nama, tanggal, bayar, status, keterangan, tipe) VALUES ('$nama', '$tgl', '$bayar', '$status', '$keterangan', 'masuk')");

    header("location:index.php?halaman=pemasukan");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Tambah Pemasukan</title>
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
        /* Tambahkan textarea di style agar seragam */
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
            background: #28a745;
            color: white;
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
        <h3>Tambah Pemasukan Servis</h3>
        <form method="POST">
            <label>Nama Pelanggan:</label>
            <input type="text" name="nama" required placeholder="Nama Pelanggan">

            <label>Tanggal:</label>
            <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>

            <label>Jumlah Bayar (Rp):</label>
            <input type="number" name="bayar" required placeholder="500000">

            <label>Status Pembayaran:</label>
            <select name="status">
                <option value="Lunas">Lunas</option>
                <option value="Hutang">Hutang</option>
            </select>

            <label>Keterangan:</label>
            <textarea name="keterangan" placeholder="Contoh: Ganti LCD atau Service IC Power"></textarea>

            <button type="submit" name="simpan">Simpan Data</button>
            <a href="index.php?halaman=pemasukan" class="back">Batal</a>
        </form>
    </div>
</body>

</html>