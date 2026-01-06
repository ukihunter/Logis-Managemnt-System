<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: ../../login/login.php');
    exit;
}

// Set commonly used session variables
$business_name = $_SESSION['business_name'] ?? 'Customer';
$full_name     = $_SESSION['full_name'] ?? 'User';
$username      = $_SESSION['username'] ?? 'guest';
