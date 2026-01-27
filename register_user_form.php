<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth('ADMIN');

function getUserInputs()
{
    return [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'pincode' => trim($_POST['pincode'] ?? ''),
        'connection_type' => $_POST['connection_type'] ?? '',
        'current_reading' => $_POST['current_reading'] ?? ''
    ];
}

function validateUser($inputs)
{
    if (!filter_var($inputs['email'], FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }
    if (!preg_match('/^[0-9]{10}$/', $inputs['phone'])) {
        return "Invalid phone number";
    }
    if (!preg_match('/^[0-9]{6}$/', $inputs['pincode'])) {
        return "Invalid pincode";
    }
    if (!is_numeric($inputs['current_reading']) || $inputs['current_reading'] < 0) {
        return "Invalid initial reading";
    }
    return null;
}

function registerUser($conn, $inputs)
{
    $name = ucwords(strtolower($inputs['name']));

    $prefix = ($inputs['connection_type'] == "HOUSEHOLD") ? "HH" : (($inputs['connection_type'] == "COMMERCIAL") ? "COM" : "IND");
    $service_number = $prefix . strtoupper(rand(10000, 99999));

    $stmt = $conn->prepare("INSERT INTO users (service_number, name, email, phone, address, pincode, connection_type, previous_reading, registration_date, bill_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_DATE, 'UNPAID')");
    $stmt->bind_param("sssssssd", $service_number, $name, $inputs['email'], $inputs['phone'], $inputs['address'], $inputs['pincode'], $inputs['connection_type'], $inputs['current_reading']);

    if ($stmt->execute()) {
        return ['type' => 'success', 'msg' => "User added successfully. Service Number: " . $service_number];
    } else {
        return ['type' => 'error', 'msg' => "Database Error: " . $conn->error];
    }
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputs = getUserInputs();
    $validationError = validateUser($inputs);

    if ($validationError) {
        $error = $validationError;
    } else {
        $result = registerUser($conn, $inputs);
        if ($result['type'] === 'success') {
            $success = $result['msg'];
        } else {
            $error = $result['msg'];
        }
    }
}

$pageTitle = "Add User";
require 'includes/header.php';
?>

<div class="container">
    <h3>Add User</h3>
    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success-msg"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required
            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        <input type="email" name="email" placeholder="Email" required
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <input type="text" name="phone" placeholder="Phone (10 digits)" pattern="[0-9]{10}" required
            value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        <input type="text" name="address" placeholder="Address" required
            value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
        <input type="text" name="pincode" placeholder="Pincode (6 digits)" pattern="[0-9]{6}" required
            value="<?= htmlspecialchars($_POST['pincode'] ?? '') ?>">
        <select name="connection_type" required>
            <option value="HOUSEHOLD" <?= (($_POST['connection_type'] ?? '') == 'HOUSEHOLD') ? 'selected' : '' ?>>
                Household</option>
            <option value="COMMERCIAL" <?= (($_POST['connection_type'] ?? '') == 'COMMERCIAL') ? 'selected' : '' ?>>
                Commercial</option>
            <option value="INDUSTRY" <?= (($_POST['connection_type'] ?? '') == 'INDUSTRY') ? 'selected' : '' ?>>
                Industry</option>
        </select>
        <input type="number" name="current_reading" placeholder="Initial Reading" min="0" required
            value="<?= htmlspecialchars($_POST['current_reading'] ?? '') ?>">
        <button type="submit">Add User</button>
    </form>

    <div class="text-center" style="margin-top: 20px;">
        <a href="admin_dashboard.php" style="color:var(--secondary); text-decoration:none;">&larr; Back to Dashboard</a>
    </div>
</div>

<?php require 'includes/footer.php'; ?>