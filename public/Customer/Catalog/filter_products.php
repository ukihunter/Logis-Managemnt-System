<?php
//connction strings
require_once '../../../config/database.php';
header('Content-Type: application/json');

$conn = getDBConnection();

// Pagination settings
$items_per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Filters 
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$brands = isset($_GET['brands']) ? $_GET['brands'] : [];
$stock_status = isset($_GET['stock_status']) ? trim($_GET['stock_status']) : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
$sort_by = isset($_GET['sort_by']) ? trim($_GET['sort_by']) : 'popularity';

// Build WHERE clause
//search conditions
$where_conditions = ["status = 'active'"]; // prodct must be in the active state
$params = [];
$types = '';

// Search filter
if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR sku LIKE ? OR description LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}
// Category filter
if (!empty($category) && $category !== 'all') {
    $where_conditions[] = "category = ?";
    $params[] = $category;
    $types .= 's';
}
// Brand filter
if (!empty($brands) && is_array($brands)) {
    $placeholders = str_repeat('?,', count($brands) - 1) . '?';
    $where_conditions[] = "brand IN ($placeholders)";
    foreach ($brands as $brand) {
        $params[] = $brand;
        $types .= 's';
    }
}
// Stock status filter
if (!empty($stock_status)) {
    if ($stock_status === 'in_stock') {
        $where_conditions[] = "stock_level > 0";
    } elseif ($stock_status === 'low_stock') {
        $where_conditions[] = "stock_level > 0 AND stock_level < (max_level * 0.2)";
    }
}
// Price range filter
if ($min_price > 0) {
    $where_conditions[] = "unit_price >= ?";
    $params[] = $min_price;
    $types .= 'd';
}


// Max price filter
if ($max_price > 0) {
    $where_conditions[] = "unit_price <= ?";
    $params[] = $max_price;
    $types .= 'd';
}

// Combine WHERE conditions
$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Build ORDER BY clause
$order_by = 'created_at DESC';
switch ($sort_by) {
    case 'price_low':
        $order_by = 'unit_price ASC';
        break;
    case 'price_high':
        $order_by = 'unit_price DESC';
        break;
    case 'newest':
        $order_by = 'created_at DESC';
        break;
    case 'name':
        $order_by = 'name ASC';
        break;
    case 'popularity':
    default:
        $order_by = 'is_featured DESC, created_at DESC';
        break;
}

// Count total products
$count_query = "SELECT COUNT(*) as total FROM products {$where_clause}";
$stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$count_result = $stmt->get_result();
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $items_per_page);

// Fetch products
$query = "SELECT * FROM products {$where_clause} ORDER BY {$order_by} LIMIT ? OFFSET ?";
$params[] = $items_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Process products
$products = [];
while ($row = $result->fetch_assoc()) {
    // Calculate stock percentage
    $stock_percentage = ($row['max_level'] > 0) ? ($row['stock_level'] / $row['max_level']) * 100 : 0;
    $is_low_stock = $row['stock_level'] < ($row['max_level'] * 0.2);
    $is_out_of_stock = $row['stock_level'] <= 0 || $row['status'] === 'out_of_stock';

    // Determine stock status
    if ($is_out_of_stock) {
        $stock_status_info = [
            'status' => 'out_of_stock',
            'label' => 'Out of Stock',
            'color' => 'gray'
        ];
    } elseif ($is_low_stock) {
        $stock_status_info = [
            'status' => 'low_stock',
            'label' => 'Low Stock',
            'color' => 'orange'
        ];
    } else {
        $stock_status_info = [
            'status' => 'in_stock',
            'label' => 'In Stock',
            'color' => 'green'
        ];
    }

    // Calculate discounted price
    $discounted_price = $row['unit_price'];
    if ($row['discount_percentage'] > 0) {
        $discounted_price = $row['unit_price'] * (1 - $row['discount_percentage'] / 100);
    }
    // Add product to the list
    $products[] = [
        'id' => $row['id'],
        'sku' => $row['sku'],
        'name' => $row['name'],
        'description' => $row['description'],
        'category' => $row['category'],
        'brand' => $row['brand'],
        'image_path' => $row['image_path'],
        'unit_price' => floatval($row['unit_price']),
        'discounted_price' => floatval($discounted_price),
        'carton_quantity' => intval($row['carton_quantity']),
        'carton_price' => floatval($row['carton_price']),
        'stock_level' => intval($row['stock_level']),
        'min_order_quantity' => intval($row['min_order_quantity']),
        'offer_label' => $row['offer_label'],
        'discount_percentage' => floatval($row['discount_percentage']),
        'is_featured' => boolval($row['is_featured']),
        'stock_status' => $stock_status_info
    ];
}

// Response
echo json_encode([
    'success' => true,
    'products' => $products,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_products' => $total_products,
        'per_page' => $items_per_page,
        'showing_from' => $offset + 1,
        'showing_to' => min($offset + $items_per_page, $total_products)
    ]
]);

//connction closed
$conn->close();
