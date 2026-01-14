<?php
// Brand management handler for Admin Inventory Panel 
// Start session and include database configuration
session_start();
require_once '../../../config/database.php';

if (ob_get_level()) {
    ob_clean();
}

// Set response header to JSON
header('Content-Type: application/json');

// Check if user is logged in and has admin or staff privileges
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['admin', 'staff'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Establish database connection
$conn = getDBConnection();
$action = $_POST['action'] ?? '';

// Handle add and delete brand actions
if ($action === 'add_brand') {
    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Brand name is required']);
        exit();
    }

    // Check if brand already exists
    $checkQuery = "SELECT id FROM brands WHERE name = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Brand already exists']);
        exit();
    }

    // Insert new brand
    $insertQuery = "INSERT INTO brands (name, created_at) VALUES (?, NOW())";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Brand added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add brand']);
    }
    // Close the statement
} elseif ($action === 'delete_brand') {
    $brand_id = $_POST['brand_id'] ?? 0;

    if (empty($brand_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid brand ID']);
        exit();
    }

    $deleteQuery = "DELETE FROM brands WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $brand_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Brand deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete brand']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

// Close the database connection
$conn->close();
