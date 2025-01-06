<?php
// Tampilkan error untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database
include('../koneksi.php'); // Sesuaikan dengan file koneksi Anda

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama_kegiatan = $_POST['nama_kegiatan'];
    $jam = $_POST['jam'];

    // Query untuk update data
    $query = "UPDATE kegiatan SET nama_kegiatan = ?, jam = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssi', $nama_kegiatan, $jam, $id);

    if ($stmt->execute()) {
        header("Location: ../kegiatan/kegiatan.php?status=success");
    } else {
        echo "Error: " . $conn->error;
    }
}
