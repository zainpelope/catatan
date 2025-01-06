<?php
include '../koneksi.php';

// Periksa jika ada parameter 'edit' di URL
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];

    // Ambil data catatan berdasarkan ID
    $query = "SELECT * FROM catatan WHERE id = '$id'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $nominal = $row['nominal'];
        $keterangan = $row['keterangan'];
        $tanggal = $row['tanggal'];
    } else {
        echo "Data tidak ditemukan!";
        exit();
    }
}

// Proses update data
if (isset($_POST['update'])) {
    $nominal = $_POST['nominal'];
    $keterangan = $_POST['keterangan'];
    $tanggal = $_POST['tanggal'];

    // Menghapus format Rp dan titik dari nominal sebelum menyimpan ke database
    $nominal = str_replace(['Rp', '.'], '', $nominal);

    // Query untuk mengupdate catatan
    $updateQuery = "UPDATE catatan SET nominal = '$nominal', keterangan = '$keterangan', 
                    tanggal = '$tanggal' WHERE id = '$id'";

    if ($conn->query($updateQuery)) {
        // Setelah berhasil update, redirect ke halaman utama
        echo "<script>alert('Catatan berhasil diperbarui'); window.location='catatan.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui catatan');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Catatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        // Fungsi untuk memformat input nominal menjadi Rp dengan pemisah ribuan
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }

        document.addEventListener('DOMContentLoaded', function() {
            var nominalInput = document.getElementById('nominal');
            nominalInput.addEventListener('input', function() {
                this.value = formatRupiah(this.value, 'Rp. ');
            });
        });
    </script>
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>Edit Catatan</h3>
            </div>
            <div class="card-body">
                <!-- Form untuk mengedit data catatan -->
                <form method="POST">
                    <div class="mb-3">
                        <label for="nominal" class="form-label">Nominal</label>
                        <input type="text" class="form-control" id="nominal" name="nominal"
                            value="<?php echo 'Rp. ' . number_format($nominal, 0, ',', '.'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="5"
                            required><?php echo $keterangan; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal"
                            value="<?php echo $tanggal; ?>" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                        <a href="../catatan/catatan.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-wpNFUeIJENdJtJ6H/0IrmyQX+RZsDdMfo5WPKNBoBo1Or4+CrDOxIxtNkz0nEJG4" crossorigin="anonymous"></script>
</body>

</html>

<?php
// Tutup koneksi
$conn->close();
?>