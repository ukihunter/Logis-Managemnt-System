<?php
// checkout.php - Handle checkout process using Stripe
require_once '../../../config/session_Detils.php';
require_once '../../../config/database.php';
require_once '../../../config/stripe_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login/login.php');
    exit;
}

// stripe path 
require_once '../../../vendor/autoload.php'; // Adjust path if needed

// api key location 
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

try {
    // Get user details from database
    $user_query = "SELECT email, full_name, phone_number, business_name, address, province FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($user_query);
    $stmt_user->bind_param('i', $user_id);
    $stmt_user->execute();
    $user_result = $stmt_user->get_result();
    $user = $user_result->fetch_assoc();
    //  if account is not found 
    if (!$user) {
        $_SESSION['error'] = 'User account not found';
        header('Location: cart.php');
        exit;
    }

    // Get cart items
    $cart_query = "SELECT c.id as cart_id, c.quantity,
                          p.id as product_id, p.name, p.sku, p.image_path,
                          p.unit_price, p.discount_percentage
                   FROM cart c
                   JOIN products p ON c.product_id = p.id
                   WHERE c.user_id = ? AND p.status = 'active'
                   ORDER BY c.added_at DESC";

    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    // empty massge 
    if ($result->num_rows == 0) {
        $_SESSION['error'] = 'Your cart is empty';
        header('Location: cart.php');
        exit;
    }

    $cart_items = [];
    $subtotal = 0;
    $line_items = [];
    // loop through the cart items
    while ($row = $result->fetch_assoc()) {
        $unit_price = floatval($row['unit_price']);
        $discount_percentage = floatval($row['discount_percentage']);

        // Calculate discounted price
        $discounted_price = $unit_price;
        if ($discount_percentage > 0) {
            $discounted_price = $unit_price * (1 - $discount_percentage / 100);
        }

        $item_total = $discounted_price * $row['quantity'];
        $subtotal += $item_total;
        // cart items
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
            'item_total' => $item_total
        ];

        // Prepare Stripe line items

        $stripe_price = intval($discounted_price * 100);

        $line_items[] = [
            'price_data' => [
                'currency' => strtolower(CURRENCY),
                'product_data' => [
                    'name' => $row['name'],
                    'description' => "SKU: {$row['sku']}",
                    // 'images' => [$row['image_path']], // Optional: full URL to product image
                ],
                'unit_amount' => $stripe_price,
            ],
            'quantity' => $row['quantity'],
        ];
    }

    // Store order data in session for after payment
    $_SESSION['pending_order'] = [
        'cart_items' => $cart_items,
        'subtotal' => $subtotal,
        'tax_amount' => 0,
        'shipping_fee' => 0,
        'total' => $subtotal,
        'customer_name' => $user['full_name'],
        'customer_email' => $user['email'],
        'customer_phone' => $user['phone_number'],
        'business_name' => $user['business_name'],
        'shipping_address' => $user['address'],
        'shipping_province' => $user['province']
    ];

    // Create Stripe Checkout Session
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => PAYMENT_SUCCESS_URL . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => PAYMENT_CANCEL_URL,
        'customer_email' => $user['email'],
        'billing_address_collection' => 'auto',
        'shipping_address_collection' => [
            'allowed_countries' => ['LK'], // Sri Lanka
        ],
        'metadata' => [
            'user_id' => $user_id,
            'customer_name' => $user['full_name'],
            'business_name' => $user['business_name']
        ]
    ]);

    // Store session ID for verification
    $_SESSION['stripe_session_id'] = $checkout_session->id;

    // Redirect to Stripe Checkout
    header('Location: ' . $checkout_session->url);
    exit;
} catch (\Stripe\Exception\ApiErrorException $e) {
    $_SESSION['error'] = 'Payment processing error: ' . $e->getMessage();
    header('Location: cart.php');
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = 'An error occurred: ' . $e->getMessage();
    header('Location: cart.php');
    exit;
}

// connction close
$conn->close();
