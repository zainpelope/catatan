<?php
include '../koneksi.php';

$id = $_GET['id'];
$deleteQuery = "DELETE FROM target WHERE id = $id";
if ($conn->query($deleteQuery)) {
    header("Location: target.php");
} else {
    echo "Error: " . $conn->error;
}
