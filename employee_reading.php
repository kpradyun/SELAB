<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth('EMPLOYEE');

function getReadingInputs()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirectWithMsg("employee_reading_form.php", "Invalid Access Method", "error");
    }
    return [
        'service_number' => $_POST['service_number'] ?? '',
        'current_reading' => floatval($_POST['current_reading'] ?? 0)
    ];
}

function validateReading($conn, $serviceNumber, $currentReading)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE service_number = ?");
    $stmt->bind_param("s", $serviceNumber);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        redirectWithMsg("employee_reading_form.php", "Invalid Service Number", "error");
    }

    if ($currentReading < $user['previous_reading']) {
        redirectWithMsg("employee_reading_form.php", "Error: Current reading cannot be less than previous reading ({$user['previous_reading']})", "error");
    }

    return $user;
}

function calculateBillAmount($type, $units)
{
    if ($type == "HOUSEHOLD") {
        $minCharge = 25;
        if ($units <= 50)
            $amt = $units * 1.5;
        elseif ($units <= 100)
            $amt = (50 * 1.5) + (($units - 50) * 2.5);
        elseif ($units <= 150)
            $amt = (50 * 1.5) + (100 * 2.5) + (($units - 100) * 3.5);
        else
            $amt = (50 * 1.5) + (100 * 2.5) + (50 * 3.5) + (($units - 150) * 4.5);
    } elseif ($type == "COMMERCIAL") {
        $minCharge = 50;
        if ($units <= 50)
            $amt = $units * 2.5;
        elseif ($units <= 100)
            $amt = (50 * 2.5) + (($units - 50) * 3.5);
        elseif ($units <= 150)
            $amt = (50 * 2.5) + (100 * 3.5) + (($units - 100) * 4.5);
        else
            $amt = (50 * 2.5) + (100 * 3.5) + (50 * 4.5) + (($units - 150) * 5.5);
    } else { // INDUSTRY
        $minCharge = 100;
        if ($units <= 50)
            $amt = $units * 3.5;
        elseif ($units <= 100)
            $amt = (50 * 3.5) + (($units - 50) * 4.5);
        elseif ($units <= 150)
            $amt = (50 * 3.5) + (100 * 4.5) + (($units - 100) * 5.5);
        else
            $amt = (50 * 3.5) + (100 * 4.5) + (50 * 5.5) + (($units - 150) * 6.5);
    }

    return ($units == 0) ? $minCharge : $amt;
}

function getFineAmount($conn, $serviceNumber, $billStatus)
{
    $fineAmount = 0;
    if ($billStatus == 'UNPAID') {
        $lastBillQ = $conn->prepare("SELECT total_amount FROM bills WHERE service_number=? AND paid_status='UNPAID' ORDER BY end_date DESC LIMIT 1");
        $lastBillQ->bind_param("s", $serviceNumber);
        $lastBillQ->execute();
        $lastRes = $lastBillQ->get_result();
        if ($lastBill = $lastRes->fetch_assoc()) {
            $fineAmount = $lastBill['total_amount'] * 0.03;
        }
    }
    return $fineAmount;
}

function saveReadingAndBill($conn, $user, $currentReading, $billAmount, $fineAmount)
{
    $serviceNumber = $user['service_number'];
    $units = $currentReading - $user['previous_reading'];
    $startDate = $user['last_bill_date'] ? date("Y-m-d", strtotime($user['last_bill_date'] . " +1 day")) : $user['registration_date'];
    $endDate = date("Y-m-d");
    $billNumber = strtoupper(substr($user['connection_type'], 0, 3)) . rand(10000, 99999);
    $totalToPay = $billAmount + $fineAmount;

    $conn->begin_transaction();

    try {
        $ins = $conn->prepare("INSERT INTO bills (bill_number, service_number, start_date, end_date, units_consumed, total_amount, fine_amount, paid_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'UNPAID')");
        $ins->bind_param("ssssddd", $billNumber, $serviceNumber, $startDate, $endDate, $units, $totalToPay, $fineAmount);

        if (!$ins->execute()) {
            throw new Exception("Failed to generate bill: " . $conn->error);
        }

        $upd = $conn->prepare("UPDATE users SET previous_reading=?, last_bill_date=?, bill_status='UNPAID' WHERE service_number=?");
        $upd->bind_param("iss", $currentReading, $endDate, $serviceNumber);

        if (!$upd->execute()) {
            throw new Exception("Failed to update user reading: " . $conn->error);
        }

        $conn->commit();
        return $billNumber;

    } catch (Exception $e) {
        $conn->rollback();
        redirectWithMsg("employee_reading_form.php", $e->getMessage(), "error");
    }
}

try {
    $inputs = getReadingInputs();
    $serviceNumber = $inputs['service_number'];
    $currentReading = $inputs['current_reading'];

    $user = validateReading($conn, $serviceNumber, $currentReading);

    $units = $currentReading - $user['previous_reading'];
    $billAmount = calculateBillAmount($user['connection_type'], $units);
    $fineAmount = getFineAmount($conn, $serviceNumber, $user['bill_status']);

    $billNumber = saveReadingAndBill($conn, $user, $currentReading, $billAmount, $fineAmount);

    redirectWithMsg("bill.php?bill_no=" . $billNumber, "Bill Generated Successfully", "success");

} catch (Exception $e) {
    redirectWithMsg("employee_reading_form.php", "An unexpected error occurred: " . $e->getMessage(), "error");
}
?>