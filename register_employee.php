<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth('ADMIN');

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Invalid phone number";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $error = "Invalid password format";
    } else {
        $name = ucwords(strtolower($name));
        $stmt = $conn->prepare("INSERT INTO employees (name, email, phone, role, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $role, $password);

        if ($stmt->execute()) {
            $success = "Employee added successfully";
        } else {
            $error = "Email already exists or Database Error";
        }
    }
}

$pageTitle = "Add Employee";
require 'includes/header.php';
?>

<div class="container">
    <h3>Add Employee</h3>
    <?php if ($error): ?>
        <p class="error-msg"><?= $error ?></p><?php endif; ?>
    <?php if ($success): ?>
        <p class="success-msg"><?= $success ?></p><?php endif; ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone (10 digits)" pattern="[0-9]{10}" required>
        <select name="role" required>
            <option value="EMPLOYEE">Employee</option>
            <option value="ADMIN">Admin</option>
        </select>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Add Employee</button>
    </form>

    <div class="text-center" style="margin-top: 20px;">
        <a href="admin_dashboard.php" style="color:var(--secondary); text-decoration:none;">&larr; Back to Dashboard</a>
    </div>
</div>

<?php require 'includes/footer.php'; ?>