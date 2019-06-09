<?php
// This page logs the user out and sends them to the index page.

// Initialize the session.
session_start();

// Unset all of the session variables.
$_SESSION = array();


// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 120,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Delete the rememberMe (auto login) cookie and its value in $_COOKIE
setcookie("rememberMe", "", time() - 120); // Set the cookie to expire
unset($_COOKIE['rememberMe']); // Delete the cookie value     

session_destroy(); // Finally, destroy the session.

header("Location: index.php"); // Redirect user to the homepage