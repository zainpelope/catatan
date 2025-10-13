<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Catatan Keuangan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4CAF50;
            /* Green */
            --secondary-color: #2196F3;
            /* Blue */
            --accent-color: #FFC107;
            /* Amber */
            --danger-color: #F44336;
            /* Red */
            --text-color: #333;
            --light-text-color: #666;
            --bg-color: #f0f2f5;
            /* Lighter background */
            --card-bg: #fff;
            --border-color: #e0e0e0;
            --shadow-light: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-medium: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        h1 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 40px;
            font-weight: 700;
            font-size: 2.5em;
            letter-spacing: -0.5px;
        }

        .top-buttons {
            display: flex;
            justify-content: space-between; /* Adjusted for the back button */
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
            gap: 15px; /* Spacing between button groups */
        }

        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: flex-end; /* Align to end by default */
        }

        /* Adjust button group on smaller screens to center */
        @media (max-width: 768px) {
            .top-buttons {
                flex-direction: column;
                align-items: center;
            }
            .button-group {
                justify-content: center; /* Center buttons on mobile */
                width: 100%; /* Take full width for centering */
            }
        }


        a.button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 25px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.05em;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: var(--shadow-light);
            white-space: nowrap; /* Prevent text wrapping inside button */
        }

        a.button.secondary {
            background-color: var(--secondary-color);
        }

        a.button.secondary:hover {
            background-color: #1a7bb9; /* Darker blue */
        }


        a.button:hover {
            background-color: #45a049;
            transform: translateY(-3px);
            box-shadow: var(--shadow-medium);
        }

        a.button:active {
            transform: translateY(0);
            box-shadow: var(--shadow-light);
        }

        a.button i {
            font-size: 1.1em;
        }

        h2 {
            text-align: center;
            color: var(--secondary-color);
            margin-top: 40px;
            margin-bottom: 25px;
            font-weight: 700;
            font-size: 2em;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .summary-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow-light);
            padding: 30px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid var(--border-color);
        }

        .summary-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-medium);
        }

        .summary-card h3 {
            margin-top: 0;
            color: var(--secondary-color);
            font-size: 1.4em;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
            font-weight: 700;
            text-align: center;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 12px 0;
            border-bottom: 1px dashed var(--border-color);
            font-size: 1.05em;
        }

        .summary-item:last-of-type {
            border-bottom: none;
            padding-bottom: 0;
        }

        .summary-item span:first-child {
            font-weight: 400;
            color: var(--light-text-color);
        }

        .summary-item span:last-child {
            font-weight: 700;
            color: var(--primary-color);
        }

        .summary-item.total span:last-child,
        .summary-item.final-result span:last-child {
            font-size: 1.2em;
            color: var(--secondary-color);
        }

        .summary-item.negative span:last-child {
            color: var(--danger-color);
        }

        .summary-item.highlight span {
            background-color: #e6ffe6;
            padding: 5px 8px;
            border-radius: 6px;
        }

        .summary-item.final-result {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid var(--primary-color);
            /* Stronger border for final results */
            font-size: 1.15em;
        }

        .summary-item.final-result span:first-child {
            font-weight: 700;
            color: var(--text-color);
        }


        /* Responsive adjustments */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            h1 {
                font-size: 2em;
            }

            a.button {
                width: 100%; /* Make buttons full width on smaller screens */
                max-width: 320px;
                padding: 12px 20px;
                font-size: 1em;
            }

            .summary-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .summary-card {
                padding: 25px;
            }

            .summary-card h3 {
                font-size: 1.25em;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.8em;
                margin-bottom: 30px;
            }

            a.button {
                font-size: 0.95em;
                padding: 10px 15px;
            }

            .summary-item {
                font-size: 1em;
            }

            .summary-item span:last-child,
            .summary-item.total span:last-child,
            .summary-item.final-result span:last-child {
                font-size: 1.1em;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Ringkasan Keuangan Anda</h1>

        <div class="top-buttons">
            <a href="index.php" class="button secondary"><i class="fas fa-arrow-left"></i> Kembali ke Menu Utama</a>
            <div class="button-group">
                <a href="pendapatan.php" class="button"><i class="fas fa-plus-circle"></i> Tambah Pendapatan</a>
                <a href="pengeluaran.php" class="button"><i class="fas fa-minus-circle"></i> Tambah Pengeluaran</a>
                <a href="tabungan.php" class="button"><i class="fas fa-piggy-bank"></i> Tambah Tabungan</a>
                <a href="biaya.php" class="button"><i class="fas fa-cash-register"></i> Tambah Biaya</a>
            </div>
        </div>

        <?php
        // Ensure $conn is properly initialized from config.php
        if (!isset($conn)) {
            // Fallback for demonstration if config.php isn't fully set up or accessible
            // In a real application, you'd handle this error more robustly
            $total_pendapatan = 0;
            $total_pengeluaran = 0;
            $total_biaya = 0;
            $total_fauzan = 0;
            $total_pln = 0;
            $total_pribadi = 0;
        } else {
            // Total Pendapatan
            $total_pendapatan = $conn->query("SELECT SUM(jumlah) AS total FROM pendapatan")->fetch_assoc()['total'] ?? 0;

            // Total Pengeluaran
            $total_pengeluaran = $conn->query("SELECT SUM(jumlah) AS total FROM pengeluaran")->fetch_assoc()['total'] ?? 0;

            // Total Biaya
            $total_biaya = $conn->query("SELECT SUM(jumlah) AS total FROM biaya")->fetch_assoc()['total'] ?? 0;

            // Total Tabungan per kategori
            $tabungan_query = $conn->query("SELECT SUM(fauzan) AS fauzan, SUM(pln) AS pln, SUM(pribadi) AS pribadi FROM tabungan");
            $tabungan = $tabungan_query ? $tabungan_query->fetch_assoc() : ['fauzan' => 0, 'pln' => 0, 'pribadi' => 0];

            $total_fauzan = $tabungan['fauzan'] ?? 0;
            $total_pln = $tabungan['pln'] ?? 0;
            $total_pribadi = $tabungan['pribadi'] ?? 0;
        }

        // Calculations
        // Sisa Bersih Pendapatan = Total Pendapatan - Total Pengeluaran
        $sisa_bersih_pendapatan = ($total_pendapatan ?? 0) - ($total_pengeluaran ?? 0);

        // Total semua tabungan
        $total_tabungan = ($total_fauzan ?? 0) + ($total_pln ?? 0) + ($total_pribadi ?? 0);

        // Sisa Setelah Tabungan = Pendapatan - Pengeluaran - Tabungan
        $sisa_setelah_tabungan = ($total_pendapatan ?? 0) - ($total_pengeluaran ?? 0) - ($total_tabungan ?? 0);

        // Saldo Akhir / Potensi Hutang = Sisa Bersih Pendapatan - Total Biaya
        $saldo_akhir_hutang = ($sisa_bersih_pendapatan ?? 0) - ($total_biaya ?? 0);
        ?>

        <div class="summary-grid">
            <div class="summary-card">
                <h3>Ringkasan Transaksi</h3>
                <div class="summary-item">
                    <span>Total Pendapatan</span>
                    <span>Rp <?= number_format($total_pendapatan ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="summary-item">
                    <span>Total Pengeluaran</span>
                    <span>Rp <?= number_format($total_pengeluaran ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="summary-item">
                    <span>Total Biaya</span>
                    <span>Rp <?= number_format($total_biaya ?? 0, 0, ',', '.') ?></span>
                </div>
            </div>

            <div class="summary-card">
                <h3>Rincian Tabungan</h3>
                <div class="summary-item">
                    <span>Tabungan Fauzan</span>
                    <span>Rp <?= number_format($total_fauzan ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="summary-item">
                    <span>Tabungan PLN</span>
                    <span>Rp <?= number_format($total_pln ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="summary-item">
                    <span>Tabungan Pribadi</span>
                    <span>Rp <?= number_format($total_pribadi ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="summary-item total final-result">
                    <span>Total Keseluruhan Tabungan</span>
                    <span>Rp <?= number_format($total_tabungan ?? 0, 0, ',', '.') ?></span>
                </div>
            </div>

            <div class="summary-card">
                <h3>Status Keuangan Saat Ini</h3>
                <div class="summary-item final-result <?= $sisa_setelah_tabungan < 0 ? 'negative' : '' ?>">
                    <span>Sisa Setelah Tabungan</span>
                    <span>Rp <?= number_format($sisa_setelah_tabungan ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="summary-item final-result <?= $sisa_bersih_pendapatan < 0 ? 'negative' : '' ?>">
                    <span>Sisa Bersih Pendapatan</span>
                    <span>Rp <?= number_format($sisa_bersih_pendapatan ?? 0, 0, ',', '.') ?></span>
                </div>

                <div class="summary-item final-result <?= $saldo_akhir_hutang < 0 ? 'negative' : '' ?>">
                    <span>Hasil / Hutang</span>
                    <span>Rp <?= number_format($saldo_akhir_hutang ?? 0, 0, ',', '.') ?></span>
                </div>

            </div>
        </div>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
    </div>

</body>

</html>