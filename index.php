<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Catatan</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-family: "Segoe UI", sans-serif;
        }

        /* ================= DESKTOP ================= */
        .desktop-menu .card {
            border-radius: 18px;
            transition: 0.3s;
        }

        .desktop-menu .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .desktop-menu .icon {
            font-size: 2.4rem;
            margin-bottom: 10px;
        }

        /* ================= MOBILE (GRID ALA DANA) ================= */
        @media (max-width: 767px) {
            body {
                background: #fff;
            }

            .desktop-menu {
                display: none;
            }

            .mobile-menu {
                display: block;
            }

            .mobile-item {
                text-decoration: none;
                color: #333;
            }

            .mobile-icon {
                width: 56px;
                height: 56px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: auto;
                font-size: 1.7rem;
            }

            .mobile-text {
                font-size: 0.8rem;
                margin-top: 6px;
            }
        }

        /* ================= DESKTOP ONLY ================= */
        @media (min-width: 768px) {
            .mobile-menu {
                display: none;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container justify-content-center">
            <span class="navbar-brand fw-bold">Aplikasi Catatan</span>
        </div>
    </nav>

    <!-- ================= MOBILE MENU ================= -->
    <div class="container mobile-menu">
        <div class="row text-center g-3">

            <!-- ===== MENU LAMA ===== -->
            <div class="col-3">
                <a href="pulsa/pulsa.php" class="mobile-item">
                    <div class="mobile-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-phone"></i>
                    </div>
                    <div class="mobile-text">Pulsa</div>
                </a>
            </div>

            <div class="col-3">
                <a href="dokumen/dokumen.php" class="mobile-item">
                    <div class="mobile-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="mobile-text">Dokumen</div>
                </a>
            </div>

            <div class="col-3">
                <a href="hasil/hasil.php" class="mobile-item">
                    <div class="mobile-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="mobile-text">Hasil</div>
                </a>
            </div>

            <div class="col-3">
                <a href="hutang/hutang.php" class="mobile-item">
                    <div class="mobile-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="mobile-text">Hutang</div>
                </a>
            </div>

            <div class="col-3">
                <a href="catatan/catatan.php" class="mobile-item">
                    <div class="mobile-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <div class="mobile-text">Catatan</div>
                </a>
            </div>

            <div class="col-3">
                <a href="kegiatan/kegiatan.php" class="mobile-item">
                    <div class="mobile-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="mobile-text">Kegiatan</div>
                </a>
            </div>

            <div class="col-3">
                <a href="target/target.php" class="mobile-item">
                    <div class="mobile-icon bg-dark bg-opacity-10 text-dark">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <div class="mobile-text">Target</div>
                </a>
            </div>

            <div class="col-3">
                <a href="minuman.php" class="mobile-item">
                    <div class="mobile-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-cup-straw"></i>
                    </div>
                    <div class="mobile-text">Minuman</div>
                </a>
            </div>

            <!-- ===== MENU TAMBAHAN ===== -->
            <div class="col-3">
                <a href="konter/index.php" class="mobile-item">
                    <div class="mobile-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-shop"></i>
                    </div>
                    <div class="mobile-text">Konter</div>
                </a>
            </div>



            <div class="col-3">
                <a href="wifi/wifi.php" class="mobile-item">
                    <div class="mobile-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-wifi"></i>
                    </div>
                    <div class="mobile-text">WiFi</div>
                </a>
            </div>

            <div class="col-3">
                <a href="danapulsa/dana_pulsa.php" class="mobile-item">
                    <div class="mobile-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-phone-fill"></i>
                    </div>
                    <div class="mobile-text">Dana Pulsa</div>
                </a>
            </div>
            <div class="col-3">
                <a href="traktor/index.php" class="mobile-item">
                    <div class="mobile-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-gear-wide-connected"></i>
                    </div>
                    <div class="mobile-text">Traktor</div>
                </a>
            </div>
            <div class="col-3">
                <a href="layangan/layangan.php" class="mobile-item">
                    <div class="mobile-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-wind"></i>
                    </div>
                    <div class="mobile-text">Layangan</div>
                </a>
            </div>

        </div>
    </div>

    <!-- ================= DESKTOP MENU ================= -->
    <!-- ================= DESKTOP MENU ================= -->
    <div class="container desktop-menu">
        <div class="row g-3 justify-content-center">

            <!-- Pulsa -->
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-primary">
                    <div class="card-body p-2">
                        <div class="icon text-primary"><i class="bi bi-phone"></i></div>
                        <h5>Pulsa</h5>
                        <a href="pulsa/pulsa.php" class="btn btn-primary btn-sm">Buka</a>
                    </div>
                </div>
            </div>

            <!-- Dokumen -->
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-secondary">
                    <div class="card-body p-2">
                        <div class="icon text-secondary"><i class="bi bi-file-earmark-text"></i></div>
                        <h5>Dokumen</h5>
                        <a href="dokumen/dokumen.php" class="btn btn-secondary btn-sm">Buka</a>
                    </div>
                </div>
            </div>

            <!-- Hasil -->
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-success">
                    <div class="card-body p-2">
                        <div class="icon text-success"><i class="bi bi-graph-up-arrow"></i></div>
                        <h5>Hasil</h5>
                        <a href="hasil/hasil.php" class="btn btn-success btn-sm">Buka</a>
                    </div>
                </div>
            </div>

            <!-- Hutang -->
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-warning">
                    <div class="card-body p-2">
                        <div class="icon text-warning"><i class="bi bi-cash-coin"></i></div>
                        <h5>Hutang</h5>
                        <a href="hutang/hutang.php" class="btn btn-warning btn-sm text-white">Buka</a>
                    </div>
                </div>
            </div>

            <!-- Catatan -->
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-info">
                    <div class="card-body p-2">
                        <div class="icon text-info"><i class="bi bi-journal-text"></i></div>
                        <h5>Catatan</h5>
                        <a href="catatan/catatan.php" class="btn btn-info btn-sm text-white">Buka</a>
                    </div>
                </div>
            </div>

            <!-- Kegiatan -->
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-danger">
                    <div class="card-body p-2">
                        <div class="icon text-danger"><i class="bi bi-calendar-event"></i></div>
                        <h5>Kegiatan</h5>
                        <a href="kegiatan/kegiatan.php" class="btn btn-danger btn-sm">Buka</a>
                    </div>
                </div>
            </div>

            <!-- Target -->
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-dark">
                    <div class="card-body p-2">
                        <div class="icon text-dark"><i class="bi bi-bullseye"></i></div>
                        <h5>Target</h5>
                        <a href="target/target.php" class="btn btn-dark btn-sm">Buka</a>
                    </div>
                </div>
            </div>

            <!-- Minuman -->
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-primary">
                    <div class="card-body p-2">
                        <div class="icon text-primary"><i class="bi bi-cup-straw"></i></div>
                        <h5>Minuman</h5>
                        <a href="minuman.php" class="btn btn-primary btn-sm">Buka</a>
                    </div>
                </div>
            </div>

            <!-- Konter -->
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-success">
                    <div class="card-body p-2">
                        <div class="icon text-success"><i class="bi bi-shop"></i></div>
                        <h5>Konter</h5>
                        <a href="konter/index.php" class="btn btn-success btn-sm">Buka</a>
                    </div>
                </div>
            </div>



            <!-- WiFi -->
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-info">
                    <div class="card-body p-2">
                        <div class="icon text-info"><i class="bi bi-wifi"></i></div>
                        <h5>WiFi</h5>
                        <a href="wifi/wifi.php" class="btn btn-info btn-sm text-white">Buka</a>
                    </div>
                </div>
            </div>

            <!-- Dana Pulsa -->
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-warning">
                    <div class="card-body p-2">
                        <div class="icon text-warning"><i class="bi bi-phone-fill"></i></div>
                        <h5>Dana Pulsa</h5>
                        <a href="danapulsa/dana_pulsa.php" class="btn btn-warning btn-sm text-white">Buka</a>
                    </div>
                </div>
            </div>
            <!-- Traktor -->
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-secondary">
                    <div class="card-body p-2">
                        <div class="icon text-secondary"><i class="bi bi-gear-wide-connected"></i></div>
                        <h5>Traktor</h5>
                        <a href="traktor/index.php" class="btn btn-secondary btn-sm">Buka</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3">
                <div class="card text-center border-danger">
                    <div class="card-body p-2">
                        <div class="icon text-danger"><i class="bi bi-wind"></i></div>
                        <h5>Layangan</h5>
                        <a href="layangan/layangan.php" class="btn btn-danger btn-sm">Buka</a>
                    </div>
                </div>
            </div>

        </div>
    </div>


</body>

</html>