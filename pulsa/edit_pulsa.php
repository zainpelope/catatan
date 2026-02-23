<?php
include '../koneksi.php';

// =========================
// AMBIL DATA BERDASARKAN ID
// =========================
if (!isset($_GET['id'])) {
    header('Location: pulsa.php');
    exit;
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM pulsa WHERE id = '$id'");
$row = $result->fetch_assoc();

if (!$row) {
    echo "Data tidak ditemukan";
    exit;
}

// =========================
// PROSES UPDATE DATA
// =========================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $beli = (float) str_replace(['Rp ', '.'], '', $_POST['beli']);
    $bayar = (float) str_replace(['Rp ', '.'], '', $_POST['bayar']);
    $tanggal_input = $_POST['tanggal'];
    $status = $_POST['status'];

    // Ubah format datetime-local ke DATETIME
    $tanggal_db = str_replace('T', ' ', $tanggal_input) . ':00';

    // Logika tanggal lunas
    $tanggal_lunas_db = null;
    if ($status === 'Lunas') {
        $tanggal_lunas_db = $tanggal_db;
    }

    // Update data
    $query = "UPDATE pulsa 
              SET nama=?, beli=?, bayar=?, tanggal=?, status=?, tanggal_lunas=? 
              WHERE id=?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sddsssi",
        $nama,
        $beli,
        $bayar,
        $tanggal_db,
        $status,
        $tanggal_lunas_db,
        $id
    );

    if ($stmt->execute()) {
        header('Location: pulsa.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Pulsa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h3><i class="bi bi-pencil-square"></i> Edit Pulsa</h3>
                    </div>

                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">

                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama"
                                    value="<?= htmlspecialchars($row['nama']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nominal Beli</label>
                                <input type="text" class="form-control" id="beli" name="beli"
                                    value="Rp <?= number_format($row['beli'], 0, ',', '.') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nominal Bayar</label>
                                <input type="text" class="form-control" id="bayar" name="bayar"
                                    value="Rp <?= number_format($row['bayar'], 0, ',', '.') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal dan Waktu Transaksi</label>
                                <input type="datetime-local" class="form-control" name="tanggal"
                                    value="<?= date('Y-m-d\TH:i', strtotime($row['tanggal'])) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="Lunas" <?= $row['status'] == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                                    <option value="Hutang" <?= $row['status'] == 'Hutang' ? 'selected' : '' ?>>Hutang</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Data
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
        // ========================
        // FORMAT RUPIAH (SAMA)
        // ========================
        function formatRupiah(value) {
            let numberString = value.replace(/[^0-9]/g, '');
            let rupiah = '';
            let count = 0;

            for (let i = numberString.length - 1; i >= 0; i--) {
                rupiah = numberString[i] + rupiah;
                count++;
                if (count % 3 === 0 && i !== 0) {
                    rupiah = '.' + rupiah;
                }
            }
            return 'Rp ' + rupiah;
        }

        document.getElementById('beli').addEventListener('keyup', function() {
            this.value = formatRupiah(this.value);
        });

        document.getElementById('bayar').addEventListener('keyup', function() {
            this.value = formatRupiah(this.value);
        });
    </script>

</body>

</html>