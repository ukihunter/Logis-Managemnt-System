<?php
require_once '../../../config/admin_session.php';
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
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        /* Sidebar transition */
        .sidebar-collapsed {
            width: 5rem;
        }

        .sidebar-expanded {
            width: 16rem;
        }

        aside {
            transition: width 0.3s ease-in-out;
        }

        .sidebar-text {
            opacity: 1;
            transition: opacity 0.2s ease-in-out;
        }

        .sidebar-collapsed .sidebar-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        /* Right panel slide-in animation */
        .detail-panel {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }

        .detail-panel.active {
            transform: translateX(0);
        }
    </style>
</head>

<body class="font-display bg-background-light dark:bg-background-dark text-[#0d1b12] dark:text-gray-100 antialiased min-h-screen flex overflow-hidden">

    <div class="flex h-screen w-full overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-expanded flex-shrink-0 flex flex-col bg-surface-light dark:bg-surface-dark border-r border-[#e7f3eb] dark:border-[#2a4034] hidden lg:flex">
            <div class="p-6 border-b border-[#e7f3eb] dark:border-[#2a4034]">
                <div class="flex items-center gap-3">
                    <div class="bg-primary/20 p-2 rounded-lg text-primary">
                        <span class="material-symbols-outlined text-3xl text-primary">shopping_bag_speed</span>
                    </div>
                    <div class="flex flex-col sidebar-text">
                        <h1 class="text-base font-bold leading-none whitespace-nowrap"><?php echo htmlspecialchars($province) . " RDC"; ?></h1>
                        <p class="text-text-secondary-light dark:text-text-secondary-dark text-xs font-medium mt-1">Staff Portal</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 overflow-y-auto py-4 px-3 flex flex-col gap-5">
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors font-medium group" href="../Dasboard/dasboard.php">
                    <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark group-hover:text-primary transition-colors">dashboard</span>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary font-medium group" href="../Orders/orders.php">
                    <span class="material-symbols-outlined group-hover:scale-110 transition-transform">shopping_cart</span>
                    <span class="sidebar-text">Orders</span>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors font-medium group" href="../Inventory/inventory.php">
                    <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark group-hover:text-primary transition-colors">inventory_2</span>
                    <span class="sidebar-text">Inventory</span>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors font-medium group" href="#">
                    <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark group-hover:text-primary transition-colors">local_shipping</span>
                    <span class="sidebar-text">Logistics</span>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors font-medium group" href="#">
                    <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark group-hover:text-primary transition-colors">account_child_invert</span>
                    <span class="sidebar-text">User Management</span>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors font-medium group" href="#">
                    <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark group-hover:text-primary transition-colors">description</span>
                    <span class="sidebar-text">Reports</span>
                </a>
            </nav>
            <div class="p-4 border-t border-[#e7f3eb] dark:border-[#2a4034] relative">
                <div class="flex items-center justify-between gap-3 p-2 rounded-lg hover:bg-background-light dark:hover:bg-[#2a4034]">
                    <!-- User Info -->
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="size-8 rounded-full bg-cover bg-center flex-shrink-0"
                            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCHud_yjkjGz0pDgk3H4D4QYFsrO1s3InLEFvPAxiEQkwg2F8faGX2QWPTL1Xnz6nbukCyc6NjyJvbc997p3MJOzuvktnvnnByG5JwKvmnQyZygnoQBbmSGSu2aVDrbxPT9exPDEJ47vOpaj5hv_IcyKxCEaXMrHa3AdEfaM-Bm0Z3ablCWaVQf5UCa1raIfHzwXSaKoDYjNyzK4F6u1QMBAW5fvIqczinJn1QMGMwubGxZnlCQuyqOjuS2aOVX86NCpwnuBdGUrcU');">
                        </div>
                        <div class="flex flex-col sidebar-text min-w-0">
                            <p class="text-sm font-bold truncate">
                                <?php echo htmlspecialchars($full_name); ?>
                            </p>
                            <p class="text-xs text-text-secondary-light dark:text-text-secondary-dark truncate">
                                <?php echo htmlspecialchars($user_type); ?>
                            </p>
                        </div>
                    </div>
                    <!-- Vertical Dots Menu -->
                    <div class="relative sidebar-text flex-shrink-0">
                        <button onclick="toggleUserMenu(event)"
                            class="p-2 rounded hover:bg-gray-200 dark:hover:bg-[#2a4034] focus:outline-none">
                            &#8942;
                        </button>
                        <!-- Dropdown -->
                        <div id="userMenu"
                            class="hidden absolute right-0 bottom-full mb-2 w-32 bg-white dark:bg-[#1f2f26]
                       border border-gray-200 dark:border-[#2a4034]
                       rounded-lg shadow-lg z-[100]">
                            <a href="../../logout/logout.php"
                                class="block px-4 py-2 text-sm text-red-600
                           hover:bg-gray-100 dark:hover:bg-[#2a4034]">
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </aside>



        <!-- Main Content Area -->
        <main class="flex flex-1 overflow-hidden w-full relative">
            <!-- Left Panel: Order List (Master View) -->
            <section id="orderListSection" class="flex flex-col w-full h-full overflow-y-auto bg-background-light dark:bg-background-dark transition-all duration-300">
                <!-- Header & KPIs -->
                <div class="p-6 pb-0 space-y-6">
                    <!-- Heading -->
                    <div class="flex flex-col gap-2">
                        <h1 class="text-[#0d1b12] dark:text-white text-3xl font-black leading-tight tracking-[-0.033em]">Order Management</h1>
                        <p class="text-[#4c9a66] dark:text-gray-400 text-sm font-medium">Central Region • 45 Active Orders</p>
                    </div>
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1 rounded-xl p-4 border border-[#cfe7d7] dark:border-gray-700 bg-white dark:bg-[#152e1e] shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary" style="font-size: 20px;">new_releases</span>
                                <p class="text-[#0d1b12] dark:text-gray-300 text-sm font-medium">New Orders</p>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold">12</p>
                        </div>
                        <div class="flex flex-col gap-1 rounded-xl p-4 border border-[#cfe7d7] dark:border-gray-700 bg-white dark:bg-[#152e1e] shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-orange-500" style="font-size: 20px;">pending_actions</span>
                                <p class="text-[#0d1b12] dark:text-gray-300 text-sm font-medium">Pending Dispatch</p>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold">4</p>
                        </div>
                        <div class="flex flex-col gap-1 rounded-xl p-4 border border-[#cfe7d7] dark:border-gray-700 bg-white dark:bg-[#152e1e] shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-500" style="font-size: 20px;">analytics</span>
                                <p class="text-[#0d1b12] dark:text-gray-300 text-sm font-medium">Fill Rate</p>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold">98%</p>
                        </div>
                    </div>
                    <!-- Search & Filters -->
                    <div class="flex flex-col gap-4">
                        <!-- Search Bar -->
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-[#4c9a66]">search</span>
                            </div>
                            <input class="block w-full pl-10 pr-3 py-3 border border-transparent rounded-lg leading-5 bg-[#e7f3eb] dark:bg-[#1e3b29] text-[#0d1b12] dark:text-white placeholder-[#4c9a66] focus:outline-none focus:bg-white dark:focus:bg-[#152e1e] focus:ring-2 focus:ring-primary focus:border-transparent sm:text-sm transition-all shadow-sm" placeholder="Search by Order ID, Customer Name, or Zone..." type="text" />
                        </div>
                        <!-- Chips -->
                        <div class="flex gap-2 flex-wrap pb-2">
                            <button class="flex items-center justify-center gap-2 rounded-full bg-[#102216] dark:bg-white text-white dark:text-black px-4 py-1.5 shadow-sm transition-transform active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                                <span class="text-xs font-bold">All Orders</span>
                            </button>
                            <button class="flex items-center justify-center gap-2 rounded-full bg-white dark:bg-[#152e1e] border border-[#e7f3eb] dark:border-gray-700 hover:border-primary px-4 py-1.5 transition-colors shadow-sm text-[#0d1b12] dark:text-gray-300">
                                <span class="text-xs font-semibold">Status: New</span>
                                <span class="material-symbols-outlined text-[16px]">expand_more</span>
                            </button>
                            <button class="flex items-center justify-center gap-2 rounded-full bg-white dark:bg-[#152e1e] border border-[#e7f3eb] dark:border-gray-700 hover:border-primary px-4 py-1.5 transition-colors shadow-sm text-[#0d1b12] dark:text-gray-300">
                                <span class="text-xs font-semibold">Zone: North-East</span>
                                <span class="material-symbols-outlined text-[16px]">expand_more</span>
                            </button>
                            <button class="flex items-center justify-center gap-2 rounded-full bg-white dark:bg-[#152e1e] border border-[#e7f3eb] dark:border-gray-700 hover:border-primary px-4 py-1.5 transition-colors shadow-sm text-[#0d1b12] dark:text-gray-300">
                                <span class="text-xs font-semibold">Date: Today</span>
                                <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                            </button>
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
                        <tbody class="divide-y divide-[#e7f3eb] dark:divide-gray-800 text-sm">
                            <!-- Row 1 (Active) -->
                            <tr onclick="showOrderDetail()" class="group hover:bg-white dark:hover:bg-[#152e1e] cursor-pointer transition-colors bg-white dark:bg-[#152e1e] border-l-4 border-l-primary shadow-sm rounded-lg mb-2 block md:table-row">
                                <td class="py-4 px-2 font-bold text-[#0d1b12] dark:text-white">#ORD-2023-849</td>
                                <td class="py-4 px-2">
                                    <div class="font-bold text-[#0d1b12] dark:text-white">Island Grocers Ltd.</div>
                                    <div class="text-xs text-gray-500">Just now</div>
                                </td>
                                <td class="py-4 px-2 text-[#0d1b12] dark:text-gray-300">North-East</td>
                                <td class="py-4 px-2 text-right text-[#0d1b12] dark:text-gray-300">45</td>
                                <td class="py-4 px-2 text-right font-bold text-[#0d1b12] dark:text-white">$4,250.00</td>
                                <td class="py-4 px-2 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border border-green-200 dark:border-green-800">
                                        New
                                    </span>
                                </td>
                                <td class="py-4 px-2 text-right">
                                    <span class="material-symbols-outlined text-gray-400 group-hover:text-primary">chevron_right</span>
                                </td>
                            </tr>
                            <!-- Row 2 -->
                            <tr onclick="showOrderDetail()" class="group hover:bg-white dark:hover:bg-[#152e1e] cursor-pointer transition-colors">
                                <td class="py-4 px-2 font-bold text-[#0d1b12] dark:text-white">#ORD-2023-848</td>
                                <td class="py-4 px-2">
                                    <div class="font-bold text-[#0d1b12] dark:text-white">Sunny Minimart</div>
                                    <div class="text-xs text-gray-500">1 hour ago</div>
                                </td>
                                <td class="py-4 px-2 text-[#0d1b12] dark:text-gray-300">Central</td>
                                <td class="py-4 px-2 text-right text-[#0d1b12] dark:text-gray-300">12</td>
                                <td class="py-4 px-2 text-right font-bold text-[#0d1b12] dark:text-white">$890.50</td>
                                <td class="py-4 px-2 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border border-green-200 dark:border-green-800">
                                        New
                                    </span>
                                </td>
                                <td class="py-4 px-2 text-right">
                                    <span class="material-symbols-outlined text-gray-400 group-hover:text-primary">chevron_right</span>
                                </td>
                            </tr>
                            <!-- Row 3 -->
                            <tr onclick="showOrderDetail()" class="group hover:bg-white dark:hover:bg-[#152e1e] cursor-pointer transition-colors">
                                <td class="py-4 px-2 font-bold text-[#0d1b12] dark:text-white">#ORD-2023-847</td>
                                <td class="py-4 px-2">
                                    <div class="font-bold text-[#0d1b12] dark:text-white">Metro Wholesalers</div>
                                    <div class="text-xs text-gray-500">2 hours ago</div>
                                </td>
                                <td class="py-4 px-2 text-[#0d1b12] dark:text-gray-300">South</td>
                                <td class="py-4 px-2 text-right text-[#0d1b12] dark:text-gray-300">120</td>
                                <td class="py-4 px-2 text-right font-bold text-[#0d1b12] dark:text-white">$12,400.00</td>
                                <td class="py-4 px-2 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-200 dark:border-blue-800">
                                        Processing
                                    </span>
                                </td>
                                <td class="py-4 px-2 text-right">
                                    <span class="material-symbols-outlined text-gray-400 group-hover:text-primary">chevron_right</span>
                                </td>
                            </tr>
                            <!-- Row 4 -->
                            <tr onclick="showOrderDetail()" class="group hover:bg-white dark:hover:bg-[#152e1e] cursor-pointer transition-colors">
                                <td class="py-4 px-2 font-bold text-[#0d1b12] dark:text-white">#ORD-2023-846</td>
                                <td class="py-4 px-2">
                                    <div class="font-bold text-[#0d1b12] dark:text-white">Corner Shop LLC</div>
                                    <div class="text-xs text-gray-500">3 hours ago</div>
                                </td>
                                <td class="py-4 px-2 text-[#0d1b12] dark:text-gray-300">North-East</td>
                                <td class="py-4 px-2 text-right text-[#0d1b12] dark:text-gray-300">8</td>
                                <td class="py-4 px-2 text-right font-bold text-[#0d1b12] dark:text-white">$450.00</td>
                                <td class="py-4 px-2 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200 border border-amber-200 dark:border-amber-800">
                                        Pending Payment
                                    </span>
                                </td>
                                <td class="py-4 px-2 text-right">
                                    <span class="material-symbols-outlined text-gray-400 group-hover:text-primary">chevron_right</span>
                                </td>
                            </tr>
                            <!-- Row 5 -->
                            <tr onclick="showOrderDetail()" class="group hover:bg-white dark:hover:bg-[#152e1e] cursor-pointer transition-colors opacity-60">
                                <td class="py-4 px-2 font-bold text-[#0d1b12] dark:text-white">#ORD-2023-845</td>
                                <td class="py-4 px-2">
                                    <div class="font-bold text-[#0d1b12] dark:text-white">Beachside Cafe</div>
                                    <div class="text-xs text-gray-500">Yesterday</div>
                                </td>
                                <td class="py-4 px-2 text-[#0d1b12] dark:text-gray-300">East Coast</td>
                                <td class="py-4 px-2 text-right text-[#0d1b12] dark:text-gray-300">22</td>
                                <td class="py-4 px-2 text-right font-bold text-[#0d1b12] dark:text-white">$1,100.00</td>
                                <td class="py-4 px-2 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                        Dispatched
                                    </span>
                                </td>
                                <td class="py-4 px-2 text-right">
                                    <span class="material-symbols-outlined text-gray-400 group-hover:text-primary">chevron_right</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            <!-- Right Panel: Order Details (Detail View) -->
            <aside id="detailPanel" class="detail-panel hidden flex-col bg-white dark:bg-[#152e1e] h-full shadow-xl z-20 overflow-hidden fixed right-0 top-0 w-5/12 xl:w-4/12">
                <!-- Detail Header -->
                <div class="px-6 py-5 border-b border-[#e7f3eb] dark:border-gray-800 flex justify-between items-start bg-white dark:bg-[#152e1e]">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h2 class="text-xl font-black text-[#0d1b12] dark:text-white">#ORD-2023-849</h2>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-primary/10 text-primary border border-primary/20">
                                New Order
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Created: Oct 24, 2023 • 09:42 AM</p>
                    </div>
                    <button onclick="closeOrderDetail()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
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
                                    <h3 class="text-sm font-bold text-[#0d1b12] dark:text-white">Island Grocers Ltd.</h3>
                                    <p class="text-xs text-primary font-medium">Regular Customer • Tier 1</p>
                                </div>
                            </div>
                            <button class="text-xs text-[#4c9a66] hover:underline font-medium">View Profile</button>
                        </div>
                        <div class="grid grid-cols-2 gap-y-3 gap-x-2 text-xs">
                            <div>
                                <p class="text-gray-400 mb-0.5">Contact Person</p>
                                <p class="font-medium text-[#0d1b12] dark:text-gray-200">John Doe</p>
                            </div>
                            <div>
                                <p class="text-gray-400 mb-0.5">Phone</p>
                                <p class="font-medium text-[#0d1b12] dark:text-gray-200">+1 555-0199</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-400 mb-0.5">Shipping Address</p>
                                <p class="font-medium text-[#0d1b12] dark:text-gray-200">142 Market Street, North-East Zone, Logistics Hub A</p>
                            </div>
                        </div>
                    </div>
                    <!-- Line Items -->
                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <h3 class="text-sm font-bold text-[#0d1b12] dark:text-white">Order Items (3)</h3>
                            <span class="text-xs text-gray-500">Total Wt: 1,100kg</span>
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
                                <tbody class="divide-y divide-[#e7f3eb] dark:divide-gray-800 bg-white dark:bg-[#152e1e]">
                                    <tr>
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-[#0d1b12] dark:text-white">Premium Jasmine Rice (20kg)</div>
                                            <div class="text-[10px] text-primary flex items-center gap-1 mt-0.5">
                                                <span class="material-symbols-outlined text-[10px]">check_circle</span> In Stock (400)
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 text-center font-medium">50</td>
                                        <td class="py-3 px-3 text-right font-medium">$2,500.00</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-[#0d1b12] dark:text-white">Sunflower Cooking Oil (5L)</div>
                                            <div class="text-[10px] text-amber-500 flex items-center gap-1 mt-0.5">
                                                <span class="material-symbols-outlined text-[10px]">warning</span> Low Stock (25)
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 text-center font-medium">20</td>
                                        <td class="py-3 px-3 text-right font-medium">$800.00</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-[#0d1b12] dark:text-white">Canned Tuna (Case of 24)</div>
                                            <div class="text-[10px] text-red-500 flex items-center gap-1 mt-0.5">
                                                <span class="material-symbols-outlined text-[10px]">block</span> Out of Stock
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 text-center font-medium">10</td>
                                        <td class="py-3 px-3 text-right font-medium text-gray-400 line-through">$950.00</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <td class="py-3 px-3 text-right font-bold text-gray-600 dark:text-gray-300" colspan="2">Grand Total</td>
                                        <td class="py-3 px-3 text-right font-black text-lg text-[#0d1b12] dark:text-white">$4,250.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- Logistics Assignment -->
                    <div>
                        <h3 class="text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Logistics Assignment</h3>
                        <div class="relative">
                            <select class="block w-full pl-3 pr-10 py-2.5 text-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-[#102216] focus:outline-none focus:ring-primary focus:border-primary rounded-lg appearance-none text-[#0d1b12] dark:text-white border shadow-sm">
                                <option>Select Delivery Partner...</option>
                                <option selected="">Internal Fleet (Van 04)</option>
                                <option>3PL - FastLogistics</option>
                                <option>Customer Pickup</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                <span class="material-symbols-outlined">expand_more</span>
                            </div>
                        </div>
                    </div>
                    <!-- Delivery Notes -->
                    <div>
                        <h3 class="text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Delivery Notes</h3>
                        <textarea class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-[#102216] text-sm text-[#0d1b12] dark:text-white p-3 focus:ring-primary focus:border-primary shadow-sm border" placeholder="Add special instructions for the driver..." rows="3">Gate code 1234. Please call 10 mins before arrival.</textarea>
                    </div>
                </div>
                <!-- Sticky Bottom Actions -->
                <div class="p-6 border-t border-[#e7f3eb] dark:border-gray-800 bg-white dark:bg-[#152e1e]">
                    <div class="flex gap-3">
                        <button class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 font-bold text-sm transition-colors">
                            <span class="material-symbols-outlined text-[18px]">close</span> Reject
                        </button>
                        <button class="flex-[2] flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-[#0ebf49] text-white rounded-lg font-bold text-sm shadow-md shadow-primary/20 transition-all active:scale-[0.98]">
                            <span class="material-symbols-outlined text-[18px]">check</span> Accept &amp; Process
                        </button>
                    </div>
                </div>
            </aside>
        </main>
    </div>

    <script src="../Orders/js/script.js"></script>
</body>

</html>