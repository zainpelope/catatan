<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // ================= GET =================
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $conn->prepare("SELECT * FROM pulsa WHERE id = ?");
            $stmt->bind_param("i", $_GET['id']);
        } else {
            $stmt = $conn->prepare("SELECT * FROM pulsa");
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode($data);
        break;

    // ================= POST =================
    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);

        $stmt = $conn->prepare(
            "INSERT INTO pulsa (nama, beli, bayar, tanggal, status) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssss",
            $input['nama'],
            $input['beli'],
            $input['bayar'],
            $input['tanggal'],
            $input['status']
        );

        echo json_encode([
            "success" => $stmt->execute()
        ]);
        break;

    // ================= PUT =================
    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);

        $stmt = $conn->prepare(
            "UPDATE pulsa SET nama=?, beli=?, bayar=?, tanggal=?, status=? WHERE id=?"
        );
        $stmt->bind_param(
            "sssssi",
            $input['nama'],
            $input['beli'],
            $input['bayar'],
            $input['tanggal'],
            $input['status'],
            $input['id']
        );

        echo json_encode([
            "success" => $stmt->execute()
        ]);
        break;

    // ================= DELETE =================
    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "ID required"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM pulsa WHERE id=?");
        $stmt->bind_param("i", $_GET['id']);

        echo json_encode([
            "success" => $stmt->execute()
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
