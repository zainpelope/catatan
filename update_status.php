<?php
include 'koneksi.php';

$id = $_POST['id'];
$type = $_POST['type'];

if ($type === 'pulsa') {
    $query = "UPDATE pulsa SET status = 'Lunas' WHERE id = $id";
} else if ($type === 'dokumen') {
    $query = "UPDATE dokumen SET status = 'Lunas' WHERE id = $id";
}

if ($conn->query($query)) {
    header('Location: hutang.php');
} else {
    echo "Error: " . $conn->error;
}
