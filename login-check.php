<?php
session_start();

// Check if user is logged in
if (isset($_SESSION["email"])) {
    // Redirect to dashboard page
    header("Location: dashboard.php");
    exit();
} else {
    // Redirect to index page or login page
    header("Location: index.php");
    exit();
}
?>
