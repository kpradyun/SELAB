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

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
    } else {
        $email = '';
    }

    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $password = '';
    }

    if ($email == "" || $password == "") {
        $error = "Email and Password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password, role FROM employees WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($password === $row['password']) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['role'] = $row['role'];

                // Redirect
                if ($row['role'] === 'ADMIN') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: employee_dashboard.php");
                }
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with this email.";
        }
    }
}

$pageTitle = "Login - TGSPDCL";
require 'includes/header.php';
?>

<div class="container" style="max-width: 450px; margin-top: 80px;">
    <h2 class="text-center" style="color:var(--primary); margin-bottom: 20px;">TGSPDCL Login</h2>

    <form action="" method="POST">
        <input type="email" name="email" placeholder="Email Address" required
            value="<?php if (isset($_POST['email'])) {
                echo htmlspecialchars($_POST['email']);
            } ?>">
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn btn-block">Sign In</button>
    </form>

    <?php if ($error != "") { ?>
        <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?>

    <div class="text-center" style="margin-top: 20px;">
        <a href="index.html" style="color:var(--secondary); text-decoration:none;">&larr; Back to Home</a>
    </div>
</div>

<?php require 'includes/footer.php'; ?>