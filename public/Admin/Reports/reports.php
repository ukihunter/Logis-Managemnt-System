<?php
require_once '../../../config/admin_session.php';
?>

<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Head Office Management Dashboard</title>
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
        /* Custom scrollbar for table containers */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #e2e8f0;
            border-radius: 20px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #334155;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-text-main-light dark:text-text-main-dark transition-colors duration-200 min-h-screen flex">
    <div class="flex h-screen w-full overflow-hidden">
        <?php include '../components/sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-y-auto">
            <!-- Top Navigation -->
            <header class="sticky top-0 z-50 bg-card-light dark:bg-card-dark border-b border-border-light dark:border-border-dark px-6 py-3 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="size-8 text-primary flex items-center justify-center">
                            <svg class="w-full h-full" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_6_319)">
                                    <path d="M8.57829 8.57829C5.52816 11.6284 3.451 15.5145 2.60947 19.7452C1.76794 23.9758 2.19984 28.361 3.85056 32.3462C5.50128 36.3314 8.29667 39.7376 11.8832 42.134C15.4698 44.5305 19.6865 45.8096 24 45.8096C28.3135 45.8096 32.5302 44.5305 36.1168 42.134C39.7033 39.7375 42.4987 36.3314 44.1494 32.3462C45.8002 28.361 46.2321 23.9758 45.3905 19.7452C44.549 15.5145 42.4718 11.6284 39.4217 8.57829L24 24L8.57829 8.57829Z" fill="currentColor"></path>
                                </g>
                                <defs>
                                    <clippath id="clip0_6_319">
                                        <rect fill="white" height="48" width="48"></rect>
                                    </clippath>
                                </defs>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold tracking-tight">Distribution Command Center</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <!-- Date Range Picker -->
                        <button class="hidden md:flex items-center gap-2 px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-sm font-medium hover:bg-border-light dark:hover:bg-border-dark transition-colors">
                            <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                            <span>Last 30 Days</span>
                            <span class="material-symbols-outlined text-[16px]">expand_more</span>
                        </button>
                        <div class="h-6 w-px bg-border-light dark:bg-border-dark mx-2"></div>
                        <div class="flex gap-2">
                            <button class="flex items-center justify-center rounded-lg size-10 bg-background-light dark:bg-background-dark hover:bg-border-light dark:hover:bg-border-dark transition-colors text-text-sec-light dark:text-text-sec-dark">
                                <span class="material-symbols-outlined">search</span>
                            </button>
                            <button class="flex items-center justify-center rounded-lg size-10 bg-background-light dark:bg-background-dark hover:bg-border-light dark:hover:bg-border-dark transition-colors text-text-sec-light dark:text-text-sec-dark relative">
                                <span class="material-symbols-outlined">notifications</span>
                                <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border-2 border-white dark:border-card-dark"></span>
                            </button>
                            <button class="flex items-center justify-center rounded-lg size-10 bg-background-light dark:bg-background-dark hover:bg-border-light dark:hover:bg-border-dark transition-colors text-text-sec-light dark:text-text-sec-dark">
                                <span class="material-symbols-outlined">settings</span>
                            </button>
                        </div>

                    </div>
                </div>
            </header>
            <!-- Main Content -->
            <main class="flex-1 p-6 md:px-10 lg:px-12 max-w-[1600px] mx-auto w-full">
                <!-- Header Section with Actions -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight mb-2">Monthly Reports</h1>
                        <p class="text-text-sec-light dark:text-text-sec-dark">Sales Performance, Stock Turnover & Delivery Efficiency</p>
                    </div>
                    <div class="flex gap-3">
                        <!-- Month Selector -->
                        <div class="relative">
                            <select id="monthFilter" class="appearance-none px-4 py-2.5 pr-10 bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark rounded-lg font-medium text-sm focus:outline-none focus:ring-2 focus:ring-primary cursor-pointer">
                                <!-- Options will be populated by JS -->
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-text-sec-light dark:text-text-sec-dark">calendar_month</span>
                        </div>

                        <!-- Download Dropdown -->
                        <div class="relative">
                            <button id="downloadBtn" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-black font-bold text-sm rounded-lg hover:bg-primary-hover shadow-lg shadow-primary/20 transition-all">
                                <span class="material-symbols-outlined text-[20px]">download</span>
                                Download Report
                                <span class="material-symbols-outlined text-[16px]">expand_more</span>
                            </button>
                            <div id="downloadMenu" class="hidden absolute right-0 mt-2 w-56 bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark rounded-lg shadow-xl z-50">
                                <button onclick="downloadReport('full')" class="w-full text-left px-4 py-3 hover:bg-background-light dark:hover:bg-background-dark flex items-center gap-3 rounded-t-lg transition-colors">
                                    <span class="material-symbols-outlined text-primary">description</span>
                                    <div>
                                        <div class="font-bold text-sm">Full Report</div>
                                        <div class="text-xs text-text-sec-light dark:text-text-sec-dark">All data combined</div>
                                    </div>
                                </button>
                                <button onclick="downloadReport('sales')" class="w-full text-left px-4 py-3 hover:bg-background-light dark:hover:bg-background-dark flex items-center gap-3 transition-colors">
                                    <span class="material-symbols-outlined text-blue-500">payments</span>
                                    <div>
                                        <div class="font-bold text-sm">Sales Performance</div>
                                        <div class="text-xs text-text-sec-light dark:text-text-sec-dark">Revenue & orders</div>
                                    </div>
                                </button>
                                <button onclick="downloadReport('stock')" class="w-full text-left px-4 py-3 hover:bg-background-light dark:hover:bg-background-dark flex items-center gap-3 transition-colors">
                                    <span class="material-symbols-outlined text-purple-500">inventory_2</span>
                                    <div>
                                        <div class="font-bold text-sm">Stock Turnover</div>
                                        <div class="text-xs text-text-sec-light dark:text-text-sec-dark">Inventory analysis</div>
                                    </div>
                                </button>
                                <button onclick="downloadReport('delivery')" class="w-full text-left px-4 py-3 hover:bg-background-light dark:hover:bg-background-dark flex items-center gap-3 rounded-b-lg transition-colors">
                                    <span class="material-symbols-outlined text-orange-500">local_shipping</span>
                                    <div>
                                        <div class="font-bold text-sm">Delivery Efficiency</div>
                                        <div class="text-xs text-text-sec-light dark:text-text-sec-dark">Driver & location data</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                    <!-- KPI 1 - Revenue -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark group hover:border-primary/50 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-primary/10 rounded-lg text-primary">
                                <span class="material-symbols-outlined">payments</span>
                            </div>
                            <span id="revenueChange" class="flex items-center text-primary text-sm font-bold bg-primary/5 px-2 py-1 rounded">
                                <span class="material-symbols-outlined text-[16px] mr-1">trending_up</span> +0%
                            </span>
                        </div>
                        <p class="text-text-sec-light dark:text-text-sec-dark text-sm font-medium">Total Revenue</p>
                        <h3 id="totalRevenue" class="text-2xl font-bold mt-1">Rs0</h3>
                        <p id="revenuePrevious" class="text-xs text-text-sec-light dark:text-text-sec-dark mt-2">Vs. previous period (Rs0)</p>
                    </div>
                    <!-- KPI 2 - Units -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark group hover:border-primary/50 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-600 dark:text-blue-400">
                                <span class="material-symbols-outlined">inventory_2</span>
                            </div>
                            <span id="unitsChange" class="flex items-center text-primary text-sm font-bold bg-primary/5 px-2 py-1 rounded">
                                <span class="material-symbols-outlined text-[16px] mr-1">trending_up</span> +0%
                            </span>
                        </div>
                        <p class="text-text-sec-light dark:text-text-sec-dark text-sm font-medium">Total Units Sold</p>
                        <h3 id="totalUnits" class="text-2xl font-bold mt-1">0</h3>
                        <p id="unitsPrevious" class="text-xs text-text-sec-light dark:text-text-sec-dark mt-2">Vs. previous period (0)</p>
                    </div>
                    <!-- KPI 3 - Deliveries -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark group hover:border-primary/50 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-purple-600 dark:text-purple-400">
                                <span class="material-symbols-outlined">local_shipping</span>
                            </div>
                        </div>
                        <p class="text-text-sec-light dark:text-text-sec-dark text-sm font-medium">Active Deliveries</p>
                        <h3 id="activeDeliveries" class="text-2xl font-bold mt-1">0</h3>
                        <p id="ontimeRate" class="text-xs text-text-sec-light dark:text-text-sec-dark mt-2">0% On-time rate</p>
                    </div>
                    <!-- KPI 4 - Stock Health -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark group hover:border-primary/50 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg text-orange-600 dark:text-orange-400">
                                <span class="material-symbols-outlined">health_and_safety</span>
                            </div>
                        </div>
                        <p class="text-text-sec-light dark:text-text-sec-dark text-sm font-medium">Stock Health Score</p>
                        <h3 id="stockHealth" class="text-2xl font-bold mt-1">0%</h3>
                        <p id="lowStockAlerts" class="text-xs text-text-sec-light dark:text-text-sec-dark mt-2">Low stock alerts: 0</p>
                    </div>
                </div>
                <!-- Bento Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Left Column (Main Charts) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Sales Trend Chart -->
                        <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h3 class="text-lg font-bold">Sales Performance Trend</h3>
                                    <p class="text-sm text-text-sec-light dark:text-text-sec-dark">Weekly revenue for selected month</p>
                                </div>
                            </div>
                            <div id="salesChart" class="h-[280px] w-full flex items-center justify-center">
                                <div class="text-text-sec-light dark:text-text-sec-dark">Loading chart...</div>
                            </div>
                        </div>

                        <!-- Top Products Table -->
                        <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark">
                            <h3 class="text-lg font-bold mb-4">Top Selling Products</h3>
                            <div class="overflow-x-auto overflow-y-auto custom-scrollbar" style="max-height: 400px;">
                                <table class="w-full text-left text-sm">
                                    <thead class="sticky top-0 bg-card-light dark:bg-card-dark z-10">
                                        <tr class="text-text-sec-light dark:text-text-sec-dark border-b border-border-light dark:border-border-dark">
                                            <th class="pb-3 font-medium">Product</th>
                                            <th class="pb-3 font-medium">SKU</th>
                                            <th class="pb-3 font-medium text-right">Units Sold</th>
                                            <th class="pb-3 font-medium text-right">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="topProductsTable" class="divide-y divide-border-light dark:divide-border-dark">
                                        <tr>
                                            <td colspan="4" class="py-8 text-center text-text-sec-light dark:text-text-sec-dark">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Right Column (Stats & Lists) -->
                    <div class="lg:col-span-4 space-y-6 flex flex-col">
                        <!-- Efficiency Gauges -->
                        <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark">
                            <h3 class="text-lg font-bold mb-6">Delivery Efficiency</h3>
                            <div class="flex items-center justify-around">
                                <!-- Gauge 1 - On-time Rate -->
                                <div class="flex flex-col items-center gap-2">
                                    <div class="relative size-24">
                                        <svg class="size-full" viewbox="0 0 36 36">
                                            <path class="text-gray-200 dark:text-gray-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3"></path>
                                            <path id="ontimeGauge" class="text-primary" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="0, 100" stroke-linecap="round" stroke-width="3"></path>
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                                            <span id="ontimeGaugeText" class="text-xl font-bold">0%</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium text-text-sec-light dark:text-text-sec-dark text-center">On-Time<br />Rate</span>
                                </div>
                                <div class="w-px h-24 bg-border-light dark:bg-border-dark"></div>
                                <!-- Gauge 2 - Avg Delivery Time -->
                                <div class="flex flex-col items-center gap-2">
                                    <div class="relative size-24">
                                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                                            <span id="avgDeliveryTime" class="text-xl font-bold">0h</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium text-text-sec-light dark:text-text-sec-dark text-center">Avg.<br />Turnaround</span>
                                </div>
                            </div>
                        </div>
                        <!-- Stock Turnover Table -->
                        <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden flex flex-col" style="min-height: 400px; max-height: 500px;">
                            <div class="p-6 pb-2">
                                <h3 class="text-lg font-bold">Stock Turnover Analysis</h3>
                                <p class="text-sm text-text-sec-light dark:text-text-sec-dark mt-1">Products sorted by turnover ratio</p>
                            </div>
                            <div class="overflow-y-auto custom-scrollbar flex-1 p-6 pt-2">
                                <table class="w-full text-left text-sm">
                                    <thead>
                                        <tr class="text-text-sec-light dark:text-text-sec-dark border-b border-border-light dark:border-border-dark">
                                            <th class="pb-3 font-medium">Product</th>
                                            <th class="pb-3 font-medium text-right">Ratio</th>
                                            <th class="pb-3 font-medium text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="stockTurnoverTable" class="divide-y divide-border-light dark:divide-border-dark">
                                        <tr>
                                            <td colspan="3" class="py-8 text-center text-text-sec-light dark:text-text-sec-dark">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Driver Performance -->
                        <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold">Top Drivers</h3>
                            </div>
                            <div id="driverPerformance" class="space-y-3">
                                <div class="text-center text-text-sec-light dark:text-text-sec-dark py-4">Loading...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Mobile Bottom Nav Spacer -->
            <div class="h-16 lg:hidden"></div>
        </div>

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
            <a href="../User_Management/user_managment.php" class="flex items-center gap-3 px-4 py-3 hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors border-b border-[#e7f3eb] dark:border-[#2a4034]">
                <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark">account_child_invert</span>
                <span class="text-sm font-medium">Users</span>
            </a>
            <a href="reports.php" class="flex items-center gap-3 px-4 py-3 bg-primary/10 text-primary transition-colors">
                <span class="material-symbols-outlined">description</span>
                <span class="text-sm font-medium">Reports</span>
            </a>
        </div>
    </div>

    <?php include '../components/scripts.php'; ?>
    <script src="js/script.js"></script>
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