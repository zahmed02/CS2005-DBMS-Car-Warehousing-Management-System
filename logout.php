<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Optional: Clear the session cookie (for added safety)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login page
echo "<script>alert('You have been logged out successfully.');</script>";
echo "<script>window.location.href = 'login.php';</script>";
exit();
?>