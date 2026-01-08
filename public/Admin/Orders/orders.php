<?php
require_once '../../../config/admin_session.php';
require_once '../../../config/database.php';

// Get order stats
$conn = getDBConnection();
$statsQuery = "SELECT 
                COUNT(CASE WHEN order_status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN order_status = 'processing' THEN 1 END) as processing_count,
                COUNT(CASE WHEN order_status = 'packed' THEN 1 END) as packed_count,
                COUNT(CASE WHEN order_status = 'shipped' THEN 1 END) as shipped_count,
                COUNT(CASE WHEN order_status = 'delivered' THEN 1 END) as delivered_count
            FROM orders";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();

// Get all orders
$ordersQuery = "SELECT 
                o.id,
                o.order_number,
                o.customer_name,
                o.business_name,
                o.shipping_city,
                o.shipping_province,
                o.total_amount,
                o.order_status,
                o.payment_status,
                o.created_at,
                o.driver_id,
                d.full_name as driver_name,
                d.employee_id as driver_employee_id,
                COUNT(oi.id) as total_items
            FROM orders o
            LEFT JOIN drivers d ON o.driver_id = d.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.created_at DESC";
$ordersResult = $conn->query($ordersQuery);
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Order Management - RDC Staff View</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet" />
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Theme Configuration -->
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#11d452",
                        "background-light": "#f6f8f6",
                        "background-dark": "#102216",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1c2e24",
                        "text-main-light": "#0d1b12",
                        "text-main-dark": "#e0ece4",
                        "text-secondary-light": "#4c9a66",
                        "text-secondary-dark": "#8dbca0",
                    },
                    fontFamily: {
                        "display": ["Manrope", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <?php include '../components/styles.php'; ?>
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="font-display bg-background-light dark:bg-background-dark text-[#0d1b12] dark:text-gray-100 antialiased min-h-screen flex overflow-hidden">

    <div class="flex h-screen w-full overflow-hidden">
        <?php include '../components/sidebar.php'; ?>

        <!-- Main Content Area -->
        <main class="flex flex-1 overflow-hidden w-full relative">
            <!-- Left Panel: Order List (Master View) -->
            <section id="orderListSection" class="flex flex-col w-full h-full overflow-y-auto bg-background-light dark:bg-background-dark transition-all duration-300">
                <!-- Header & KPIs -->
                <div class="p-6 pb-0 space-y-6">
                    <!-- Heading -->
                    <div class="flex flex-col gap-2">
                        <h1 class="text-[#0d1b12] dark:text-white text-3xl font-black leading-tight tracking-[-0.033em]">Order Management</h1>
                        <p class="text-[#4c9a66] dark:text-gray-400 text-sm font-medium">Central Region â€¢ 45 Active Orders</p>
                    </div>
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        <!-- Pending Orders -->
                        <div class="flex flex-col gap-1 rounded-xl p-4 border border-[#cfe7d7] dark:border-gray-700 bg-white dark:bg-[#152e1e] shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-500" style="font-size: 20px;">schedule</span>
                                <p class="text-[#0d1b12] dark:text-gray-300 text-xs font-medium">Pending</p>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold"><?php echo $stats['pending_count'] ?? 0; ?></p>
                        </div>
                        <!-- Processing Orders -->
                        <div class="flex flex-col gap-1 rounded-xl p-4 border border-[#cfe7d7] dark:border-gray-700 bg-white dark:bg-[#152e1e] shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-500" style="font-size: 20px;">autorenew</span>
                                <p class="text-[#0d1b12] dark:text-gray-300 text-xs font-medium">Processing</p>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold"><?php echo $stats['processing_count'] ?? 0; ?></p>
                        </div>
                        <!-- Packed Orders -->
                        <div class="flex flex-col gap-1 rounded-xl p-4 border border-[#cfe7d7] dark:border-gray-700 bg-white dark:bg-[#152e1e] shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-purple-500" style="font-size: 20px;">inventory_2</span>
                                <p class="text-[#0d1b12] dark:text-gray-300 text-xs font-medium">Packed</p>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold"><?php echo $stats['packed_count'] ?? 0; ?></p>
                        </div>
                        <!-- Shipped Orders -->
                        <div class="flex flex-col gap-1 rounded-xl p-4 border border-[#cfe7d7] dark:border-gray-700 bg-white dark:bg-[#152e1e] shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-indigo-500" style="font-size: 20px;">local_shipping</span>
                                <p class="text-[#0d1b12] dark:text-gray-300 text-xs font-medium">Shipped</p>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold"><?php echo $stats['shipped_count'] ?? 0; ?></p>
                        </div>
                        <!-- Delivered Orders -->
                        <div class="flex flex-col gap-1 rounded-xl p-4 border border-[#cfe7d7] dark:border-gray-700 bg-white dark:bg-[#152e1e] shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-green-500" style="font-size: 20px;">check_circle</span>
                                <p class="text-[#0d1b12] dark:text-gray-300 text-xs font-medium">Delivered</p>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold"><?php echo $stats['delivered_count'] ?? 0; ?></p>
                        </div>
                    </div>
                    <!-- Search & Filters -->
                    <div class="flex flex-col gap-4">
                        <!-- Search Bar and Export Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-[#4c9a66]">search</span>
                                </div>
                                <input id="searchInput" class="block w-full pl-10 pr-3 py-3 border border-transparent rounded-lg leading-5 bg-[#e7f3eb] dark:bg-[#1e3b29] text-[#0d1b12] dark:text-white placeholder-[#4c9a66] focus:outline-none focus:bg-white dark:focus:bg-[#152e1e] focus:ring-2 focus:ring-primary focus:border-transparent sm:text-sm transition-all shadow-sm" placeholder="Search by Order ID, Customer Name, or Zone..." type="text" />
                            </div>
                            <div class="flex gap-2">
                                <button onclick="exportToCSV()" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#152e1e] border border-[#e7f3eb] dark:border-gray-700 rounded-lg hover:border-primary transition-colors shadow-sm text-[#0d1b12] dark:text-gray-300">
                                    <span class="material-symbols-outlined text-[20px]">download</span>
                                    <span class="text-sm font-semibold hidden sm:inline">Export CSV</span>
                                </button>
                                <button onclick="exportToExcel()" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#152e1e] border border-[#e7f3eb] dark:border-gray-700 rounded-lg hover:border-primary transition-colors shadow-sm text-[#0d1b12] dark:text-gray-300">
                                    <span class="material-symbols-outlined text-[20px]">table_chart</span>
                                    <span class="text-sm font-semibold hidden sm:inline">Export Excel</span>
                                </button>
                            </div>
                        </div>
                        <!-- Chips -->
                        <div class="flex gap-2 flex-wrap pb-2">
                            <button onclick="filterByStatus('all')" id="filterAll" class="flex items-center justify-center gap-2 rounded-full bg-[#102216] dark:bg-white text-white dark:text-black px-4 py-1.5 shadow-sm transition-transform active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                                <span class="text-xs font-bold">All Orders</span>
                            </button>
                            <div class="relative">
                                <button onclick="toggleStatusFilter()" class="flex items-center justify-center gap-2 rounded-full bg-white dark:bg-[#152e1e] border border-[#e7f3eb] dark:border-gray-700 hover:border-primary px-4 py-1.5 transition-colors shadow-sm text-[#0d1b12] dark:text-gray-300">
                                    <span class="text-xs font-semibold" id="statusFilterLabel">Status: All</span>
                                    <span class="material-symbols-outlined text-[16px]">expand_more</span>
                                </button>
                                <div id="statusFilterDropdown" class="hidden absolute top-full mt-1 bg-white dark:bg-[#152e1e] border border-[#e7f3eb] dark:border-gray-700 rounded-lg shadow-lg z-50 min-w-[150px]">
                                    <button onclick="filterByStatus('all')" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 text-[#0d1b12] dark:text-gray-300">All</button>
                                    <button onclick="filterByStatus('pending')" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 text-[#0d1b12] dark:text-gray-300">Pending</button>
                                    <button onclick="filterByStatus('processing')" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 text-[#0d1b12] dark:text-gray-300">Processing</button>
                                    <button onclick="filterByStatus('packed')" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 text-[#0d1b12] dark:text-gray-300">Packed</button>
                                    <button onclick="filterByStatus('shipped')" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 text-[#0d1b12] dark:text-gray-300">Shipped</button>
                                    <button onclick="filterByStatus('delivered')" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 text-[#0d1b12] dark:text-gray-300">Delivered</button>
                                    <button onclick="filterByStatus('cancelled')" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 text-[#0d1b12] dark:text-gray-300">Cancelled</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order List Table -->
                <div class="flex-1 overflow-auto mt-2 px-6 pb-6">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-background-light dark:bg-background-dark z-10">
                            <tr>
                                <th class="py-3 px-2 text-xs font-semibold text-[#4c9a66] uppercase tracking-wider">Order ID</th>
                                <th class="py-3 px-2 text-xs font-semibold text-[#4c9a66] uppercase tracking-wider">Customer</th>
                                <th class="py-3 px-2 text-xs font-semibold text-[#4c9a66] uppercase tracking-wider">Zone</th>
                                <th class="py-3 px-2 text-xs font-semibold text-[#4c9a66] uppercase tracking-wider text-right">Items</th>
                                <th class="py-3 px-2 text-xs font-semibold text-[#4c9a66] uppercase tracking-wider text-right">Total</th>
                                <th class="py-3 px-2 text-xs font-semibold text-[#4c9a66] uppercase tracking-wider text-center">Status</th>
                                <th class="py-3 px-2 text-xs font-semibold text-[#4c9a66] uppercase tracking-wider"></th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody" class="divide-y divide-[#e7f3eb] dark:divide-gray-800 text-sm">
                            <?php
                            function getStatusBadge($status)
                            {
                                $badges = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-800',
                                    'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-200 dark:border-blue-800',
                                    'packed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 border border-purple-200 dark:border-purple-800',
                                    'shipped' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 border border-indigo-200 dark:border-indigo-800',
                                    'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border border-green-200 dark:border-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 border border-red-200 dark:border-red-800'
                                ];
                                return $badges[$status] ?? 'bg-gray-100 text-gray-800';
                            }

                            function getTimeAgo($datetime)
                            {
                                $timestamp = strtotime($datetime);
                                $diff = time() - $timestamp;

                                if ($diff < 60) return 'Just now';
                                if ($diff < 3600) return floor($diff / 60) . ' mins ago';
                                if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
                                if ($diff < 604800) return floor($diff / 86400) . ' days ago';
                                return date('M d, Y', $timestamp);
                            }

                            while ($order = $ordersResult->fetch_assoc()):
                            ?>
                                <tr onclick="showOrderDetail(<?php echo $order['id']; ?>)"
                                    class="order-row group hover:bg-white dark:hover:bg-[#152e1e] cursor-pointer transition-colors <?php echo $order['order_status'] == 'pending' ? 'bg-white dark:bg-[#152e1e] border-l-4 border-l-primary shadow-sm' : ''; ?>"
                                    data-order-id="<?php echo htmlspecialchars($order['order_number']); ?>"
                                    data-customer="<?php echo htmlspecialchars($order['business_name'] ?? $order['customer_name']); ?>"
                                    data-zone="<?php echo htmlspecialchars($order['shipping_city']); ?>"
                                    data-status="<?php echo htmlspecialchars($order['order_status']); ?>">
                                    <td class="py-4 px-2 font-bold text-[#0d1b12] dark:text-white">#<?php echo htmlspecialchars($order['order_number']); ?></td>
                                    <td class="py-4 px-2">
                                        <div class="font-bold text-[#0d1b12] dark:text-white"><?php echo htmlspecialchars($order['business_name'] ?? $order['customer_name']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo getTimeAgo($order['created_at']); ?></div>
                                    </td>
                                    <td class="py-4 px-2 text-[#0d1b12] dark:text-gray-300"><?php echo htmlspecialchars(($order['shipping_city'] ?? '') . ($order['shipping_province'] ? ', ' . $order['shipping_province'] : '') ?: 'N/A'); ?></td>
                                    <td class="py-4 px-2 text-right text-[#0d1b12] dark:text-gray-300"><?php echo $order['total_items']; ?></td>
                                    <td class="py-4 px-2 text-right font-bold text-[#0d1b12] dark:text-white">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td class="py-4 px-2 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo getStatusBadge($order['order_status']); ?>">
                                            <?php echo ucfirst($order['order_status']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-2 text-right">
                                        <span class="material-symbols-outlined text-gray-400 group-hover:text-primary">chevron_right</span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <!-- Right Panel: Order Details (Detail View) -->
            <aside id="detailPanel" class="detail-panel hidden flex-col bg-white dark:bg-[#152e1e] h-full shadow-xl z-20 overflow-hidden fixed right-0 top-0 w-5/12 xl:w-4/12">
                <!-- Detail Header -->
                <div class="px-6 py-5 border-b border-[#e7f3eb] dark:border-gray-800 bg-white dark:bg-[#152e1e]">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h2 id="orderNumber" class="text-xl font-black text-[#0d1b12] dark:text-white">Loading...</h2>
                                <span id="orderStatus" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-gray-100 text-gray-800 border border-gray-200">
                                    ...
                                </span>
                            </div>
                            <p id="orderDate" class="text-xs text-gray-500 dark:text-gray-400">Loading...</p>
                        </div>
                        <button onclick="closeOrderDetail()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <!-- Invoice Action Buttons -->
                    <div class="flex gap-2">
                        <button onclick="printOrderInvoice()" class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-white dark:bg-[#102216] border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-primary hover:text-primary dark:hover:border-primary dark:hover:text-primary rounded-lg font-medium text-xs transition-all">
                            <span class="material-symbols-outlined text-[16px]">print</span>
                            <span>Print</span>
                        </button>
                        <button onclick="downloadOrderInvoice()" class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-primary text-white hover:bg-[#0ebf49] rounded-lg font-medium text-xs transition-all">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            <span>Download</span>
                        </button>
                    </div>
                </div>
                <!-- Scrollable Content -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <!-- Status Timeline (Simple) -->
                    <div class="flex items-center justify-between relative px-2">
                        <div class="absolute top-1/2 left-0 w-full h-0.5 bg-gray-100 dark:bg-gray-700 -z-10"></div>
                        <div class="flex flex-col items-center gap-1 z-10">
                            <div class="size-6 rounded-full bg-primary flex items-center justify-center text-white shadow-lg shadow-primary/30">
                                <span class="material-symbols-outlined text-[14px] font-bold">check</span>
                            </div>
                            <span class="text-[10px] font-bold text-primary">Received</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 z-10">
                            <div class="size-6 rounded-full bg-white dark:bg-[#152e1e] border-2 border-primary flex items-center justify-center text-primary">
                                <span class="text-[10px] font-bold">2</span>
                            </div>
                            <span class="text-[10px] font-bold text-[#0d1b12] dark:text-white">Review</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 z-10">
                            <div class="size-6 rounded-full bg-white dark:bg-[#152e1e] border-2 border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-400">
                                <span class="text-[10px] font-bold">3</span>
                            </div>
                            <span class="text-[10px] font-medium text-gray-400">Pick</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 z-10">
                            <div class="size-6 rounded-full bg-white dark:bg-[#152e1e] border-2 border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-400">
                                <span class="text-[10px] font-bold">4</span>
                            </div>
                            <span class="text-[10px] font-medium text-gray-400">Dispatch</span>
                        </div>
                    </div>
                    <!-- Customer Card -->
                    <div class="bg-background-light dark:bg-background-dark rounded-xl p-4 border border-[#e7f3eb] dark:border-gray-800">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="bg-white dark:bg-gray-800 rounded-full p-2 border border-gray-100 dark:border-gray-700">
                                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-300">storefront</span>
                                </div>
                                <div>
                                    <h3 id="customerName" class="text-sm font-bold text-[#0d1b12] dark:text-white">Loading...</h3>
                                    <p id="customerTier" class="text-xs text-primary font-medium">...</p>
                                </div>
                            </div>
                            <!--    <button class="text-xs text-[#4c9a66] hover:underline font-medium">View Profile</button>-->
                        </div>
                        <div class="grid grid-cols-2 gap-y-3 gap-x-2 text-xs">
                            <div>
                                <p class="text-gray-400 mb-0.5">Contact Person</p>
                                <p id="contactPerson" class="font-medium text-[#0d1b12] dark:text-gray-200">...</p>
                            </div>
                            <div>
                                <p class="text-gray-400 mb-0.5">Phone</p>
                                <p id="contactPhone" class="font-medium text-[#0d1b12] dark:text-gray-200">...</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-400 mb-0.5">Shipping Address</p>
                                <p id="shippingAddress" class="font-medium text-[#0d1b12] dark:text-gray-200">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <!-- Line Items -->
                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <h3 class="text-sm font-bold text-[#0d1b12] dark:text-white">Order Items (<span id="orderItemCount">0</span>)</h3>
                        </div>
                        <div class="border border-[#e7f3eb] dark:border-gray-800 rounded-lg overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <th class="py-2 px-3 font-medium text-gray-500">Product</th>
                                        <th class="py-2 px-3 font-medium text-gray-500 text-center">Qty</th>
                                        <th class="py-2 px-3 font-medium text-gray-500 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="orderItemsBody" class="divide-y divide-[#e7f3eb] dark:divide-gray-800 bg-white dark:bg-[#152e1e]">
                                    <!-- Order items will be loaded here by JavaScript -->
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <td class="py-3 px-3 text-right font-bold text-gray-600 dark:text-gray-300" colspan="2">Grand Total</td>
                                        <td id="orderGrandTotal" class="py-3 px-3 text-right font-black text-lg text-[#0d1b12] dark:text-white">$0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- Logistics Assignment -->
                    <div>
                        <h3 class="text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Driver Assignment</h3>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <select id="driverSelect" class="block w-full pl-3 pr-10 py-2.5 text-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-[#102216] focus:outline-none focus:ring-primary focus:border-primary rounded-lg appearance-none text-[#0d1b12] dark:text-white border shadow-sm">
                                    <option value="">Select Delivery Partner...</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <span class="material-symbols-outlined">expand_more</span>
                                </div>
                            </div>
                            <button onclick="saveDriverAssignment()" class="px-4 py-2.5 bg-primary hover:bg-[#0ebf49] text-white rounded-lg font-medium text-sm transition-colors flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">save</span> Save
                            </button>
                        </div>
                    </div>
                    <!-- Delivery Notes -->
                    <div>
                        <h3 class="text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Delivery Notes (Admin)</h3>
                        <div class="flex gap-2">
                            <textarea id="deliveryNotes" class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-[#102216] text-sm text-[#0d1b12] dark:text-white p-3 focus:ring-primary focus:border-primary shadow-sm border" placeholder="Add special instructions for the driver..." rows="3"></textarea>
                            <button id="saveNotesBtn" onclick="saveDeliveryNotes()" class="px-4 py-2.5 bg-primary hover:bg-[#0ebf49] text-white rounded-lg font-medium text-sm transition-colors flex items-center gap-1 h-fit">
                                <span class="material-symbols-outlined text-[18px]">save</span> Save
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Sticky Bottom Actions -->
                <div id="orderActions" class="p-6 border-t border-[#e7f3eb] dark:border-gray-800 bg-white dark:bg-[#152e1e] space-y-3">
                    <!-- Invoice Buttons -->
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="printOrderInvoice()" class="flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-[#102216] border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-lg font-bold text-sm transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[20px]">print</span>
                            <span>Print</span>
                        </button>
                        <button onclick="downloadOrderInvoice()" class="flex items-center justify-center gap-2 px-4 py-3 bg-primary text-white hover:bg-[#0ebf49] rounded-lg font-bold text-sm transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[20px]">download</span>
                            <span>Download</span>
                        </button>
                    </div>
                    <!-- Action buttons will be dynamically loaded based on order status -->
                </div>
            </aside>

            <!-- Delivery Confirmation Modal -->
            <div id="deliveryConfirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-[#152e1e] rounded-xl shadow-2xl max-w-md w-full p-6 space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-2xl">check_circle</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-[#0d1b12] dark:text-white mb-2">Confirm Delivery</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Are you sure this order has been successfully delivered to the customer?</p>

                            <div class="space-y-3">
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="material-symbols-outlined text-gray-500 text-[18px]">tag</span>
                                    <span class="text-gray-600 dark:text-gray-400">Order: <span id="modalOrderNumber" class="font-bold text-[#0d1b12] dark:text-white"></span></span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="material-symbols-outlined text-gray-500 text-[18px]">storefront</span>
                                    <span class="text-gray-600 dark:text-gray-400">Customer: <span id="modalCustomerName" class="font-bold text-[#0d1b12] dark:text-white"></span></span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="material-symbols-outlined text-gray-500 text-[18px]">local_shipping</span>
                                    <span class="text-gray-600 dark:text-gray-400">Driver: <span id="modalDriverName" class="font-bold text-[#0d1b12] dark:text-white"></span></span>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <p class="text-xs text-green-800 dark:text-green-300 flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[16px] mt-0.5">info</span>
                                    <span>This action will mark the order as completed and cannot be undone.</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button onclick="closeDeliveryConfirmModal()" class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 font-medium text-sm transition-colors">
                            Cancel
                        </button>
                        <button onclick="confirmDelivery()" class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold text-sm shadow-md shadow-green-600/20 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            Confirm Delivery
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Bottom Nav Spacer -->
            <div class="h-16 lg:hidden"></div>
        </main>

        <!-- Mobile Bottom Nav -->
        <div class="fixed bottom-0 left-0 w-full bg-surface-light dark:bg-surface-dark border-t border-[#e7f3eb] dark:border-[#2a4034] flex lg:hidden justify-around py-3 px-2 z-50">
            <a href="../Dasboard/dasboard.php" class="flex flex-col items-center gap-1 text-text-secondary-light dark:text-text-secondary-dark">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-[10px] font-medium">Dashboard</span>
            </a>
            <a href="orders.php" class="flex flex-col items-center gap-1 text-primary">
                <span class="material-symbols-outlined">shopping_cart</span>
                <span class="text-[10px] font-bold">Orders</span>
            </a>
            <a href="../Inventory/inventory.php" class="flex flex-col items-center gap-1 text-text-secondary-light dark:text-text-secondary-dark">
                <span class="material-symbols-outlined">inventory_2</span>
                <span class="text-[10px] font-medium">Inventory</span>
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
    <script src="../Orders/js/script.js"></script>
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

        // Export to CSV function
        function exportToCSV() {
            const rows = document.querySelectorAll('#ordersTableBody tr:not(.hidden)');
            if (rows.length === 0) {
                alert('No orders to export');
                return;
            }

            let csvContent = "Order ID,Customer,Business Name,Zone,Items,Total,Status,Payment,Date\n";

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const orderId = cells[0]?.textContent.trim();
                const customer = cells[1]?.querySelector('.font-bold')?.textContent.trim();
                const businessName = cells[1]?.querySelector('.text-xs')?.textContent.trim();
                const zone = cells[2]?.textContent.trim();
                const items = cells[3]?.textContent.trim();
                const total = cells[4]?.textContent.trim();
                const status = cells[5]?.textContent.trim();
                const payment = cells[6]?.textContent.trim();
                const date = cells[7]?.textContent.trim();

                csvContent += `"${orderId}","${customer}","${businessName}","${zone}","${items}","${total}","${status}","${payment}","${date}"\n`;
            });

            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `orders_export_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Export to Excel function (HTML table format)
        function exportToExcel() {
            const rows = document.querySelectorAll('#ordersTableBody tr:not(.hidden)');
            if (rows.length === 0) {
                alert('No orders to export');
                return;
            }

            let htmlContent = `
                <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
                <head><meta charset="UTF-8"></head>
                <body>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Business Name</th>
                            <th>Zone</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const orderId = cells[0]?.textContent.trim();
                const customer = cells[1]?.querySelector('.font-bold')?.textContent.trim();
                const businessName = cells[1]?.querySelector('.text-xs')?.textContent.trim();
                const zone = cells[2]?.textContent.trim();
                const items = cells[3]?.textContent.trim();
                const total = cells[4]?.textContent.trim();
                const status = cells[5]?.textContent.trim();
                const payment = cells[6]?.textContent.trim();
                const date = cells[7]?.textContent.trim();

                htmlContent += `
                    <tr>
                        <td>${orderId}</td>
                        <td>${customer}</td>
                        <td>${businessName}</td>
                        <td>${zone}</td>
                        <td>${items}</td>
                        <td>${total}</td>
                        <td>${status}</td>
                        <td>${payment}</td>
                        <td>${date}</td>
                    </tr>
                `;
            });

            htmlContent += '</tbody></table></body></html>';

            const blob = new Blob([htmlContent], {
                type: 'application/vnd.ms-excel'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `orders_export_${new Date().toISOString().split('T')[0]}.xls`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>

</html>