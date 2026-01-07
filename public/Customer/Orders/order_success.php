<?php
require_once '../../../config/session_Detils.php';
require_once '../../../config/database.php';
require_once '../../../config/stripe_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login/login.php');
    exit;
}

// Install Stripe PHP library first
require_once '../../../vendor/autoload.php';
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : '';

if (empty($session_id) || !isset($_SESSION['pending_order'])) {
    $_SESSION['error'] = 'Invalid payment session';
    header('Location: ../Cart/cart.php');
    exit;
}

try {
    // Verify the Stripe session
    $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);

    if ($checkout_session->payment_status !== 'paid') {
        $_SESSION['error'] = 'Payment was not completed';
        header('Location: ../Cart/cart.php');
        exit;
    }

    // Get pending order from session
    $pending_order = $_SESSION['pending_order'];

    // Generate unique order number
    $order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

    // Get shipping address from Stripe if provided
    $shipping_address = $pending_order['shipping_address'];
    $shipping_city = '';
    $shipping_province = $pending_order['shipping_province'];
    $shipping_postal_code = '';

    if (isset($checkout_session->shipping_details) && $checkout_session->shipping_details) {
        $stripe_shipping = $checkout_session->shipping_details->address;
        $shipping_address = implode(', ', array_filter([
            $stripe_shipping->line1,
            $stripe_shipping->line2,
            $stripe_shipping->city,
            $stripe_shipping->state,
            $stripe_shipping->postal_code,
            $stripe_shipping->country
        ]));
        $shipping_city = $stripe_shipping->city ?? '';
        $shipping_province = $stripe_shipping->state ?? $pending_order['shipping_province'];
        $shipping_postal_code = $stripe_shipping->postal_code ?? '';
    }

    // Start transaction
    $conn->begin_transaction();

    // Insert order
    $insert_order = "INSERT INTO orders (
        user_id, order_number, customer_name, customer_email, customer_phone, business_name,
        shipping_address, shipping_city, shipping_province, shipping_postal_code,
        subtotal, tax_amount, shipping_fee, discount_amount, total_amount,
        payment_method, payment_status, stripe_payment_intent_id, stripe_charge_id,
        paid_at, order_status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'stripe', 'paid', ?, ?, NOW(), 'pending')";

    $stmt = $conn->prepare($insert_order);
    $discount_amount = 0; // Must be a variable for bind_param
    $stmt->bind_param(
        'isssssssssddddsss',
        $user_id,
        $order_number,
        $pending_order['customer_name'],
        $pending_order['customer_email'],
        $pending_order['customer_phone'],
        $pending_order['business_name'],
        $shipping_address,
        $shipping_city,
        $shipping_province,
        $shipping_postal_code,
        $pending_order['subtotal'],
        $pending_order['tax_amount'],
        $pending_order['shipping_fee'],
        $discount_amount,
        $pending_order['total'],
        $checkout_session->payment_intent,
        $checkout_session->id
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to create order');
    }

    $order_id = $conn->insert_id;

    // Insert order items
    $insert_item = "INSERT INTO order_items (
        order_id, product_id, product_name, product_sku, product_image,
        unit_price, quantity, discount_percentage, subtotal
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insert_item);

    foreach ($pending_order['cart_items'] as $item) {
        $stmt->bind_param(
            'iisssdids',
            $order_id,
            $item['product_id'],
            $item['name'],
            $item['sku'],
            $item['image_path'],
            $item['unit_price'],
            $item['quantity'],
            $item['discount_percentage'],
            $item['item_total']
        );

        if (!$stmt->execute()) {
            throw new Exception('Failed to add order item');
        }

        // Update product stock (reduce allocated stock)
        $update_stock = "UPDATE products SET allocated = allocated + ? WHERE id = ?";
        $stmt_stock = $conn->prepare($update_stock);
        $stmt_stock->bind_param('ii', $item['quantity'], $item['product_id']);
        $stmt_stock->execute();
    }

    // Insert order status history
    $insert_history = "INSERT INTO order_status_history (order_id, status, notes) VALUES (?, 'pending', 'Order placed successfully')";
    $stmt = $conn->prepare($insert_history);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();

    // Clear user's cart
    $clear_cart = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($clear_cart);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    // Clear pending order from session
    unset($_SESSION['pending_order']);
    unset($_SESSION['stripe_session_id']);

    // Set success message
    $_SESSION['success'] = 'Order placed successfully!';
    $_SESSION['last_order_id'] = $order_id;
    $_SESSION['last_order_number'] = $order_number;

    // Redirect to order confirmation page
    header('Location: order_confirmation.php?order=' . $order_number);
    exit;
} catch (\Stripe\Exception\ApiErrorException $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    $_SESSION['error'] = 'Payment verification failed: ' . $e->getMessage();
    header('Location: ../Cart/cart.php');
    exit;
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    $_SESSION['error'] = 'Order processing failed: ' . $e->getMessage();
    header('Location: ../Cart/cart.php');
    exit;
}

$conn->close();
