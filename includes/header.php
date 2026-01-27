<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = isset($pageTitle) ? $pageTitle : 'TGSPDCL';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($pageTitle) ?>
    </title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="navbar">
            <h1>TGSPDCL Portal</h1>
            <div class="nav-links">
                <span>Welcome,
                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
                </span>
                <a href="logout.php" style="background:var(--danger); padding: 5px 10px; border-radius:4px;">Logout</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="main-content">
        <?php
        require_once 'functions.php';
        $flash = getSessionMsg();
        if ($flash): ?>
            <div class="<?= $flash['type'] == 'success' ? 'success-msg' : 'error-msg' ?>">
                <?= htmlspecialchars($flash['msg']) ?>
            </div>
        <?php endif; ?>