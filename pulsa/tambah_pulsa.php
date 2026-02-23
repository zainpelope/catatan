<?php
// Pastikan file koneksi.php sudah tersedia dan berfungsi
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Membersihkan dan mengambil data dari POST
    $nama = $_POST['nama'];
    $beli = (float) str_replace(['Rp ', '.'], '', $_POST['beli']);
    $bayar = (float) str_replace(['Rp ', '.'], '', $_POST['bayar']);
    $tanggal_input = $_POST['tanggal'];
    $status = $_POST['status'];

    // 2. Mengubah format input datetime-local menjadi format DATETIME database
    $tanggal_db = str_replace('T', ' ', $tanggal_input) . ':00';

    // --- START MODIFIKASI: LOGIKA TANGGAL LUNAS ---
    $tanggal_lunas_db = null; // Default: NULL (Kosong)

    // Jika Status yang dipilih adalah 'Lunas', maka tanggal lunas diisi dengan tanggal transaksi saat ini
    if ($status === 'Lunas') {
        $tanggal_lunas_db = $tanggal_db;
    }
    // Jika statusnya 'Hutang', maka tanggal_lunas_db tetap NULL
    // --- END MODIFIKASI ---

    // 3. Menggunakan Prepared Statement untuk Keamanan (Tambahkan kolom tanggal_lunas)
    // Parameter: s=string, d=double/decimal, s=string, s=string, s=string (untuk tanggal_lunas)
    $query = "INSERT INTO pulsa (nama, beli, bayar, tanggal, status, tanggal_lunas) VALUES (?, ?, ?, ?, ?, ?)";

    // Persiapan statement
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Binding parameter: s=nama, d=beli, d=bayar, s=tanggal, s=status, s=tanggal_lunas
    // Catatan: Jika tanggal_lunas_db adalah NULL, kita tetap bind sebagai 's' (string) 
    // karena MySQLi dapat menanganinya dengan parameter tipe string.
    $stmt->bind_param("sddsss", $nama, $beli, $bayar, $tanggal_db, $status, $tanggal_lunas_db);

    if ($stmt->execute()) {
        header('Location: pulsa.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pulsa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h3><i class="bi bi-plus-circle"></i> Tambah Pulsa</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama" required>
                            </div>

                            <div class="mb-3">
                                <label for="beli" class="form-label">Nominal Beli</label>
                                <input type="text" class="form-control" id="beli" name="beli" placeholder="Contoh: Rp 10.000" required>
                            </div>

                            <div class="mb-3">
                                <label for="bayar" class="form-label">Nominal Bayar</label>
                                <input type="text" class="form-control" id="bayar" name="bayar" placeholder="Contoh: Rp 12.000" required>
                            </div>

                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal dan Waktu Transaksi</label>
                                <input type="datetime-local" class="form-control" id="tanggal" name="tanggal" required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Lunas">Lunas</option>
                                    <option value="Hutang">Hutang</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan Data
                                </button>
                                <a href="pulsa.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left-circle"></i> Batal
                                </a>
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

        // Fungsi untuk format angka menjadi dua digit (misal 5 -> 05)
        const pad = (num) => String(num).padStart(2, '0');

        // Mendapatkan tanggal dan waktu saat ini
        const now = new Date();
        const year = now.getFullYear();
        const month = pad(now.getMonth() + 1);
        const day = pad(now.getDate());
        const hours = pad(now.getHours());
        const minutes = pad(now.getMinutes());

        // Format untuk datetime-local: YYYY-MM-DDTHH:MM
        const dateTimeLocal = `${year}-${month}-${day}T${hours}:${minutes}`;

        // Mengatur nilai input tanggal menjadi tanggal dan waktu sekarang
        inputTanggal.value = dateTimeLocal;

        // --- FUNGSI FORMAT RUPIAH ---

        // Fungsi untuk memformat nilai input menjadi format Rupiah
        function formatRupiah(value) {
            // Hapus semua karakter non-digit kecuali koma (jika digunakan untuk desimal)
            let numberString = value.replace(/[^0-9]/g, '').toString();

            // Format ribuan
            const parts = numberString.split('');
            let rupiah = '';
            let count = 0;
            for (let i = parts.length - 1; i >= 0; i--) {
                rupiah = parts[i] + rupiah;
                count++;
                if (count % 3 === 0 && i !== 0) {
                    rupiah = '.' + rupiah;
                }
            }

            return 'Rp ' + rupiah;
        }

        // Event listener untuk memformat input secara langsung saat pengguna mengetik
        document.getElementById('beli').addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value);
        });

        document.getElementById('bayar').addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value);
        });
    </script>
</body>

</html>