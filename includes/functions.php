<?php
function sanitizeInput($data)
{
    if (is_array($data)) {
        $cleanData = [];
        foreach ($data as $key => $value) {
            $cleanData[$key] = sanitizeInput($value);
        }
        return $cleanData;
    }
    return htmlspecialchars(stripslashes(trim($data)));
}


function redirectWithMsg($url, $msg, $type = 'error')
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['msg'] = $msg;
    $_SESSION['msg_type'] = $type;
    header("Location: $url");
    exit();
}


function getSessionMsg()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['msg'])) {
        $msg = $_SESSION['msg'];
        if (isset($_SESSION['msg_type'])) {
            $type = $_SESSION['msg_type'];
        } else {
            $type = 'error';
        }

        unset($_SESSION['msg']);
        unset($_SESSION['msg_type']);

        return ['msg' => $msg, 'type' => $type];
    }
    return null;
}

function checkAuth($requiredRole = null)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    if ($requiredRole != null) {
        if ($_SESSION['role'] !== $requiredRole) {
            if ($_SESSION['role'] === 'ADMIN') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: employee_dashboard.php");
            }
            exit();
        }
    }
}
?>