<?php
//  db connction and the sesion detils
require_once '../../../config/database.php';
require_once '../../../config/session_Detils.php';

// opn the conction 
$conn = getDBConnection();
// get the data acording to the  login session data
$user_id = $_SESSION['user_id'];

// Get filter and search parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 7;
$offset = ($page - 1) * $per_page;

// Get stats for the customer
$stats = [
    'active_orders' => 0,
    'arriving_today' => 0,
    'pending_payment' => 0,
    'total_spend' => 0,
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
                   AND order_status = 'shipped'";
$stmt = $conn->prepare($arriving_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['arriving_today'] = $result->fetch_assoc()['count'];

// Pending Payment
$payment_query = "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders 
                  WHERE user_id = ? 
                  AND payment_status = 'pending'";
$stmt = $conn->prepare($payment_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['pending_payment'] = $result->fetch_assoc()['total'];

// Total Spend (all paid orders)
$spend_query = "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders 
                WHERE user_id = ? 
                AND payment_status = 'paid'";
$stmt = $conn->prepare($spend_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['total_spend'] = $result->fetch_assoc()['total'];

// Build orders query with filters
$where_conditions = ["user_id = ?"];
$params = [$user_id];
$param_types = "i";

// Status filter
if ($status_filter !== 'all') {
    $status_map = [
        'pending' => 'pending',
        'in_transit' => 'shipped',
        'completed' => 'delivered',
        'cancelled' => 'cancelled'
    ];

    if (isset($status_map[$status_filter])) {
        $where_conditions[] = "order_status = ?";
        $params[] = $status_map[$status_filter];
        $param_types .= "s";
    }
}

// Search filter
if (!empty($search_query)) {
    $where_conditions[] = "order_number LIKE ?";
    $params[] = "%$search_query%";
    $param_types .= "s";
}

$where_clause = implode(" AND ", $where_conditions);

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM orders WHERE $where_clause";
$stmt = $conn->prepare($count_query);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$total_orders = $result->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $per_page);

// Get orders with pagination
$orders_query = "SELECT id, order_number, created_at, order_status, total_amount, payment_status 
                 FROM orders 
                 WHERE $where_clause
                 ORDER BY created_at DESC
                 LIMIT ? OFFSET ?";

$params[] = $per_page;
$params[] = $offset;
$param_types .= "ii";

$stmt = $conn->prepare($orders_query);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$orders_result = $stmt->get_result();

$orders = [];
while ($row = $orders_result->fetch_assoc()) {
    $orders[] = $row;
}

// Function to get status badge HTML
function getStatusBadge($status)
{
    $badges = [
        'pending' => [
            'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800',
            'dot' => 'bg-yellow-500',
            'label' => 'Pending',
            'animate' => false
        ],
        'processing' => [
            'class' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 border-purple-200 dark:border-purple-800',
            'dot' => 'bg-purple-500',
            'label' => 'Processing',
            'animate' => true
        ],
        'packed' => [
            'class' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
            'dot' => 'bg-indigo-500',
            'label' => 'Packed',
            'animate' => false
        ],
        'shipped' => [
            'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200 dark:border-blue-800',
            'dot' => 'bg-blue-500',
            'label' => 'In Transit',
            'animate' => true
        ],
        'delivered' => [
            'class' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 border-green-200 dark:border-green-800',
            'dot' => 'bg-green-500',
            'label' => 'Completed',
            'animate' => false
        ],
        'cancelled' => [
            'class' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300 border-red-200 dark:border-red-800',
            'dot' => 'bg-red-500',
            'label' => 'Cancelled',
            'animate' => false
        ]
    ];

    return $badges[$status] ?? $badges['pending'];
}


// connction closed 
$conn->close();
