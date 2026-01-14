<?php
// db connction and the sesion detils 
require_once '../../../config/session_Detils.php';
require_once '../../../config/database.php';
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}


// Create database connection
$conn = getDBConnection();

//  get the user id from the session
$user_id = $_SESSION['user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';

try {
    switch ($action) {
        // Add item to cart
        case 'add':
            $product_id = intval($_POST['product_id']);
            $quantity = intval($_POST['quantity']);

            // Check if product exists and has enough stock
            $product_query = "SELECT stock_level, min_order_quantity FROM products WHERE id = ? AND status = 'active'";
            $stmt = $conn->prepare($product_query);
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            // Validate product
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit;
            }
            // Validate quantity
            if ($product['stock_level'] < $quantity) {
                echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
                exit;
            }
            // Validate minimum order quantity
            if ($quantity < $product['min_order_quantity']) {
                echo json_encode(['success' => false, 'message' => "Minimum order quantity is {$product['min_order_quantity']}"]);
                exit;
            }

            // Check if item already in cart
            $check_query = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param('ii', $user_id, $product_id);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();
            // If exists, update quantity; else insert new
            if ($existing) {
                // Update quantity
                $new_quantity = $existing['quantity'] + $quantity;
                if ($new_quantity > $product['stock_level']) {
                    echo json_encode(['success' => false, 'message' => 'Cannot add more than available stock']);
                    exit;
                }
                // Update query
                $update_query = "UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param('ii', $new_quantity, $existing['id']);
                $stmt->execute();
            } else {
                // Insert new item
                $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param('iii', $user_id, $product_id, $quantity);
                $stmt->execute();
            }

            // Get cart count
            $count_query = "SELECT COUNT(*) as count, SUM(quantity) as total_items FROM cart WHERE user_id = ?";
            $stmt = $conn->prepare($count_query);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $cart_data = $stmt->get_result()->fetch_assoc();
            // Return success response
            echo json_encode([
                'success' => true,
                'message' => 'Product added to cart',
                'cart_count' => $cart_data['count'],
                'total_items' => $cart_data['total_items']
            ]);
            break;
        // Update cart item quantity
        case 'update':
            $cart_id = intval($_POST['cart_id']);
            $quantity = intval($_POST['quantity']);
            // Validate quantity
            if ($quantity <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
                exit;
            }

            // Get cart item and check stock
            $cart_query = "SELECT c.product_id, p.stock_level, p.min_order_quantity 
                          FROM cart c 
                          JOIN products p ON c.product_id = p.id 
                          WHERE c.id = ? AND c.user_id = ?";
            $stmt = $conn->prepare($cart_query);
            $stmt->bind_param('ii', $cart_id, $user_id);
            $stmt->execute();
            $cart_item = $stmt->get_result()->fetch_assoc();
            // Validate cart item
            if (!$cart_item) {
                echo json_encode(['success' => false, 'message' => 'Cart item not found']);
                exit;
            }
            // Validate stock and minimum order quantity
            if ($quantity > $cart_item['stock_level']) {
                echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
                exit;
            }
            // Validate minimum order quantity
            if ($quantity < $cart_item['min_order_quantity']) {
                echo json_encode(['success' => false, 'message' => "Minimum order quantity is {$cart_item['min_order_quantity']}"]);
                exit;
            }
            // Update quantity
            $update_query = "UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('iii', $quantity, $cart_id, $user_id);
            $stmt->execute();
            // Return success response
            echo json_encode(['success' => true, 'message' => 'Cart updated']);
            break;
        // Remove item from cart
        case 'remove':
            $cart_id = intval($_POST['cart_id']);
            // Remove item from cart
            $delete_query = "DELETE FROM cart WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($delete_query);
            $stmt->bind_param('ii', $cart_id, $user_id);
            $stmt->execute();
            // Return success response
            echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
            break;
        // Clear entire cart
        case 'clear':
            $clear_query = "DELETE FROM cart WHERE user_id = ?";
            $stmt = $conn->prepare($clear_query);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            // Return success response
            echo json_encode(['success' => true, 'message' => 'Cart cleared']);
            break;
        // Get cart items and summary
        case 'get':
            $cart_query = "SELECT c.id as cart_id, c.quantity, c.added_at,
                                  p.id as product_id, p.name, p.sku, p.image_path,
                                  p.unit_price, p.carton_quantity, p.carton_price,
                                  p.stock_level, p.discount_percentage, p.offer_label,
                                  p.is_featured
                           FROM cart c
                           JOIN products p ON c.product_id = p.id
                           WHERE c.user_id = ?
                           ORDER BY c.added_at DESC";
            // Execute query
            $stmt = $conn->prepare($cart_query);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            // Process results
            $cart_items = [];
            $subtotal = 0;
            // Loop through cart items
            while ($row = $result->fetch_assoc()) {
                // Parse prices and discounts    
                $unit_price = floatval($row['unit_price']);
                $discount_percentage = floatval($row['discount_percentage']);

                // Calculate discounted price
                $discounted_price = $unit_price;
                if ($discount_percentage > 0) {
                    $discounted_price = $unit_price * (1 - $discount_percentage / 100);
                }

                $item_total = $discounted_price * $row['quantity'];
                $subtotal += $item_total;

                // Check stock status
                $stock_percentage = ($row['stock_level'] > 0) ? 100 : 0;
                $is_low_stock = $row['stock_level'] < 50; // Simple threshold
                $is_out_of_stock = $row['stock_level'] <= 0;
                // Build cart item array
                $cart_items[] = [
                    'cart_id' => $row['cart_id'],
                    'product_id' => $row['product_id'],
                    'name' => $row['name'],
                    'sku' => $row['sku'],
                    'image_path' => $row['image_path'],
                    'unit_price' => $unit_price,
                    'discounted_price' => $discounted_price,
                    'discount_percentage' => $discount_percentage,
                    'quantity' => intval($row['quantity']),
                    'carton_quantity' => intval($row['carton_quantity']),
                    'carton_price' => floatval($row['carton_price']),
                    'stock_level' => intval($row['stock_level']),
                    'is_low_stock' => $is_low_stock,
                    'is_out_of_stock' => $is_out_of_stock,
                    'is_featured' => boolval($row['is_featured']),
                    'offer_label' => $row['offer_label'],
                    'item_total' => $item_total,
                    'added_at' => $row['added_at']
                ];
            }

            // Calculate totals
            $tax_rate = 0.0; // Adjust as needed
            $shipping_fee = 0.0; // Free shipping or calculate based on rules
            $tax_amount = $subtotal * $tax_rate;
            $total = $subtotal + $tax_amount + $shipping_fee;
            // Return cart details
            echo json_encode([
                'success' => true,
                'cart_items' => $cart_items,
                'summary' => [
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax_amount,
                    'shipping_fee' => $shipping_fee,
                    'total' => $total,
                    'item_count' => count($cart_items),
                    'total_quantity' => array_sum(array_column($cart_items, 'quantity'))
                ]
            ]);
            break;
        // defult action 
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    // error massge
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}


// connction closed
$conn->close();
