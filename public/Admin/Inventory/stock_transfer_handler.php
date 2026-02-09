<?php
require_once '../../../config/admin_session.php';
require_once '../../../config/database.php';

header('Content-Type: application/json');

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Get branches for transfer modal
    if ($action === 'get_branches') {
        $query = "SELECT id, name, location FROM branches WHERE status = 'active' ORDER BY name";
        $result = $conn->query($query);

        $branches = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $branches[] = $row;
            }
        }

        echo json_encode(['success' => true, 'branches' => $branches]);
        exit;
    }

    // Process stock transfer
    if ($action === 'transfer_stock') {
        $product_id = intval($_POST['product_id'] ?? 0);
        $branch_id = intval($_POST['branch_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');

        // Validation
        if ($product_id <= 0 || $branch_id <= 0 || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid input data']);
            exit;
        }

        // Start transaction
        $conn->begin_transaction();

        try {
            // Get current product stock
            $product_query = "SELECT name, stock_level, allocated FROM products WHERE id = ?";
            $stmt = $conn->prepare($product_query);
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $product_result = $stmt->get_result();
            $product = $product_result->fetch_assoc();

            if (!$product) {
                throw new Exception('Product not found');
            }

            // Check if enough stock is available for transfer
            if ($quantity > $product['stock_level']) {
                throw new Exception('Insufficient stock available. Current stock: ' . $product['stock_level']);
            }

            // Get branch name
            $branch_query = "SELECT name FROM branches WHERE id = ?";
            $stmt = $conn->prepare($branch_query);
            $stmt->bind_param('i', $branch_id);
            $stmt->execute();
            $branch_result = $stmt->get_result();
            $branch = $branch_result->fetch_assoc();

            if (!$branch) {
                throw new Exception('Branch not found');
            }

            // Update product stock level (subtract transferred quantity)
            $update_query = "UPDATE products SET stock_level = stock_level - ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('ii', $quantity, $product_id);

            if (!$stmt->execute()) {
                throw new Exception('Failed to update product stock');
            }

            // Record the transfer
            $transfer_query = "INSERT INTO stock_transfers (product_id, branch_id, quantity, notes, transferred_by, transferred_at) 
                              VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($transfer_query);
            $stmt->bind_param('iiisi', $product_id, $branch_id, $quantity, $notes, $_SESSION['user_id']);

            if (!$stmt->execute()) {
                throw new Exception('Failed to record transfer');
            }

            // Commit transaction
            $conn->commit();

            echo json_encode([
                'success' => true,
                'message' => "Successfully transferred {$quantity} units of {$product['name']} to {$branch['name']}"
            ]);
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }

        exit;
    }

    // Get transfer history for a product
    if ($action === 'get_transfer_history') {
        $product_id = intval($_POST['product_id'] ?? 0);

        $query = "SELECT st.*, b.name as branch_name, u.full_name as transferred_by_name 
                  FROM stock_transfers st 
                  LEFT JOIN branches b ON st.branch_id = b.id 
                  LEFT JOIN users u ON st.transferred_by = u.id 
                  WHERE st.product_id = ? 
                  ORDER BY st.transferred_at DESC 
                  LIMIT 10";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $transfers = [];
        while ($row = $result->fetch_assoc()) {
            $transfers[] = $row;
        }

        echo json_encode(['success' => true, 'transfers' => $transfers]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
