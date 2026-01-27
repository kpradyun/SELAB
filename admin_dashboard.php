<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth('ADMIN');

$pageTitle = "Admin Dashboard";
require 'includes/header.php';
?>

<div class="container">
    <h3>System Overview</h3>
    <div class="grid">
        <div class="card">
            <h4>Staff Management</h4>
            <p>Add or view employees</p>
            <br>
            <a href="register_employee.php" class="btn">Add Employee</a>
            <br><br>
            <a href="view_employees.php" style="color:var(--secondary);">View All Staff</a>
        </div>

        <div class="card">
            <h4>User Management</h4>
            <p>Manage consumer connections</p>
            <br>
            <a href="register_user_form.php" class="btn">Add User</a>
            <br><br>
            <a href="view_users.php" style="color:var(--secondary);">View Consumers</a>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>