<?php
include '../koneksi.php';

// Ambil ID yang akan dihapus
$id = $_GET['id'];

// Query untuk menghapus data berdasarkan ID
$query = "DELETE FROM pulsa WHERE id = '$id'";
if ($conn->query($query)) {
    header('Location: pulsa/pulsa.php');  // Redirect ke halaman pulsa setelah penghapusan
} else {
    echo "Error: " . $conn->error;
}
