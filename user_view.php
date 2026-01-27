<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$user = null;
$bills = null;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceNumber = trim($_POST['service_number']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE service_number = ?");
    $stmt->bind_param("s", $serviceNumber);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        $error = "Invalid Service Number";
    } else {
        $user = $res->fetch_assoc();

        $b = $conn->prepare(
            "SELECT * FROM bills WHERE service_number = ? ORDER BY end_date DESC"
        );
        $b->bind_param("s", $serviceNumber);
        $b->execute();
        $bills = $b->get_result();
    }
}

$pageTitle = "Electricity Bill";
require 'includes/header.php';
?>

<?php if (!isset($_SESSION['user_id'])): ?>
    <div class="navbar">
        <h1>TGSPDCL</h1>
        <div class="nav-links">
            <a href="login.php" style="background:var(--secondary); padding:6px 12px; border-radius:4px;">
                Go to Login
            </a>
        </div>
    </div>
<?php endif; ?>


<div class="container">

    <?php if ($user === null): ?>

        <h3>View Electricity Bill</h3>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="no-print">
            <input type="text" name="service_number" placeholder="Service Number" required>
            <button type="submit" class="btn-block">View Bill</button>
        </form>

    <?php else: ?>

        <?php while ($bill = $bills->fetch_assoc()): ?>

            <h2>Electricity Supply Board</h2>
            <h4>Consumer Electricity Bill</h4>

            <table class="table">
                <tr>
                    <td><b>Consumer Name</b><br><?= htmlspecialchars($user['name']) ?></td>
                    <td><b>Service Number</b><br><?= htmlspecialchars($user['service_number']) ?></td>
                </tr>
                <tr>
                    <td><b>Connection Type</b><br><?= htmlspecialchars($user['connection_type']) ?></td>
                    <td><b>Bill Number</b><br><?= htmlspecialchars($bill['bill_number']) ?></td>
                </tr>
                <tr>
                    <td><b>Email</b><br><?= htmlspecialchars($user['email']) ?></td>
                    <td><b>Phone</b><br><?= htmlspecialchars($user['phone']) ?></td>
                </tr>
                <tr>
                    <td><b>Area PIN</b><br><?= htmlspecialchars($user['pincode']) ?></td>
                    <td><b>Status</b><br><?= htmlspecialchars($bill['paid_status']) ?></td>
                </tr>
            </table>

            <table class="table">
                <tr>
                    <th>Billing Period</th>
                    <th>Units Consumed</th>
                    <th>Due Date</th>
                </tr>
                <tr>
                    <td>
                        <?= date("d-m-Y", strtotime($bill['start_date'])) ?>
                        to
                        <?= date("d-m-Y", strtotime($bill['end_date'])) ?>
                    </td>
                    <td><?= htmlspecialchars($bill['units_consumed']) ?> Units</td>
                    <td><?= date("d-m-Y", strtotime($bill['end_date'] . " +15 days")) ?></td>
                </tr>
            </table>

            <table class="table">
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
                <tr>
                    <td>Energy Charges</td>
                    <td class="text-right">
                        ₹<?= number_format($bill['total_amount'] - $bill['fine_amount'], 2) ?>
                    </td>
                </tr>
                <tr>
                    <td>Late Payment Charges</td>
                    <td class="text-right">
                        ₹<?= number_format($bill['fine_amount'], 2) ?>
                    </td>
                </tr>
                <tr>
                    <th>Total Amount Payable</th>
                    <th class="text-right">
                        ₹<?= number_format($bill['total_amount'], 2) ?>
                    </th>
                </tr>
            </table>

            <div class="text-center" style="margin-top:20px;">
                This is a system generated bill. Please pay on or before due date.
            </div>

            <hr style="margin:30px 0; border:1px dashed #ccc;">

        <?php endwhile; ?>

        <a href="user_view.php" class="back-link no-print">Check another Service Number</a>

    <?php endif; ?>

</div>

</div>
<?php require 'includes/footer.php'; ?>