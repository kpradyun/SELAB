<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

checkAuth('ADMIN');

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['name'])) {
        $name = trim($_POST['name']);
    } else {
        $name = '';
    }

    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
    } else {
        $email = '';
    }

    if (isset($_POST['phone'])) {
        $phone = trim($_POST['phone']);
    } else {
        $phone = '';
    }

    if (isset($_POST['role'])) {
        $role = $_POST['role'];
    } else {
        $role = 'EMPLOYEE';
    }

    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $password = '';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (strlen($phone) != 10 || !is_numeric($phone)) {
        $error = "Invalid phone number";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters";
    } else {
        $name = ucwords(strtolower($name));

        $stmt = $conn->prepare("INSERT INTO employees (name, email, phone, role, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $role, $password);

        if ($stmt->execute()) {
            $success = "Employee added successfully";
        } else {
            $error = "Email or Phone might already exist.";
        }
    }
}

$pageTitle = "Add Employee";
require 'includes/header.php';
?>

<div class="container">
    <h3>Add Employee</h3>

    <?php if ($error != "") { ?>
        <p class="error-msg"><?php echo $error; ?></p>
    <?php } ?>

    <?php if ($success != "") { ?>
        <p class="success-msg"><?php echo $success; ?></p>
    <?php } ?>

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

        <select name="role" required>
            <option value="EMPLOYEE" <?php if (isset($_POST['role']) && $_POST['role'] == 'EMPLOYEE') {
                echo 'selected';
            } ?>>Employee</option>
            <option value="ADMIN" <?php if (isset($_POST['role']) && $_POST['role'] == 'ADMIN') {
                echo 'selected';
            } ?>>
                Admin</option>
        </select>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Add Employee</button>
    </form>

    <div class="text-center" style="margin-top: 20px;">
        <a href="admin_dashboard.php" style="color:var(--secondary); text-decoration:none;">&larr; Back to Dashboard</a>
    </div>
</div>

<?php require 'includes/footer.php'; ?>