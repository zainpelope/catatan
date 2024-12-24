<?php
header('Content-Type: application/json');
include 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Mendapatkan semua data atau data berdasarkan ID
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $query = "SELECT * FROM dokumen WHERE id = '$id'";
        } else {
            $query = "SELECT * FROM dokumen";
        }
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode($data);
        } else {
            echo json_encode(['message' => 'No data found']);
        }
        break;

    case 'POST':
        // Menambahkan data baru
        $input = json_decode(file_get_contents('php://input'), true);
        $nama = $input['nama'];
        $tanggal = $input['tanggal'];
        $bayar = $input['bayar'];
        $keterangan = $input['keterangan'];
        $status = $input['status'];

        $query = "INSERT INTO dokumen (nama, tanggal, bayar, keterangan, status) VALUES ('$nama', '$tanggal', '$bayar', '$keterangan', '$status')";
        if ($conn->query($query)) {
            echo json_encode(['message' => 'Data created successfully']);
        } else {
            echo json_encode(['message' => 'Error: ' . $conn->error]);
        }
        break;

    case 'PUT':
        // Mengedit data berdasarkan ID
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];
        $nama = $input['nama'];
        $tanggal = $input['tanggal'];
        $bayar = $input['bayar'];
        $keterangan = $input['keterangan'];
        $status = $input['status'];

        $query = "UPDATE dokumen SET nama = '$nama', tanggal = '$tanggal', bayar = '$bayar', keterangan = '$keterangan', status = '$status' WHERE id = '$id'";
        if ($conn->query($query)) {
            echo json_encode(['message' => 'Data updated successfully']);
        } else {
            echo json_encode(['message' => 'Error: ' . $conn->error]);
        }
        break;

    case 'DELETE':
        // Menghapus data berdasarkan ID
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $query = "DELETE FROM dokumen WHERE id = '$id'";
            if ($conn->query($query)) {
                echo json_encode(['message' => 'Data deleted successfully']);
            } else {
                echo json_encode(['message' => 'Error: ' . $conn->error]);
            }
        } else {
            echo json_encode(['message' => 'ID not provided']);
        }
        break;

    default:
        echo json_encode(['message' => 'Invalid request method']);
        break;
}

$conn->close();
