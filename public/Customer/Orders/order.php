<?php
require_once '../../../config/session_Detils.php';
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

<body class="bg-background-light dark:bg-background-dark text-gray-800 dark:text-gray-200 transition-colors duration-200 min-h-screen">
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
                        <a class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition" href="#">Invoices</a>
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
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 transition hover:shadow-md">
                <p class="text-sm font-medium text-primary mb-1">Active Orders</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">12</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                        2 Arriving
                    </span>
                </div>
            </div>
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 transition hover:shadow-md">
                <p class="text-sm font-medium text-primary mb-1">Pending Payment</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">Rs. 12,000</h3>
                </div>
            </div>
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 transition hover:shadow-md">
                <p class="text-sm font-medium text-primary mb-1">Total Spend</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">Rs. 1.2M</h3>
                </div>
            </div>
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 transition hover:shadow-md">
                <p class="text-sm font-medium text-primary mb-1">Loyalty Points</p>
                <div class="flex items-end justify-between">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">100</h3>
                    <a class="text-sm font-medium text-primary hover:text-green-600 dark:hover:text-green-400 transition" href="#">Redeem</a>
                </div>
            </div>
        </div>
        <div class="w-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">All Orders</h2>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[18px]">search</span>
                        </div>
                        <input class="w-full pl-9 pr-4 py-2 text-sm border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-surface-dark text-gray-900 dark:text-white focus:ring-primary focus:border-primary shadow-sm" placeholder="Search Order ID..." type="text" />
                    </div>
                </div>
            </div>
            <div class="bg-surface-light dark:bg-surface-dark rounded-t-xl border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex flex-wrap items-center gap-2">
                <button class="px-4 py-2 rounded-lg text-sm font-medium bg-primary text-white shadow-sm hover:bg-primary-hover transition-colors">
                    All Orders
                </button>
                <button class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    Pending
                </button>
                <button class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    In Transit
                </button>
                <button class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    Completed
                </button>
                <button class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    Cancelled
                </button>
            </div>
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
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">#ORD-2026-0045</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    Oct 24, 2026
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                                        In Transit
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    Rs. 15,400.00
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors tooltip-trigger" title="View Details">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </button>
                                        <button class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 p-1 rounded transition-colors" title="Track Shipment">
                                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                        </button>
                                        <button class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors" title="Download Invoice">
                                            <span class="material-symbols-outlined text-[20px]">download</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">#ORD-2026-0045</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    Oct 24, 2026
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                                        In Transit
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    Rs. 15,400.00
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors tooltip-trigger" title="View Details">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </button>
                                        <button class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 p-1 rounded transition-colors" title="Track Shipment">
                                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                        </button>
                                        <button class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors" title="Download Invoice">
                                            <span class="material-symbols-outlined text-[20px]">download</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">#ORD-2026-0044</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    Oct 23, 2026
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-yellow-500 rounded-full"></span>
                                        Pending
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    Rs. 8,250.00
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors" title="View Details">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </button>
                                        <button class="text-gray-300 cursor-not-allowed dark:text-gray-600 p-1 rounded" disabled="" title="Track Shipment">
                                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                        </button>
                                        <button class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors" title="Download Invoice">
                                            <span class="material-symbols-outlined text-[20px]">download</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">#ORD-2026-0043</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    Oct 20, 2026
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 border border-green-200 dark:border-green-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full"></span>
                                        Completed
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    Rs. 24,100.00
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors" title="View Details">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </button>
                                        <button class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 p-1 rounded transition-colors" title="Track Shipment">
                                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                        </button>
                                        <button class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors" title="Download Invoice">
                                            <span class="material-symbols-outlined text-[20px]">download</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">#ORD-2026-0042</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    Oct 18, 2026
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 border border-green-200 dark:border-green-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full"></span>
                                        Completed
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    Rs. 99,600.00
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors" title="View Details">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </button>
                                        <button onclick="location.href='../../Customer/Orders/track_shipment.php'" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 p-1 rounded transition-colors" title="Track Shipment">
                                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                        </button>
                                        <button class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors" title="Download Invoice">
                                            <span class="material-symbols-outlined text-[20px]">download</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">#ORD-2026-0041</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    Oct 15, 2026
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300 border border-red-200 dark:border-red-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-red-500 rounded-full"></span>
                                        Cancelled
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    Rs. 1,200.00
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-white p-1 rounded transition-colors" title="View Details">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </button>
                                        <button class="text-gray-300 cursor-not-allowed dark:text-gray-600 p-1 rounded" disabled="" title="Track Shipment">
                                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                        </button>
                                        <button class="text-gray-300 cursor-not-allowed dark:text-gray-600 p-1 rounded" disabled="" title="Download Invoice">
                                            <span class="material-symbols-outlined text-[20px]">download</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-white dark:bg-surface-dark px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-400">
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">6</span> of <span class="font-medium">42</span> results
                                </p>
                            </div>
                            <div>
                                <nav aria-label="Pagination" class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <a class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-surface-dark text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800" href="#">
                                        <span class="sr-only">Previous</span>
                                        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                                    </a>
                                    <a aria-current="page" class="z-10 bg-primary border-primary text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="#">
                                        1
                                    </a>
                                    <a class="bg-white dark:bg-surface-dark border-gray-300 dark:border-gray-600 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="#">
                                        2
                                    </a>
                                    <a class="bg-white dark:bg-surface-dark border-gray-300 dark:border-gray-600 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 relative inline-flex items-center px-4 py-2 border text-sm font-medium" href="#">
                                        3
                                    </a>
                                    <a class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-surface-dark text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800" href="#">
                                        <span class="sr-only">Next</span>
                                        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm text-gray-400 dark:text-gray-600">
        © 2026 IslandDistro Inc. All rights reserved.
    </footer>


</body>
<script src="../Cart/js/script.js"></script>

</html>