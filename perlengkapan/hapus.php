<?php
include '../koneksi.php';

$id = $_GET['id'];
$sql = "DELETE FROM perlengkapan WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    header("Location: ../perlengkapan/perlengkapan.php");
} else {
    echo "Error: " . $conn->error;
}
