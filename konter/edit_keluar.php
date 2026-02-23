<?php
include '../koneksi.php';

// Ambil ID dari URL
$id = $_GET['id'];
$query = $conn->query("SELECT * FROM transaksi_konter WHERE id = $id");
$data = $query->fetch_assoc();

if (isset($_POST['update'])) {
    $jml = $_POST['jumlah'];
    $tgl = $_POST['tanggal'];
    $ket = $_POST['keterangan'];

    $conn->query("UPDATE transaksi_konter SET jumlah_pengeluaran='$jml', tanggal='$tgl', keterangan='$ket' WHERE id=$id");
    header("location:index.php?halaman=pengeluaran");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Pengeluaran</title>
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
        <h3>Edit Pengeluaran</h3>
        <form method="POST">
            <label>Jumlah Pengeluaran (Rp):</label>
            <input type="number" name="jumlah" value="<?= $data['jumlah_pengeluaran'] ?>" required>

            <label>Tanggal:</label>
            <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" required>

            <label>Keterangan:</label>
            <textarea name="keterangan" rows="3" required><?= $data['keterangan'] ?></textarea>

            <button type="submit" name="update">Update Pengeluaran</button>
            <a href="index.php?halaman=pengeluaran" class="back">Batal</a>
        </form>
    </div>
</body>

</html>