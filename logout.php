<?php
/**
 * Logout Script
 * Ends the customer session and redirects to products page
 */

session_start();

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to products page
header('Location: products.php');
exit;
?>
