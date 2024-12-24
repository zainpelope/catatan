<?php
include '../koneksi.php';

$nama = $_POST['nama'];
$beli = $_POST['beli'];
$bayar = $_POST['bayar'];
$tanggal = $_POST['tanggal'];
$status = $_POST['status'];

$query = "INSERT INTO pulsa (nama, beli, bayar, tanggal, status) VALUES ('$nama', '$beli', '$bayar', '$tanggal', '$status')";
$conn->query($query);

header('Location: pulsa/pulsa.php');
