<?php
session_start();
require_once '../../../config/database.php';

if (ob_get_level()) {
    ob_clean();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['admin', 'staff'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$conn = getDBConnection();
$action = $_POST['action'] ?? '';

if ($action === 'add_category') {
    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Category name is required']);
        exit();
    }

    // Check if category already exists
    $checkQuery = "SELECT id FROM categories WHERE name = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Category already exists']);
        exit();
    }

    // Insert new category
    $insertQuery = "INSERT INTO categories (name, created_at) VALUES (?, NOW())";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Category added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add category']);
    }
} elseif ($action === 'delete_category') {
    $category_id = $_POST['category_id'] ?? 0;

    if (empty($category_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
        exit();
    }

    $deleteQuery = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $category_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
