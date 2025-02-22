<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $bayar = str_replace(['Rp ', '.', ','], '', $_POST['bayar']); // Menghapus format Rp
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $status = $_POST['status'];

    $query = "INSERT INTO dokumen (nama, bayar, tanggal, keterangan, status) VALUES ('$nama', '$bayar', '$tanggal', '$keterangan', '$status')";
    if ($conn->query($query)) {
        header('Location: dokumen.php');
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
    <title>Tambah Dokumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Tambah Dokumen</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama dokumen" required>
                            </div>
                            <div class="mb-3">
                                <label for="bayar" class="form-label">Bayar</label>
                                <input type="text" class="form-control" id="bayar" name="bayar" placeholder="Masukkan nominal bayar" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Masukkan keterangan" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Lunas">Lunas</option>
                                    <option value="Hutang">Hutang</option>
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-block">Save</button>
                                <a href="dokumen.php" class="btn btn-secondary btn-block mt-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mendapatkan elemen input tanggal
        const inputTanggal = document.getElementById('tanggal');
        const today = new Date().toISOString().split('T')[0];
        inputTanggal.value = today;

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
        document.getElementById('bayar').addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value);
        });
    </script>
</body>

</html>