<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth('ADMIN');

$result = $conn->query("SELECT id,name,email,phone,role FROM employees");

$pageTitle = "All Employees";
require 'includes/header.php';
?>

<div class="container">
    <h3>All Employees</h3>
    <?php if ($result->num_rows > 0): ?>
        <table class="table">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['phone'] ?></td>
                    <td><?= $row['role'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No employees found.</p>
    <?php endif; ?>
    <a href="admin_dashboard.php" class="button">Back to Dashboard</a>
</div>
<?php require 'includes/footer.php'; ?>
<?php $conn->close(); ?>