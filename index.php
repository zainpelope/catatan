<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Catatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <style>
        body {
            background: #f4f6f9;
        }

        .card {
            border-radius: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        @media (max-width: 576px) {
            h1 {
                font-size: 1.8rem;
            }

            .card-icon {
                font-size: 2rem;
            }

            .btn {
                font-size: 0.9rem;
                padding: 6px 12px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container justify-content-center">
            <a class="navbar-brand fw-bold" href="#">Aplikasi Catatan</a>
        </div>
    </nav>


    <!-- Content -->
    <div class="container">
        <div class="row g-4 justify-content-center">
            <!-- Template Card -->
            <!-- Ganti sesuai kebutuhan -->

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card text-center border-primary">
                    <div class="card-body">
                        <div class="card-icon text-primary"><i class="bi bi-phone"></i></div>
                        <h5 class="card-title">Pulsa</h5>
                        <a href="pulsa/pulsa.php" class="btn btn-primary">Lihat Pulsa</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card text-center border-secondary">
                    <div class="card-body">
                        <div class="card-icon text-secondary"><i class="bi bi-file-earmark-text"></i></div>
                        <h5 class="card-title">Dokumen</h5>
                        <a href="dokumen/dokumen.php" class="btn btn-secondary">Lihat Dokumen</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card text-center border-success">
                    <div class="card-body">
                        <div class="card-icon text-success"><i class="bi bi-graph-up-arrow"></i></div>
                        <h5 class="card-title">Hasil</h5>
                        <a href="hasil/hasil.php" class="btn btn-success">Lihat Hasil</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card text-center border-warning">
                    <div class="card-body">
                        <div class="card-icon text-warning"><i class="bi bi-cash-coin"></i></div>
                        <h5 class="card-title">Hutang</h5>
                        <a href="hutang/hutang.php" class="btn btn-warning text-white">Lihat Hutang</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card text-center border-info">
                    <div class="card-body">
                        <div class="card-icon text-info"><i class="bi bi-journal-text"></i></div>
                        <h5 class="card-title">Catatan</h5>
                        <a href="catatan/catatan.php" class="btn btn-info text-white">Lihat Catatan</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card text-center border-danger">
                    <div class="card-body">
                        <div class="card-icon text-danger"><i class="bi bi-calendar-event"></i></div>
                        <h5 class="card-title">Kegiatan</h5>
                        <a href="kegiatan/kegiatan.php" class="btn btn-danger">Lihat Kegiatan</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card text-center border-dark">
                    <div class="card-body">
                        <div class="card-icon text-dark"><i class="bi bi-bullseye"></i></div>
                        <h5 class="card-title">Target</h5>
                        <a href="target/target.php" class="btn btn-dark">Lihat Target</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-wpNFUeIJENdJtJ6H/0IrmyQX+RZsDdMfo5WPKNBoBo1Or4+CrDOxIxtNkz0nEJG4" crossorigin="anonymous"></script>
</body>

</html>