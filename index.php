<?php
session_start();

// Inisialisasi data
foreach (['pendapatan', 'pengeluaran', 'tabungan'] as $k) {
    if (!isset($_SESSION[$k])) $_SESSION[$k] = [];
}

// Fungsi bantu untuk mendapatkan nama hari otomatis dari tanggal
function getHariIndo($date) {
    if (!$date) return '-';
    $hari = date('l', strtotime($date));
    $daftar_hari = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
    ];
    return $daftar_hari[$hari] ?? $hari;
}

// --- LOGIKA PELUNASAN HUTANG ---
if (isset($_GET['lunasi_kat'])) {
    $kat = $_GET['lunasi_kat'];
    foreach ($_SESSION['pengeluaran'] as $key => $val) {
        if ($val['kategori_bayar'] == $kat && $val['jenis_transaksi'] == 'Hutang') {
            $_SESSION['pengeluaran'][$key]['sudah_dibayar'] = true;
        }
    }
    header("Location: index.php?tab=pengeluaran");
    exit();
}

// --- LOGIKA HAPUS ---
if (isset($_GET['hapus'])) {
    unset($_SESSION[$_GET['cat']][$_GET['hapus']]);
    $_SESSION[$_GET['cat']] = array_values($_SESSION[$_GET['cat']]);
    header("Location: index.php?tab=" . $_GET['cat']);
    exit();
}

// --- LOGIKA SIMPAN ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
    $jenis = $_POST['jenis'];
    $id_edit = $_POST['id_edit'];

    $data = [
        'tanggal' => $_POST['tanggal'],
        'nominal' => (int)$_POST['nominal'],
        'keterangan' => ($jenis == 'pengeluaran') ? ($_POST['keterangan_input'] ?: '-') : '-'
    ];

    if ($jenis == 'pengeluaran') {
        $kat = ($_POST['kat_pembayaran'] == 'Lainnya') ? $_POST['kat_pembayaran_lain'] : $_POST['kat_pembayaran'];
        $data['kategori_bayar'] = $kat;
        $data['jenis_transaksi'] = ($kat == 'Tunai') ? 'Tunai' : 'Hutang';
        $data['sudah_dibayar'] = ($kat == 'Tunai') ? true : false;
    }

    if ($id_edit == "-1") {
        $_SESSION[$jenis][] = $data;
    } else {
        if($jenis == 'pengeluaran') {
            $data['sudah_dibayar'] = $_SESSION[$jenis][$id_edit]['sudah_dibayar'];
        }
        $_SESSION[$jenis][$id_edit] = $data;
    }
    header("Location: index.php?tab=" . $jenis);
    exit();
}

// --- LOGIKA PAGINATION ---
function getPaginatedData($data, $page, $perPage = 10) {
    $total = count($data);
    $pages = ceil($total / $perPage);
    $offset = ($page - 1) * $perPage;
    $slice = array_slice($data, $offset, $perPage, true);
    return ['data' => $slice, 'total_pages' => $pages];
}

$active_tab = $_GET['tab'] ?? 'pendapatan';
$current_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;

// --- PERHITUNGAN DASHBOARD ---
$total_pendapatan = array_sum(array_column($_SESSION['pendapatan'], 'nominal'));
$total_tabungan = array_sum(array_column($_SESSION['tabungan'], 'nominal'));
$bayar_tunai = 0; $hutang_lunas = 0; $hutang_blm = 0; $rekap_hutang = [];

foreach ($_SESSION['pengeluaran'] as $p) {
    if ($p['jenis_transaksi'] == 'Tunai') {
        $bayar_tunai += $p['nominal'];
    } else {
        if ($p['sudah_dibayar']) { $hutang_lunas += $p['nominal']; }
        else {
            $hutang_blm += $p['nominal'];
            $rekap_hutang[$p['kategori_bayar']] = ($rekap_hutang[$p['kategori_bayar']] ?? 0) + $p['nominal'];
        }
    }
}
$total_keluar_tunai = $bayar_tunai + $hutang_lunas + $total_tabungan;
$hasil_bersih = $total_pendapatan - $total_keluar_tunai;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seblak Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; font-size: 0.85rem; }
        .card-custom { border: none; border-radius: 12px; color: white; padding: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.07); text-align: center; }
        .bg-hasil { background: #2c3e50; } .bg-pendapatan { background: #27ae60; } .bg-keluar { background: #2980b9; }
        .bg-lunas { background: #16a085; } .bg-blm { background: #c0392b; } .bg-tabungan { background: #8e44ad; }
        .table-card { background: white; border-radius: 15px; padding: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .nav-pills .nav-link.active { background: #333 !important; }
        .nav-link { color: #555; font-weight: bold; }
        .btn-close-custom { position: absolute; right: 15px; top: 15px; cursor: pointer; font-size: 1.5rem; font-weight: bold; color: white; text-decoration: none; opacity: 0.8; }
        .btn-close-custom:hover { color: #ff4d4d; opacity: 1; }
        .hari-badge { font-weight: bold; color: #333; display: block; }
        .tgl-sub { font-size: 0.75rem; color: #777; }
    </style>
</head>
<body>

<div class="container py-4">
    <h4 class="mb-4 fw-bold text-center">🌶️ KEUANGAN SEBLAK BASAH</h4>

    <div class="row g-2 mb-4">
        <div class="col-6 col-lg-4"><div class="card-custom bg-hasil"><small>SALDO AKHIR</small><h6>Rp <?= number_format($hasil_bersih) ?></h6></div></div>
        <div class="col-6 col-lg-4"><div class="card-custom bg-pendapatan"><small>TOTAL MASUK</small><h6>Rp <?= number_format($total_pendapatan) ?></h6></div></div>
        <div class="col-6 col-lg-4"><div class="card-custom bg-keluar"><small>KELUAR TUNAI</small><h6>Rp <?= number_format($total_keluar_tunai) ?></h6></div></div>
        <div class="col-6 col-lg-4"><div class="card-custom bg-lunas"><small>HUTANG LUNAS</small><h6>Rp <?= number_format($hutang_lunas) ?></h6></div></div>
        <div class="col-6 col-lg-4"><div class="card-custom bg-blm"><small>BELUM LUNAS</small><h6>Rp <?= number_format($hutang_blm) ?></h6></div></div>
        <div class="col-6 col-lg-4"><div class="card-custom bg-tabungan"><small>TABUNGAN</small><h6>Rp <?= number_format($total_tabungan) ?></h6></div></div>
    </div>

    <ul class="nav nav-pills mb-3 justify-content-center bg-white p-1 rounded-pill shadow-sm">
        <li class="nav-item"><a href="?tab=pendapatan" class="nav-link <?= $active_tab == 'pendapatan' ? 'active' : '' ?>">Masuk</a></li>
        <li class="nav-item"><a href="?tab=pengeluaran" class="nav-link <?= $active_tab == 'pengeluaran' ? 'active' : '' ?>">Keluar</a></li>
        <li class="nav-item"><a href="?tab=tabungan" class="nav-link <?= $active_tab == 'tabungan' ? 'active' : '' ?>">Tabung</a></li>
    </ul>

    <div class="table-card shadow-sm">
        <?php 
        $page_data = getPaginatedData($_SESSION[$active_tab], $current_page);
        $display_list = $page_data['data'];
        $total_pages = $page_data['total_pages'];
        ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold m-0">Riwayat <?= ucfirst($active_tab) ?></h6>
            <button class="btn btn-dark btn-sm rounded-pill px-3" onclick="openModal('<?= $active_tab ?>')">+ Tambah</button>
        </div>

        <?php if($active_tab == 'pengeluaran' && !empty($rekap_hutang)): ?>
            <div class="alert alert-warning py-2 small shadow-sm">
                <b class="d-block mb-1">Hutang Aktif:</b>
                <?php foreach($rekap_hutang as $k => $v): ?>
                    <div class="d-flex justify-content-between mb-1">
                        <span><?= $k ?>: Rp <?= number_format($v) ?></span>
                        <a href="?lunasi_kat=<?= urlencode($k) ?>" class="badge bg-success text-decoration-none" onclick="return confirm('Lunasi <?= $k ?>?')">LUNASI</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No.</th>
                        <th width="30%">Hari / Tanggal</th>
                        <?php if($active_tab == 'pengeluaran'): ?>
                            <th width="20%">Kategori</th>
                            <th width="25%">Keterangan</th>
                        <?php endif; ?>
                        <th>Nominal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($display_list)): ?>
                        <tr><td colspan="6" class="text-center text-muted small py-4">Belum ada data.</td></tr>
                    <?php else: ?>
                        <?php foreach($display_list as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <span class="hari-badge"><?= getHariIndo($r['tanggal']) ?></span>
                                <span class="tgl-sub"><?= date('d/m/Y', strtotime($r['tanggal'])) ?></span>
                            </td>
                            <?php if($active_tab == 'pengeluaran'): ?>
                                <td><span class="badge bg-secondary"><?= $r['kategori_bayar'] ?></span></td>
                                <td class="small"><?= $r['keterangan'] ?></td>
                            <?php endif; ?>
                            <td class="fw-bold text-dark">Rp <?= number_format($r['nominal']) ?></td>
                            <td class="text-end">
                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-info border-0" onclick='editData("<?= $active_tab ?>", <?= $i ?>, <?= json_encode($r) ?>)'>✎</a>
                                <a href="?hapus=<?= $i ?>&cat=<?= $active_tab ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus?')">×</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($total_pages > 1): ?>
        <nav><ul class="pagination pagination-sm justify-content-center mt-3">
            <?php for($p=1; $p<=$total_pages; $p++): ?>
                <li class="page-item <?= $p == $current_page ? 'active' : '' ?>">
                    <a class="page-link" href="?tab=<?= $active_tab ?>&p=<?= $p ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
        </ul></nav>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content shadow-lg border-0" method="POST" onsubmit="return confirm('Simpan data?')">
            <div class="modal-header bg-dark text-white position-relative">
                <h6 class="modal-title" id="mTitle">Input Data</h6>
                <a href="javascript:void(0)" class="btn-close-custom" data-bs-dismiss="modal">×</a>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="jenis" id="mJenis">
                <input type="hidden" name="id_edit" id="mId" value="-1">
                
                <div class="mb-3">
                    <label class="fw-bold small mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="mTanggal" class="form-control shadow-sm" value="<?= date('Y-m-d') ?>" required>
                </div>
                
                <div class="mb-3" id="mDivKet">
                    <label class="fw-bold small mb-1">Keterangan Barang/Toko</label>
                    <input type="text" name="keterangan_input" id="mKet" class="form-control shadow-sm" placeholder="Contoh: Beli Cabe, Bayar Gas">
                </div>

                <div class="mb-3">
                    <label class="fw-bold small mb-1">Nominal (Rp)</label>
                    <input type="number" name="nominal" id="mNominal" class="form-control shadow-sm" required>
                </div>
                
                <div id="mDivPengeluaran" class="d-none bg-light p-3 rounded">
                    <label class="fw-bold small mb-1">Kategori / Metode Bayar</label>
                    <select name="kat_pembayaran" id="mKat" class="form-select mb-2" onchange="toggleLain(this.value)">
                        <option value="Tunai">Tunai (Bayar Langsung)</option>
                        <option value="Pulsa">Pulsa</option>
                        <option value="Voucher">Voucher</option>
                        <option value="Es Krim">Es Krim</option>
                        <option value="Seblak Kering">Seblak Kering</option>
                        <option value="Minuman">Minuman</option>
                        <option value="Pribadi">Pribadi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <input type="text" name="kat_pembayaran_lain" id="mInputLain" class="form-control d-none shadow-sm" placeholder="Sebutkan kategorinya...">
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" name="simpan" class="btn btn-dark w-100 fw-bold py-2 shadow">SIMPAN DATA</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modal = new bootstrap.Modal(document.getElementById('modalForm'));

    function openModal(jenis) {
        document.getElementById('mId').value = "-1";
        document.getElementById('mJenis').value = jenis;
        document.getElementById('mTitle').innerText = "TAMBAH " + jenis.toUpperCase();
        document.getElementById('mNominal').value = "";
        document.getElementById('mKet').value = "";
        toggleUI(jenis);
        modal.show();
    }

    function editData(jenis, index, data) {
        document.getElementById('mId').value = index;
        document.getElementById('mJenis').value = jenis;
        document.getElementById('mTitle').innerText = "EDIT " + jenis.toUpperCase();
        document.getElementById('mTanggal').value = data.tanggal;
        document.getElementById('mNominal').value = data.nominal;
        document.getElementById('mKet').value = data.keterangan || "";

        if(jenis === 'pengeluaran') { 
            document.getElementById('mKat').value = data.kategori_bayar; 
        }
        
        toggleUI(jenis);
        modal.show();
    }

    function toggleUI(jenis) {
        // Form Hari dihapus total, digantikan otomatis oleh sistem
        document.getElementById('mDivKet').style.display = (jenis === 'pengeluaran') ? 'block' : 'none';
        document.getElementById('mDivPengeluaran').classList.toggle('d-none', jenis !== 'pengeluaran');
    }

    function toggleLain(val) { 
        document.getElementById('mInputLain').classList.toggle('d-none', val !== 'Lainnya'); 
    }
</script>
</body>
</html>