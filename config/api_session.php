<?php
session_start();

// Check if user is logged in and is admin or staff
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['admin', 'staff'])) {
    // For API requests, return JSON error instead of redirecting
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized. Please login.'
    ]);
    exit;
}

// Set commonly used session variables
$business_name = $_SESSION['business_name'] ?? 'Admin';
$full_name     = $_SESSION['full_name'] ?? 'Administrator';
$username      = $_SESSION['username'] ?? 'admin';
$user_type     = $_SESSION['user_type'] ?? 'admin';
$province      = $_SESSION['province'] ?? '';
