<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth();

if (!isset($_GET['bill_no']))
    die("Invalid Request");

$billNo = $_GET['bill_no'];
$stmt = $conn->prepare("SELECT b.*, u.name, u.address, u.connection_type FROM bills b JOIN users u ON b.service_number = u.service_number WHERE b.bill_number = ?");
$stmt->bind_param("s", $billNo);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc())
    die("Bill Not Found");

$dueDate = date("d-M-Y", strtotime($row['end_date'] . ' + 15 days'));

$pageTitle = "Bill #" . $row['bill_number'];
require 'includes/header.php';
?>

<div class="navbar no-print">
    <h1>Bill Generated</h1>
    <div class="nav-links">
        <a href="javascript:window.print()">Print Bill</a>
        <a href="employee_dashboard.php">Back to Dashboard</a>
    </div>
</div>

<div class="container" style="max-width: 700px; margin-top: 20px;">
    <div style="border-bottom: 2px solid #0d47a1; padding-bottom: 10px; margin-bottom: 20px; text-align: center;">
        <h2 style="color:#0d47a1;">ELECTRICITY BILL</h2>
        <p><strong>TGSPDCL Distribution Company</strong></p>
    </div>

    <table style="width:100%; border:none;">
        <tr>
            <td style="border:none;">
                <strong>Name:</strong> <?= htmlspecialchars($row['name']) ?><br>
                <strong>Address:</strong> <?= htmlspecialchars($row['address']) ?><br>
                <strong>Connection:</strong> <?= $row['connection_type'] ?>
            </td>
            <td style="border:none; text-align:right;">
                <strong>Bill No:</strong> <?= $row['bill_number'] ?><br>
                <strong>Date:</strong> <?= date("d-M-Y", strtotime($row['end_date'])) ?><br>
                <strong>Service No:</strong> <?= $row['service_number'] ?><br>
                <strong style="color:#d32f2f;">Due Date: <?= $dueDate ?></strong>
            </td>
        </tr>
    </table>

    <table class="table" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align:right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Units Consumed (<?= $row['units_consumed'] ?> Units)</td>
                <td style="text-align:right;">-</td>
            </tr>
            <tr>
                <td>Energy Charges</td>
                <td style="text-align:right;">₹<?= number_format($row['total_amount'] - $row['fine_amount'], 2) ?>
                </td>
            </tr>
            <tr>
                <td>Late Payment Fines</td>
                <td style="text-align:right; color:red;">₹<?= number_format($row['fine_amount'], 2) ?></td>
            </tr>
            <tr style="background:#e3f2fd; font-weight:bold;">
                <td>Total Payable</td>
                <td style="text-align:right; font-size: 1.2rem;">₹<?= number_format($row['total_amount'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <p style="text-align:center; margin-top:30px; font-size:0.9rem; color:#666;">This is a computer generated
        invoice.</p>
</div>
</div>
<?php require 'includes/footer.php'; ?>