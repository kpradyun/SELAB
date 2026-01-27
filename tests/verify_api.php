<?php
require_once __DIR__ . '/../includes/db.php';

$result = $conn->query("SELECT service_number FROM users LIMIT 1");
if ($row = $result->fetch_assoc()) {
    $svc = $row['service_number'];
    echo "Found Service Number: $svc\n";

    $url = "http://localhost/api/get_bill.php?service_number=$svc";
    echo "Test URL: $url\n";
} else {
    echo "No users found in DB.\n";
}
?>