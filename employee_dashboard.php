<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth('EMPLOYEE');

$pageTitle = "Employee Dashboard";
require 'includes/header.php';
?>

<div class="container">
    <h3 class="text-center">Meter Reading Entry</h3>
    <form action="employee_reading.php" method="POST" style="margin-top: 30px;">
        <label>Service Number</label>
        <input type="text" name="service_number" placeholder="e.g. HH66251" required>

        <label>Current Meter Reading (kWh)</label>
        <input type="number" step="0.01" name="current_reading" placeholder="Enter Reading" required>

        <button type="submit" class="btn btn-block">Generate Bill</button>
    </form>
</div>

<?php require 'includes/footer.php'; ?>