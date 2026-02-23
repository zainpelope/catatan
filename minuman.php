<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Keuangan Modern</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4caf50;
            --danger: #ef233c;
            --warning: #f7b731;
            --info: #4895ef;
            --dark: #2b2d42;
            --light: #f8f9fa;
            --card-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            --glass: rgba(255, 255, 255, 0.95);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            background-image: radial-gradient(at 0% 0%, hsla(253, 16%, 7%, 1) 0, transparent 50%),
                radial-gradient(at 50% 0%, hsla(225, 39%, 30%, 1) 0, transparent 50%),
                radial-gradient(at 100% 0%, hsla(339, 49%, 30%, 1) 0, transparent 50%);
            background-attachment: fixed;
            background-size: cover;
            min-height: 100vh;
            color: var(--dark);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        header h1 {
            font-size: 2.5rem;
            margin: 0;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        header p {
            opacity: 0.8;
            font-size: 1.1rem;
        }

        /* Navigasi / Buttons */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }

        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-add {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-add:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            background: var(--primary);
            color: white;
        }

        /* Grid System */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }

        .card {
            background: var(--glass);
            border-radius: 24px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .card-header i {
            font-size: 1.5rem;
            color: var(--primary);
        }

        .card-header h3 {
            margin: 0;
            font-size: 1.25rem;
            color: var(--dark);
        }

        /* Data Styling */
        .data-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .data-row:last-child {
            border-bottom: none;
        }

        .data-label {
            color: var(--light-text-color);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }

        .data-value {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .total-row {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9ff;
            border-radius: 15px;
            border-left: 4px solid var(--primary);
        }

        .highlight-red .data-value {
            color: var(--danger);
        }

        .highlight-green .data-value {
            color: var(--success);
        }

        .balance-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }

        .balance-card .card-header i,
        .balance-card .card-header h3 {
            color: white;
        }

        .balance-card .data-row {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        .balance-card .total-row {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-group {
                justify-content: center;
            }

            header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <header>
            <h1><i class="fas fa-wallet"></i> Ringkasan Keuangan</h1>

        </header>

        <div class="action-bar">
            <a href="index.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Menu Utama
            </a>
            <div class="btn-group">
                <a href="pendapatan.php" class="btn btn-add"><i class="fas fa-plus-circle"></i> Pendapatan</a>
                <a href="pengeluaran.php" class="btn btn-add"><i class="fas fa-minus-circle"></i> Pengeluaran</a>
                <a href="tabungan.php" class="btn btn-add"><i class="fas fa-piggy-bank"></i> Tabungan</a>
                <a href="biaya.php" class="btn btn-add"><i class="fas fa-cash-register"></i> Biaya</a>
            </div>
        </div>

        <?php
        // Logic PHP tetap sama dengan database Anda
        if (!isset($conn)) {
            $total_pendapatan = 0;
            $total_pengeluaran = 0;
            $total_biaya = 0;
            $total_fauzan = 0;
            $total_pln = 0;
            $total_pribadi = 0;
        } else {
            $total_pendapatan = $conn->query("SELECT SUM(jumlah) AS total FROM pendapatan")->fetch_assoc()['total'] ?? 0;
            $total_pengeluaran = $conn->query("SELECT SUM(jumlah) AS total FROM pengeluaran")->fetch_assoc()['total'] ?? 0;
            $total_biaya = $conn->query("SELECT SUM(jumlah) AS total FROM biaya")->fetch_assoc()['total'] ?? 0;
            $tabungan_query = $conn->query("SELECT SUM(fauzan) AS fauzan, SUM(pln) AS pln, SUM(pribadi) AS pribadi FROM tabungan");
            $tabungan = $tabungan_query ? $tabungan_query->fetch_assoc() : ['fauzan' => 0, 'pln' => 0, 'pribadi' => 0];
            $total_fauzan = $tabungan['fauzan'] ?? 0;
            $total_pln = $tabungan['pln'] ?? 0;
            $total_pribadi = $tabungan['pribadi'] ?? 0;
        }

        $total_tabungan = $total_fauzan + $total_pln + $total_pribadi;
        $sisa_bersih_pendapatan = $total_pendapatan - $total_pengeluaran;
        $sisa_setelah_tabungan = $sisa_bersih_pendapatan - $total_tabungan;
        $saldo_akhir_hutang = $sisa_bersih_pendapatan - $total_biaya;
        ?>

        <div class="summary-grid">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-exchange-alt"></i>
                    <h3>Transaksi</h3>
                </div>
                  <div class="data-row">
                    <span class="data-label"><i class="fas fa-receipt" style="color:var(--warning)"></i> Total Biaya Minuman</span>
                    <span class="data-value">Rp <?= number_format($total_biaya, 0, ',', '.') ?></span>
                </div>
                <div class="data-row">
                    <span class="data-label"><i class="fas fa-arrow-up" style="color:var(--success)"></i> Total Pendapatan</span>
                    <span class="data-value">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></span>
                </div>
                <div class="data-row">
                    <span class="data-label"><i class="fas fa-arrow-down" style="color:var(--danger)"></i> Total Pengeluaran</span>
                    <span class="data-value">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></span>
                </div>
              
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-vault"></i>
                    <h3>Tabungan</h3>
                </div>
                <div class="data-row">
                    <span class="data-label">Tabungan Fauzan</span>
                    <span class="data-value">Rp <?= number_format($total_fauzan, 0, ',', '.') ?></span>
                </div>
                <div class="data-row">
                    <span class="data-label">Tabungan PLN</span>
                    <span class="data-value">Rp <?= number_format($total_pln, 0, ',', '.') ?></span>
                </div>
                <div class="data-row">
                    <span class="data-label">Tabungan Pribadi</span>
                    <span class="data-value">Rp <?= number_format($total_pribadi, 0, ',', '.') ?></span>
                </div>
                <div class="total-row data-row">
                    <span class="data-label"><strong>Total Tabungan</strong></span>
                    <span class="data-value" style="color:var(--primary)">Rp <?= number_format($total_tabungan, 0, ',', '.') ?></span>
                </div>
            </div>

            <div class="card balance-card">
                <div class="card-header">
                    <i class="fas fa-chart-pie"></i>
                    <h3>Saldo</h3>
                </div>
            
                <div class="total-row data-row" style="border-left-color: <?= $saldo_akhir_hutang >= 0 ? 'var(--success)' : 'var(--danger)' ?>">
                    <span class="data-label"><strong>Sisa Setelah Ditabung</strong></span>
                    <span class="data-value" style="font-size: 1.3rem;">
                        <span class="data-value">Rp <?= number_format($sisa_setelah_tabungan, 0, ',', '.') ?></span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div style="height: 100px;"></div>

</body>

</html>