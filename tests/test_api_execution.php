<?php
$_GET['service_number'] = 'HH696FA75B3A2C0';
ob_start();
require __DIR__ . '/../api/get_bill.php';
$output = ob_get_clean();

echo "API Response:\n" . $output;

$data = json_decode($output, true);
if (isset($data['status']) && $data['status'] === 'success') {
    echo "\n\nTEST PASSED: Status is success";
} else {
    echo "\n\nTEST FAILED: Status is not success";
}
?>