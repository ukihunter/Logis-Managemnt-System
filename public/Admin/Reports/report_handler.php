<?php
require_once '../../../config/database.php';
require_once '../../../config/api_session.php';

// Get database connection
$conn = getDBConnection();

$action = $_GET['action'] ?? '';

// Handle download separately (no JSON header)
if ($action === 'download_report') {
    downloadReport($conn);
    exit;
}

// Set JSON header for API responses
header('Content-Type: application/json');

try {
    switch ($action) {
        case 'get_monthly_stats':
            getMonthlyStats($conn);
            break;
        case 'get_sales_performance':
            getSalesPerformance($conn);
            break;
        case 'get_stock_turnover':
            getStockTurnover($conn);
            break;
        case 'get_delivery_efficiency':
            getDeliveryEfficiency($conn);
            break;
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function getMonthlyStats($conn)
{
    $month = $_GET['month'] ?? date('Y-m');

    // Total Revenue
    $revenueQuery = "SELECT 
        COALESCE(SUM(total_amount), 0) as current_revenue,
        (SELECT COALESCE(SUM(total_amount), 0) 
         FROM orders 
         WHERE DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(DATE_SUB('$month-01', INTERVAL 1 MONTH), '%Y-%m')
         AND payment_status = 'paid') as previous_revenue
    FROM orders 
    WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'
    AND payment_status = 'paid'";

    $revenueResult = $conn->query($revenueQuery);
    $revenueData = $revenueResult->fetch_assoc();

    // Total Units Sold
    $unitsQuery = "SELECT 
        COALESCE(SUM(oi.quantity), 0) as current_units,
        (SELECT COALESCE(SUM(oi.quantity), 0)
         FROM order_items oi
         JOIN orders o ON oi.order_id = o.id
         WHERE DATE_FORMAT(o.created_at, '%Y-%m') = DATE_FORMAT(DATE_SUB('$month-01', INTERVAL 1 MONTH), '%Y-%m')
         AND o.payment_status = 'paid') as previous_units
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE DATE_FORMAT(o.created_at, '%Y-%m') = '$month'
    AND o.payment_status = 'paid'";

    $unitsResult = $conn->query($unitsQuery);
    $unitsData = $unitsResult->fetch_assoc();

    // Active Deliveries & On-time Rate
    $deliveryQuery = "SELECT 
        COUNT(CASE WHEN order_status IN ('processing', 'packed', 'shipped') THEN 1 END) as active_deliveries,
        COUNT(CASE WHEN order_status = 'delivered' THEN 1 END) as total_delivered,
        COUNT(CASE WHEN order_status = 'delivered' AND driver_id IS NOT NULL THEN 1 END) as ontime_delivered
    FROM orders 
    WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'";

    $deliveryResult = $conn->query($deliveryQuery);
    $deliveryData = $deliveryResult->fetch_assoc();

    $ontimeRate = $deliveryData['total_delivered'] > 0
        ? round(($deliveryData['ontime_delivered'] / $deliveryData['total_delivered']) * 100)
        : 0;

    // Stock Health Score
    $stockQuery = "SELECT 
        COUNT(*) as total_products,
        COUNT(CASE WHEN stock_level > (max_level * 0.3) THEN 1 END) as healthy_stock,
        COUNT(CASE WHEN stock_level <= (max_level * 0.3) AND stock_level > 0 THEN 1 END) as low_stock,
        COUNT(CASE WHEN stock_level = 0 THEN 1 END) as out_of_stock
    FROM products 
    WHERE status = 'active'";

    $stockResult = $conn->query($stockQuery);
    $stockData = $stockResult->fetch_assoc();

    $stockHealth = $stockData['total_products'] > 0
        ? round(($stockData['healthy_stock'] / $stockData['total_products']) * 100)
        : 0;

    // Calculate percentage changes
    $revenueChange = $revenueData['previous_revenue'] > 0
        ? round((($revenueData['current_revenue'] - $revenueData['previous_revenue']) / $revenueData['previous_revenue']) * 100, 1)
        : 0;

    $unitsChange = $unitsData['previous_units'] > 0
        ? round((($unitsData['current_units'] - $unitsData['previous_units']) / $unitsData['previous_units']) * 100, 1)
        : 0;

    echo json_encode([
        'success' => true,
        'revenue' => [
            'current' => $revenueData['current_revenue'],
            'previous' => $revenueData['previous_revenue'],
            'change' => $revenueChange
        ],
        'units' => [
            'current' => $unitsData['current_units'],
            'previous' => $unitsData['previous_units'],
            'change' => $unitsChange
        ],
        'deliveries' => [
            'active' => $deliveryData['active_deliveries'],
            'ontime_rate' => $ontimeRate
        ],
        'stock' => [
            'health_score' => $stockHealth,
            'low_stock_alerts' => $stockData['low_stock']
        ]
    ]);
}

function getSalesPerformance($conn)
{
    $month = $_GET['month'] ?? date('Y-m');

    // Weekly sales data for the selected month
    $query = "SELECT 
        WEEKOFYEAR(created_at) - WEEKOFYEAR(DATE_FORMAT(created_at, '%Y-%m-01')) + 1 as week_num,
        SUM(total_amount) as revenue,
        COUNT(*) as order_count
    FROM orders 
    WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'
    AND payment_status = 'paid'
    GROUP BY week_num
    ORDER BY week_num";

    $result = $conn->query($query);
    $weeklyData = [];

    while ($row = $result->fetch_assoc()) {
        $weeklyData[] = [
            'week' => 'Week ' . $row['week_num'],
            'revenue' => floatval($row['revenue']),
            'orders' => intval($row['order_count'])
        ];
    }

    // Top selling products
    $topProductsQuery = "SELECT 
        p.name,
        p.sku,
        SUM(oi.quantity) as units_sold,
        SUM(oi.subtotal) as revenue
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    JOIN products p ON oi.product_id = p.id
    WHERE DATE_FORMAT(o.created_at, '%Y-%m') = '$month'
    AND o.payment_status = 'paid'
    GROUP BY p.id
    ORDER BY revenue DESC
    LIMIT 10";

    $topProducts = [];
    $result = $conn->query($topProductsQuery);
    while ($row = $result->fetch_assoc()) {
        $topProducts[] = $row;
    }

    echo json_encode([
        'success' => true,
        'weekly_sales' => $weeklyData,
        'top_products' => $topProducts
    ]);
}

function getStockTurnover($conn)
{
    $month = $_GET['month'] ?? date('Y-m');

    $query = "SELECT 
        p.id,
        p.name,
        p.sku,
        p.stock_level,
        p.max_level,
        p.category,
        COALESCE(SUM(oi.quantity), 0) as units_sold,
        CASE 
            WHEN p.stock_level > 0 THEN ROUND(COALESCE(SUM(oi.quantity), 0) / p.stock_level, 2)
            ELSE 0 
        END as turnover_ratio,
        CASE 
            WHEN p.stock_level <= (p.max_level * 0.3) AND p.stock_level > 0 THEN 'low'
            WHEN p.stock_level = 0 THEN 'out'
            ELSE 'healthy'
        END as stock_status
    FROM products p
    LEFT JOIN order_items oi ON p.id = oi.product_id
    LEFT JOIN orders o ON oi.order_id = o.id AND DATE_FORMAT(o.created_at, '%Y-%m') = '$month' AND o.payment_status = 'paid'
    WHERE p.status = 'active'
    GROUP BY p.id
    ORDER BY turnover_ratio DESC";

    $result = $conn->query($query);
    $stockData = [];

    while ($row = $result->fetch_assoc()) {
        $stockData[] = $row;
    }

    echo json_encode([
        'success' => true,
        'stock_turnover' => $stockData
    ]);
}

function getDeliveryEfficiency($conn)
{
    $month = $_GET['month'] ?? date('Y-m');

    // Delivery performance by status
    $statusQuery = "SELECT 
        order_status,
        COUNT(*) as count,
        AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_processing_time
    FROM orders 
    WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'
    GROUP BY order_status";

    $statusResult = $conn->query($statusQuery);
    $statusData = [];

    while ($row = $statusResult->fetch_assoc()) {
        $statusData[] = [
            'status' => $row['order_status'],
            'count' => intval($row['count']),
            'avg_time' => round($row['avg_processing_time'], 1)
        ];
    }

    // Driver performance
    $driverQuery = "SELECT 
        d.full_name,
        d.employee_id,
        COUNT(o.id) as total_deliveries,
        COUNT(CASE WHEN o.order_status = 'delivered' THEN 1 END) as completed,
        AVG(CASE 
            WHEN o.order_status = 'delivered' 
            THEN TIMESTAMPDIFF(HOUR, o.created_at, o.updated_at) 
        END) as avg_delivery_time
    FROM drivers d
    LEFT JOIN orders o ON d.id = o.driver_id AND DATE_FORMAT(o.created_at, '%Y-%m') = '$month'
    WHERE d.status = 'active'
    GROUP BY d.id
    HAVING total_deliveries > 0
    ORDER BY completed DESC";

    $driverResult = $conn->query($driverQuery);
    $driverData = [];

    while ($row = $driverResult->fetch_assoc()) {
        $driverData[] = [
            'driver' => $row['full_name'],
            'employee_id' => $row['employee_id'],
            'total' => intval($row['total_deliveries']),
            'completed' => intval($row['completed']),
            'avg_time' => round($row['avg_delivery_time'], 1)
        ];
    }

    // City/Province breakdown
    $locationQuery = "SELECT 
        shipping_province as location,
        COUNT(*) as total_orders,
        COUNT(CASE WHEN order_status = 'delivered' THEN 1 END) as delivered,
        AVG(total_amount) as avg_order_value
    FROM orders 
    WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'
    AND shipping_province IS NOT NULL
    GROUP BY shipping_province
    ORDER BY total_orders DESC";

    $locationResult = $conn->query($locationQuery);
    $locationData = [];

    while ($row = $locationResult->fetch_assoc()) {
        $locationData[] = [
            'location' => $row['location'],
            'orders' => intval($row['total_orders']),
            'delivered' => intval($row['delivered']),
            'avg_value' => floatval($row['avg_order_value'])
        ];
    }

    echo json_encode([
        'success' => true,
        'by_status' => $statusData,
        'by_driver' => $driverData,
        'by_location' => $locationData
    ]);
}

function downloadReport($conn)
{
    $month = $_GET['month'] ?? date('Y-m');
    $reportType = $_GET['type'] ?? 'full';

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="report_' . $month . '_' . $reportType . '.csv"');

    $output = fopen('php://output', 'w');

    if ($reportType === 'sales') {
        // Sales Performance Report
        fputcsv($output, ['Sales Performance Report - ' . $month]);
        fputcsv($output, ['']);
        fputcsv($output, ['Order Number', 'Date', 'Customer', 'Items', 'Subtotal', 'Tax', 'Total', 'Status', 'Payment']);

        $query = "SELECT 
            order_number,
            DATE(created_at) as order_date,
            customer_name,
            (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id) as items,
            subtotal,
            tax_amount,
            total_amount,
            order_status,
            payment_status
        FROM orders 
        WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'
        ORDER BY created_at DESC";

        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    } elseif ($reportType === 'stock') {
        // Stock Turnover Report
        fputcsv($output, ['Stock Turnover Report - ' . $month]);
        fputcsv($output, ['']);
        fputcsv($output, ['SKU', 'Product Name', 'Category', 'Current Stock', 'Max Level', 'Units Sold', 'Turnover Ratio', 'Status']);

        $query = "SELECT 
            p.sku,
            p.name,
            p.category,
            p.stock_level,
            p.max_level,
            COALESCE(SUM(oi.quantity), 0) as units_sold,
            CASE 
                WHEN p.stock_level > 0 THEN ROUND(COALESCE(SUM(oi.quantity), 0) / p.stock_level, 2)
                ELSE 0 
            END as turnover_ratio,
            CASE 
                WHEN p.stock_level <= (p.max_level * 0.3) AND p.stock_level > 0 THEN 'Low Stock'
                WHEN p.stock_level = 0 THEN 'Out of Stock'
                ELSE 'Healthy'
            END as stock_status
        FROM products p
        LEFT JOIN order_items oi ON p.id = oi.product_id
        LEFT JOIN orders o ON oi.order_id = o.id AND DATE_FORMAT(o.created_at, '%Y-%m') = '$month'
        WHERE p.status = 'active'
        GROUP BY p.id
        ORDER BY turnover_ratio DESC";

        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    } elseif ($reportType === 'delivery') {
        // Delivery Efficiency Report
        fputcsv($output, ['Delivery Efficiency Report - ' . $month]);
        fputcsv($output, ['']);
        fputcsv($output, ['Order Number', 'Customer', 'Location', 'Driver', 'Status', 'Created', 'Updated', 'Processing Time (hrs)']);

        $query = "SELECT 
            o.order_number,
            o.customer_name,
            CONCAT(o.shipping_city, ', ', o.shipping_province) as location,
            COALESCE(d.full_name, 'Unassigned') as driver,
            o.order_status,
            o.created_at,
            o.updated_at,
            TIMESTAMPDIFF(HOUR, o.created_at, o.updated_at) as processing_hours
        FROM orders o
        LEFT JOIN drivers d ON o.driver_id = d.id
        WHERE DATE_FORMAT(o.created_at, '%Y-%m') = '$month'
        ORDER BY o.created_at DESC";

        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    } else {
        // Full Report
        fputcsv($output, ['Complete Monthly Report - ' . $month]);
        fputcsv($output, ['Generated: ' . date('Y-m-d H:i:s')]);
        fputcsv($output, ['']);

        // Summary Section
        fputcsv($output, ['SUMMARY']);

        $summaryQuery = "SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as total_revenue,
            COUNT(CASE WHEN order_status = 'delivered' THEN 1 END) as delivered_orders,
            AVG(total_amount) as avg_order_value
        FROM orders 
        WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month'";

        $summary = $conn->query($summaryQuery)->fetch_assoc();
        fputcsv($output, ['Total Orders', $summary['total_orders']]);
        fputcsv($output, ['Total Revenue', 'Rs' . number_format($summary['total_revenue'], 2)]);
        fputcsv($output, ['Delivered Orders', $summary['delivered_orders']]);
        fputcsv($output, ['Average Order Value', 'Rs' . number_format($summary['avg_order_value'], 2)]);
        fputcsv($output, ['']);

        // Detailed Orders
        fputcsv($output, ['DETAILED ORDERS']);
        fputcsv($output, ['Order Number', 'Date', 'Customer', 'Business', 'Location', 'Total', 'Status', 'Driver', 'Payment']);

        $detailQuery = "SELECT 
            o.order_number,
            DATE(o.created_at) as order_date,
            o.customer_name,
            COALESCE(o.business_name, '-') as business,
            CONCAT(o.shipping_city, ', ', o.shipping_province) as location,
            o.total_amount,
            o.order_status,
            COALESCE(d.full_name, 'Unassigned') as driver,
            o.payment_status
        FROM orders o
        LEFT JOIN drivers d ON o.driver_id = d.id
        WHERE DATE_FORMAT(o.created_at, '%Y-%m') = '$month'
        ORDER BY o.created_at DESC";

        $result = $conn->query($detailQuery);
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    }

    fclose($output);
    exit;
}
