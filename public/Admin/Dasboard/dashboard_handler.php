<?php
require_once '../../../config/database.php';
require_once '../../../config/admin_session.php';

header('Content-Type: application/json');

$conn = getDBConnection();
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_kpi_stats':
            getKPIStats($conn);
            break;
        case 'get_pending_orders':
            getPendingOrders($conn);
            break;
        case 'get_low_stock_items':
            getLowStockItems($conn);
            break;
        case 'get_logistics_schedule':
            getLogisticsSchedule($conn);
            break;
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function getKPIStats($conn)
{
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    // Pending Orders Count
    $pendingQuery = "SELECT COUNT(*) as count FROM orders 
                     WHERE order_status IN ('pending', 'processing')";
    $pendingResult = $conn->query($pendingQuery);
    $pendingCount = $pendingResult->fetch_assoc()['count'];

    // Previous period pending count (yesterday)
    $pendingPrevQuery = "SELECT COUNT(*) as count FROM orders 
                         WHERE order_status IN ('pending', 'processing') 
                         AND DATE(created_at) = '$yesterday'";
    $pendingPrevResult = $conn->query($pendingPrevQuery);
    $pendingPrevCount = $pendingPrevResult->fetch_assoc()['count'];

    $pendingChange = $pendingPrevCount > 0
        ? round((($pendingCount - $pendingPrevCount) / $pendingPrevCount) * 100)
        : 0;

    // Low Stock Items
    $lowStockQuery = "SELECT COUNT(*) as count FROM products 
                      WHERE stock_level <= (max_level * 0.3) 
                      AND status = 'active'";
    $lowStockResult = $conn->query($lowStockQuery);
    $lowStockCount = $lowStockResult->fetch_assoc()['count'];

    // New low stock today
    $newLowStockQuery = "SELECT COUNT(*) as count FROM products 
                         WHERE stock_level <= (max_level * 0.3) 
                         AND DATE(updated_at) = '$today' 
                         AND status = 'active'";
    $newLowStockResult = $conn->query($newLowStockQuery);
    $newLowStockCount = $newLowStockResult->fetch_assoc()['count'];

    // Active Trucks (drivers with orders in shipped status)
    $activeTrucksQuery = "SELECT COUNT(DISTINCT driver_id) as count FROM orders 
                          WHERE order_status = 'shipped' 
                          AND driver_id IS NOT NULL";
    $activeTrucksResult = $conn->query($activeTrucksQuery);
    $activeTrucksCount = $activeTrucksResult->fetch_assoc()['count'];

    // Today's Revenue
    $revenueQuery = "SELECT SUM(total_amount) as revenue FROM orders 
                     WHERE DATE(created_at) = '$today' 
                     AND payment_status = 'paid'";
    $revenueResult = $conn->query($revenueQuery);
    $todayRevenue = $revenueResult->fetch_assoc()['revenue'] ?? 0;

    // Yesterday's Revenue
    $revenuePrevQuery = "SELECT SUM(total_amount) as revenue FROM orders 
                         WHERE DATE(created_at) = '$yesterday' 
                         AND payment_status = 'paid'";
    $revenuePrevResult = $conn->query($revenuePrevQuery);
    $yesterdayRevenue = $revenuePrevResult->fetch_assoc()['revenue'] ?? 0;

    $revenueChange = $yesterdayRevenue > 0
        ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100)
        : 0;

    echo json_encode([
        'pending_orders' => [
            'count' => intval($pendingCount),
            'change' => $pendingChange,
            'trend' => $pendingChange >= 0 ? 'up' : 'down'
        ],
        'low_stock' => [
            'count' => intval($lowStockCount),
            'new_count' => intval($newLowStockCount)
        ],
        'active_trucks' => [
            'count' => intval($activeTrucksCount)
        ],
        'today_revenue' => [
            'amount' => floatval($todayRevenue),
            'change' => $revenueChange,
            'trend' => $revenueChange >= 0 ? 'up' : 'down'
        ]
    ]);
}

function getPendingOrders($conn)
{
    $query = "SELECT 
        o.order_number,
        o.customer_name,
        o.business_name,
        DATE_FORMAT(o.created_at, '%b %d, %h:%i %p') as order_date,
        o.order_status,
        o.total_amount,
        (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
    FROM orders o
    WHERE o.order_status IN ('pending', 'processing', 'packed', 'shipped')
    ORDER BY o.created_at DESC
    LIMIT 10";

    $result = $conn->query($query);
    $orders = [];

    while ($row = $result->fetch_assoc()) {
        $orders[] = [
            'order_number' => $row['order_number'],
            'customer' => $row['business_name'] ?: $row['customer_name'],
            'date' => $row['order_date'],
            'status' => $row['order_status'],
            'total' => floatval($row['total_amount']),
            'item_count' => intval($row['item_count'])
        ];
    }

    echo json_encode($orders);
}

function getLowStockItems($conn)
{
    $query = "SELECT 
        p.name,
        p.image_path,
        p.stock_level,
        ROUND(p.max_level * 0.3) as min_threshold,
        p.sku
    FROM products p
    WHERE p.stock_level <= (p.max_level * 0.3) 
    AND p.status = 'active'
    ORDER BY p.stock_level ASC
    LIMIT 8";

    $result = $conn->query($query);
    $items = [];

    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'name' => $row['name'],
            'image' => $row['image_path'] ?: 'https://via.placeholder.com/100',
            'stock' => intval($row['stock_level']),
            'min_threshold' => intval($row['min_threshold']),
            'sku' => $row['sku']
        ];
    }

    echo json_encode($items);
}

function getLogisticsSchedule($conn)
{
    $today = date('Y-m-d');

    $query = "SELECT 
        d.id,
        d.full_name as driver_name,
        d.license_plate as vehicle_number,
        COUNT(o.id) as total_deliveries,
        MIN(o.created_at) as start_time,
        MAX(o.updated_at) as end_time,
        GROUP_CONCAT(DISTINCT o.shipping_city SEPARATOR ', ') as destinations
    FROM drivers d
    LEFT JOIN orders o ON d.id = o.driver_id 
        AND DATE(o.created_at) = '$today'
        AND o.order_status IN ('shipped', 'delivered')
    WHERE d.status = 'active'
    GROUP BY d.id
    HAVING total_deliveries > 0
    ORDER BY total_deliveries DESC
    LIMIT 5";

    $result = $conn->query($query);
    $schedules = [];

    while ($row = $result->fetch_assoc()) {
        $startHour = $row['start_time'] ? date('H', strtotime($row['start_time'])) : 8;
        $endHour = $row['end_time'] ? date('H', strtotime($row['end_time'])) : 18;

        $schedules[] = [
            'vehicle_number' => $row['vehicle_number'],
            'driver_name' => $row['driver_name'],
            'deliveries' => intval($row['total_deliveries']),
            'start_hour' => intval($startHour),
            'end_hour' => intval($endHour),
            'destinations' => $row['destinations'] ?: 'Various locations'
        ];
    }

    echo json_encode($schedules);
}
