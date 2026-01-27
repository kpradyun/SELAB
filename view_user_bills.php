<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth();

$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN');
$serviceNumber = $_GET['service_number'] ?? null;

if (!$serviceNumber) {
    die("Invalid Service Number");
}

if ($isAdmin && isset($_GET['action']) && $_GET['action'] == 'toggle' && isset($_GET['bill_id'])) {
    $bill_id = intval($_GET['bill_id']);

    $stmt = $conn->prepare("SELECT paid_status FROM bills WHERE id = ?");
    $stmt->bind_param("i", $bill_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $new_status = ($row['paid_status'] === 'PAID') ? 'UNPAID' : 'PAID';

        $update = $conn->prepare("UPDATE bills SET paid_status = ? WHERE id = ?");
        $update->bind_param("si", $new_status, $bill_id);
        $update->execute();

        $checkLatest = $conn->prepare("SELECT paid_status FROM bills WHERE service_number = ? ORDER BY end_date DESC LIMIT 1");
        $checkLatest->bind_param("s", $serviceNumber);
        $checkLatest->execute();

        if ($latestRow = $checkLatest->get_result()->fetch_assoc()) {
            $userStatus = $latestRow['paid_status'];
            $updateUser = $conn->prepare("UPDATE users SET bill_status = ? WHERE service_number = ?");
            $updateUser->bind_param("ss", $userStatus, $serviceNumber);
            $updateUser->execute();
        }
    }

    redirectWithMsg("view_user_bills.php?service_number=" . $serviceNumber, "Bill status updated", "success");
}

$stmt = $conn->prepare("SELECT * FROM bills WHERE service_number=? ORDER BY start_date DESC");
$stmt->bind_param("s", $serviceNumber);
$stmt->execute();
$result = $stmt->get_result();

$pageTitle = "Bills of " . htmlspecialchars($serviceNumber);
require 'includes/header.php';
?>

<div class="container">
    <h3>History for Service #: <?= htmlspecialchars($serviceNumber) ?></h3>

    <?php if ($result->num_rows > 0): ?>
        <table class="table">
            <tr>
                <th>Bill #</th>
                <th>Period</th>
                <th>Units</th>
                <th>Total</th>
                <th>Status</th>
                <?php if ($isAdmin): ?>
                    <th>Admin Action</th>
                <?php endif; ?>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['bill_number'] ?></td>
                    <td>
                        <?= date("d M Y", strtotime($row['start_date'])) ?> -
                        <?= date("d M Y", strtotime($row['end_date'])) ?>
                    </td>
                    <td><?= $row['units_consumed'] ?></td>
                    <td>₹<?= number_format($row['total_amount'], 2) ?></td>

                    <td>
                        <?php if ($row['paid_status'] == 'PAID'): ?>
                            <span style="color:green; font-weight:bold;">PAID</span>
                        <?php else: ?>
                            <span style="color:red; font-weight:bold;">UNPAID</span>
                        <?php endif; ?>
                    </td>

                    <?php if ($isAdmin): ?>
                        <td class="text-center">
                            <?php if ($row['paid_status'] == 'UNPAID'): ?>
                                <a href="view_user_bills.php?service_number=<?= $serviceNumber ?>&action=toggle&bill_id=<?= $row['id'] ?>"
                                    class="btn" style="padding: 5px 10px; font-size: 12px; background: #2e7d32;">
                                    Mark Paid
                                </a>
                            <?php else: ?>
                                <a href="view_user_bills.php?service_number=<?= $serviceNumber ?>&action=toggle&bill_id=<?= $row['id'] ?>"
                                    class="btn" style="padding: 5px 10px; font-size: 12px; background: #d32f2f;">
                                    Mark Unpaid
                                </a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>

                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p class="text-center">No bills found for this user.</p>
    <?php endif; ?>

    <div class="text-center" style="margin-top:20px;">
        <?php if ($isAdmin): ?>
            <a href="view_users.php" class="back-link">Back to User List</a>
        <?php else: ?>
            <a href="employee_dashboard.php" class="back-link">Back to Dashboard</a>
        <?php endif; ?>
    </div>
</div>
<?php require 'includes/footer.php'; ?>