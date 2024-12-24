<?php
header('Content-Type: application/json');
include 'koneksi.php';

// Query untuk mendapatkan data pulsa dengan status 'Lunas'
$query_pulsa = "SELECT * FROM pulsa WHERE status = 'Lunas'";
$result_pulsa = $conn->query($query_pulsa);

// Query untuk mendapatkan data dokumen dengan status 'Lunas'
$query_dokumen = "SELECT * FROM dokumen WHERE status = 'Lunas'";
$result_dokumen = $conn->query($query_dokumen);

$total_beli_pulsa = 0;
$total_bayar_pulsa = 0;

// Hitung total beli dan bayar untuk pulsa
while ($row = $result_pulsa->fetch_assoc()) {
    $total_beli_pulsa += $row['beli'];
    $total_bayar_pulsa += $row['bayar'];
}

// Hitung total bayar untuk dokumen
$total_bayar_dokumen = 0;
while ($row = $result_dokumen->fetch_assoc()) {
    $total_bayar_dokumen += $row['bayar'];
}

// Hitung selisih bayar pulsa
$selisih_bayar_pulsa = $total_bayar_pulsa - $total_beli_pulsa;

// Buat response JSON
$response = [
    "pulsa" => [
        "total_beli" => $total_beli_pulsa,
        "total_bayar" => $total_bayar_pulsa,
        "selisih_bayar" => $selisih_bayar_pulsa
    ],
    "dokumen" => [
        "total_bayar" => $total_bayar_dokumen
    ]
];

// Tampilkan data dalam format JSON
echo json_encode($response);
