<?php
session_start();

// Check if user is logged in and is admin or staff
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['admin', 'staff'])) {
    header('Location: ../../login/login.php');
    exit;
}

// Set commonly used session variables
$business_name = $_SESSION['business_name'] ?? 'Admin';
$full_name     = $_SESSION['full_name'] ?? 'Administrator';
$username      = $_SESSION['username'] ?? 'admin';
$user_type     = $_SESSION['user_type'] ?? 'admin';
$province      = $_SESSION['province'] ?? '';
