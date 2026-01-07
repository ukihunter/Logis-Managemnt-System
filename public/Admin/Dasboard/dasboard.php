<?php
require_once '../../../config/admin_session.php';
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>RDC Staff Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
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
</head>

<body class="bg-background-light dark:bg-background-dark text-text-main-light dark:text-text-main-dark font-display antialiased overflow-hidden">
    <div class="flex h-screen w-full overflow-hidden">
        <?php include '../components/sidebar.php'; ?>
        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full overflow-hidden bg-background-light dark:bg-background-dark relative">
            <!-- Top Navbar -->
            <header class="h-16 flex items-center justify-between px-6 bg-surface-light dark:bg-surface-dark border-b border-[#e7f3eb] dark:border-[#2a4034] flex-shrink-0 z-10">
                <div class="flex items-center gap-4 lg:hidden">
                    <button class="text-text-main-light dark:text-text-main-dark">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <span class="font-bold text-lg">RDC Dashboard</span>
                </div>
                <div class="hidden lg:flex w-full max-w-md">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark">search</span>
                        </div>
                        <input class="block w-full pl-10 pr-3 py-2 border-none rounded-lg leading-5 bg-[#e7f3eb] dark:bg-[#2a4034] text-text-main-light dark:text-text-main-dark placeholder-text-secondary-light dark:placeholder-text-secondary-dark focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm" placeholder="Search Order ID, SKU, or Customer..." type="text" />
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button class="bg-primary hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span class="hidden sm:inline">New Order</span>
                    </button>
                    <div class="h-8 w-px bg-[#e7f3eb] dark:bg-[#2a4034] mx-1 hidden sm:block"></div>
                    <button class="relative p-2 text-text-main-light dark:text-text-main-dark hover:bg-[#e7f3eb] dark:hover:bg-[#2a4034] rounded-lg transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border border-surface-light dark:border-surface-dark"></span>
                    </button>
                    <button class="p-2 text-text-main-light dark:text-text-main-dark hover:bg-[#e7f3eb] dark:hover:bg-[#2a4034] rounded-lg transition-colors">
                        <span class="material-symbols-outlined">settings</span>
                    </button>
                </div>
            </header>
            <!-- Scrollable Content Area -->
            <div class="flex-1 overflow-y-auto p-4 lg:p-8 scroll-smooth">
                <div class="max-w-[1400px] mx-auto flex flex-col gap-8">
                    <!-- KPI Stats -->
                    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-surface-light dark:bg-surface-dark p-5 rounded-xl border border-[#e7f3eb] dark:border-[#2a4034] shadow-sm relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                <span class="material-symbols-outlined text-6xl text-primary">pending_actions</span>
                            </div>
                            <div class="relative z-10">
                                <p class="text-text-secondary-light dark:text-text-secondary-dark text-sm font-medium">Pending Orders</p>
                                <div class="flex items-end gap-2 mt-1">
                                    <h2 class="text-3xl font-bold text-text-main-light dark:text-text-main-dark">42</h2>
                                    <span class="text-xs font-bold text-green-600 bg-green-100 dark:bg-green-900/30 dark:text-green-400 px-1.5 py-0.5 rounded flex items-center mb-1">
                                        <span class="material-symbols-outlined text-[14px] mr-0.5">trending_up</span> 12%
                                    </span>
                                </div>
                                <p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-2">Requires immediate processing</p>
                            </div>
                        </div>
                        <div class="bg-surface-light dark:bg-surface-dark p-5 rounded-xl border border-[#e7f3eb] dark:border-[#2a4034] shadow-sm relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                <span class="material-symbols-outlined text-6xl text-orange-500">warning</span>
                            </div>
                            <div class="relative z-10">
                                <p class="text-text-secondary-light dark:text-text-secondary-dark text-sm font-medium">Low Stock Items</p>
                                <div class="flex items-end gap-2 mt-1">
                                    <h2 class="text-3xl font-bold text-text-main-light dark:text-text-main-dark">15</h2>
                                    <span class="text-xs font-bold text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400 px-1.5 py-0.5 rounded flex items-center mb-1">
                                        <span class="material-symbols-outlined text-[14px] mr-0.5">priority_high</span> 5 New
                                    </span>
                                </div>
                                <p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-2">Below safety threshold</p>
                            </div>
                        </div>
                        <div class="bg-surface-light dark:bg-surface-dark p-5 rounded-xl border border-[#e7f3eb] dark:border-[#2a4034] shadow-sm relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                <span class="material-symbols-outlined text-6xl text-blue-500">local_shipping</span>
                            </div>
                            <div class="relative z-10">
                                <p class="text-text-secondary-light dark:text-text-secondary-dark text-sm font-medium">Active Trucks</p>
                                <div class="flex items-end gap-2 mt-1">
                                    <h2 class="text-3xl font-bold text-text-main-light dark:text-text-main-dark">8</h2>
                                    <span class="text-xs font-bold text-text-secondary-light dark:text-text-secondary-dark bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded flex items-center mb-1">
                                        <span class="material-symbols-outlined text-[14px] mr-0.5">remove</span> Stable
                                    </span>
                                </div>
                                <p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-2">Currently on route</p>
                            </div>
                        </div>
                        <div class="bg-surface-light dark:bg-surface-dark p-5 rounded-xl border border-[#e7f3eb] dark:border-[#2a4034] shadow-sm relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                <span class="material-symbols-outlined text-6xl text-primary">payments</span>
                            </div>
                            <div class="relative z-10">
                                <p class="text-text-secondary-light dark:text-text-secondary-dark text-sm font-medium">Today's Revenue</p>
                                <div class="flex items-end gap-2 mt-1">
                                    <h2 class="text-3xl font-bold text-text-main-light dark:text-text-main-dark">$12.4k</h2>
                                    <span class="text-xs font-bold text-green-600 bg-green-100 dark:bg-green-900/30 dark:text-green-400 px-1.5 py-0.5 rounded flex items-center mb-1">
                                        <span class="material-symbols-outlined text-[14px] mr-0.5">trending_up</span> 8%
                                    </span>
                                </div>
                                <p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-2">Vs. yesterday average</p>
                            </div>
                        </div>
                    </section>
                    <!-- Main Grid Content -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                        <!-- Pending Orders Table (Spans 2 columns on large screens) -->
                        <section class="xl:col-span-2 flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-text-main-light dark:text-text-main-dark">Recent Pending Orders</h2>
                                <button class="text-primary text-sm font-bold hover:underline">View All</button>
                            </div>
                            <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-[#e7f3eb] dark:border-[#2a4034] shadow-sm overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="bg-background-light dark:bg-[#22362b] border-b border-[#e7f3eb] dark:border-[#2a4034]">
                                                <th class="p-4 text-xs font-semibold text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">Order ID</th>
                                                <th class="p-4 text-xs font-semibold text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">Customer</th>
                                                <th class="p-4 text-xs font-semibold text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">Date</th>
                                                <th class="p-4 text-xs font-semibold text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">Status</th>
                                                <th class="p-4 text-xs font-semibold text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider text-right">Total</th>
                                                <th class="p-4 text-xs font-semibold text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#e7f3eb] dark:divide-[#2a4034]">
                                            <tr class="group hover:bg-background-light dark:hover:bg-[#22362b] transition-colors">
                                                <td class="p-4 text-sm font-medium text-text-main-light dark:text-text-main-dark">ORD-2023-001</td>
                                                <td class="p-4 text-sm text-text-secondary-light dark:text-text-secondary-dark">Supermart A</td>
                                                <td class="p-4 text-sm text-text-secondary-light dark:text-text-secondary-dark">Oct 24, 10:30 AM</td>
                                                <td class="p-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                                                        Pending
                                                    </span>
                                                </td>
                                                <td class="p-4 text-sm font-medium text-text-main-light dark:text-text-main-dark text-right">$450.00</td>
                                                <td class="p-4 text-center">
                                                    <button class="bg-primary/10 hover:bg-primary hover:text-white text-primary rounded-lg px-3 py-1.5 text-xs font-bold transition-all">Process</button>
                                                </td>
                                            </tr>
                                            <tr class="group hover:bg-background-light dark:hover:bg-[#22362b] transition-colors">
                                                <td class="p-4 text-sm font-medium text-text-main-light dark:text-text-main-dark">ORD-2023-002</td>
                                                <td class="p-4 text-sm text-text-secondary-light dark:text-text-secondary-dark">Local Grocer B</td>
                                                <td class="p-4 text-sm text-text-secondary-light dark:text-text-secondary-dark">Oct 24, 09:45 AM</td>
                                                <td class="p-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                        Packing
                                                    </span>
                                                </td>
                                                <td class="p-4 text-sm font-medium text-text-main-light dark:text-text-main-dark text-right">$120.50</td>
                                                <td class="p-4 text-center">
                                                    <button class="bg-background-light dark:bg-[#2a4034] hover:bg-gray-200 dark:hover:bg-gray-700 text-text-main-light dark:text-text-main-dark rounded-lg px-3 py-1.5 text-xs font-bold transition-all">View</button>
                                                </td>
                                            </tr>
                                            <tr class="group hover:bg-background-light dark:hover:bg-[#22362b] transition-colors">
                                                <td class="p-4 text-sm font-medium text-text-main-light dark:text-text-main-dark">ORD-2023-003</td>
                                                <td class="p-4 text-sm text-text-secondary-light dark:text-text-secondary-dark">HyperCity</td>
                                                <td class="p-4 text-sm text-text-secondary-light dark:text-text-secondary-dark">Oct 23, 04:15 PM</td>
                                                <td class="p-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                        Ready
                                                    </span>
                                                </td>
                                                <td class="p-4 text-sm font-medium text-text-main-light dark:text-text-main-dark text-right">$1,200.00</td>
                                                <td class="p-4 text-center">
                                                    <button class="bg-background-light dark:bg-[#2a4034] hover:bg-gray-200 dark:hover:bg-gray-700 text-text-main-light dark:text-text-main-dark rounded-lg px-3 py-1.5 text-xs font-bold transition-all">Manifest</button>
                                                </td>
                                            </tr>
                                            <tr class="group hover:bg-background-light dark:hover:bg-[#22362b] transition-colors">
                                                <td class="p-4 text-sm font-medium text-text-main-light dark:text-text-main-dark">ORD-2023-004</td>
                                                <td class="p-4 text-sm text-text-secondary-light dark:text-text-secondary-dark">Corner Store</td>
                                                <td class="p-4 text-sm text-text-secondary-light dark:text-text-secondary-dark">Oct 23, 02:20 PM</td>
                                                <td class="p-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                        Delayed
                                                    </span>
                                                </td>
                                                <td class="p-4 text-sm font-medium text-text-main-light dark:text-text-main-dark text-right">$85.00</td>
                                                <td class="p-4 text-center">
                                                    <button class="bg-primary/10 hover:bg-primary hover:text-white text-primary rounded-lg px-3 py-1.5 text-xs font-bold transition-all">Resolve</button>
                                                </td>
                                            </tr>
                                            <tr class="group hover:bg-background-light dark:hover:bg-[#22362b] transition-colors">
                                                <td class="p-4 text-sm font-medium text-text-main-light dark:text-text-main-dark">ORD-2023-005</td>
                                                <td class="p-4 text-sm text-text-secondary-light dark:text-text-secondary-dark">Fresh Foods</td>
                                                <td class="p-4 text-sm text-text-secondary-light dark:text-text-secondary-dark">Oct 22, 11:00 AM</td>
                                                <td class="p-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                        Shipped
                                                    </span>
                                                </td>
                                                <td class="p-4 text-sm font-medium text-text-main-light dark:text-text-main-dark text-right">$320.00</td>
                                                <td class="p-4 text-center">
                                                    <button class="bg-background-light dark:bg-[#2a4034] hover:bg-gray-200 dark:hover:bg-gray-700 text-text-main-light dark:text-text-main-dark rounded-lg px-3 py-1.5 text-xs font-bold transition-all">Track</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="px-4 py-3 bg-background-light dark:bg-[#22362b] border-t border-[#e7f3eb] dark:border-[#2a4034] flex justify-center">
                                    <button class="text-xs font-semibold text-text-secondary-light dark:text-text-secondary-dark hover:text-primary transition-colors flex items-center gap-1">
                                        Show More <span class="material-symbols-outlined text-sm">expand_more</span>
                                    </button>
                                </div>
                            </div>
                        </section>
                        <!-- Inventory Alerts Widget (Spans 1 column) -->
                        <section class="flex flex-col gap-4">
                            <h2 class="text-xl font-bold text-text-main-light dark:text-text-main-dark">Critical Inventory Alerts</h2>
                            <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-[#e7f3eb] dark:border-[#2a4034] shadow-sm p-4 flex flex-col gap-3 h-full max-h-[500px] overflow-y-auto">
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30">
                                    <div class="bg-white dark:bg-surface-dark p-2 rounded-md shadow-sm shrink-0">
                                        <div class="size-8 bg-cover bg-center rounded" data-alt="Product image of a bag of premium rice" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuADtOz5vQAkCrPVdmLLnFklFdp6-UcjkKlaAOqWPQwRyLI8ljPjh3SOFef85XKC1RzvzI1mPPNgd7vk-tN9IxyXRu9tDENm6LM7Lx_x7wevUqhg_fIlKlhp-Kf1SpUGBxDnZ-_n86QG6_90gupUsXe-OgySpvnvIRuTZA_ZgVbc0dAXuQgVd_nlhpaNBwAnVBOB4Zk3ehBjzdxIhvbkJ_ZBh49PWLaKqGswVUU_wKYZKXg2MtSQNPweb2M5O5VNOfd7nj0l6KRsC9k');"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-text-main-light dark:text-text-main-dark truncate">Premium Basmati Rice 5kg</p>
                                        <p class="text-xs text-red-600 dark:text-red-400 font-medium">Stock: 12 Units (Min: 50)</p>
                                    </div>
                                    <button class="text-primary hover:bg-primary/10 p-1.5 rounded-lg transition-colors" title="Quick Reorder">
                                        <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                                    </button>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30">
                                    <div class="bg-white dark:bg-surface-dark p-2 rounded-md shadow-sm shrink-0">
                                        <div class="size-8 bg-cover bg-center rounded" data-alt="Product image of a bottle of cooking oil" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAp8g1tt_R82B_KmNXVtWOuP-ISCWRPrZ_b5uSOqrTV8zty3xx1jUJroCoHjoIRUsVhKeQQlOyHt50I0R__mByUIp1u7VVGTPH0kK4ImENo2BRB4V8NB2k_aP0e9jcq-WLu4pZ3hJJdEXTDcIY_hjUtC7bn9itU-vFilNSH29oPNS0fN9DQNhVT9bYPPWhVspk21q9eiME6-jha_CIKOfV3uLuEsV_xa4dlJrqFpIUAN-OhbjQNPRrljbYt2NwnxYLKY4lQKrDEiCk');"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-text-main-light dark:text-text-main-dark truncate">Sunflower Oil 1L</p>
                                        <p class="text-xs text-red-600 dark:text-red-400 font-medium">Stock: 8 Units (Min: 40)</p>
                                    </div>
                                    <button class="text-primary hover:bg-primary/10 p-1.5 rounded-lg transition-colors" title="Quick Reorder">
                                        <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                                    </button>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-orange-50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-900/30">
                                    <div class="bg-white dark:bg-surface-dark p-2 rounded-md shadow-sm shrink-0">
                                        <div class="size-8 bg-cover bg-center rounded" data-alt="Product image of canned tomatoes" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAWvVia8tCVvQ7RO7Up8IPBNw0e_muDGUp-uAYjiGrU1zI72yZPfgIF2T1ToIgO3iOxr-GsPzLkKcfR0uw5MvzkhaQ-H5wPKDJjQ_wpcNRn17JhfolhZurv6QOsNlrreq7-X33-THTsvPajmGAaketWSQ6_nPBs8gUI8ox4oa5cGKOyPkXXIzPaixNq4kgzWqZ9XZyDv1z6T-bxvJS1ehxL4bGLsETNbJUDB1pd9QqkygEZG_t-6qqNlY1wGCKEpqdEzf7PHGZx-J0');"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-text-main-light dark:text-text-main-dark truncate">Canned Tomatoes 400g</p>
                                        <p class="text-xs text-orange-600 dark:text-orange-400 font-medium">Stock: 45 Units (Min: 60)</p>
                                    </div>
                                    <button class="text-primary hover:bg-primary/10 p-1.5 rounded-lg transition-colors" title="Quick Reorder">
                                        <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                                    </button>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-orange-50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-900/30">
                                    <div class="bg-white dark:bg-surface-dark p-2 rounded-md shadow-sm shrink-0">
                                        <div class="size-8 bg-cover bg-center rounded" data-alt="Product image of whole wheat flour bag" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDPuzRpLx_X7bp9gtpKCkASufvg0RO9WY-1j8RxbYsV7sa0NcQJ9LABWXcrMeVLhckL3zNbqyCJaB4kdvrmTzOXSSiljWfAe3Oy2Ki5L8Ok4cGV9GtHqA_QV-TJIMloB7GWAUZdqSTUXwdspfPgBy4D-9jLrLtyLwKzDn4yo8GDiJDWeSlNY_B3mZmPGJJ65ayo_93dm1dOo41wmpVLL3WBrV9k2XsFF_zeIBJRkBkudlCa8Q58J40X0Tm687Uy_fqbamZx4IuJKOc');"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-text-main-light dark:text-text-main-dark truncate">Whole Wheat Flour 1kg</p>
                                        <p class="text-xs text-orange-600 dark:text-orange-400 font-medium">Stock: 32 Units (Min: 40)</p>
                                    </div>
                                    <button class="text-primary hover:bg-primary/10 p-1.5 rounded-lg transition-colors" title="Quick Reorder">
                                        <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                                    </button>
                                </div>
                                <button class="mt-2 w-full py-2.5 rounded-lg border border-[#e7f3eb] dark:border-[#2a4034] text-sm font-bold text-text-secondary-light dark:text-text-secondary-dark hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors">
                                    View Full Inventory
                                </button>
                            </div>
                        </section>
                    </div>
                    <!-- Logistics Section -->
                    <section class="flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-text-main-light dark:text-text-main-dark">Today's Logistics Schedule</h2>
                            <div class="flex gap-2">
                                <span class="text-sm text-text-secondary-light dark:text-text-secondary-dark font-medium self-center mr-2">Oct 24, 2023</span>
                                <button class="size-8 flex items-center justify-center rounded-lg bg-surface-light dark:bg-surface-dark border border-[#e7f3eb] dark:border-[#2a4034] text-text-secondary-light hover:text-primary">
                                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                                </button>
                                <button class="size-8 flex items-center justify-center rounded-lg bg-surface-light dark:bg-surface-dark border border-[#e7f3eb] dark:border-[#2a4034] text-text-secondary-light hover:text-primary">
                                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                                </button>
                            </div>
                        </div>
                        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-[#e7f3eb] dark:border-[#2a4034] shadow-sm p-6 overflow-x-auto">
                            <!-- Timeline Header -->
                            <div class="flex min-w-[800px] mb-4 border-b border-[#e7f3eb] dark:border-[#2a4034] pb-2 text-xs font-semibold text-text-secondary-light dark:text-text-secondary-dark uppercase">
                                <div class="w-32 shrink-0">Truck ID</div>
                                <div class="w-24 shrink-0">Driver</div>
                                <div class="flex-1 grid grid-cols-12 gap-1 text-center">
                                    <div>08:00</div>
                                    <div>09:00</div>
                                    <div>10:00</div>
                                    <div>11:00</div>
                                    <div>12:00</div>
                                    <div>13:00</div>
                                    <div>14:00</div>
                                    <div>15:00</div>
                                    <div>16:00</div>
                                    <div>17:00</div>
                                    <div>18:00</div>
                                    <div>19:00</div>
                                </div>
                            </div>
                            <!-- Timeline Rows -->
                            <div class="flex flex-col gap-4 min-w-[800px]">
                                <!-- Truck 1 -->
                                <div class="flex items-center group">
                                    <div class="w-32 shrink-0 font-bold text-sm text-text-main-light dark:text-text-main-dark">TRK-092</div>
                                    <div class="w-24 shrink-0 text-sm text-text-secondary-light dark:text-text-secondary-dark flex items-center gap-2">
                                        <div class="size-6 rounded-full bg-gray-200 dark:bg-gray-700 bg-cover bg-center" data-alt="Driver portrait" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuChqI_O1Xh6oPRGB3T1tAi17Vtt0a8QBDT-v0O7-_aKx2Bsv0tBl8vylKEcfk58SHLBf3rnawSb_3qV8HZ06XndPM-zImYKktnboa0z5dDgYDq23K2WAGeW-vOTkgO8MkjiaaWR2zCsLpXimB85vSmlgFjUA0emDYiSJ-AoAscMiSOUw8xj_zeOhLyIwdim8ep262J55fhIV1nhSRMBqIZjqft0Xo4MpWHa8VH7nozsGdMxw9Xkc-8So-cYyMSQbbKrcrNUTZlqYdE');"></div>
                                        <span>Mike</span>
                                    </div>
                                    <div class="flex-1 relative h-10 bg-background-light dark:bg-[#22362b] rounded-lg">
                                        <!-- Schedule Bar -->
                                        <div class="absolute top-2 bottom-2 left-[8.3%] width-[33.3%] w-[40%] bg-primary/20 border border-primary text-primary rounded-md flex items-center px-2 text-xs font-bold overflow-hidden whitespace-nowrap" title="Route A: North District">
                                            Route A: North
                                        </div>
                                    </div>
                                </div>
                                <!-- Truck 2 -->
                                <div class="flex items-center group">
                                    <div class="w-32 shrink-0 font-bold text-sm text-text-main-light dark:text-text-main-dark">TRK-105</div>
                                    <div class="w-24 shrink-0 text-sm text-text-secondary-light dark:text-text-secondary-dark flex items-center gap-2">
                                        <div class="size-6 rounded-full bg-gray-200 dark:bg-gray-700 bg-cover bg-center" data-alt="Driver portrait" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBEiqaDL_CHwiqssmrtmtNBOKob4z42f_eIvN1eGnxn5idaZt-5aRoUfXDfJnkDjH2evvXxufgZOgBGXqfDfMaHf83N_K1uOUTT036cWU7NRdQei5kuvAkn5PPga2mY8NxN4mYqei27M9uZ3Jagy4Tk0pIsOBV2q4hPYONwXEZPRAZB6dzR0d1NliOkHZhinmKaFAv3w04QdYnXaFhrlMJDez1h5I65Dj4nZAtasIdjg1io4DsfZpnK4jvJFUxKKDM5OhGDoG7j7ko');"></div>
                                        <span>Sarah</span>
                                    </div>
                                    <div class="flex-1 relative h-10 bg-background-light dark:bg-[#22362b] rounded-lg">
                                        <!-- Schedule Bar -->
                                        <div class="absolute top-2 bottom-2 left-[25%] w-[50%] bg-blue-100 border border-blue-400 text-blue-700 dark:bg-blue-900/30 dark:border-blue-500/50 dark:text-blue-300 rounded-md flex items-center px-2 text-xs font-bold overflow-hidden whitespace-nowrap" title="Route B: Central Hub">
                                            Route B: Central Hub
                                        </div>
                                    </div>
                                </div>
                                <!-- Truck 3 -->
                                <div class="flex items-center group">
                                    <div class="w-32 shrink-0 font-bold text-sm text-text-main-light dark:text-text-main-dark">TRK-088</div>
                                    <div class="w-24 shrink-0 text-sm text-text-secondary-light dark:text-text-secondary-dark flex items-center gap-2">
                                        <div class="size-6 rounded-full bg-gray-200 dark:bg-gray-700 bg-cover bg-center" data-alt="Driver portrait" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDNnsjVpGfrK6ZvB57YXvUJhPIfaRfTs48tvsfQ3lZbQKtrL_GL0mE6fJH1wAhY8cmRDrl4jPl4Q43A7eltBBVLe__lJkZG0G0gXNTG8ful5h-f82UPPEkiLy0KXlSMMH0HwzQbchiOqunsQVdYiiTXPaQrQ9RCSDGjxGYD6uXfxRDzu0FGVr_SM62DZXGPRkjhG8siCyG4s4ompoeaFBPqLrbbP5-PZF_U1ArYQN1sd_-mto7bmsxIDUfcyMJmrnzdUI8gdByF6H0');"></div>
                                        <span>Davide</span>
                                    </div>
                                    <div class="flex-1 relative h-10 bg-background-light dark:bg-[#22362b] rounded-lg">
                                        <!-- Schedule Bar -->
                                        <div class="absolute top-2 bottom-2 left-[58.3%] w-[25%] bg-orange-100 border border-orange-400 text-orange-700 dark:bg-orange-900/30 dark:border-orange-500/50 dark:text-orange-300 rounded-md flex items-center px-2 text-xs font-bold overflow-hidden whitespace-nowrap" title="Route C: Express">
                                            Route C: Express
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <?php include '../components/scripts.php'; ?>
</body>

</html>