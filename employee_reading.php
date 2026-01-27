<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth('EMPLOYEE');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['service_number'])) {
        $serviceNumber = $_POST['service_number'];
    } else {
        $serviceNumber = '';
    }

    if (isset($_POST['current_reading'])) {
        $currentReading = floatval($_POST['current_reading']);
    } else {
        $currentReading = 0;
    }

    if ($serviceNumber == '') {
        redirectWithMsg("employee_reading_form.php", "Service Number is required", "error");
    }
    $stmt = $conn->prepare("SELECT * FROM users WHERE service_number = ?");
    $stmt->bind_param("s", $serviceNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        redirectWithMsg("employee_reading_form.php", "Invalid Service Number", "error");
    }

    if ($currentReading < $user['previous_reading']) {
        redirectWithMsg("employee_reading_form.php", "Error: Current reading cannot be less than previous reading (" . $user['previous_reading'] . ")", "error");
    }
    $units = $currentReading - $user['previous_reading'];

    $type = $user['connection_type'];
    $amt = 0;
    $minCharge = 0;

    if ($type == "HOUSEHOLD") {
        $minCharge = 25;
        if ($units <= 50) {
            $amt = $units * 1.5;
        } elseif ($units <= 100) {
            $amt = (50 * 1.5) + (($units - 50) * 2.5);
        } elseif ($units <= 150) {
            $amt = (50 * 1.5) + (100 * 2.5) + (($units - 100) * 3.5);
        } else {
            $amt = (50 * 1.5) + (100 * 2.5) + (50 * 3.5) + (($units - 150) * 4.5);
        }
    } elseif ($type == "COMMERCIAL") {
        $minCharge = 50;
        if ($units <= 50) {
            $amt = $units * 2.5;
        } elseif ($units <= 100) {
            $amt = (50 * 2.5) + (($units - 50) * 3.5);
        } elseif ($units <= 150) {
            $amt = (50 * 2.5) + (100 * 3.5) + (($units - 100) * 4.5);
        } else {
            $amt = (50 * 2.5) + (100 * 3.5) + (50 * 4.5) + (($units - 150) * 5.5);
        }
    } else { // INDUSTRY
        $minCharge = 100;
        if ($units <= 50) {
            $amt = $units * 3.5;
        } elseif ($units <= 100) {
            $amt = (50 * 3.5) + (($units - 50) * 4.5);
        } elseif ($units <= 150) {
            $amt = (50 * 3.5) + (100 * 4.5) + (($units - 100) * 5.5);
        } else {
            $amt = (50 * 3.5) + (100 * 4.5) + (50 * 5.5) + (($units - 150) * 6.5);
        }
    }

    if ($units == 0) {
        $billAmount = $minCharge;
    } else {
        $billAmount = $amt;
    }

    $fineAmount = 0;
    if ($user['bill_status'] == 'UNPAID') {
        $lastBillQ = $conn->prepare("SELECT total_amount FROM bills WHERE service_number=? AND paid_status='UNPAID' ORDER BY end_date DESC LIMIT 1");
        $lastBillQ->bind_param("s", $serviceNumber);
        $lastBillQ->execute();
        $lastRes = $lastBillQ->get_result();
        if ($lastBill = $lastRes->fetch_assoc()) {
            $fineAmount = $lastBill['total_amount'] + 150;
        }
    }

    if ($user['last_bill_date']) {
        $startDate = date("Y-m-d", strtotime($user['last_bill_date'] . " +1 day"));
    } else {
        $startDate = $user['registration_date'];
    }
    $endDate = date("Y-m-d");

    $billNumber = strtoupper(substr($user['connection_type'], 0, 3)) . rand(10000, 99999);
    $totalToPay = $billAmount + $fineAmount;

    $ins = $conn->prepare("INSERT INTO bills (bill_number, service_number, start_date, end_date, units_consumed, total_amount, fine_amount, paid_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'UNPAID')");
    $ins->bind_param("ssssddd", $billNumber, $serviceNumber, $startDate, $endDate, $units, $totalToPay, $fineAmount);

    if ($ins->execute()) {
        $upd = $conn->prepare("UPDATE users SET previous_reading=?, last_bill_date=?, bill_status='UNPAID' WHERE service_number=?");
        $upd->bind_param("iss", $currentReading, $endDate, $serviceNumber);

        if ($upd->execute()) {
            redirectWithMsg("bill.php?bill_no=" . $billNumber, "Bill Generated Successfully", "success");
        } else {
            redirectWithMsg("employee_reading_form.php", "Failed to update user reading", "error");
        }
    } else {
        redirectWithMsg("employee_reading_form.php", "Failed to generate bill", "error");
    }

} else {
    redirectWithMsg("employee_reading_form.php", "Invalid Access Method", "error");
}
?>