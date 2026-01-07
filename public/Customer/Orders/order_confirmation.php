<?php
require_once '../../../config/session_Detils.php';
require_once '../../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login/login.php');
    exit;
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$order_number = isset($_GET['order']) ? trim($_GET['order']) : '';

if (empty($order_number)) {
    header('Location: order.php');
    exit;
}

// Fetch order details
$order_query = "SELECT o.*, 
                       (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
                FROM orders o
                WHERE o.order_number = ? AND o.user_id = ?";

$stmt = $conn->prepare($order_query);
$stmt->bind_param('si', $order_number, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    $_SESSION['error'] = 'Order not found';
    header('Location: order.php');
    exit;
}

// Fetch order items
$items_query = "SELECT * FROM order_items WHERE order_id = ? ORDER BY id";
$stmt = $conn->prepare($items_query);
$stmt->bind_param('i', $order['id']);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - IslandDistro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#11d452",
                        "background-light": "#f6f8f6",
                        "background-dark": "#102216",
                    },
                    fontFamily: {
                        "display": ["Manrope", "sans-serif"]
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
            <!-- Success Message -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full mb-4 animate-bounce">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-5xl">check_circle</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Payment Successful!</h1>
                <p class="text-lg text-green-600 dark:text-green-400 font-semibold mb-2">Your order has been confirmed</p>
                <p class="text-gray-600 dark:text-gray-400">Thank you for your purchase. Your cart has been cleared.</p>
            </div>

            <!-- Order Details -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 mb-6">
                <!-- Payment Status -->
                <div class="flex items-center justify-between mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-green-600 dark:text-green-400">payment</span>
                        <span class="font-semibold text-green-700 dark:text-green-300">Payment Status</span>
                    </div>
                    <span class="px-3 py-1 bg-green-600 text-white text-sm font-bold rounded-full">PAID</span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Order Number</p>
                        <p class="font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($order['order_number']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Order Date</p>
                        <p class="font-bold text-gray-900 dark:text-white"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span class="font-semibold">Rs <?php echo number_format($order['subtotal'], 2); ?></span>
                    </div>
                    <?php if ($order['tax_amount'] > 0): ?>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600 dark:text-gray-400">Tax</span>
                            <span class="font-semibold">Rs <?php echo number_format($order['tax_amount'], 2); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['shipping_fee'] > 0): ?>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                            <span class="font-semibold">Rs <?php echo number_format($order['shipping_fee'], 2); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span>Total</span>
                        <span class="text-primary">Rs <?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Order Items (<?php echo $order['item_count']; ?>)</h3>
                <div class="space-y-3">
                    <?php foreach ($order_items as $item): ?>
                        <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                            <?php if (!empty($item['product_image'])): ?>
                                <img src="../../../<?php echo htmlspecialchars($item['product_image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="w-16 h-16 object-cover rounded">
                            <?php else: ?>
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-800 rounded flex items-center justify-center">
                                    <span class="material-symbols-outlined text-gray-400">inventory_2</span>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">SKU: <?php echo htmlspecialchars($item['product_sku']); ?> • Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">Rs <?php echo number_format($item['subtotal'], 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 mb-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Shipping Address</h3>
                <p class="text-gray-700 dark:text-gray-300"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                <?php if ($order['shipping_city']): ?>
                    <p class="text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($order['shipping_city']); ?>, <?php echo htmlspecialchars($order['shipping_province']); ?></p>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="flex gap-4">
                <a href="order.php" class="flex-1 bg-primary hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                    View All Orders
                </a>
                <a href="../Catalog/catalog.php" class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</body>

</html>