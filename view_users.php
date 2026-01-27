<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth('ADMIN');

$result = $conn->query("SELECT id,name,service_number,email,phone,connection_type FROM users");

$pageTitle = "All Users";
require 'includes/header.php';
?>

<div class="container">
    <h3>All Users</h3>
    <?php if ($result->num_rows > 0): ?>
        <table class="table">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Service #</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Connection</th>
                <th>View Bills</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['service_number'] ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['phone'] ?></td>
                    <td><?= $row['connection_type'] ?></td>
                    <td><a href="view_user_bills.php?service_number=<?= $row['service_number'] ?>" class="button">View
                            Bills</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
    <a href="admin_dashboard.php" class="button">Back to Dashboard</a>
</div>
<?php require 'includes/footer.php'; ?>