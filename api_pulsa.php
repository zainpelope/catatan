<?php
header('Content-Type: application/json');
include 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $query = "SELECT * FROM pulsa WHERE id = '$id'";
        } else {
            $query = "SELECT * FROM pulsa";
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
        $input = json_decode(file_get_contents('php://input'), true);
        $nama = $input['nama'];
        $beli = $input['beli'];
        $bayar = $input['bayar'];
        $tanggal = $input['tanggal'];
        $status = $input['status'];

        $query = "INSERT INTO pulsa (nama, beli, bayar, tanggal, status) VALUES ('$nama', '$beli', '$bayar', '$tanggal', '$status')";
        if ($conn->query($query)) {
            echo json_encode(['message' => 'Data created successfully']);
        } else {
            echo json_encode(['message' => 'Error: ' . $conn->error]);
        }
        break;

    case 'PUT':

        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];
        $nama = $input['nama'];
        $beli = $input['beli'];
        $bayar = $input['bayar'];
        $tanggal = $input['tanggal'];
        $status = $input['status'];

        $query = "UPDATE pulsa SET nama = '$nama', beli = '$beli', bayar = '$bayar', tanggal = '$tanggal', status = '$status' WHERE id = '$id'";
        if ($conn->query($query)) {
            echo json_encode(['message' => 'Data updated successfully']);
        } else {
            echo json_encode(['message' => 'Error: ' . $conn->error]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $query = "DELETE FROM pulsa WHERE id = '$id'";
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
