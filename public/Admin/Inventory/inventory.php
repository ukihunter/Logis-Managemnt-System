<?php
require_once '../../../config/admin_session.php';
require_once '../../../config/database.php';

$conn = getDBConnection();

// Pagination settings
$items_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Search and filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';

// Build query with search and filters
$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR sku LIKE ? OR description LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = &$search_param;
    $params[] = &$search_param;
    $params[] = &$search_param;
    $types .= 'sss';
}

if (!empty($category_filter)) {
    $where_conditions[] = "category = ?";
    $params[] = &$category_filter;
    $types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Count total products
$count_query = "SELECT COUNT(*) as total FROM products {$where_clause}";
if (!empty($params)) {
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $count_result = $stmt->get_result();
    $total_products = $count_result->fetch_assoc()['total'];
} else {
    $total_products = $conn->query($count_query)->fetch_assoc()['total'];
}

$total_pages = ceil($total_products / $items_per_page);

// Fetch products with pagination
$query = "SELECT * FROM products {$where_clause} ORDER BY created_at DESC LIMIT {$items_per_page} OFFSET {$offset}";
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Fetch categories
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = $conn->query($categories_query);
$categories = [];
if ($categories_result) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch brands
$brands_query = "SELECT * FROM brands ORDER BY name";
$brands_result = $conn->query($brands_query);
$brands = [];
if ($brands_result) {
    while ($row = $brands_result->fetch_assoc()) {
        $brands[] = $row;
    }
}

// Calculate statistics from ALL products (not just current page)
$stats_query = "SELECT SUM(stock_level * unit_price) as total_value, SUM(CASE WHEN stock_level < (max_level * 0.2) THEN 1 ELSE 0 END) as low_stock FROM products";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();
$total_stock_value = $stats['total_value'] ?? 0;
$low_stock_items = $stats['low_stock'] ?? 0;
?>

<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Inventory Management - IslandDistro Hub</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet" />
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <?php include '../components/styles.php'; ?>
    <style>
        /* Image preview */
        .image-preview {
            display: none;
        }

        .image-preview.active {
            display: block;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-[#0d1b12] dark:text-white transition-colors duration-200">
    <div class="flex h-screen w-full overflow-hidden">
        <?php include '../components/sidebar.php'; ?>

        <!-- Main Content -->
        <main id="mainContent" class="flex-1 flex flex-col h-full overflow-hidden relative transition-all duration-300">



            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark p-6 md:p-8">

                <div class="max-w-7xl mx-auto flex flex-col gap-8">
                    <div class="flex justify-end gap-3">
                        <button onclick="openCategoryPanel()" class="flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-white dark:bg-white/10 border border-[#cfe7d7] dark:border-white/20 text-[#0d1b12] dark:text-white text-sm font-medium hover:bg-gray-50 dark:hover:bg-white/20 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">category</span>
                            <span class="truncate">Add Category</span>
                        </button>
                        <button onclick="openBrandPanel()" class="flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-white dark:bg-white/10 border border-[#cfe7d7] dark:border-white/20 text-[#0d1b12] dark:text-white text-sm font-medium hover:bg-gray-50 dark:hover:bg-white/20 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">loyalty</span>
                            <span class="truncate">Add Brand</span>
                        </button>
                        <button onclick="openProductPanel('new')" class="flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-primary hover:bg-green-400 text-[#0d1b12] text-sm font-bold transition-colors shadow-sm shadow-green-200 dark:shadow-none">
                            <span class="material-symbols-outlined text-[20px]">add</span>
                            <span class="truncate">New Stock Entry</span>
                        </button>


                    </div>
                    <!-- Stats Section -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Card 1 -->
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#cfe7d7] dark:border-white/10 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex justify-between items-start">
                                <p class="text-[#4c9a66] dark:text-gray-400 text-sm font-medium">Total Stock Value</p>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold tracking-tight mt-2">Rs <?php echo number_format($total_stock_value, 2); ?></p>
                            <p class="text-xs text-[#4c9a66] dark:text-gray-500 mt-1"><?php echo count($products); ?> products</p>
                        </div>
                        <!-- Card 2 -->
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#cfe7d7] dark:border-white/10 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex justify-between items-start">
                                <p class="text-[#4c9a66] dark:text-gray-400 text-sm font-medium">Low Stock Items</p>
                                <?php if ($low_stock_items > 0): ?>
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs px-2 py-1 rounded-full font-bold">Action Needed</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold tracking-tight mt-2"><?php echo $low_stock_items; ?></p>
                            <p class="text-xs text-[#4c9a66] dark:text-gray-500 mt-1">Below 20% threshold</p>
                        </div>
                        <!-- Card 3 -->
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#cfe7d7] dark:border-white/10 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex justify-between items-start">
                                <p class="text-[#4c9a66] dark:text-gray-400 text-sm font-medium">Total Categories</p>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold tracking-tight mt-2"><?php echo count($categories); ?></p>
                            <p class="text-xs text-[#4c9a66] dark:text-gray-500 mt-1">Active categories</p>
                        </div>
                        <!-- Card 4 -->
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#cfe7d7] dark:border-white/10 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex justify-between items-start">
                                <p class="text-[#4c9a66] dark:text-gray-400 text-sm font-medium">Total Brands</p>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold tracking-tight mt-2"><?php echo count($brands); ?></p>
                            <p class="text-xs text-[#4c9a66] dark:text-gray-500 mt-1">Registered brands</p>
                        </div>
                    </div>
                    <!-- Inventory Section -->
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <h3 class="text-xl font-bold text-[#0d1b12] dark:text-white">Inventory Overview</h3>
                            <!-- Toolbar Actions -->
                            <div class="flex flex-wrap gap-2">
                                <button class="flex items-center justify-center gap-2 rounded-lg h-9 px-3 bg-white dark:bg-white/10 border border-[#cfe7d7] dark:border-white/10 text-[#0d1b12] dark:text-white text-sm font-medium shadow-sm hover:bg-gray-50 dark:hover:bg-white/20 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                                    <span>Filter</span>
                                </button>
                                <button class="flex items-center justify-center gap-2 rounded-lg h-9 px-3 bg-white dark:bg-white/10 border border-[#cfe7d7] dark:border-white/10 text-[#0d1b12] dark:text-white text-sm font-medium shadow-sm hover:bg-gray-50 dark:hover:bg-white/20 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">download</span>
                                    <span>Export</span>
                                </button>
                            </div>
                        </div>
                        <!-- Search and Filter Bar -->
                        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-[#cfe7d7] dark:border-white/10 shadow-sm">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <!-- Search -->
                                <div class="flex-1 flex items-center rounded-lg h-10 bg-[#f6f8f6] dark:bg-white/5 border border-transparent focus-within:border-primary/50 transition-colors">
                                    <div class="text-[#4c9a66] dark:text-gray-400 flex items-center justify-center pl-3">
                                        <span class="material-symbols-outlined text-[20px]">search</span>
                                    </div>
                                    <input id="searchInput" type="text" class="w-full bg-transparent border-none text-[#0d1b12] dark:text-white placeholder:text-[#4c9a66] dark:placeholder:text-gray-500 focus:ring-0 text-sm px-3" placeholder="Find by Product Name, SKU..." />
                                </div>

                                <!-- Filters -->
                                <div class="flex gap-3 w-full sm:w-auto">
                                    <select id="categoryFilter" class="flex-1 sm:flex-none sm:w-40 h-10 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                                        <option value="">All Categories</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo $category_filter === $cat['name'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                    <select id="brandFilter" class="flex-1 sm:flex-none sm:w-40 h-10 px-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                                        <option value="">All Brands</option>
                                        <?php foreach ($brands as $brand): ?>
                                            <option value="<?php echo htmlspecialchars($brand['name']); ?>"><?php echo htmlspecialchars($brand['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Main Inventory Table -->
                        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-[#cfe7d7] dark:border-white/10 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse min-w-[800px]">
                                    <thead class="bg-[#f8fcf9] dark:bg-white/5 border-b border-[#cfe7d7] dark:border-white/10">
                                        <tr>
                                            <th class="py-3 px-4 sm:py-4 sm:px-6 text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider min-w-[200px]">Product &amp; SKU</th>
                                            <th class="py-3 px-4 sm:py-4 sm:px-6 text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider min-w-[120px]">Category</th>
                                            <th class="py-3 px-4 sm:py-4 sm:px-6 text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider min-w-[140px]">Stock Level</th>
                                            <th class="py-3 px-4 sm:py-4 sm:px-6 text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider min-w-[100px]">Allocated</th>
                                            <th class="py-3 px-4 sm:py-4 sm:px-6 text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider min-w-[110px]">Unit Price</th>
                                            <th class="py-3 px-4 sm:py-4 sm:px-6 text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider min-w-[120px]">Status</th>
                                            <th class="py-3 px-4 sm:py-4 sm:px-6 text-right text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider min-w-[120px]">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productTableBody" class="divide-y divide-[#e7f3eb] dark:divide-white/5">
                                        <?php if (empty($products)): ?>
                                            <tr>
                                                <td colspan="7" class="py-8 px-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    No products found. Click "New Stock Entry" to add your first product.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($products as $product):
                                                // Calculate stock status
                                                $stock_percentage = ($product['max_level'] > 0) ? ($product['stock_level'] / $product['max_level']) * 100 : 0;
                                                $is_low_stock = $product['stock_level'] < ($product['max_level'] * 0.2);
                                                $is_out_of_stock = $product['stock_level'] <= 0 || $product['status'] === 'out_of_stock';

                                                // Stock status badge
                                                if ($is_out_of_stock) {
                                                    $status_badge = '<span class="inline-flex items-center gap-1 rounded-full bg-gray-50 dark:bg-gray-900/20 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-400 ring-1 ring-inset ring-gray-600/20"><span class="size-1.5 rounded-full bg-gray-600 dark:bg-gray-400"></span>Out of Stock</span>';
                                                } elseif ($is_low_stock) {
                                                    $status_badge = '<span class="inline-flex items-center gap-1 rounded-full bg-red-50 dark:bg-red-900/20 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-600/10"><span class="size-1.5 rounded-full bg-red-600 dark:bg-red-400"></span>Low Stock</span>';
                                                } else {
                                                    $status_badge = '<span class="inline-flex items-center gap-1 rounded-full bg-green-50 dark:bg-green-900/20 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-400 ring-1 ring-inset ring-green-600/20"><span class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"></span>In Stock</span>';
                                                }

                                                // Progress bar color
                                                $bar_color = $is_low_stock ? 'bg-red-500' : 'bg-primary';
                                                $stock_text_color = $is_low_stock ? 'text-red-600 dark:text-red-400' : 'text-[#0d1b12] dark:text-white';
                                            ?>
                                                <tr class="group hover:bg-[#f6f8f6] dark:hover:bg-white/5 transition-colors cursor-pointer">
                                                    <td class="py-3 px-4 sm:py-4 sm:px-6">
                                                        <div class="flex items-center gap-2 sm:gap-3">
                                                            <?php if (!empty($product['image_path']) && file_exists('../../../' . $product['image_path'])): ?>
                                                                <div class="size-8 sm:size-10 rounded-lg bg-gray-100 dark:bg-gray-800 bg-center bg-cover shrink-0" style='background-image: url("../../../<?php echo htmlspecialchars($product['image_path']); ?>");'></div>
                                                            <?php else: ?>
                                                                <div class="size-8 sm:size-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center shrink-0">
                                                                    <span class="material-symbols-outlined text-gray-400 text-[18px] sm:text-[20px]">inventory_2</span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="min-w-0">
                                                                <p class="text-xs sm:text-sm font-bold text-[#0d1b12] dark:text-white truncate"><?php echo htmlspecialchars($product['name']); ?></p>
                                                                <p class="text-[10px] sm:text-xs text-[#4c9a66] dark:text-gray-500 truncate">SKU: <?php echo htmlspecialchars($product['sku']); ?></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-4 sm:py-4 sm:px-6">
                                                        <span class="inline-flex items-center rounded-md bg-[#e7f3eb] dark:bg-white/10 px-2 py-1 text-[10px] sm:text-xs font-medium text-[#0d1b12] dark:text-white ring-1 ring-inset ring-gray-500/10"><?php echo htmlspecialchars($product['category']); ?></span>
                                                    </td>
                                                    <td class="py-3 px-4 sm:py-4 sm:px-6">
                                                        <div class="flex flex-col gap-1 w-20 sm:w-24">
                                                            <div class="flex justify-between text-[10px] sm:text-xs">
                                                                <span class="font-bold <?php echo $stock_text_color; ?>"><?php echo number_format($product['stock_level']); ?></span>
                                                                <span class="text-gray-400">/ <?php echo number_format($product['max_level']); ?></span>
                                                            </div>
                                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                                                <div class="<?php echo $bar_color; ?> h-1.5 rounded-full" style="width: <?php echo min($stock_percentage, 100); ?>%"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-4 sm:py-4 sm:px-6 text-xs sm:text-sm text-[#0d1b12] dark:text-white"><?php echo number_format($product['allocated']); ?></td>
                                                    <td class="py-3 px-4 sm:py-4 sm:px-6 text-xs sm:text-sm font-medium text-[#0d1b12] dark:text-white">Rs <?php echo number_format($product['unit_price'], 2); ?></td>
                                                    <td class="py-3 px-4 sm:py-4 sm:px-6">
                                                        <?php echo $status_badge; ?>
                                                    </td>
                                                    <td class="py-3 px-4 sm:py-4 sm:px-6 text-right">
                                                        <div class="flex items-center justify-end gap-1 sm:gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                                            <button onclick="openDetailsPanel(<?php echo $product['id']; ?>)" class="p-1 sm:p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="View Details">
                                                                <span class="material-symbols-outlined text-[18px] sm:text-[20px]">visibility</span>
                                                            </button>
                                                            <button onclick="openProductPanel('edit', <?php echo $product['id']; ?>)" class="p-1 sm:p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="Edit Details">
                                                                <span class="material-symbols-outlined text-[18px] sm:text-[20px]">edit</span>
                                                            </button>
                                                            <button onclick="deleteProduct(<?php echo $product['id']; ?>)" class="p-1 sm:p-1.5 rounded-md text-red-500 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors" title="Delete Product">
                                                                <span class="material-symbols-outlined text-[18px] sm:text-[20px]">delete</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div id="paginationContainer">
                            <?php if ($total_pages > 1): ?>
                                <div class="mt-6 flex justify-between items-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to
                                        <span class="font-medium"><?php echo min($offset + $items_per_page, $total_products); ?></span> of
                                        <span class="font-medium"><?php echo $total_products; ?></span> products
                                    </p>
                                    <div class="flex gap-2">
                                        <nav class="inline-flex rounded-md shadow-sm -space-x-px">
                                            <?php if ($page > 1): ?>
                                                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category_filter); ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#102216] text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5">
                                                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-[#102216] text-sm font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed">
                                                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                                                </span>
                                            <?php endif; ?>

                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <?php if ($i == $page): ?>
                                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 bg-primary text-sm font-medium text-white"><?php echo $i; ?></span>
                                                <?php else: ?>
                                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category_filter); ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#102216] text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5"><?php echo $i; ?></a>
                                                <?php endif; ?>
                                            <?php endfor; ?>

                                            <?php if ($page < $total_pages): ?>
                                                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category_filter); ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#102216] text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5">
                                                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-[#102216] text-sm font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed">
                                                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                                                </span>
                                            <?php endif; ?>
                                        </nav>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </main> <!-- Right Panel: Product Form -->
        <aside id="productPanel" class="detail-panel hidden flex-col bg-white dark:bg-[#152e1e] h-full shadow-xl z-20 overflow-hidden fixed right-0 top-0 w-[28rem]">
            <!-- Panel Header -->
            <div class="px-6 py-5 border-b border-[#e7f3eb] dark:border-gray-800 flex justify-between items-start bg-white dark:bg-[#152e1e]">
                <div>
                    <h2 id="panelTitle" class="text-xl font-black text-[#0d1b12] dark:text-white">New Stock Entry</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Fill in the product details below</p>
                </div>
                <button onclick="closeProductPanel()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Scrollable Form Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <form id="productForm" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" id="productId" name="product_id" value="">
                    <input type="hidden" id="formAction" name="action" value="add">

                    <!-- Product Image Upload -->
                    <div>
                        <label class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Product Image</label>
                        <div class="flex flex-col gap-3">
                            <div id="imagePreviewContainer" class="image-preview relative w-full h-48 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700 overflow-hidden bg-gray-50 dark:bg-gray-800">
                                <img id="imagePreview" src="" alt="Preview" class="w-full h-full object-cover">
                                <button type="button" onclick="removeImage()" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <span class="material-symbols-outlined text-gray-400 text-3xl mb-2">upload</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-400">PNG, JPG up to 5MB</p>
                                </div>
                                <input id="productImage" name="product_image" type="file" class="hidden" accept="image/*" onchange="previewImage(event)">
                            </label>
                        </div>
                    </div>

                    <!-- Product Name -->
                    <div>
                        <label for="productName" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" id="productName" name="name" required class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., Coca-Cola Original Taste 330ml Can">
                    </div>

                    <!-- SKU -->
                    <div>
                        <label for="productSKU" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">SKU <span class="text-red-500">*</span></label>
                        <input type="text" id="productSKU" name="sku" required class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., BV-001-COKE">
                    </div>

                    <!-- Category and Brand -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="productCategory" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Category <span class="text-red-500">*</span></label>
                            <select id="productCategory" name="category" required class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Select category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['name']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="productBrand" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Brand</label>
                            <select id="productBrand" name="brand" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Select brand</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo htmlspecialchars($brand['name']); ?>"><?php echo htmlspecialchars($brand['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Unit Price and Carton Quantity -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="unitPrice" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Unit Price (Rs) <span class="text-red-500">*</span></label>
                            <input type="number" id="unitPrice" name="unit_price" required min="0" step="0.01" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0.85">
                        </div>
                        <div>
                            <label for="cartonQuantity" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Carton Qty</label>
                            <input type="number" id="cartonQuantity" name="carton_quantity" min="1" value="1" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="24">
                        </div>
                    </div>

                    <!-- Stock Quantity and Max Level -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="stockLevel" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Stock Level <span class="text-red-500">*</span></label>
                            <input type="number" id="stockLevel" name="stock_level" required min="0" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0">
                        </div>
                        <div>
                            <label for="maxLevel" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Max Level</label>
                            <input type="number" id="maxLevel" name="max_level" min="0" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0">
                        </div>
                    </div>

                    <!-- Allocated -->
                    <div>
                        <label for="allocated" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Allocated</label>
                        <input type="number" id="allocated" name="allocated" min="0" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0">
                    </div>

                    <!-- Offer Label and Discount -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="offerLabel" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Offer Label</label>
                            <select id="offerLabel" name="offer_label" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">None</option>
                                <option value="Best Seller">Best Seller</option>
                                <option value="New Arrival">New Arrival</option>
                                <option value="Sale">Sale</option>
                                <option value="Limited Stock">Limited Stock</option>
                            </select>
                        </div>
                        <div>
                            <label for="discount" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Discount %</label>
                            <input type="number" id="discount" name="discount_percentage" min="0" max="100" step="0.1" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Description</label>
                        <textarea id="description" name="description" rows="3" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-none" placeholder="Add product description..."></textarea>
                    </div>

                    <!-- Status and Featured -->
                    <div>
                        <label class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Status</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="active" checked class="w-4 h-4 text-primary focus:ring-primary">
                                <span class="text-sm text-[#0d1b12] dark:text-white">Active</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="inactive" class="w-4 h-4 text-primary focus:ring-primary">
                                <span class="text-sm text-[#0d1b12] dark:text-white">Inactive</span>
                            </label>
                        </div>
                    </div>

                    <!-- Featured Product -->
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="isFeatured" name="is_featured" value="1" class="w-5 h-5 text-primary rounded focus:ring-primary">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-[#0d1b12] dark:text-white">Featured Product</span>
                                <span class="text-xs text-gray-500">Display this product prominently in the catalog</span>
                            </div>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Sticky Bottom Actions -->
            <div class="p-6 border-t border-[#e7f3eb] dark:border-gray-800 bg-white dark:bg-[#152e1e]">
                <div class="flex gap-3">
                    <button onclick="closeProductPanel()" type="button" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 dark:border-gray-700 text-[#0d1b12] dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 font-bold text-sm transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="submitProduct()" class="flex-[2] flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-[#0ebf49] text-[#0d1b12] rounded-lg font-bold text-sm shadow-md shadow-primary/20 transition-all active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        <span id="submitButtonText">Add Product</span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Category Panel -->
        <aside id="categoryPanel" class="detail-panel hidden flex-col bg-white dark:bg-[#152e1e] h-full shadow-xl z-20 overflow-hidden fixed right-0 top-0 w-[28rem]">
            <div class="px-6 py-5 border-b border-[#e7f3eb] dark:border-gray-800 flex justify-between items-start bg-white dark:bg-[#152e1e]">
                <div>
                    <h2 class="text-xl font-black text-[#0d1b12] dark:text-white">Manage Categories</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Add or manage product categories</p>
                </div>
                <button onclick="closeCategoryPanel()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6">
                <form id="categoryForm" class="space-y-4 mb-6">
                    <input type="hidden" id="categoryId" name="category_id">
                    <div>
                        <label for="categoryName" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Category Name <span class="text-red-500">*</span></label>
                        <input type="text" id="categoryName" name="name" required class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white focus:ring-2 focus:ring-primary" placeholder="e.g., Beverages">
                    </div>
                    <button type="button" onclick="submitCategory()" class="w-full px-4 py-3 bg-primary hover:bg-[#0ebf49] text-[#0d1b12] rounded-lg font-bold text-sm transition-colors">
                        <span id="categoryButtonText">Add Category</span>
                    </button>
                </form>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h3 class="text-sm font-bold text-[#0d1b12] dark:text-white mb-3">Existing Categories</h3>
                    <div id="categoryList" class="space-y-2">
                        <?php foreach ($categories as $cat): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-white/5 rounded-lg">
                                <span class="text-sm text-[#0d1b12] dark:text-white"><?php echo htmlspecialchars($cat['name']); ?></span>
                                <button onclick="deleteCategory(<?php echo $cat['id']; ?>)" class="text-red-500 hover:text-red-700">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Brand Panel -->
        <aside id="brandPanel" class="detail-panel hidden flex-col bg-white dark:bg-[#152e1e] h-full shadow-xl z-20 overflow-hidden fixed right-0 top-0 w-[28rem]">
            <div class="px-6 py-5 border-b border-[#e7f3eb] dark:border-gray-800 flex justify-between items-start bg-white dark:bg-[#152e1e]">
                <div>
                    <h2 class="text-xl font-black text-[#0d1b12] dark:text-white">Manage Brands</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Add or manage product brands</p>
                </div>
                <button onclick="closeBrandPanel()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6">
                <form id="brandForm" class="space-y-4 mb-6">
                    <input type="hidden" id="brandId" name="brand_id">
                    <div>
                        <label for="brandName" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Brand Name <span class="text-red-500">*</span></label>
                        <input type="text" id="brandName" name="name" required class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white focus:ring-2 focus:ring-primary" placeholder="e.g., Coca-Cola">
                    </div>
                    <button type="button" onclick="submitBrand()" class="w-full px-4 py-3 bg-primary hover:bg-[#0ebf49] text-[#0d1b12] rounded-lg font-bold text-sm transition-colors">
                        <span id="brandButtonText">Add Brand</span>
                    </button>
                </form>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h3 class="text-sm font-bold text-[#0d1b12] dark:text-white mb-3">Existing Brands</h3>
                    <div id="brandList" class="space-y-2">
                        <?php foreach ($brands as $brand): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-white/5 rounded-lg">
                                <span class="text-sm text-[#0d1b12] dark:text-white"><?php echo htmlspecialchars($brand['name']); ?></span>
                                <button onclick="deleteBrand(<?php echo $brand['id']; ?>)" class="text-red-500 hover:text-red-700">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Product Details Panel -->
        <aside id="detailsPanel" class="detail-panel hidden flex-col bg-white dark:bg-[#152e1e] h-full shadow-xl z-20 overflow-hidden fixed right-0 top-0 w-[28rem]">
            <div class="px-6 py-5 border-b border-[#e7f3eb] dark:border-gray-800 flex justify-between items-start bg-white dark:bg-[#152e1e]">
                <div>
                    <h2 class="text-xl font-black text-[#0d1b12] dark:text-white">Product Details</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">View complete product information</p>
                </div>
                <button onclick="closeDetailsPanel()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6">
                <div id="productDetails" class="space-y-6">
                    <!-- Product details will be loaded here via JavaScript -->
                </div>
            </div>

            <!-- Mobile Bottom Nav Spacer -->
            <div class="h-16 lg:hidden"></div>
        </aside>

        <!-- Mobile Bottom Nav -->
        <div class="fixed bottom-0 left-0 w-full bg-surface-light dark:bg-surface-dark border-t border-[#e7f3eb] dark:border-[#2a4034] flex lg:hidden justify-around py-3 px-2 z-50">
            <a href="../Dasboard/dasboard.php" class="flex flex-col items-center gap-1 text-text-secondary-light dark:text-text-secondary-dark">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-[10px] font-medium">Dashboard</span>
            </a>
            <a href="../Orders/orders.php" class="flex flex-col items-center gap-1 text-text-secondary-light dark:text-text-secondary-dark">
                <span class="material-symbols-outlined">shopping_cart</span>
                <span class="text-[10px] font-medium">Orders</span>
            </a>
            <a href="inventory.php" class="flex flex-col items-center gap-1 text-primary">
                <span class="material-symbols-outlined">inventory_2</span>
                <span class="text-[10px] font-bold">Inventory</span>
            </a>
            <button id="mobileMoreBtn" class="flex flex-col items-center gap-1 text-text-secondary-light dark:text-text-secondary-dark">
                <span class="material-symbols-outlined">more_horiz</span>
                <span class="text-[10px] font-medium">More</span>
            </button>
        </div>

        <!-- Mobile More Menu -->
        <div id="mobileMoreMenu" class="hidden fixed bottom-16 right-2 bg-surface-light dark:bg-surface-dark border border-[#e7f3eb] dark:border-[#2a4034] rounded-lg shadow-xl z-50 min-w-[200px]">
            <a href="../Logistics/logistics.php" class="flex items-center gap-3 px-4 py-3 hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors border-b border-[#e7f3eb] dark:border-[#2a4034]">
                <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark">local_shipping</span>
                <span class="text-sm font-medium">Logistics</span>
            </a>
            <a href="../User_Management/user_managment.php" class="flex items-center gap-3 px-4 py-3 hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors <?php echo ($user_type === 'admin') ? 'border-b border-[#e7f3eb] dark:border-[#2a4034]' : ''; ?>">
                <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark">account_child_invert</span>
                <span class="text-sm font-medium">Users</span>
            </a>
            <?php if ($user_type === 'admin'): ?>
                <a href="../Reports/reports.php" class="flex items-center gap-3 px-4 py-3 hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors">
                    <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark">description</span>
                    <span class="text-sm font-medium">Reports</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../components/scripts.php'; ?>
    <script src="js/scripts.js"></script>
    <script>
        // Mobile more menu toggle
        document.getElementById('mobileMoreBtn')?.addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('mobileMoreMenu')?.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('mobileMoreMenu');
            const btn = document.getElementById('mobileMoreBtn');
            if (menu && !menu.contains(e.target) && !btn?.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>

</html>