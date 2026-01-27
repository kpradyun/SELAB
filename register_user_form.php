<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth('ADMIN');

function getUserInputs()
{
    $inputs = [];
    
    if (isset($_POST['name'])) {
        $inputs['name'] = trim($_POST['name']);
    } else {
        $inputs['name'] = '';
    }

    if (isset($_POST['email'])) {
        $inputs['email'] = trim($_POST['email']);
    } else {
        $inputs['email'] = '';
    }

    if (isset($_POST['phone'])) {
        $inputs['phone'] = trim($_POST['phone']);
    } else {
        $inputs['phone'] = '';
    }

    if (isset($_POST['address'])) {
        $inputs['address'] = trim($_POST['address']);
    } else {
        $inputs['address'] = '';
    }

    if (isset($_POST['pincode'])) {
        $inputs['pincode'] = trim($_POST['pincode']);
    } else {
        $inputs['pincode'] = '';
    }

    if (isset($_POST['connection_type'])) {
        $inputs['connection_type'] = $_POST['connection_type'];
    } else {
        $inputs['connection_type'] = '';
    }

    if (isset($_POST['current_reading'])) {
        $inputs['current_reading'] = $_POST['current_reading'];
    } else {
        $inputs['current_reading'] = '';
    }

    return $inputs;
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

    $checkPhone = $conn->prepare("SELECT id FROM users WHERE phone = ?");
    $checkPhone->bind_param("s", $inputs['phone']);
    $checkPhone->execute();
    $result = $checkPhone->get_result();
    if ($result->num_rows > 0) {
        return ['type' => 'error', 'msg' => "Phone number already exists. Please use a different number."];
    }

    $service_number = "";
    $isUnique = false;

    while (!$isUnique) {
        $prefix = "";
        if ($inputs['connection_type'] == "HOUSEHOLD") {
            $prefix = "1";
        } elseif ($inputs['connection_type'] == "COMMERCIAL") {
            $prefix = "2";
        } else { // INDUSTRY
            $prefix = "3";
        }

        $randomPart = rand(100000000, 999999999);
        $service_number = $prefix . $randomPart;

        $checkSn = $conn->prepare("SELECT id FROM users WHERE service_number = ?");
        $checkSn->bind_param("s", $service_number);
        $checkSn->execute();
        $snResult = $checkSn->get_result();

        if ($snResult->num_rows == 0) {
            $isUnique = true;
        }
    }

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
            value="<?php if (isset($_POST['name'])) {
                echo htmlspecialchars($_POST['name']);
            } ?>">
        <input type="email" name="email" placeholder="Email" required
            value="<?php if (isset($_POST['email'])) {
                echo htmlspecialchars($_POST['email']);
            } ?>">
        <input type="text" name="phone" placeholder="Phone (10 digits)" pattern="[0-9]{10}" required
            value="<?php if (isset($_POST['phone'])) {
                echo htmlspecialchars($_POST['phone']);
            } ?>">
        <input type="text" name="address" placeholder="Address" required
            value="<?php if (isset($_POST['address'])) {
                echo htmlspecialchars($_POST['address']);
            } ?>">
        <input type="text" name="pincode" placeholder="Pincode (6 digits)" pattern="[0-9]{6}" required
            value="<?php if (isset($_POST['pincode'])) {
                echo htmlspecialchars($_POST['pincode']);
            } ?>">
        <select name="connection_type" required>
            <option value="HOUSEHOLD" <?php if (isset($_POST['connection_type']) && $_POST['connection_type'] == 'HOUSEHOLD') {
                echo 'selected';
            } ?>>
                Household</option>
            <option value="COMMERCIAL" <?php if (isset($_POST['connection_type']) && $_POST['connection_type'] == 'COMMERCIAL') {
                echo 'selected';
            } ?>>
                Commercial</option>
            <option value="INDUSTRY" <?php if (isset($_POST['connection_type']) && $_POST['connection_type'] == 'INDUSTRY') {
                echo 'selected';
            } ?>>
                Industry</option>
        </select>
        <input type="number" name="current_reading" placeholder="Initial Reading" min="0" required
            value="<?php if (isset($_POST['current_reading'])) {
                echo htmlspecialchars($_POST['current_reading']);
            } ?>">
        <button type="submit">Add User</button>
    </form>

    <div class="text-center" style="margin-top: 20px;">
        <a href="admin_dashboard.php" style="color:var(--secondary); text-decoration:none;">&larr; Back to Dashboard</a>
    </div>
</div>

<?php require 'includes/footer.php'; ?>