<?php
// Connection to database and session details
require_once '../../../config/database.php';
require_once '../../../config/session_Detils.php';

// Set header for JSON response
header('Content-Type: application/json');

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID not provided']);
    exit;
}

// Sanitize and validate order_id
$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Connection start
$conn = getDBConnection();

// Get order details with security check
$order_query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Order not found or does not belong to user
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    $conn->close();
    exit;
}

// Fetch order data
$order = $result->fetch_assoc();

// Get order items
$items_query = "SELECT * FROM order_items WHERE order_id = ? ORDER BY id";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();


// Fetch items
$items = [];
while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}

// Close the database connection
$conn->close();

// Return JSON response
echo json_encode([
    'success' => true,
    'order' => $order,
    'items' => $items
]);
