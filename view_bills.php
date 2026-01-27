<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth();

$serviceNumber = $_POST['service_number'];

$stmt = $conn->prepare("SELECT * FROM bills WHERE service_number=? ORDER BY start_date ASC");
$stmt->bind_param("s", $serviceNumber);
$stmt->execute();
$result = $stmt->get_result();

echo "<div class='container'><h3>All Bills for $serviceNumber</h3>";

if ($result->num_rows > 0) {
    echo "<table class='table'><tr><th>Bill #</th><th>Period</th><th>Units</th><th>Total</th><th>Fine</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['bill_number']}</td>
            <td>{$row['start_date']} to {$row['end_date']}</td>
            <td>{$row['units_consumed']}</td>
            <td>₹" . number_format($row['total_amount'], 2) . "</td>
            <td>₹" . number_format($row['fine_amount'], 2) . "</td>
            <td>{$row['paid_status']}</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "No bills found";
}

echo "</div>";
$conn->close();
?>