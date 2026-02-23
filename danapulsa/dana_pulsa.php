<?php
include '../koneksi.php';

// 2. LOGIKA HAPUS (DELETE)
if (isset($_GET['hapus_dana'])) {
    $id = $_GET['hapus_dana'];
    $conn->query("DELETE FROM dana WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
if (isset($_GET['hapus_keluar'])) {
    $id = $_GET['hapus_keluar'];
    $conn->query("DELETE FROM pengeluaran_dana WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// 3. LOGIKA SIMPAN (CREATE)
if (isset($_POST['simpan_dana'])) {
    $nama = $_POST['nama'];
    $tgl = $_POST['tanggal'];
    $nom = $_POST['nominal'];
    $conn->query("INSERT INTO dana (nama, tanggal, nominal) VALUES ('$nama', '$tgl', '$nom')");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
if (isset($_POST['simpan_pengeluaran'])) {
    $nama = $_POST['nama'];
    $tgl = $_POST['tanggal'];
    $jml = $_POST['jumlah'];
    $ket = $_POST['keterangan'];
    $conn->query("INSERT INTO pengeluaran_dana (nama, tanggal, jumlah, keterangan) VALUES ('$nama', '$tgl', '$jml', '$ket')");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// 4. LOGIKA EDIT (UPDATE)
if (isset($_POST['update_dana'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $tgl = $_POST['tanggal'];
    $nom = $_POST['nominal'];
    $conn->query("UPDATE dana SET nama='$nama', tanggal='$tgl', nominal='$nom' WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
if (isset($_POST['update_keluar'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $tgl = $_POST['tanggal'];
    $jml = $_POST['jumlah'];
    $ket = $_POST['keterangan'];
    $conn->query("UPDATE pengeluaran_dana SET nama='$nama', tanggal='$tgl', jumlah='$jml', keterangan='$ket' WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// 5. AMBIL DATA
$total_dana = $conn->query("SELECT SUM(nominal) as total FROM dana")->fetch_assoc()['total'] ?? 0;
$total_pengeluaran = $conn->query("SELECT SUM(jumlah) as total FROM pengeluaran_dana")->fetch_assoc()['total'] ?? 0;
$sisa_saldo = $total_dana - $total_pengeluaran;

$result_dana = $conn->query("SELECT * FROM dana ORDER BY tanggal DESC");
$result_pengeluaran = $conn->query("SELECT * FROM pengeluaran_dana ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dana Pulsa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-grad: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --danger-grad: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%);
            --success-grad: linear-gradient(135deg, #42e695 0%, #3bb2b8 100%);
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        .card-summary {
            border: none;
            border-radius: 20px;
            color: white;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .bg-grad-primary {
            background: var(--primary-grad);
        }

        .bg-grad-danger {
            background: var(--danger-grad);
        }

        .bg-grad-success {
            background: var(--success-grad);
        }

        .table-container {
            background: white;
            padding: 25px;
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            min-height: 400px;
        }

        .btn-rounded {
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
        }

        .form-control {
            border-radius: 12px;
            background: #f8f9fa;
            border: 1px solid #eee;
            padding: 12px;
        }

        /* Floating Buttons Mobile */
        .fab-container {
            position: fixed;
            bottom: 25px;
            right: 25px;
            display: none;
            z-index: 1050;
        }

        @media (max-width: 768px) {
            .fab-container {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .desktop-buttons {
                display: none;
            }

            .container {
                padding-bottom: 100px;
            }
        }
    </style>
</head>

<body>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
            <div>
                <h1 class="fw-bold mb-0 text-dark">Dana Pulsa</h1>
               
            </div>
            <div class="desktop-buttons">
                <button class="btn btn-primary btn-rounded shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#modalAddDana">
                    <i class="fas fa-plus-circle me-1"></i> Dana Masuk
                </button>
                <button class="btn btn-danger btn-rounded shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddKeluar">
                    <i class="fas fa-minus-circle me-1"></i> Pengeluaran
                </button>
            </div>
        </div>

        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card card-summary bg-grad-primary p-4 h-100">
                    <div class="small opacity-75 fw-bold mb-1 text-uppercase">Total Saldo Masuk</div>
                    <h2 class="fw-bold mb-0">Rp <?= number_format($total_dana, 0, ',', '.') ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-summary bg-grad-danger p-4 h-100">
                    <div class="small opacity-75 fw-bold mb-1 text-uppercase">Total Pengeluaran</div>
                    <h2 class="fw-bold mb-0">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-summary bg-grad-success p-4 h-100">
                    <div class="small opacity-75 fw-bold mb-1 text-uppercase">Sisa Saldo Saat Ini</div>
                    <h2 class="fw-bold mb-0">Rp <?= number_format($sisa_saldo, 0, ',', '.') ?></h2>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="table-container shadow-sm">
                    <h5 class="fw-bold text-primary mb-4"><i class="fas fa-arrow-alt-circle-down me-2"></i>History Pemasukan</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%;">Tanggal</th>
                                    <th>Nama</th>
                                    <th>Nominal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($d = $result_dana->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-muted small">
                                            <?= date('d/m/Y', strtotime($d['tanggal'])) ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= $d['nama'] ?></div>
                                        </td>
                                        <td class="text-success fw-bold">Rp <?= number_format($d['nominal'], 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-outline-warning btn-sm border-0 rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#editDana<?= $d['id'] ?>"><i class="fas fa-edit"></i></button>
                                                <a href="?hapus_dana=<?= $d['id'] ?>" class="btn btn-outline-danger btn-sm border-0 rounded-circle" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editDana<?= $d['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-warning text-white border-0">
                                                    <h5 class="modal-title fw-bold">Edit Dana Masuk</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="" method="POST">
                                                    <div class="modal-body p-4">
                                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Sumber Dana</label>
                                                            <input type="text" name="nama" class="form-control" value="<?= $d['nama'] ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Tanggal</label>
                                                            <input type="date" name="tanggal" class="form-control" value="<?= $d['tanggal'] ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Nominal (Rp)</label>
                                                            <input type="number" name="nominal" class="form-control" value="<?= $d['nominal'] ?>" required>
                                                        </div>
                                                        <button type="submit" name="update_dana" class="btn btn-warning w-100 btn-rounded text-white mt-3 shadow">Update Pemasukan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="table-container shadow-sm">
                    <h5 class="fw-bold text-danger mb-4"><i class="fas fa-arrow-alt-circle-up me-2"></i>History Pengeluaran</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%;">Tanggal</th>
                                    <th>Nama & Keterangan</th>
                                    <th>Jumlah</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($p = $result_pengeluaran->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-muted small">
                                            <?= date('d/m/Y', strtotime($p['tanggal'])) ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= $p['nama'] ?></div>
                                            <div class="text-muted x-small italic" style="font-size: 0.75rem;"><?= $p['keterangan'] ?></div>
                                        </td>
                                        <td class="text-danger fw-bold">Rp <?= number_format($p['jumlah'], 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-outline-warning btn-sm border-0 rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#editKeluar<?= $p['id'] ?>"><i class="fas fa-edit"></i></button>
                                                <a href="?hapus_keluar=<?= $p['id'] ?>" class="btn btn-outline-danger btn-sm border-0 rounded-circle" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editKeluar<?= $p['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-danger text-white border-0">
                                                    <h5 class="modal-title fw-bold">Edit Pengeluaran</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="" method="POST">
                                                    <div class="modal-body p-4">
                                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Keperluan</label>
                                                            <input type="text" name="nama" class="form-control" value="<?= $p['nama'] ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Tanggal</label>
                                                            <input type="date" name="tanggal" class="form-control" value="<?= $p['tanggal'] ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Jumlah (Rp)</label>
                                                            <input type="number" name="jumlah" class="form-control" value="<?= $p['jumlah'] ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Keterangan</label>
                                                            <textarea name="keterangan" class="form-control" rows="2"><?= $p['keterangan'] ?></textarea>
                                                        </div>
                                                        <button type="submit" name="update_keluar" class="btn btn-danger w-100 btn-rounded mt-3 shadow">Update Pengeluaran</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fab-container">
        <button class="btn btn-primary rounded-circle shadow-lg p-3" data-bs-toggle="modal" data-bs-target="#modalAddDana"><i class="fas fa-plus"></i></button>
        <button class="btn btn-danger rounded-circle shadow-lg p-3" data-bs-toggle="modal" data-bs-target="#modalAddKeluar"><i class="fas fa-minus"></i></button>
    </div>

    <div class="modal fade" id="modalAddDana" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold">Input Dana</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3"><label class="form-label fw-bold">Sumber Dana</label><input type="text" name="nama" class="form-control" placeholder="Nama" required></div>
                        <div class="mb-3"><label class="form-label fw-bold">Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                        <div class="mb-3"><label class="form-label fw-bold">Nominal (Rp)</label><input type="number" name="nominal" class="form-control" required></div>
                        <button type="submit" name="simpan_dana" class="btn btn-primary w-100 btn-rounded mt-3 shadow">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddKeluar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fw-bold">Input Pengeluaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3"><label class="form-label fw-bold">Nama</label><input type="text" name="nama" class="form-control" placeholder="Nama" required></div>
                        <div class="mb-3"><label class="form-label fw-bold">Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                        <div class="mb-3"><label class="form-label fw-bold">Jumlah (Rp)</label><input type="number" name="jumlah" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-bold">Keterangan</label><textarea name="keterangan" class="form-control" rows="2" placeholder="Detail opsional"></textarea></div>
                        <button type="submit" name="simpan_pengeluaran" class="btn btn-danger w-100 btn-rounded mt-3 shadow">Simpan Pengeluaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>