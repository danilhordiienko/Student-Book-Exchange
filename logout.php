<?php
// Simple logout script for the Student Book Exchange project.
// Clears the current session and redirects the user back to the homepage.

require_once __DIR__ . '/db.php';

// Clear all session data for the current user.
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 3600,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy the session completely.
session_destroy();

// Redirect to the homepage after logout.
header('Location: index.php');
exit;
?>