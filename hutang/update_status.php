<?php
include '../koneksi.php';

$id = $_POST['id'] ?? null;
$type = $_POST['type'] ?? null;

// Hentikan eksekusi jika data ID atau Tipe kosong
if (!$id || !$type) {
    echo "<script>alert('Error: Data tidak lengkap.'); window.location='../hutang/hutang.php';</script>";
    exit;
}

// Gunakan prepared statement untuk keamanan
$table = ($type === 'pulsa') ? 'pulsa' : (($type === 'dokumen') ? 'dokumen' : null);

if (!$table) {
    echo "<script>alert('Error: Tipe transaksi tidak valid.'); window.location='../hutang/hutang.php';</script>";
    exit;
}

// QUERY FINAL: Mengubah status menjadi Lunas dan mencatat tanggal saat ini menggunakan fungsi NOW()
$updateQuery = "UPDATE {$table} SET status = 'Lunas', tanggal_lunas = NOW() WHERE id = ?";

// Persiapan statement
$stmt = $conn->prepare($updateQuery);

if ($stmt === false) {
    echo "Error persiapan statement: " . $conn->error;
    exit;
}

// Bind parameter (i = integer)
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Ambil tanggal saat ini untuk ditampilkan di alert
    $current_datetime = date("d-m-Y H:i:s");

    // Tampilkan notifikasi dengan tanggal pelunasan yang baru
    echo "<script>alert('Hutang {$type} berhasil dilunasi pada {$current_datetime}.'); window.location='../hutang/hutang.php';</script>";
} else {
    echo "Error eksekusi query: " . $stmt->error;
}

$stmt->close();
$conn->close();
