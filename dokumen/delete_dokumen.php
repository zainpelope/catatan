
<?php
include 'koneksi.php';

// Ambil ID yang akan dihapus
$id = $_GET['id'];

// Query untuk menghapus data berdasarkan ID
$query = "DELETE FROM dokumen WHERE id = '$id'";
if ($conn->query($query)) {
    header('Location: dokumen.php');  // Redirect ke halaman dokumen setelah penghapusan
} else {
    echo "Error: " . $conn->error;
}
?>
