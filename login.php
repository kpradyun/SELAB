<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'ADMIN') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: employee_dashboard.php");
    }
    exit();
}

function getLoginInputs()
{
    return [
        'email' => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? ''
    ];
}

function attemptsLogin($conn, $email, $password)
{
    if (empty($email) || empty($password)) {
        return "Email and Password are required.";
    }

    $stmt = $conn->prepare("SELECT id, name, password, role FROM employees WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            $dashboard = ($row['role'] === 'ADMIN') ? 'admin_dashboard.php' : 'employee_dashboard.php';
            header("Location: $dashboard");
            exit();
        } else {
            return "Incorrect password.";
        }
    } else {
        return "No account found with this email.";
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputs = getLoginInputs();
    $error = attemptsLogin($conn, $inputs['email'], $inputs['password']);
}

$pageTitle = "Login - TGSPDCL";
require 'includes/header.php';
?>

<div class="container" style="max-width: 450px; margin-top: 80px;">
    <h2 class="text-center" style="color:var(--primary); margin-bottom: 20px;">TGSPDCL Login</h2>

    <form action="" method="POST">
        <input type="email" name="email" placeholder="Email Address" required
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn btn-block">Sign In</button>
    </form>

    <?php if ($error): ?>
        <p class="error-msg"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="text-center" style="margin-top: 20px;">
        <a href="index.html" style="color:var(--secondary); text-decoration:none;">&larr; Back to Home</a>
    </div>
</div>

<?php require 'includes/footer.php'; ?>