<?php
include '../koneksi.php';

$id = $_GET['id'];
$sql = "SELECT * FROM perlengkapan WHERE id = $id";
$result = $conn->query($sql);
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $harga = str_replace(['Rp ', '.', ','], '', $_POST['harga']); // Menghapus format Rp
    $tanggal_buat = $_POST['tanggal_buat'];

    $sql = "UPDATE perlengkapan SET nama = '$nama', harga = '$harga', tanggal_buat = '$tanggal_buat' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: perlengkapan.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Perlengkapan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="card shadow">
                    <div class="card-header bg-warning text-white text-center">
                        <h3>Edit Perlengkapan</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?= $data['nama'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga</label>
                                <input type="text" class="form-control" id="harga" name="harga" value="<?= number_format($data['harga'], 0, ',', '.') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_buat" class="form-label">Tanggal Buat</label>
                                <input type="date" class="form-control" id="tanggal_buat" name="tanggal_buat" value="<?= $data['tanggal_buat'] ?>" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning btn-block">Update</button>
                                <a href="perlengkapan.php" class="btn btn-secondary btn-block mt-2">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk memformat input menjadi format Rupiah
        function formatRupiah(value) {
            const numberString = value.replace(/[^,\d]/g, '').toString();
            const split = numberString.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            const ribuan = split[0].substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                const separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return 'Rp ' + rupiah + (split[1] !== undefined ? ',' + split[1] : '');
        }

        // Fungsi untuk memformat input secara langsung saat pengguna mengetik
        document.getElementById('harga').addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value);
        });
    </script>
</body>

</html>