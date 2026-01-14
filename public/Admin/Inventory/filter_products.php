<?php
// Set up environment
require_once '../../../config/database.php';


// Clean output buffer if needed
$conn = getDBConnection();

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$brand = isset($_GET['brand']) ? trim($_GET['brand']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 6;
$offset = ($page - 1) * $items_per_page;

// Build query with filters
$where_conditions = [];
$params = [];
$types = '';


// Add search condition
if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR sku LIKE ? OR description LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = &$search_param;
    $params[] = &$search_param;
    $params[] = &$search_param;
    $types .= 'sss';
}

// Add category filter
if (!empty($category)) {
    $where_conditions[] = "category = ?";
    $params[] = &$category;
    $types .= 's';
}

// Add brand filter
if (!empty($brand)) {
    $where_conditions[] = "brand = ?";
    $params[] = &$brand;
    $types .= 's';
}

// Combine where conditions
$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Count total
$count_query = "SELECT COUNT(*) as total FROM products {$where_clause}";
if (!empty($params)) {
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total_products = $stmt->get_result()->fetch_assoc()['total'];
} else {
    $total_products = $conn->query($count_query)->fetch_assoc()['total'];
}

// Calculate total pages
$total_pages = ceil($total_products / $items_per_page);

// Fetch products
$query = "SELECT * FROM products {$where_clause} ORDER BY created_at DESC LIMIT {$items_per_page} OFFSET {$offset}";
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

// Fetch products into array
$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Generate HTML
ob_start();

// Output table rows
if (empty($products)) {
    echo '<tr><td colspan="7" class="py-8 px-6 text-center text-sm text-gray-500 dark:text-gray-400">No products found</td></tr>';
} else {
    foreach ($products as $product) {
        $stock_percentage = ($product['max_level'] > 0) ? ($product['stock_level'] / $product['max_level']) * 100 : 0;
        $is_low_stock = $product['stock_level'] < ($product['max_level'] * 0.2);
        $is_out_of_stock = $product['stock_level'] <= 0 || $product['status'] === 'out_of_stock';

        if ($is_out_of_stock) {
            $status_badge = '<span class="inline-flex items-center gap-1 rounded-full bg-gray-50 dark:bg-gray-900/20 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-400 ring-1 ring-inset ring-gray-600/20"><span class="size-1.5 rounded-full bg-gray-600 dark:bg-gray-400"></span>Out of Stock</span>';
        } elseif ($is_low_stock) {
            $status_badge = '<span class="inline-flex items-center gap-1 rounded-full bg-red-50 dark:bg-red-900/20 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-600/10"><span class="size-1.5 rounded-full bg-red-600 dark:bg-red-400"></span>Low Stock</span>';
        } else {
            $status_badge = '<span class="inline-flex items-center gap-1 rounded-full bg-green-50 dark:bg-green-900/20 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-400 ring-1 ring-inset ring-green-600/20"><span class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"></span>In Stock</span>';
        }

        $bar_color = $is_low_stock ? 'bg-red-500' : 'bg-primary';
        $stock_text_color = $is_low_stock ? 'text-red-600 dark:text-red-400' : 'text-[#0d1b12] dark:text-white';

        $image_html = '';
        if (!empty($product['image_path']) && file_exists('../../../' . $product['image_path'])) {
            $image_html = '<div class="size-8 sm:size-10 shrink-0 rounded-lg bg-gray-100 dark:bg-gray-800 bg-center bg-cover" style="background-image: url(\'../../../' . htmlspecialchars($product['image_path']) . '\');"></div>';
        } else {
            $image_html = '<div class="size-8 sm:size-10 shrink-0 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center"><span class="material-symbols-outlined text-gray-400 text-[16px] sm:text-[20px]">inventory_2</span></div>';
        }
        // Output table row
        echo '<tr class="group hover:bg-[#f6f8f6] dark:hover:bg-white/5 transition-colors cursor-pointer">
            <td class="py-3 px-4 sm:py-4 sm:px-6">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    ' . $image_html . '
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-bold text-[#0d1b12] dark:text-white truncate">' . htmlspecialchars($product['name']) . '</p>
                        <p class="text-[10px] sm:text-xs text-[#4c9a66] dark:text-gray-500 truncate">SKU: ' . htmlspecialchars($product['sku']) . '</p>
                    </div>
                </div>
            </td>
            <td class="py-3 px-4 sm:py-4 sm:px-6">
                <span class="inline-flex items-center rounded-md bg-[#e7f3eb] dark:bg-white/10 px-2 py-1 text-[10px] sm:text-xs font-medium text-[#0d1b12] dark:text-white ring-1 ring-inset ring-gray-500/10">' . htmlspecialchars($product['category']) . '</span>
            </td>
            <td class="py-3 px-4 sm:py-4 sm:px-6">
                <div class="flex flex-col gap-1 w-20 sm:w-24">
                    <div class="flex justify-between text-[10px] sm:text-xs">
                        <span class="font-bold ' . $stock_text_color . '">' . number_format($product['stock_level']) . '</span>
                        <span class="text-gray-400">/ ' . number_format($product['max_level']) . '</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                        <div class="' . $bar_color . ' h-1.5 rounded-full" style="width: ' . min($stock_percentage, 100) . '%"></div>
                    </div>
                </div>
            </td>
            <td class="py-3 px-4 sm:py-4 sm:px-6 text-xs sm:text-sm text-[#0d1b12] dark:text-white">' . number_format($product['allocated']) . '</td>
            <td class="py-3 px-4 sm:py-4 sm:px-6 text-xs sm:text-sm font-medium text-[#0d1b12] dark:text-white">Rs ' . number_format($product['unit_price'], 2) . '</td>
            <td class="py-3 px-4 sm:py-4 sm:px-6">' . $status_badge . '</td>
            <td class="py-3 px-4 sm:py-4 sm:px-6 text-right">
                <div class="flex items-center justify-end gap-1 sm:gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                    <button onclick="openDetailsPanel(' . $product['id'] . ')" class="p-1 sm:p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="View Details">
                        <span class="material-symbols-outlined text-[18px] sm:text-[20px]">visibility</span>
                    </button>
                    <button onclick="openProductPanel(\'edit\', ' . $product['id'] . ')" class="p-1 sm:p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="Edit Details">
                        <span class="material-symbols-outlined text-[18px] sm:text-[20px]">edit</span>
                    </button>
                    <button onclick="deleteProduct(' . $product['id'] . ')" class="p-1 sm:p-1.5 rounded-md text-red-500 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors" title="Delete Product">
                        <span class="material-symbols-outlined text-[18px] sm:text-[20px]">delete</span>
                    </button>
                </div>
            </td>
        </tr>';
    }
}

$table_html = ob_get_clean();

// Generate pagination HTML
ob_start();
if ($total_pages > 1) {
    echo '<div class="mt-6 flex justify-between items-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Showing <span class="font-medium">' . ($offset + 1) . '</span> to 
            <span class="font-medium">' . min($offset + $items_per_page, $total_products) . '</span> of 
            <span class="font-medium">' . $total_products . '</span> products
        </p>
        <div class="flex gap-2">
            <nav class="inline-flex rounded-md shadow-sm -space-x-px">';

    // Previous button
    if ($page > 1) {
        echo '<button onclick="filterProducts(' . ($page - 1) . ')" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#102216] text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5">
            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </button>';
    } else {
        echo '<span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-[#102216] text-sm font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed">
            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </span>';
    }

    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $page) {
            echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 bg-primary text-sm font-medium text-white">' . $i . '</span>';
        } else {
            echo '<button onclick="filterProducts(' . $i . ')" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#102216] text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5">' . $i . '</button>';
        }
    }

    // Next button
    if ($page < $total_pages) {
        echo '<button onclick="filterProducts(' . ($page + 1) . ')" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#102216] text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5">
            <span class="material-symbols-outlined text-[20px]">chevron_right</span>
        </button>';
    } else {
        echo '<span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-[#102216] text-sm font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed">
            <span class="material-symbols-outlined text-[20px]">chevron_right</span>
        </span>';
    }

    echo '</nav></div></div>';
}

$pagination_html = ob_get_clean();

echo json_encode([
    'success' => true,
    'html' => $table_html,
    'pagination' => $pagination_html,
    'total' => $total_products
]);

// Close database connection
$conn->close();
