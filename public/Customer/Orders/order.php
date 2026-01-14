<?php
// db connction and the order_data_handler
require_once '../../../config/session_Detils.php';
require_once 'order_data_handler.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>IslandDistro - Customer Order Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#22c55e",
                        "primary-hover": "#16a34a",
                        "background-light": "#f3f4f6",
                        "background-dark": "#111827",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1f2937",
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                    },
                },
            },
        };
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }
    </style>
</head>

<!-- This will show the order detils acordigg to the  login session data  -->

<body class="bg-background-light dark:bg-background-dark text-gray-800 dark:text-gray-200 transition-colors duration-200 min-h-screen">
    <div id="mainWrapper" class="transition-all duration-300 ease-in-out">
        <nav class="sticky top-0 z-50 bg-surface-light dark:bg-surface-dark border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center flex-1">
                        <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer">
                            <span class="material-symbols-outlined text-primary text-3xl">shopping_bag_speed</span>
                            <span class="font-bold text-xl tracking-tight text-gray-900 dark:text-white">IslandDistro</span>
                        </div>
                        <div class="hidden md:flex ml-10 flex-1 max-w-lg">
                            <div class="relative w-full text-gray-400 focus-within:text-gray-600 dark:focus-within:text-gray-200">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-gray-400 text-[20px]">search</span>
                                </div>
                                <input class="block w-full pl-10 pr-3 py-2 border-transparent rounded-lg leading-5 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm transition duration-150 ease-in-out" id="global-search" name="search" placeholder="Search products, orders..." type="search" />
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div class="hidden md:flex items-center space-x-6 text-sm font-medium">
                            <a class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition" href="../Dashboard/dashboard.php">Dashboard</a>
                            <a class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition" href="../Catalog/catalog.php">Catalog</a>
                            <a class="text-primary font-semibold" href="#">Orders</a>
                            <!--<a class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition" href="#">Invoices</a> -->
                        </div>
                        <div class="relative ml-2">
                            <button id="profileMenuBtn" class="size-9 rounded-full bg-slate-300 dark:bg-slate-700 bg-cover bg-center border-2 border-slate-100 dark:border-slate-800 hover:border-primary dark:hover:border-primary transition-colors" data-alt="User profile avatar showing a store logo or generic user icon" style='background-image: url("https://avatar.iran.liara.run/username?username=<?php echo urlencode($business_name); ?>");'></button>
                            <!-- Dropdown Menu -->
                            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-lg shadow-lg overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800">
                                    <p class="text-sm font-bold"><?php echo htmlspecialchars($full_name); ?></p>
                                    <p class="text-xs text-text-secondary dark:text-emerald-400"><?php echo htmlspecialchars($business_name); ?></p>
                                </div>
                                <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">person</span>
                                    <span class="text-sm font-medium">Edit Profile</span>
                                </a>
                                <a href="../../logout/logout.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors text-red-600 dark:text-red-400">
                                    <span class="material-symbols-outlined text-[20px]">logout</span>
                                    <span class="text-sm font-medium">Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- this secion will load the data to the tabel and this is the main secion -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 transition hover:shadow-md">
                    <p class="text-sm font-medium text-primary mb-1">Active Orders</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $stats['active_orders']; ?></h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            <?php echo $stats['arriving_today']; ?> Arriving
                        </span>
                    </div>
                </div>
                <!-- cards that is show in the top -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 transition hover:shadow-md">
                    <p class="text-sm font-medium text-primary mb-1">Pending Payment</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white">Rs. <?php echo number_format($stats['pending_payment'], 2); ?></h3>
                    </div>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 transition hover:shadow-md">
                    <p class="text-sm font-medium text-primary mb-1">Total Spend</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white">Rs. <?php echo number_format($stats['total_spend'], 2); ?></h3>
                    </div>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 transition hover:shadow-md">
                    <p class="text-sm font-medium text-primary mb-1">Loyalty Points</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $stats['loyalty_points']; ?></h3>
                        <a class="text-sm font-medium text-primary hover:text-green-600 dark:hover:text-green-400 transition" href="#">Redeem</a>
                    </div>
                </div>
            </div>
            <div class="w-full">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">All Orders</h2>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <form method="GET" action="" class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400 text-[18px]">search</span>
                            </div>
                            <!-- search input -->
                            <input name="search" value="<?php echo htmlspecialchars($search_query); ?>" class="w-full pl-9 pr-12 py-2 text-sm border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-surface-dark text-gray-900 dark:text-white focus:ring-primary focus:border-primary shadow-sm" placeholder="Search Order ID..." type="text" />
                            <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>" />
                            <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <span class="material-symbols-outlined text-gray-400 hover:text-primary text-[18px]">arrow_forward</span>
                            </button>
                        </form>
                    </div>
                </div>
                <!-- table filters -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-t-xl border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex flex-wrap items-center gap-2">
                    <a href="?status=all<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo $status_filter === 'all' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'; ?> transition-colors">
                        All Orders
                    </a>
                    <a href="?status=pending<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo $status_filter === 'pending' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'; ?> transition-colors">
                        Pending
                    </a>
                    <a href="?status=in_transit<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo $status_filter === 'in_transit' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'; ?> transition-colors">
                        In Transit
                    </a>
                    <a href="?status=completed<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo $status_filter === 'completed' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'; ?> transition-colors">
                        Completed
                    </a>
                    <a href="?status=cancelled<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo $status_filter === 'cancelled' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'; ?> transition-colors">
                        Cancelled
                    </a>
                </div>
                <!-- table filters -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-b-xl shadow-sm border border-t-0 border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Order ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Date Placed</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Total Amount</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-surface-dark">
                                <?php if (count($orders) > 0): ?>
                                    <?php foreach ($orders as $order):
                                        $badge = getStatusBadge($order['order_status']);
                                        $can_track = in_array($order['order_status'], ['processing', 'packed', 'shipped']);
                                        $can_download = $order['payment_status'] === 'paid' && $order['order_status'] !== 'cancelled';
                                    ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition group">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($order['order_number']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $badge['class']; ?> border">
                                                    <span class="w-1.5 h-1.5 mr-1.5 <?php echo $badge['dot']; ?> rounded-full <?php echo $badge['animate'] ? 'animate-pulse' : ''; ?>"></span>
                                                    <?php echo $badge['label']; ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                                Rs. <?php echo number_format($order['total_amount'], 2); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button onclick="viewOrderDetails('<?php echo htmlspecialchars($order['order_number']); ?>', <?php echo $order['id']; ?>)" class="text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors" title="View Details">
                                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                                    </button>
                                                    <?php if ($can_track): ?>
                                                        <button onclick="location.href='track_shipment.php?order_id=<?php echo $order['id']; ?>'" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 p-1 rounded transition-colors" title="Track Shipment">
                                                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="text-gray-300 cursor-not-allowed dark:text-gray-600 p-1 rounded" disabled title="Tracking not available">
                                                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($can_download): ?>
                                                        <button onclick="window.open('download_invoice.php?order_id=<?php echo $order['id']; ?>', '_blank')" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors" title="Download Invoice">
                                                            <span class="material-symbols-outlined text-[20px]">download</span>
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="text-gray-300 cursor-not-allowed dark:text-gray-600 p-1 rounded" disabled title="Invoice not available">
                                                            <span class="material-symbols-outlined text-[20px]">download</span>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <!-- no orders found message -->
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <span class="material-symbols-outlined text-5xl text-gray-300 dark:text-gray-600 mb-3">receipt_long</span>
                                                <p class="text-gray-500 dark:text-gray-400">No orders found</p>
                                                <?php if (!empty($search_query) || $status_filter !== 'all'): ?>
                                                    <a href="?" class="mt-3 text-primary hover:text-primary-hover font-medium text-sm">Clear filters</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!--page numbers -->
                    <div class="bg-white dark:bg-surface-dark px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">
                                        Showing <span class="font-medium"><?php echo $total_orders > 0 ? $offset + 1 : 0; ?></span> to <span class="font-medium"><?php echo min($offset + $per_page, $total_orders); ?></span> of <span class="font-medium"><?php echo $total_orders; ?></span> results
                                    </p>
                                </div>
                                <?php if ($total_pages > 1): ?>
                                    <div>
                                        <nav aria-label="Pagination" class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                            <?php if ($page > 1): ?>
                                                <a href="?page=<?php echo $page - 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_query); ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-surface-dark text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800">
                                                    <span class="sr-only">Previous</span>
                                                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-sm font-medium text-gray-400 cursor-not-allowed">
                                                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                                                </span>
                                            <?php endif; ?>

                                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                                <?php if ($i == $page): ?>
                                                    <a aria-current="page" class="z-10 bg-primary border-primary text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_query); ?>">
                                                        <?php echo $i; ?>
                                                    </a>
                                                <?php else: ?>
                                                    <a class="bg-white dark:bg-surface-dark border-gray-300 dark:border-gray-600 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_query); ?>">
                                                        <?php echo $i; ?>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endfor; ?>

                                            <?php if ($page < $total_pages): ?>
                                                <a href="?page=<?php echo $page + 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search_query); ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-surface-dark text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800">
                                                    <span class="sr-only">Next</span>
                                                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-sm font-medium text-gray-400 cursor-not-allowed">
                                                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                                                </span>
                                            <?php endif; ?>
                                        </nav>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- footer section  -->
        <footer class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm text-gray-400 dark:text-gray-600">
            © 2026 IslandDistro Inc. All rights reserved.
        </footer>

        <!-- Mobile Bottom Nav Spacer -->
        <div class="h-16 lg:hidden"></div>

        <!-- Mobile Bottom Nav -->
        <div class="fixed bottom-0 left-0 w-full bg-surface-light dark:bg-surface-dark border-t border-slate-200 dark:border-slate-800 flex lg:hidden justify-around py-3 px-2 z-40">
            <a href="../Dashboard/dashboard.php" class="flex flex-col items-center gap-1 text-text-secondary dark:text-emerald-400">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-[10px] font-medium">Home</span>
            </a>
            <a href="../Catalog/catalog.php" class="flex flex-col items-center gap-1 text-text-secondary dark:text-emerald-400">
                <span class="material-symbols-outlined">manage_search</span>
                <span class="text-[10px] font-medium">Catalog</span>
            </a>
            <a href="order.php" class="flex flex-col items-center gap-1 text-primary">
                <span class="material-symbols-outlined">receipt_long</span>
                <span class="text-[10px] font-bold">Orders</span>
            </a>
            <button id="mobileProfileBtn" class="flex flex-col items-center gap-1 text-text-secondary dark:text-emerald-400">
                <span class="material-symbols-outlined">person</span>
                <span class="text-[10px] font-medium">Account</span>
            </button>
        </div>
    </div>

    <!-- Order Details Sidebar -->
    <div id="orderDetailsSidebar" class="fixed inset-y-0 right-0 w-full sm:w-96 md:w-[500px] bg-white dark:bg-surface-dark shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50 overflow-y-auto border-l border-gray-200 dark:border-gray-700">
        <div class="sticky top-0 bg-white dark:bg-surface-dark border-b border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between z-10">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Order Details</h2>
            <button onclick="closeOrderDetails()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-gray-500 dark:text-gray-400">close</span>
            </button>
        </div>
        <div id="orderDetailsContent" class="p-6">
            <div class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>
        </div>
    </div>

    <script>
        // Profile dropdown toggle
        const profileMenuBtn = document.getElementById('profileMenuBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        const mobileProfileBtn = document.getElementById('mobileProfileBtn');

        if (profileMenuBtn && profileDropdown) {
            profileMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });
        }

        if (mobileProfileBtn && profileDropdown) {
            mobileProfileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (profileDropdown && !profileMenuBtn?.contains(e.target) && !mobileProfileBtn?.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });

        // Order Details Sidebar Functions
        function viewOrderDetails(orderNumber, orderId) {
            const sidebar = document.getElementById('orderDetailsSidebar');
            const mainWrapper = document.getElementById('mainWrapper');
            const content = document.getElementById('orderDetailsContent');

            // Show sidebar and adjust main content
            sidebar.classList.remove('translate-x-full');

            // Adjust main wrapper on desktop only
            if (window.innerWidth >= 640) {
                mainWrapper.style.marginRight = '500px';
            }

            // Show loading state
            content.innerHTML = `
                <div class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                </div>
            `;

            // Fetch order details
            fetch('get_order_details.php?order_id=' + orderId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayOrderDetails(data.order, data.items);
                    } else {
                        content.innerHTML = `
                            <div class="text-center py-12">
                                <span class="material-symbols-outlined text-5xl text-red-500 mb-3 block">error</span>
                                <p class="text-gray-500 dark:text-gray-400">Failed to load order details</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = `
                        <div class="text-center py-12">
                            <span class="material-symbols-outlined text-5xl text-red-500 mb-3 block">error</span>
                            <p class="text-gray-500 dark:text-gray-400">Error loading order details</p>
                        </div>
                    `;
                });
        }

        function closeOrderDetails() {
            const sidebar = document.getElementById('orderDetailsSidebar');
            const mainWrapper = document.getElementById('mainWrapper');

            sidebar.classList.add('translate-x-full');
            mainWrapper.style.marginRight = '0';
        }

        // Adjust on window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('orderDetailsSidebar');
            const mainWrapper = document.getElementById('mainWrapper');

            if (!sidebar.classList.contains('translate-x-full')) {
                if (window.innerWidth >= 640) {
                    mainWrapper.style.marginRight = '500px';
                } else {
                    mainWrapper.style.marginRight = '0';
                }
            }
        });
        // display order details in sidebar
        function displayOrderDetails(order, items) {
            const content = document.getElementById('orderDetailsContent');

            const statusBadges = {
                'pending': {
                    class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                    label: 'Pending'
                },
                'processing': {
                    class: 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
                    label: 'Processing'
                },
                'packed': {
                    class: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
                    label: 'Packed'
                },
                'shipped': {
                    class: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                    label: 'Shipped'
                },
                'delivered': {
                    class: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                    label: 'Delivered'
                },
                'cancelled': {
                    class: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                    label: 'Cancelled'
                }
            };

            const status = statusBadges[order.order_status] || statusBadges['pending'];
            const paymentStatus = order.payment_status === 'paid' ? 'Paid' : 'Pending';
            const paymentClass = order.payment_status === 'paid' ? 'bg-green-600' : 'bg-yellow-600';
            //  html for order items
            let itemsHtml = '';
            items.forEach(item => {
                itemsHtml += `
                    <div class="flex justify-between items-start py-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 dark:text-white">${item.product_name}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">SKU: ${item.product_sku}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Qty: ${item.quantity} × Rs. ${parseFloat(item.unit_price).toFixed(2)}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900 dark:text-white">Rs. ${parseFloat(item.subtotal).toFixed(2)}</p>
                        </div>
                    </div>
                `;
            });

            content.innerHTML = `
                <div class="space-y-6">
                    <!-- Order Header -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">${order.order_number}</h3>
                            <span class="px-3 py-1 ${status.class} text-xs font-semibold rounded-full">${status.label}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Order Date</p>
                                <p class="font-semibold text-gray-900 dark:text-white">${new Date(order.created_at).toLocaleDateString()}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Payment Status</p>
                                <span class="inline-block px-2 py-1 ${paymentClass} text-white text-xs font-bold rounded">${paymentStatus}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customer Info -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Customer Information</h4>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-2 text-sm">
                            <p class="text-gray-900 dark:text-white font-semibold">${order.customer_name}</p>
                            <p class="text-gray-600 dark:text-gray-400">${order.customer_email}</p>
                            <p class="text-gray-600 dark:text-gray-400">${order.customer_phone}</p>
                            ${order.business_name ? `<p class="text-gray-600 dark:text-gray-400">${order.business_name}</p>` : ''}
                        </div>
                    </div>
                    
                    <!-- Shipping Address -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Shipping Address</h4>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-sm text-gray-600 dark:text-gray-400">
                            <p>${order.shipping_address}</p>
                            ${order.shipping_city ? `<p>${order.shipping_city}, ${order.shipping_province || ''} ${order.shipping_postal_code || ''}</p>` : ''}
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Order Items</h4>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            ${itemsHtml}
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Order Summary</h4>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                <span class="text-gray-900 dark:text-white">Rs. ${parseFloat(order.subtotal).toFixed(2)}</span>
                            </div>
                            ${order.tax_amount > 0 ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Tax</span>
                                <span class="text-gray-900 dark:text-white">Rs. ${parseFloat(order.tax_amount).toFixed(2)}</span>
                            </div>` : ''}
                            ${order.shipping_fee > 0 ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                                <span class="text-gray-900 dark:text-white">Rs. ${parseFloat(order.shipping_fee).toFixed(2)}</span>
                            </div>` : ''}
                            ${order.discount_amount > 0 ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Discount</span>
                                <span class="text-red-600 dark:text-red-400">-Rs. ${parseFloat(order.discount_amount).toFixed(2)}</span>
                            </div>` : ''}
                            <div class="flex justify-between pt-2 border-t border-gray-300 dark:border-gray-600">
                                <span class="font-bold text-gray-900 dark:text-white">Total</span>
                                <span class="font-bold text-gray-900 dark:text-white text-lg">Rs. ${parseFloat(order.total_amount).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                    
                    ${order.customer_notes ? `
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Notes</h4>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-sm text-gray-600 dark:text-gray-400">
                            ${order.customer_notes}
                        </div>
                    </div>` : ''}
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        ${order.payment_status === 'paid' && order.order_status !== 'cancelled' ? `
                        <button onclick="window.open('download_invoice.php?order_id=${order.id}', '_blank')" class="flex-1 px-4 py-2 bg-primary text-black font-semibold rounded-lg hover:brightness-110 transition-all">
                            Download Invoice
                        </button>` : ''}
                        ${['processing', 'packed', 'shipped'].includes(order.order_status) ? `
                        <button onclick="location.href='track_shipment.php?order_id=${order.id}'" class="flex-1 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                            Track Shipment
                        </button>` : ''}
                    </div>
                </div>
            `;
        }
    </script>

</body>
<script src="../Cart/js/script.js"></script>

</html>