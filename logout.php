<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the login page
header('Location: /php_exam/pages/login.php');
exit;
?>
