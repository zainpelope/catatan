<?php
include 'koneksi.php';

$nama = $_POST['nama'];
$tanggal = $_POST['tanggal'];
$bayar = $_POST['bayar'];
$keterangan = $_POST['keterangan'];
$status = $_POST['status'];

$query = "INSERT INTO dokumen (nama, tanggal, bayar, keterangan, status) VALUES ('$nama', '$tanggal', '$bayar', '$keterangan', '$status')";
$conn->query($query);

header('Location: dokumen.php');
