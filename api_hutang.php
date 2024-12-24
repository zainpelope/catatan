<?php
header('Content-Type: application/json');
include 'koneksi.php';

// Query untuk mendapatkan data hutang pulsa
$queryPulsa = "SELECT * FROM pulsa WHERE status = 'Hutang'";
$resultPulsa = $conn->query($queryPulsa);

// Query untuk mendapatkan data hutang dokumen
$queryDokumen = "SELECT * FROM dokumen WHERE status = 'Hutang'";
$resultDokumen = $conn->query($queryDokumen);

// Array untuk menyimpan data hutang
$data = [
    "pulsa" => [],
    "dokumen" => []
];

// Ambil data hutang pulsa
while ($row = $resultPulsa->fetch_assoc()) {
    $data["pulsa"][] = [
        "id" => $row['id'],
        "nama" => $row['nama'],
        "beli" => $row['beli'],
        "bayar" => $row['bayar'],
        "tanggal" => $row['tanggal']
    ];
}

// Ambil data hutang dokumen
while ($row = $resultDokumen->fetch_assoc()) {
    $data["dokumen"][] = [
        "id" => $row['id'],
        "nama" => $row['nama'],
        "tanggal" => $row['tanggal'],
        "bayar" => $row['bayar'],
        "keterangan" => $row['keterangan']
    ];
}

// Tampilkan data dalam format JSON
echo json_encode($data);
