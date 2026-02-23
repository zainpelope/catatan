<?php
include '../koneksi.php';

// --- LOGIKA 1: PROSES INPUT, UPDATE & DELETE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tab_aktif = "beli";

    if (isset($_POST['simpan_beli'])) {
        $tgl = $_POST['tanggal_beli'];
        $jumlah = str_replace('.', '', $_POST['jumlah_beli']);
        $ket = $_POST['keterangan_beli'];
        if (!empty($_POST['id_edit'])) {
            $id = $_POST['id_edit'];
            $conn->query("UPDATE data_beli SET tanggal='$tgl', jumlah_beli='$jumlah', keterangan='$ket' WHERE id='$id'");
        } else {
            $conn->query("INSERT INTO data_beli (tanggal, jumlah_beli, keterangan) VALUES ('$tgl', '$jumlah', '$ket')");
        }
        $tab_aktif = "beli";
    }

    if (isset($_POST['simpan_laku'])) {
        $tgl = $_POST['tanggal_laku'];
        $jumlah = str_replace('.', '', $_POST['jumlah_laku']);
        if (!empty($_POST['id_edit'])) {
            $id = $_POST['id_edit'];
            $conn->query("UPDATE data_laku SET tanggal='$tgl', jumlah_laku='$jumlah' WHERE id='$id'");
        } else {
            $conn->query("INSERT INTO data_laku (tanggal, jumlah_laku) VALUES ('$tgl', '$jumlah')");
        }
        $tab_aktif = "laku";
    }

    if (isset($_POST['simpan_hutang'])) {
        $nm = $_POST['nama'];
        $tgl = $_POST['tanggal'];
        $st = $_POST['status'];
        $ht = str_replace('.', '', $_POST['hutang']);
        if (!empty($_POST['id_edit'])) {
            $id = $_POST['id_edit'];
            $conn->query("UPDATE daftar_hutang SET nama='$nm', tanggal='$tgl', status='$st', nominal_hutang='$ht' WHERE id='$id'");
        } else {
            $conn->query("INSERT INTO daftar_hutang (nama, tanggal, status, nominal_hutang) VALUES ('$nm', '$tgl', '$st', '$ht')");
            if ($st == 'Lunas') {
                $conn->query("INSERT INTO data_laku (tanggal, jumlah_laku) VALUES ('$tgl', '$ht')");
            }
        }
        $tab_aktif = "hutang";
    }

    if (isset($_POST['proses_lunasi'])) {
        $id_htg = $_POST['id_hutang'];
        $data = $conn->query("SELECT * FROM daftar_hutang WHERE id = '$id_htg'")->fetch_assoc();
        $conn->query("UPDATE daftar_hutang SET status = 'Lunas' WHERE id = '$id_htg'");
        $conn->query("INSERT INTO data_laku (tanggal, jumlah_laku) VALUES ('" . date('Y-m-d') . "', '{$data['nominal_hutang']}')");
        $tab_aktif = "hutang";
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?tab=" . $tab_aktif);
    exit();
}

if (isset($_GET['hapus']) && isset($_GET['id']) && isset($_GET['tab'])) {
    $id = $_GET['id'];
    $tabel = "";
    if ($_GET['hapus'] == 'beli') $tabel = "data_beli";
    if ($_GET['hapus'] == 'laku') $tabel = "data_laku";
    if ($_GET['hapus'] == 'hutang') $tabel = "daftar_hutang";

    if ($tabel != "") $conn->query("DELETE FROM $tabel WHERE id = '$id'");
    header("Location: " . $_SERVER['PHP_SELF'] . "?tab=" . $_GET['tab']);
    exit();
}

// --- PERHITUNGAN TOTAL ---
$current_tab = $_GET['tab'] ?? 'beli';

$q_beli = $conn->query("SELECT SUM(jumlah_beli) as total FROM data_beli");
$total_beli = $q_beli->fetch_assoc()['total'] ?? 0;

$q_laku = $conn->query("SELECT SUM(jumlah_laku) as total FROM data_laku");
$total_laku = $q_laku->fetch_assoc()['total'] ?? 0;

$q_hutang = $conn->query("SELECT SUM(nominal_hutang) as total FROM daftar_hutang WHERE status = 'Hutang'");
$total_hutang = $q_hutang->fetch_assoc()['total'] ?? 0;

$hasil_akhir = $total_laku - $total_beli;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Voucher - Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #c9ceb9ff;
            margin: 0;
            padding: 15px;
            font-size: 13px;
        }

        .wrapper {
            max-width: 1000px;
            margin: auto;
            position: relative;
        }

        /* --- STATS DASHBOARD --- */
        .stats-container {
            display: grid;
            /* 2 kolom di HP, 4 kolom di Desktop */
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        @media (min-width: 768px) {
            .stats-container {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .stat-card {
            background: white;
            padding: 12px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .stat-card .label {
            display: block;
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-card .value {
            font-size: 14px;
            font-weight: bold;
        }

        .text-beli {
            color: #ef4444;
        }

        .text-laku {
            color: #10b981;
        }

        .text-hutang {
            color: #f59e0b;
        }

        .hasil-box {
            background: #1e293b !important;
            color: #10b981 !important;
        }

        /* --- NAVIGATION --- */
        .nav-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 15px;
        }

        .nav-btn {
            flex: 1;
            padding: 12px 5px;
            border: none;
            background: white;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 600;
            color: #64748b;
            font-size: 11px;
        }

        @media (min-width: 768px) {
            .nav-btn {
                font-size: 13px;
            }
        }

        .nav-btn.active {
            background: #416fb9ff;
            color: white;
        }

        .card {
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: none;
        }

        .card.active {
            display: block;
        }

        .btn-add {
            background: #10b981;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 15px;
        }

        /* --- TABLE RESPONSIVE --- */
        .overflow-x {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 450px;
            /* Supaya kolom tidak berhimpitan di HP */
        }

        th,
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
        }

        /* --- MODAL / DIALOG --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
        }

        .modal-content {
            background: white;
            margin: 15% auto;
            padding: 20px;
            width: 85%;
            max-width: 380px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .btn-submit {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }

        .btn-close-page {
            position: absolute;
            top: -5px;
            right: 0;
            background: #ef4444;
            color: white;
            text-decoration: none;
            width: 30px;
            height: 30px;
            line-height: 28px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            border: 2px solid white;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }

        .bg-red {
            background: #ef4444;
        }

        .bg-green {
            background: #10b981;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <a href="../index.php" class="btn-close-page">&times;</a>

        <div class="stats-container">
            <div class="stat-card">
                <span class="label">Beli</span>
                <span class="value text-beli">Rp <?= number_format($total_beli, 0, ',', '.') ?></span>
            </div>
            <div class="stat-card">
                <span class="label">Laku</span>
                <span class="value text-laku">Rp <?= number_format($total_laku, 0, ',', '.') ?></span>
            </div>
            <div class="stat-card">
                <span class="label">Hutang</span>
                <span class="value text-hutang">Rp <?= number_format($total_hutang, 0, ',', '.') ?></span>
            </div>
            <div class="stat-card hasil-box">
                <span class="label" style="color:#94a3b8">Hasil</span>
                <span class="value">Rp <?= number_format($hasil_akhir, 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="nav-tabs">
            <button class="nav-btn <?= $current_tab == 'beli' ? 'active' : '' ?>" onclick="switchPage('beli')">Beli</button>
            <button class="nav-btn <?= $current_tab == 'laku' ? 'active' : '' ?>" onclick="switchPage('laku')">Laku</button>
            <button class="nav-btn <?= $current_tab == 'hutang' ? 'active' : '' ?>" onclick="switchPage('hutang')">Hutang</button>
        </div>

  <div id="page-beli" class="card <?= $current_tab == 'beli' ? 'active' : '' ?>">
    <button class="btn-add" onclick="openModal('modal-beli')">+ Stok Beli</button>
    <div class="overflow-x">
        <table>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nominal</th>
                <th>Keterangan</th> <th>Aksi</th>
            </tr>
            <?php $res = $conn->query("SELECT * FROM data_beli ORDER BY id DESC");
            $no = 1;
            while ($row = $res->fetch_assoc()) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/y', strtotime($row['tanggal'])) ?></td>
                    <td><?= number_format($row['jumlah_beli'], 0, ',', '.') ?></td>
                    <td><small><?= htmlspecialchars($row['keterangan']) ?></small></td> <td>
                        <a href="javascript:void(0)" class="text-beli" onclick="editBeli('<?= $row['id'] ?>','<?= $row['tanggal'] ?>','<?= $row['jumlah_beli'] ?>','<?= addslashes($row['keterangan']) ?>')">Edit</a> |
                        <a href="?hapus=beli&id=<?= $row['id'] ?>&tab=beli" class="btn-hapus" onclick="return confirm('Hapus?')">Hapus</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

        <div id="page-laku" class="card <?= $current_tab == 'laku' ? 'active' : '' ?>">
            <button class="btn-add" onclick="openModal('modal-laku')">+ Stok Laku</button>
            <div class="overflow-x">
                <table>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nominal</th>
                        <th>Aksi</th>
                    </tr>
                    <?php $res = $conn->query("SELECT * FROM data_laku ORDER BY id DESC");
                    $no = 1;
                    while ($row = $res->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/y', strtotime($row['tanggal'])) ?></td>
                            <td><?= number_format($row['jumlah_laku'], 0, ',', '.') ?></td>
                            <td>
                                <a href="javascript:void(0)" class="text-beli" onclick="editLaku('<?= $row['id'] ?>','<?= $row['tanggal'] ?>','<?= $row['jumlah_laku'] ?>')">Edit</a> |
                                <a href="?hapus=laku&id=<?= $row['id'] ?>&tab=laku" class="btn-hapus" onclick="return confirm('Hapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <div id="page-hutang" class="card <?= $current_tab == 'hutang' ? 'active' : '' ?>">
            <button class="btn-add" onclick="openModal('modal-hutang')">+ Hutang Baru</button>
            <div class="overflow-x">
                <table>
                    <tr>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Nominal</th>
                        <th>Aksi</th>
                    </tr>
                    <?php $res = $conn->query("SELECT * FROM daftar_hutang ORDER BY id DESC");
                    while ($row = $res->fetch_assoc()) {
                        $htg = ($row['status'] == 'Hutang'); ?>
                        <tr>
                            <td><?= $row['nama'] ?><br><small><?= date('d/m/y', strtotime($row['tanggal'])) ?></small></td>
                            <td><span class="badge <?= $htg ? 'bg-red' : 'bg-green' ?>"><?= $row['status'] ?></span></td>
                            <td><?= number_format($row['nominal_hutang'], 0, ',', '.') ?></td>
                            <td>
                                <a href="javascript:void(0)" class="text-beli" onclick="editHutang('<?= $row['id'] ?>','<?= $row['nama'] ?>','<?= $row['tanggal'] ?>','<?= $row['status'] ?>','<?= $row['nominal_hutang'] ?>')">Edit</a> |

                                <a href="?hapus=hutang&id=<?= $row['id'] ?>&tab=hutang" class="btn-hapus" onclick="return confirm('Hapus data hutang ini?')">Hapus</a>

                                <?php if ($htg) { ?>
                                    | <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id_hutang" value="<?= $row['id'] ?>">
                                        <button type="submit" name="proses_lunasi" class="text-laku" style="background:none; border:none; cursor:pointer; font-weight:bold; padding:0;">Lunasi</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>

    <div id="modal-beli" class="modal">
        <div class="modal-content">
            <form method="POST">
                <h3 id="head-beli">Tambah Beli</h3>
                <input type="hidden" name="id_edit" id="eb-id">
                <input type="date" name="tanggal_beli" id="eb-tgl" value="<?= date('Y-m-d') ?>" required>
                <input type="text" name="jumlah_beli" id="eb-jml" class="rupiah" placeholder="Nominal" required>
                <textarea name="keterangan_beli" id="eb-ket" placeholder="Keterangan"></textarea>
                <button type="submit" name="simpan_beli" class="btn-submit">Simpan</button>
                <button type="button" onclick="closeModal('modal-beli')" style="background:none; border:none; width:100%; color:gray; cursor:pointer; margin-top:10px;">Batal</button>
            </form>
        </div>
    </div>

    <div id="modal-laku" class="modal">
        <div class="modal-content">
            <form method="POST">
                <h3 id="head-laku">Tambah Laku</h3>
                <input type="hidden" name="id_edit" id="el-id">
                <input type="date" name="tanggal_laku" id="el-tgl" value="<?= date('Y-m-d') ?>" required>
                <input type="text" name="jumlah_laku" id="el-jml" class="rupiah" placeholder="Nominal" required>
                <button type="submit" name="simpan_laku" class="btn-submit">Simpan</button>
                <button type="button" onclick="closeModal('modal-laku')" style="background:none; border:none; width:100%; color:gray; cursor:pointer; margin-top:10px;">Batal</button>
            </form>
        </div>
    </div>

    <div id="modal-hutang" class="modal">
        <div class="modal-content">
            <form method="POST">
                <h3 id="head-hutang">Tambah Hutang</h3>
                <input type="hidden" name="id_edit" id="eh-id">
                <input type="text" name="nama" id="eh-nama" placeholder="Nama" required>
                <input type="date" name="tanggal" id="eh-tgl" value="<?= date('Y-m-d') ?>" required>
                <select name="status" id="eh-st">
                    <option value="Hutang">Hutang</option>
                    <option value="Lunas">Lunas</option>
                </select>
                <input type="text" name="hutang" id="eh-jml" class="rupiah" placeholder="Nominal" required>
                <button type="submit" name="simpan_hutang" class="btn-submit">Simpan</button>
                <button type="button" onclick="closeModal('modal-hutang')" style="background:none; border:none; width:100%; color:gray; cursor:pointer; margin-top:10px;">Batal</button>
            </form>
        </div>
    </div>

    <script>
        function switchPage(p) {
            document.querySelectorAll('.card').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('page-' + p).classList.add('active');

            // Mencari button yang aktif
            const btns = document.querySelectorAll('.nav-btn');
            btns.forEach(btn => {
                if (btn.getAttribute('onclick').includes(p)) btn.classList.add('active');
            });
        }

        function openModal(id) {
            document.getElementById(id).style.display = "block";
        }

        function closeModal(id) {
            document.getElementById(id).style.display = "none";
            // Reset form saat tutup
            if (id === 'modal-beli') document.getElementById('head-beli').innerText = 'Tambah Beli';
            if (id === 'modal-laku') document.getElementById('head-laku').innerText = 'Tambah Laku';
            if (id === 'modal-hutang') document.getElementById('head-hutang').innerText = 'Tambah Hutang';
        }

        function editBeli(id, tgl, jml, ket) {
            document.getElementById('eb-id').value = id;
            document.getElementById('eb-tgl').value = tgl;
            document.getElementById('eb-jml').value = new Intl.NumberFormat('id-ID').format(jml);
            document.getElementById('eb-ket').value = ket;
            document.getElementById('head-beli').innerText = 'Edit Beli';
            openModal('modal-beli');
        }

        function editLaku(id, tgl, jml) {
            document.getElementById('el-id').value = id;
            document.getElementById('el-tgl').value = tgl;
            document.getElementById('el-jml').value = new Intl.NumberFormat('id-ID').format(jml);
            document.getElementById('head-laku').innerText = 'Edit Laku';
            openModal('modal-laku');
        }

        function editHutang(id, nama, tgl, st, jml) {
            document.getElementById('eh-id').value = id;
            document.getElementById('eh-nama').value = nama;
            document.getElementById('eh-tgl').value = tgl;
            document.getElementById('eh-st').value = st;
            document.getElementById('eh-jml').value = new Intl.NumberFormat('id-ID').format(jml);
            document.getElementById('head-hutang').innerText = 'Edit Hutang';
            openModal('modal-hutang');
        }

        // Format Rupiah Otomatis
        document.querySelectorAll('.rupiah').forEach(i => {
            i.addEventListener('keyup', function() {
                let v = this.value.replace(/\D/g, '');
                this.value = new Intl.NumberFormat('id-ID').format(v);
            });
        });

        // Menutup modal jika klik di luar box modal
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = "none";
            }
        }
    </script>
</body>

</html>