<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the active session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();