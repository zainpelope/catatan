<?php
include '../koneksi.php';

// --- LOGIKA SETTING ---
$harga_beli_traktor = 16500000;

// --- 1. PROSES DELETE ---
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    $table = $_GET['type'];
    if ($table == 'pendapatan_traktor' || $table == 'pengeluaran_traktor') {
        $conn->query("DELETE FROM $table WHERE id = $id");
    }
    header("Location: index.php");
    exit();
}

// --- 2. PROSES CREATE ---
if (isset($_POST['save_pendapatan'])) {
    $tgl = $_POST['tanggal'];
    $kotor = $_POST['kotor'];
    $musim = $_POST['musim'];
    $bersih = $_POST['bersih'];
    $conn->query("INSERT INTO pendapatan_traktor (tanggal, pendapatan_kotor, musim, pendapatan_bersih) VALUES ('$tgl', '$kotor', '$musim', '$bersih')");
    header("Location: index.php");
    exit();
}

if (isset($_POST['save_pengeluaran'])) {
    $tgl = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    $tgl_up = date('Y-m-d');
    $ket = $_POST['keterangan'];
    $conn->query("INSERT INTO pengeluaran_traktor (tanggal_transaksi, jumlah_pengeluaran, tanggal_update, keterangan) VALUES ('$tgl', '$jumlah', '$tgl_up', '$ket')");
    header("Location: index.php");
    exit();
}

// --- 3. PROSES UPDATE ---
if (isset($_POST['update_pendapatan'])) {
    $id = $_POST['id'];
    $tgl = $_POST['tanggal']; $kotor = $_POST['kotor']; $musim = $_POST['musim']; $bersih = $_POST['bersih'];
    $conn->query("UPDATE pendapatan_traktor SET tanggal='$tgl', pendapatan_kotor='$kotor', musim='$musim', pendapatan_bersih='$bersih' WHERE id=$id");
    header("Location: index.php");
    exit();
}

if (isset($_POST['update_pengeluaran'])) {
    $id = $_POST['id'];
    $tgl = $_POST['tanggal']; $jumlah = $_POST['jumlah']; $ket = $_POST['keterangan']; $tgl_up = date('Y-m-d');
    $conn->query("UPDATE pengeluaran_traktor SET tanggal_transaksi='$tgl', jumlah_pengeluaran='$jumlah', tanggal_update='$tgl_up', keterangan='$ket' WHERE id=$id");
    header("Location: index.php");
    exit();
}

// --- KALKULASI ---
$sql_p = $conn->query("SELECT SUM(pendapatan_kotor) as kotor, SUM(pendapatan_bersih) as bersih FROM pendapatan_traktor")->fetch_assoc();
$sql_e = $conn->query("SELECT SUM(jumlah_pengeluaran) as keluar FROM pengeluaran_traktor")->fetch_assoc();
$total_pendapatan_kotor = $sql_p['kotor'] ?? 0;
$total_pendapatan_bersih = $sql_p['bersih'] ?? 0;
$total_pengeluaran       = $sql_e['keluar'] ?? 0;

$hasil_kotor  = $total_pendapatan_kotor - $harga_beli_traktor;
$hasil_bersih = $total_pendapatan_bersih - $harga_beli_traktor;
$sisa_uang    = $total_pendapatan_bersih - $total_pengeluaran;

function formatRp($angka) {
    $tanda = $angka < 0 ? "- " : "";
    return $tanda . "Rp " . number_format(abs($angka), 0, ',', '.');
}
$tgl_sekarang = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uang Traktor</title>
    <style>
        :root { --primary: #2980b9; --success: #27ae60; --danger: #e74c3c; --warning: #f1c40f; --dark: #2c3e50; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; background: #f4f7f6; }
        
        .navbar { background: var(--dark); color: white; padding: 10px 15px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { margin: 0; font-size: 1.1rem; }
        .btn-back { background: #555; color: white; padding: 5px 12px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; border: 1px solid #777; }

        .header-sticky { position: sticky; top: 0; z-index: 10; background: #fff; padding: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .dashboard { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .card { padding: 8px; border-radius: 6px; color: white; text-align: center; }
        .card h4 { margin: 0; font-size: 0.6rem; opacity: 0.9; text-transform: uppercase; }
        .card p { margin: 4px 0 0; font-size: 0.8rem; font-weight: bold; }
        .card small { font-size: 0.55rem; display: block; opacity: 0.8; }
        
        .content { padding: 15px; }
        .tab-buttons { display: flex; gap: 8px; margin-bottom: 15px; }
        .tab-btn { flex: 1; padding: 12px; cursor: pointer; border: none; border-radius: 6px; font-weight: bold; background: #ddd; font-size: 0.8rem; }
        .tab-btn.active { background: var(--primary); color: white; }

        .table-section { display: none; background: white; padding: 10px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow-x: auto; }
        .table-section.active { display: block; }
        table { width: 100%; border-collapse: collapse; font-size: 0.75rem; min-width: 500px; }
        th, td { padding: 8px 5px; border: 1px solid #eee; text-align: left; }
        
        .btn { padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 0.75rem; font-weight: bold; }
        .btn-add { background: var(--success); color: white; margin-bottom: 10px; float: right; }
        .btn-edit { background: var(--warning); color: #333; }
        .btn-danger { background: var(--danger); color: white; display: inline-block; width: 20px; text-align: center; }

        .modal { display: none; position: fixed; z-index: 100; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); }
        .modal-content { background: white; margin: 10% auto; padding: 20px; width: 85%; max-width: 350px; border-radius: 12px; position: relative; }
        .close-x { position: absolute; right: 15px; top: 10px; font-size: 24px; cursor: pointer; color: #aaa; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; margin-bottom: 4px; font-weight: bold; font-size: 0.85rem; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        
        .bg-green { background: var(--success); } .bg-red { background: var(--danger); } 
        .bg-blue { background: var(--primary); } .bg-purple { background: #8e44ad; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Uang Traktor</h2>
        <a href="../index.php" class="btn-back">Kembali</a>
    </div>

    <div class="header-sticky">
        <div class="dashboard">
            <div class="card bg-green"><h4>P. Kotor</h4><p><?= formatRp($total_pendapatan_kotor) ?></p></div>
            <div class="card bg-green"><h4>P. Bersih</h4><p><?= formatRp($total_pendapatan_bersih) ?></p></div>
            <div class="card bg-red"><h4>Keluar</h4><p><?= formatRp($total_pengeluaran) ?></p></div>
            <div class="card bg-blue"><h4>H. Kotor</h4><p><?= formatRp($hasil_kotor) ?></p><small>Modal 16.5jt</small></div>
            <div class="card bg-blue"><h4>H. Bersih</h4><p><?= formatRp($hasil_bersih) ?></p><small>Modal 16.5jt</small></div>
            <div class="card bg-purple"><h4>KAS</h4><p><?= formatRp($sisa_uang) ?></p><small>Sisa Bersih</small></div>
        </div>
    </div>

    <div class="content">
        <div class="tab-buttons">
            <button class="tab-btn active" onclick="switchTab('pendapatan', this)">💰 PENDAPATAN</button>
            <button class="tab-btn" onclick="switchTab('pengeluaran', this)">💸 PENGELUARAN</button>
        </div>

        <div id="pendapatan" class="table-section active">
            <button class="btn btn-add" onclick="openModal('modalP1')">+ Tambah</button>
            <table>
                <tr style="background:#f9f9f9;">
                    <th>No</th>
                    <th>Tgl</th>
                    <th>Kotor</th> <th>Musim</th>
                    <th>Bersih</th>
                    <th>Aksi</th>
                </tr>
                <?php $res = $conn->query("SELECT * FROM pendapatan_traktor ORDER BY tanggal DESC"); $no = 1;
                while ($r = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/y', strtotime($r['tanggal'])) ?></td>
                    <td><?= formatRp($r['pendapatan_kotor']) ?></td> <td><?= $r['musim'] ?></td>
                    <td><?= formatRp($r['pendapatan_bersih']) ?></td>
                    <td>
                        <button class="btn btn-edit" onclick='editPendapatan(<?= json_encode($r) ?>)'>Edit</button>
                        <a href="index.php?action=delete&type=pendapatan_traktor&id=<?= $r['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus?')">&times;</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div id="pengeluaran" class="table-section">
            <button class="btn btn-add" onclick="openModal('modalP2')">+ Tambah</button>
            <table>
                <tr style="background:#f9f9f9;"><th>No</th><th>Tgl</th><th>Jumlah</th><th>Ket</th><th>Aksi</th></tr>
                <?php $res = $conn->query("SELECT * FROM pengeluaran_traktor ORDER BY tanggal_transaksi DESC"); $no = 1;
                while ($r = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/y', strtotime($r['tanggal_transaksi'])) ?></td>
                    <td><?= formatRp($r['jumlah_pengeluaran']) ?></td>
                    <td><?= $r['keterangan'] ?></td>
                    <td>
                        <button class="btn btn-edit" onclick='editPengeluaran(<?= json_encode($r) ?>)'>Edit</button>
                        <a href="index.php?action=delete&type=pengeluaran_traktor&id=<?= $r['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus?')">&times;</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <div id="modalP1" class="modal"><div class="modal-content">
        <span class="close-x" onclick="closeModal('modalP1')">&times;</span>
        <h3>Tambah Pendapatan</h3>
        <form method="POST">
            <div class="form-group"><label>Tanggal</label><input type="date" name="tanggal" value="<?= $tgl_sekarang ?>" required></div>
            <div class="form-group"><label>Kotor (Rp)</label><input type="number" name="kotor" placeholder="Rp" required></div>
            <div class="form-group"><label>Musim</label>
                <select name="musim" required>
                    <option value="Kemarau">Kemarau</option>
                    <option value="Penghujan">Penghujan</option>
                </select>
            </div>
            <div class="form-group"><label>Bersih (Rp)</label><input type="number" name="bersih" placeholder="Rp" required></div>
            <button type="submit" name="save_pendapatan" class="btn bg-green" style="color:white; width:100%; padding:12px;">Simpan</button>
        </form>
    </div></div>

    <div id="modalEditP1" class="modal"><div class="modal-content">
        <span class="close-x" onclick="closeModal('modalEditP1')">&times;</span>
        <h3>Edit Pendapatan</h3>
        <form method="POST">
            <input type="hidden" name="id" id="edit_p_id">
            <div class="form-group"><label>Tanggal</label><input type="date" name="tanggal" id="edit_p_tgl" required></div>
            <div class="form-group"><label>Kotor (Rp)</label><input type="number" name="kotor" id="edit_p_kotor" required></div>
            <div class="form-group"><label>Musim</label>
                <select name="musim" id="edit_p_musim" required>
                    <option value="Kemarau">Kemarau</option>
                    <option value="Penghujan">Penghujan</option>
                </select>
            </div>
            <div class="form-group"><label>Bersih (Rp)</label><input type="number" name="bersih" id="edit_p_bersih" required></div>
            <button type="submit" name="update_pendapatan" class="btn bg-blue" style="color:white; width:100%; padding:12px;">Update</button>
        </form>
    </div></div>

    <div id="modalP2" class="modal"><div class="modal-content">
        <span class="close-x" onclick="closeModal('modalP2')">&times;</span>
        <h3>Tambah Pengeluaran</h3>
        <form method="POST">
            <div class="form-group"><label>Tanggal</label><input type="date" name="tanggal" value="<?= $tgl_sekarang ?>" required></div>
            <div class="form-group"><label>Jumlah (Rp)</label><input type="number" name="jumlah" placeholder="Rp" required></div>
            <div class="form-group"><label>Keterangan</label><textarea name="keterangan" rows="3" style="width:100%; border:1px solid #ddd; border-radius:4px; padding:8px;"></textarea></div>
            <button type="submit" name="save_pengeluaran" class="btn bg-green" style="color:white; width:100%; padding:12px;">Simpan</button>
        </form>
    </div></div>

    <div id="modalEditP2" class="modal"><div class="modal-content">
        <span class="close-x" onclick="closeModal('modalEditP2')">&times;</span>
        <h3>Edit Pengeluaran</h3>
        <form method="POST">
            <input type="hidden" name="id" id="edit_e_id">
            <div class="form-group"><label>Tanggal</label><input type="date" name="tanggal" id="edit_e_tgl" required></div>
            <div class="form-group"><label>Jumlah (Rp)</label><input type="number" name="jumlah" id="edit_e_jumlah" required></div>
            <div class="form-group"><label>Keterangan</label><textarea name="keterangan" id="edit_e_ket" rows="3" style="width:100%; border:1px solid #ddd; border-radius:4px; padding:8px;"></textarea></div>
            <button type="submit" name="update_pengeluaran" class="btn bg-blue" style="color:white; width:100%; padding:12px;">Update</button>
        </form>
    </div></div>

    <script>
        function switchTab(tabName, btn) {
            document.querySelectorAll('.table-section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            btn.classList.add('active');
        }
        function openModal(id) { document.getElementById(id).style.display = "block"; }
        function closeModal(id) { document.getElementById(id).style.display = "none"; }
        function editPendapatan(data) {
            document.getElementById('edit_p_id').value = data.id;
            document.getElementById('edit_p_tgl').value = data.tanggal;
            document.getElementById('edit_p_kotor').value = data.pendapatan_kotor;
            document.getElementById('edit_p_musim').value = data.musim;
            document.getElementById('edit_p_bersih').value = data.pendapatan_bersih;
            openModal('modalEditP1');
        }
        function editPengeluaran(data) {
            document.getElementById('edit_e_id').value = data.id;
            document.getElementById('edit_e_tgl').value = data.tanggal_transaksi;
            document.getElementById('edit_e_jumlah').value = data.jumlah_pengeluaran;
            document.getElementById('edit_e_ket').value = data.keterangan;
            openModal('modalEditP2');
        }
        window.onclick = function(e) { if (e.target.className === 'modal') e.target.style.display = "none"; }
    </script>
</body>
</html>