<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .card {
            transition: transform 0.3s;
        }

        .card:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <!-- Content Section -->
    <div class="container mt-5">
        <h1 class="text-center mb-4">Catatan</h1>
        <div class="row g-4">
            <!-- Card: Pulsa -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-primary text-center">
                    <div class="card-body">
                        <h5 class="card-title">Pulsa</h5>
                        <a href="pulsa/pulsa.php" class="btn btn-primary">Lihat Pulsa</a>
                    </div>
                </div>
            </div>

            <!-- Card: Dokumen -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-secondary text-center">
                    <div class="card-body">
                        <h5 class="card-title">Dokumen</h5>
                        <a href="dokumen/dokumen.php" class="btn btn-secondary">Lihat Dokumen</a>
                    </div>
                </div>
            </div>

            <!-- Card: Hasil -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-success text-center">
                    <div class="card-body">
                        <h5 class="card-title">Hasil</h5>
                        <a href="hasil/hasil.php" class="btn btn-success">Lihat Hasil</a>
                    </div>
                </div>
            </div>

            <!-- Card: Hutang -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-warning text-center">
                    <div class="card-body">
                        <h5 class="card-title">Hutang</h5>
                        <a href="hutang/hutang.php" class="btn btn-warning">Lihat Hutang</a>
                    </div>
                </div>
            </div>

            <!-- Card: Catatan -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-info text-center">
                    <div class="card-body">
                        <h5 class="card-title">Catatan</h5>
                        <a href="catatan/catatan.php" class="btn btn-info">Lihat Catatan</a>
                    </div>
                </div>
            </div>

            <!-- Card: Kegiatanku -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-danger text-center">
                    <div class="card-body">
                        <h5 class="card-title">Kegiatan</h5>
                        <a href="kegiatan/kegiatan.php" class="btn btn-danger">Lihat Kegiatan</a>
                    </div>
                </div>
            </div>

            <!-- Card: Targetku -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-warning text-center">
                    <div class="card-body">
                        <h5 class="card-title">Target</h5>
                        <a href="target/target.php" class="btn btn-warning">Lihat Target</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-wpNFUeIJENdJtJ6H/0IrmyQX+RZsDdMfo5WPKNBoBo1Or4+CrDOxIxtNkz0nEJG4" crossorigin="anonymous"></script>
</body>

</html>