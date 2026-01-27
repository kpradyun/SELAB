<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_GET['service_number']) && !isset($_GET['bill_no'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing service_number or bill_no"]);
    exit();
}

try {
    $sql = "SELECT b.bill_number, b.service_number, b.units_consumed, b.total_amount, b.fine_amount, b.end_date,
                   u.name, u.address, u.connection_type
            FROM bills b 
            JOIN users u ON b.service_number = u.service_number";

    $params = [];
    $types = "";

    if (isset($_GET['bill_no'])) {
        $sql .= " WHERE b.bill_number = ?";
        $params[] = $_GET['bill_no'];
        $types = "s";
    } else {
        $sql .= " WHERE b.service_number = ? ORDER BY b.end_date DESC LIMIT 1";
        $params[] = $_GET['service_number'];
        $types = "s";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $dueDate = date("Y-m-d", strtotime($row['end_date'] . ' + 15 days'));

        $response = [
            "status" => "success",
            "data" => [
                "bill_number" => $row['bill_number'],
                "service_number" => $row['service_number'],
                "consumer_name" => $row['name'],
                "address" => $row['address'],
                "connection_type" => $row['connection_type'],
                "units_consumed" => (int) $row['units_consumed'],
                "total_amount" => (float) $row['total_amount'],
                "fine_amount" => (float) $row['fine_amount'],
                "bill_date" => $row['end_date'],
                "due_date" => $dueDate
            ]
        ];
        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Bill not found"]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Internal Server Error"]);
}
?>