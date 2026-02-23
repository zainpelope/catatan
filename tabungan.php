<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Jakarta');

include 'koneksi.php';

// --- Ambil Parameter Filter ---
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Bangun string WHERE untuk filter tanggal
$filter_sql = "";
if ($bulan != '' && $tahun != '') {
    $filter_sql = " WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'";
} elseif ($tahun != '') {
    $filter_sql = " WHERE YEAR(tanggal) = '$tahun'";
}

// --- Logika Hapus ---
if (isset($_GET['hapus_tabungan'])) {
    $id = $_GET['hapus_tabungan'];
    $stmt = $conn->prepare("DELETE FROM tabungan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: tabungan.php?tab=masuk&bulan=$bulan&tahun=$tahun");
    exit();
}

if (isset($_GET['hapus_keluar'])) {
    $id = $_GET['hapus_keluar'];
    $stmt = $conn->prepare("DELETE FROM pengeluaran_tabungan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: tabungan.php?tab=keluar&bulan=$bulan&tahun=$tahun");
    exit();
}

// --- Handle Simpan ---
if (isset($_POST['simpan_tabungan'])) {
    $tanggal = $_POST['tanggal'];
    $fauzan = $_POST['fauzan'];
    $pln = $_POST['pln'];
    $pribadi = $_POST['pribadi'];
    $stmt = $conn->prepare("INSERT INTO tabungan (tanggal, fauzan, pln, pribadi) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siii", $tanggal, $fauzan, $pln, $pribadi);
    $stmt->execute();
    header("Location: tabungan.php?tab=masuk");
    exit();
}

if (isset($_POST['simpan_pengeluaran'])) {
    $tanggal = $_POST['tanggal'];
    $kategori = $_POST['kategori'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    $stmt = $conn->prepare("INSERT INTO pengeluaran_tabungan (tanggal, kategori, jumlah, keterangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $tanggal, $kategori, $jumlah, $keterangan);
    $stmt->execute();
    header("Location: tabungan.php?tab=keluar");
    exit();
}

// --- Data Rangkuman Saldo (Selalu Akumulasi Total) ---
$total = $conn->query("SELECT SUM(fauzan) AS fauzan, SUM(pln) AS pln, SUM(pribadi) AS pribadi FROM tabungan")->fetch_assoc();
$keluar_res = $conn->query("SELECT kategori, SUM(jumlah) AS total FROM pengeluaran_tabungan GROUP BY kategori");
$keluarData = ['fauzan' => 0, 'pln' => 0, 'pribadi' => 0];
while ($row = $keluar_res->fetch_assoc()) {
    $keluarData[$row['kategori']] = $row['total'];
}

$sisa = [
    'fauzan' => ($total['fauzan'] ?? 0) - ($keluarData['fauzan'] ?? 0),
    'pln' => ($total['pln'] ?? 0) - ($keluarData['pln'] ?? 0),
    'pribadi' => ($total['pribadi'] ?? 0) - ($keluarData['pribadi'] ?? 0),
];

// --- Logika Pagination ---
$limit = 10;
$page_masuk = isset($_GET['p_in']) ? (int)$_GET['p_in'] : 1;
$start_masuk = ($page_masuk - 1) * $limit;

$page_keluar = isset($_GET['p_out']) ? (int)$_GET['p_out'] : 1;
$start_keluar = ($page_keluar - 1) * $limit;

$total_masuk = $conn->query("SELECT COUNT(*) AS total FROM tabungan $filter_sql")->fetch_assoc()['total'];
$pages_masuk = ceil($total_masuk / $limit);

$total_keluar = $conn->query("SELECT COUNT(*) AS total FROM pengeluaran_tabungan $filter_sql")->fetch_assoc()['total'];
$pages_keluar = ceil($total_keluar / $limit);

$data_masuk = $conn->query("SELECT * FROM tabungan $filter_sql ORDER BY tanggal DESC LIMIT $start_masuk, $limit");
$data_keluar = $conn->query("SELECT * FROM pengeluaran_tabungan $filter_sql ORDER BY tanggal DESC LIMIT $start_keluar, $limit");

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'saldo';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tabungan Saya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
        }

        .container {
            margin-top: 30px;
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .nav-pills .nav-link {
            color: #666;
            border: 1px solid #ddd;
            margin: 0 5px;
        }

        .nav-pills .nav-link.active {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .card-saldo {
            border: none;
            border-top: 4px solid #0d6efd;
            transition: 0.3s;
        }

        .filter-box {
            background: #fdfdfd;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #eee;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="text-center mb-4 text-primary fw-bold">💰 Manajemen Tabungan</h2>

        <div class="d-flex justify-content-center gap-2 mb-4">
            <button class="btn btn-primary px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTabungan">+ Tabungan</button>
            <button class="btn btn-danger px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalPengeluaran">- Keluar</button>
            <a href="minuman.php" class="btn btn-outline-secondary shadow-sm">Kembali</a>
        </div>

        <ul class="nav nav-pills nav-justified mb-4" id="pills-tab">
            <li class="nav-item"><button class="nav-link <?= $active_tab == 'saldo' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#tab-saldo">Ringkasan Saldo</button></li>
            <li class="nav-item"><button class="nav-link <?= $active_tab == 'masuk' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#tab-masuk">Riwayat Masuk</button></li>
            <li class="nav-item"><button class="nav-link <?= $active_tab == 'keluar' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#tab-keluar">Riwayat Keluar</button></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade <?= $active_tab == 'saldo' ? 'show active' : '' ?>" id="tab-saldo">
                <div class="row g-3">
                    <?php foreach (['fauzan' => 'Fauzan', 'pln' => 'PLN', 'pribadi' => 'Pribadi'] as $key => $label): ?>
                        <div class="col-md-4 text-center">
                            <div class="card card-saldo shadow-sm p-4">
                                <span class="text-muted small fw-bold text-uppercase"><?= $label ?></span>
                                <h3 class="text-primary my-2 fw-bold">Rp <?= number_format($sisa[$key], 0, ',', '.') ?></h3>
                                <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                    <small class="text-success fw-bold">Masuk: <?= number_format($total[$key] ?? 0, 0, ',', '.') ?></small>
                                    <small class="text-danger fw-bold">Keluar: <?= number_format($keluarData[$key] ?? 0, 0, ',', '.') ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="tab-pane fade <?= $active_tab == 'masuk' ? 'show active' : '' ?>" id="tab-masuk">
                <div class="filter-box shadow-sm">
                    <form method="GET" class="row g-2 align-items-end">
                        <input type="hidden" name="tab" value="masuk">
                        <div class="col-md-4">
                            <label class="small fw-bold">Bulan</label>
                            <select name="bulan" class="form-select form-select-sm">
                                <option value="">-- Semua Bulan --</option>
                                <?php
                                $nama_bulan = [1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                foreach ($nama_bulan as $m => $nm) echo "<option value='$m' " . ($bulan == $m ? 'selected' : '') . ">$nm</option>";
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">Tahun</label>
                            <select name="tahun" class="form-select form-select-sm">
                                <?php
                                for ($y = date('Y'); $y >= 2025; $y--) echo "<option value='$y' " . ($tahun == $y ? 'selected' : '') . ">$y</option>";
                                ?>
                            </select>
                        </div>
                        <div class="col-md-5 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-dark w-100">Filter</button>
                            <a href="tabungan.php?tab=masuk" class="btn btn-sm btn-light border">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover border">
                        <thead class="table-dark text-center small">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Fauzan</th>
                                <th>PLN</th>
                                <th>Pribadi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-center small">
                            <?php $no = $start_masuk + 1;
                            while ($row = $data_masuk->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/y', strtotime($row['tanggal'])) ?></td>
                                    <td class="text-success fw-bold"><?= number_format($row['fauzan'], 0, ',', '.') ?></td>
                                    <td class="text-success fw-bold"><?= number_format($row['pln'], 0, ',', '.') ?></td>
                                    <td class="text-success fw-bold"><?= number_format($row['pribadi'], 0, ',', '.') ?></td>
                                    <td><a href="?hapus_tabungan=<?= $row['id'] ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-xs btn-outline-danger py-0" onclick="return confirm('Hapus?')">Hapus</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination pagination-sm justify-content-center">
                        <?php for ($i = 1; $i <= $pages_masuk; $i++): ?>
                            <li class="page-item <?= ($page_masuk == $i) ? 'active' : '' ?>"><a class="page-link" href="?tab=masuk&p_in=<?= $i ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"><?= $i ?></a></li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>

            <div class="tab-pane fade <?= $active_tab == 'keluar' ? 'show active' : '' ?>" id="tab-keluar">
                <div class="filter-box shadow-sm text-danger border-danger border-opacity-10">
                    <form method="GET" class="row g-2 align-items-end">
                        <input type="hidden" name="tab" value="keluar">
                        <div class="col-md-4">
                            <label class="small fw-bold">Bulan</label>
                            <select name="bulan" class="form-select form-select-sm">
                                <option value="">-- Semua Bulan --</option>
                                <?php foreach ($nama_bulan as $m => $nm) echo "<option value='$m' " . ($bulan == $m ? 'selected' : '') . ">$nm</option>"; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">Tahun</label>
                            <select name="tahun" class="form-select form-select-sm">
                                <?php for ($y = date('Y'); $y >= 2025; $y--) echo "<option value='$y' " . ($tahun == $y ? 'selected' : '') . ">$y</option>"; ?>
                            </select>
                        </div>
                        <div class="col-md-5 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-danger w-100">Filter</button>
                            <a href="tabungan.php?tab=keluar" class="btn btn-sm btn-light border text-dark">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover border">
                        <thead class="table-dark text-center small">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-center small">
                            <?php $no = $start_keluar + 1;
                            while ($row = $data_keluar->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/y', strtotime($row['tanggal'])) ?></td>
                                    <td><span class="badge bg-secondary"><?= ucfirst($row['kategori']) ?></span></td>
                                    <td class="text-danger fw-bold">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                    <td class="text-start"><?= htmlspecialchars($row['keterangan']) ?></td>
                                    <td><a href="?hapus_keluar=<?= $row['id'] ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-xs btn-outline-danger py-0" onclick="return confirm('Hapus?')">Hapus</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination pagination-sm justify-content-center">
                        <?php for ($i = 1; $i <= $pages_keluar; $i++): ?>
                            <li class="page-item <?= ($page_keluar == $i) ? 'active' : '' ?>"><a class="page-link" href="?tab=keluar&p_out=<?= $i ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"><?= $i ?></a></li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTabungan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow-lg border-0">
                <form method="post">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">Isi Tabungan</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3"><label class="form-label fw-bold small">Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                        <div class="mb-3"><label class="form-label fw-bold small">Fauzan (Rp)</label><input type="number" name="fauzan" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-bold small">PLN (Rp)</label><input type="number" name="pln" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-bold small">Pribadi (Rp)</label><input type="number" name="pribadi" class="form-control" required></div>
                    </div>
                    <div class="modal-footer border-0"><button type="submit" name="simpan_tabungan" class="btn btn-primary w-100 fw-bold">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPengeluaran" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow-lg border-0">
                <form method="post">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">Catat Pengeluaran</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3"><label class="form-label fw-bold small">Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                        <div class="mb-3"><label class="form-label fw-bold small">Kategori</label><select name="kategori" class="form-select">
                                <option value="fauzan">Fauzan</option>
                                <option value="pln">PLN</option>
                                <option value="pribadi">Pribadi</option>
                            </select></div>
                        <div class="mb-3"><label class="form-label fw-bold small">Jumlah (Rp)</label><input type="number" name="jumlah" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-bold small">Keterangan</label><input type="text" name="keterangan" class="form-control"></div>
                    </div>
                    <div class="modal-footer border-0"><button type="submit" name="simpan_pengeluaran" class="btn btn-danger w-100 fw-bold">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>