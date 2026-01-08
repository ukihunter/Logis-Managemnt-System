<?php
require_once '../../../config/database.php';
require_once '../../../config/session_Detils.php';

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get stats for the customer
$stats = [
    'active_orders' => 0,
    'arriving_today' => 0,
    'completed_orders' => 0,
    'total_spend_ytd' => 0,
    'loyalty_points' => 0
];

// Active Orders (orders that are not delivered or cancelled)
$active_query = "SELECT COUNT(*) as count FROM orders 
                 WHERE user_id = ? 
                 AND order_status NOT IN ('delivered', 'cancelled')";
$stmt = $conn->prepare($active_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['active_orders'] = $result->fetch_assoc()['count'];

// Orders arriving today (shipped status)
$arriving_query = "SELECT COUNT(*) as count FROM orders 
                   WHERE user_id = ? 
                   AND order_status = 'shipped'
                   AND DATE(updated_at) = CURDATE()";
$stmt = $conn->prepare($arriving_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['arriving_today'] = $result->fetch_assoc()['count'];

// Completed Orders (delivered orders)
$completed_query = "SELECT COUNT(*) as count FROM orders 
                    WHERE user_id = ? 
                    AND order_status = 'delivered'";
$stmt = $conn->prepare($completed_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['completed_orders'] = $result->fetch_assoc()['count'];

// Total Spend Year-to-Date
$ytd_query = "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders 
              WHERE user_id = ? 
              AND payment_status = 'paid'
              AND YEAR(created_at) = YEAR(CURDATE())";
$stmt = $conn->prepare($ytd_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['total_spend_ytd'] = $result->fetch_assoc()['total'];

// Get most recent delivery tracking (shipped order)
$tracking_query = "SELECT o.id, o.order_number, o.order_status, o.updated_at, 
                   o.shipping_city, d.full_name as driver_name, d.phone_number as driver_phone
                   FROM orders o
                   LEFT JOIN drivers d ON o.driver_id = d.id
                   WHERE o.user_id = ? 
                   AND o.order_status IN ('shipped', 'processing', 'packed')
                   ORDER BY o.updated_at DESC
                   LIMIT 1";
$stmt = $conn->prepare($tracking_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tracking_result = $stmt->get_result();
$tracking_order = $tracking_result->fetch_assoc();

// Get recent orders (latest 6)
$orders_query = "SELECT o.order_number, o.created_at, o.order_status, o.total_amount
                 FROM orders o
                 WHERE o.user_id = ?
                 ORDER BY o.created_at DESC
                 LIMIT 6";
$stmt = $conn->prepare($orders_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$recent_orders = [];
while ($row = $orders_result->fetch_assoc()) {
    $recent_orders[] = $row;
}

$conn->close();
