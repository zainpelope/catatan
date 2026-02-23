<?php
include '../koneksi.php';
if (isset($_POST['simpan'])) {
    $jml = $_POST['jumlah'];
    $tgl = $_POST['tanggal'];
    $ket = $_POST['keterangan'];

    $conn->query("INSERT INTO transaksi_konter (tanggal, keterangan, jumlah_pengeluaran, tipe) VALUES ('$tgl', '$ket', '$jml', 'keluar')");
    header("location:index.php?halaman=pengeluaran");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Catat Pengeluaran</title>
    <style>
        /* CSS sama dengan tambah_masuk.php */
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
        textarea,
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            background: #dc3545;
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
        <h3>Catat Pengeluaran Baru</h3>
        <form method="POST">
            <label>Jumlah Pengeluaran (Rp):</label>
            <input type="number" name="jumlah" required placeholder="17000">
            <label>Tanggal:</label>
            <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>
            <label>Keterangan:</label>
            <textarea name="keterangan" placeholder="Contoh: Beli baling-baling kipas" rows="3" required></textarea>
            <button type="submit" name="simpan">Simpan Pengeluaran</button>
            <a href="index.php?halaman=pengeluaran" class="back">Batal</a>
        </form>
    </div>
</body>

</html>