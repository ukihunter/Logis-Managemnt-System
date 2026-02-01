
<?php
require_once '../../../config/session_Detils.php';
require_once '../../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid product']);
    exit;
}

$conn = getDBConnection();

// Check if product already in cart
$stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param('ii', $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Update quantity
    $new_quantity = $row['quantity'] + $quantity;
    $update = $conn->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
    $update->bind_param('ii', $new_quantity, $row['id']);
    $success = $update->execute();
} else {
    // Insert new
    $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, added_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    $insert->bind_param('iii', $user_id, $product_id, $quantity);
    $success = $insert->execute();
}

$conn->close();

echo json_encode(['success' => $success]);
exit;
