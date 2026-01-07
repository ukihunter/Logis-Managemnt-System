<?php
require_once '../../../config/admin_session.php';
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
                    <div class="flex justify-end gap-4">
                        <button onclick="openProductPanel('new')" class="hidden sm:flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-primary hover:bg-green-400 text-[#0d1b12] text-sm font-bold transition-colors shadow-sm shadow-green-200 dark:shadow-none">
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
                                <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs px-2 py-1 rounded-full font-bold">+2.5%</span>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold tracking-tight mt-2">$1.2M</p>
                            <p class="text-xs text-[#4c9a66] dark:text-gray-500 mt-1">Across 4 branches</p>
                        </div>
                        <!-- Card 2 -->
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#cfe7d7] dark:border-white/10 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex justify-between items-start">
                                <p class="text-[#4c9a66] dark:text-gray-400 text-sm font-medium">Low Stock Items</p>
                                <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs px-2 py-1 rounded-full font-bold">Action Needed</span>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold tracking-tight mt-2">14</p>
                            <p class="text-xs text-[#4c9a66] dark:text-gray-500 mt-1">+4 items since yesterday</p>
                        </div>
                        <!-- Card 3 -->
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#cfe7d7] dark:border-white/10 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex justify-between items-start">
                                <p class="text-[#4c9a66] dark:text-gray-400 text-sm font-medium">Pending Transfers</p>
                                <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs px-2 py-1 rounded-full font-bold">In Transit</span>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold tracking-tight mt-2">23</p>
                            <p class="text-xs text-[#4c9a66] dark:text-gray-500 mt-1">Approx. 450 units</p>
                        </div>
                        <!-- Card 4 -->
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#cfe7d7] dark:border-white/10 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex justify-between items-start">
                                <p class="text-[#4c9a66] dark:text-gray-400 text-sm font-medium">Recent Returns</p>
                                <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 text-xs px-2 py-1 rounded-full font-bold">Review</span>
                            </div>
                            <p class="text-[#0d1b12] dark:text-white text-2xl font-bold tracking-tight mt-2">5</p>
                            <p class="text-xs text-[#4c9a66] dark:text-gray-500 mt-1">Last 24 hours</p>
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
                        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-[#cfe7d7] dark:border-white/10 shadow-sm flex flex-col lg:flex-row gap-4 items-center justify-between">
                            <!-- Local Table Search -->
                            <label class="flex flex-col w-full lg:w-1/3">
                                <div class="flex w-full items-center rounded-lg h-10 bg-[#f6f8f6] dark:bg-white/5 border border-transparent focus-within:border-primary/50 transition-colors">
                                    <div class="text-[#4c9a66] dark:text-gray-400 flex items-center justify-center pl-3">
                                        <span class="material-symbols-outlined text-[20px]">search</span>
                                    </div>
                                    <input class="w-full bg-transparent border-none text-[#0d1b12] dark:text-white placeholder:text-[#4c9a66] dark:placeholder:text-gray-500 focus:ring-0 text-sm px-3" placeholder="Find by Product Name, SKU or Batch..." />
                                </div>
                            </label>
                            <!-- Chips -->
                            <div class="flex gap-2 overflow-x-auto w-full lg:w-auto pb-2 lg:pb-0 scrollbar-hide">
                                <button class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-[#0d1b12] dark:bg-primary text-white dark:text-[#0d1b12] px-3 transition-colors">
                                    <span class="text-xs font-bold">All Items</span>
                                </button>
                                <button class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-[#e7f3eb] dark:bg-white/5 border border-transparent hover:border-[#cfe7d7] dark:hover:border-white/20 px-3 transition-colors">
                                    <span class="text-[#0d1b12] dark:text-white text-xs font-medium">Beverages</span>
                                </button>
                                <button class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-[#e7f3eb] dark:bg-white/5 border border-transparent hover:border-[#cfe7d7] dark:hover:border-white/20 px-3 transition-colors">
                                    <span class="text-[#0d1b12] dark:text-white text-xs font-medium">Snacks</span>
                                </button>
                                <button class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-[#e7f3eb] dark:bg-white/5 border border-transparent hover:border-[#cfe7d7] dark:hover:border-white/20 px-3 transition-colors">
                                    <span class="text-[#0d1b12] dark:text-white text-xs font-medium">Staples</span>
                                </button>
                                <button class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-[#e7f3eb] dark:bg-white/5 border border-transparent hover:border-[#cfe7d7] dark:hover:border-white/20 px-3 transition-colors">
                                    <span class="text-[#0d1b12] dark:text-white text-xs font-medium">Home Care</span>
                                </button>
                            </div>
                        </div>
                        <!-- Main Inventory Table -->
                        <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-[#cfe7d7] dark:border-white/10 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-[#f8fcf9] dark:bg-white/5 border-b border-[#cfe7d7] dark:border-white/10">
                                        <tr>
                                            <th class="py-4 px-6 text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider">Product &amp; SKU</th>
                                            <th class="py-4 px-6 text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider">Category</th>
                                            <th class="py-4 px-6 text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider">Stock Level</th>
                                            <th class="py-4 px-6 text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider">Allocated</th>
                                            <th class="py-4 px-6 text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider">Unit Price</th>
                                            <th class="py-4 px-6 text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider">Status</th>
                                            <th class="py-4 px-6 text-right text-xs font-bold text-[#4c9a66] dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#e7f3eb] dark:divide-white/5">
                                        <!-- Row 1 -->
                                        <tr class="group hover:bg-[#f6f8f6] dark:hover:bg-white/5 transition-colors cursor-pointer">
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="size-10 rounded-lg bg-gray-100 dark:bg-gray-800 bg-center bg-cover" data-alt="Can of premium tuna fish" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCSROr5BtDMAAsEjm0QUNbw2bNIKTYracstmIDSd0TLqYNaWLCVnDY-QC_lNtaUjAu-uc-cPP7-eZMtQAU1tbuAMjYYwnDXY9ASq0VrOfliM5FEol7MaZc3_Gnq7XZGJfHMp-GgzJNqPtZSWk8YSFj8Zcv3A1H68UOFM4SQkSXP3Lh2ZucKOmuzQefDiIEFtoYapzbccHZw-DIsCCSQYJNc3-nz_RJzrpNyAs28h5OMP1zFUJdHMEI1FMgr8YRHS4mzBzHtptadxWQ");'></div>
                                                    <div>
                                                        <p class="text-sm font-bold text-[#0d1b12] dark:text-white">Canned Tuna 180g</p>
                                                        <p class="text-xs text-[#4c9a66] dark:text-gray-500">SKU: TN-180-ORG</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center rounded-md bg-[#e7f3eb] dark:bg-white/10 px-2 py-1 text-xs font-medium text-[#0d1b12] dark:text-white ring-1 ring-inset ring-gray-500/10">Staples</span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex flex-col gap-1 w-24">
                                                    <div class="flex justify-between text-xs">
                                                        <span class="font-bold text-[#0d1b12] dark:text-white">2,450</span>
                                                        <span class="text-gray-400">/ 3k</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                                        <div class="bg-primary h-1.5 rounded-full" style="width: 80%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-[#0d1b12] dark:text-white">120</td>
                                            <td class="py-4 px-6 text-sm font-medium text-[#0d1b12] dark:text-white">$1.85</td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 dark:bg-green-900/20 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-400 ring-1 ring-inset ring-green-600/20">
                                                    <span class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"></span>
                                                    In Stock
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-right">
                                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">

                                                    <button onclick="openProductPanel('edit', 'Canned Tuna 180g', 'TN-180-ORG')" class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="Edit Details">
                                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                                    </button>
                                                    <button class="p-1.5 rounded-md text-red-500 hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="Transfer Stock">
                                                        <span class="material-symbols-outlined text-[20px] ">heart_broken</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Row 2 -->
                                        <tr class="group hover:bg-[#f6f8f6] dark:hover:bg-white/5 transition-colors cursor-pointer">
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="size-10 rounded-lg bg-gray-100 dark:bg-gray-800 bg-center bg-cover" data-alt="Pack of gourmet coffee beans" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBuqW0rCKpzzrXLr-J7tx3iAQkuvuxL_dd_MfIdxRcsEJNumuNAw51F1hqc1UJHxDAniMULSmJwb3rmRw4-ncocOWi9rFdtcUwy5d4U8zoCuibvgQoCjdyiKzdhN3uwccpjIMLwgyp0FUZCpUnw4FNb7ij8ijYAQ1xchUfkWqRDMOp9mv_JHndndsl7Rtoc92XUzm1KEZXL0ODu6tA5tOudA6r3d1rrRJuq9ZcSFn51R8YsVTGR3_UUrvom_ASz1e1Hpf8NMixIN90");'></div>
                                                    <div>
                                                        <p class="text-sm font-bold text-[#0d1b12] dark:text-white">Arabica Coffee 500g</p>
                                                        <p class="text-xs text-[#4c9a66] dark:text-gray-500">SKU: CF-500-AR</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center rounded-md bg-[#e7f3eb] dark:bg-white/10 px-2 py-1 text-xs font-medium text-[#0d1b12] dark:text-white ring-1 ring-inset ring-gray-500/10">Beverages</span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex flex-col gap-1 w-24">
                                                    <div class="flex justify-between text-xs">
                                                        <span class="font-bold text-red-600 dark:text-red-400">45</span>
                                                        <span class="text-gray-400">/ 500</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                                        <div class="bg-red-500 h-1.5 rounded-full" style="width: 10%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-[#0d1b12] dark:text-white">10</td>
                                            <td class="py-4 px-6 text-sm font-medium text-[#0d1b12] dark:text-white">$12.50</td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center gap-1 rounded-full bg-red-50 dark:bg-red-900/20 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-600/10">
                                                    <span class="size-1.5 rounded-full bg-red-600 dark:bg-red-400"></span>
                                                    Low Stock
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-right">
                                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">

                                                    <button onclick="openProductPanel('edit', 'Canned Tuna 180g', 'TN-180-ORG')" class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="Edit Details">
                                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                                    </button>
                                                    <button class="p-1.5 rounded-md text-red-500 hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="Transfer Stock">
                                                        <span class="material-symbols-outlined text-[20px] ">heart_broken</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Row 3 -->
                                        <tr class="group hover:bg-[#f6f8f6] dark:hover:bg-white/5 transition-colors cursor-pointer">
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="size-10 rounded-lg bg-gray-100 dark:bg-gray-800 bg-center bg-cover" data-alt="Bag of basmati rice" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCDJqwPdyP_8h8bQBK273daOCb4zwnXgzbuVkuqQxv_4GsbUODnD5SqOk1mBA1HCV-5eN7QNnlOFdGm7NGJw1SGQY4jK-NUysqaBlBR0ZljpApn1LADBgfZeVpPWiemBYLHv9FAerYSUkRZDQIH8i6DkO-qqgB9cCHXx_Y9XuGvJ8fZHYaxTANcHvY4rHewUiJ5dZlbg3ZxC-0n6sdDRCgHRvNnzzz_s55uK8h2IyvrCFC2aCVpr8nmB7inJAhgnvB6oDwx7WmIHlM");'></div>
                                                    <div>
                                                        <p class="text-sm font-bold text-[#0d1b12] dark:text-white">Basmati Rice 5kg</p>
                                                        <p class="text-xs text-[#4c9a66] dark:text-gray-500">SKU: RC-5KG-BAS</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center rounded-md bg-[#e7f3eb] dark:bg-white/10 px-2 py-1 text-xs font-medium text-[#0d1b12] dark:text-white ring-1 ring-inset ring-gray-500/10">Staples</span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex flex-col gap-1 w-24">
                                                    <div class="flex justify-between text-xs">
                                                        <span class="font-bold text-[#0d1b12] dark:text-white">850</span>
                                                        <span class="text-gray-400">/ 1k</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                                        <div class="bg-primary h-1.5 rounded-full" style="width: 85%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-[#0d1b12] dark:text-white">200</td>
                                            <td class="py-4 px-6 text-sm font-medium text-[#0d1b12] dark:text-white">$8.99</td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 dark:bg-green-900/20 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-400 ring-1 ring-inset ring-green-600/20">
                                                    <span class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"></span>
                                                    In Stock
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-right">
                                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">

                                                    <button onclick="openProductPanel('edit', 'Canned Tuna 180g', 'TN-180-ORG')" class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="Edit Details">
                                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                                    </button>
                                                    <button class="p-1.5 rounded-md text-red-500 hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="Transfer Stock">
                                                        <span class="material-symbols-outlined text-[20px] ">heart_broken</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Row 4 -->
                                        <tr class="group hover:bg-[#f6f8f6] dark:hover:bg-white/5 transition-colors cursor-pointer">
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="size-10 rounded-lg bg-gray-100 dark:bg-gray-800 bg-center bg-cover" data-alt="Bottle of sparkling water" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCqHQJ8RlZpRUcl-G4TO1Sub_PPqWwqG_oEzQYtu5DSCl38jCXYubYvJhbEQU-MHTvF53cCaWMDD3nhnfSMAUeq5H0kgCCx4fKATR_HP47AWCTeX47yjVZDUqnMSEh03J5sVmN3_Ozzwl13aIJHkgof_7_Hmk2jVUWvXVwBqz3faalevoJXiMyfeCbhLSzER7EYduy4UIpN_I-EbZvWUu3Z20qH2buG9dfjckR3UDS2cBtH9FILPLDjOVmEu6VC25C43Sx1T8S17vk");'></div>
                                                    <div>
                                                        <p class="text-sm font-bold text-[#0d1b12] dark:text-white">Sparkling Water 1L</p>
                                                        <p class="text-xs text-[#4c9a66] dark:text-gray-500">SKU: WT-1L-SPK</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center rounded-md bg-[#e7f3eb] dark:bg-white/10 px-2 py-1 text-xs font-medium text-[#0d1b12] dark:text-white ring-1 ring-inset ring-gray-500/10">Beverages</span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex flex-col gap-1 w-24">
                                                    <div class="flex justify-between text-xs">
                                                        <span class="font-bold text-gray-400">0</span>
                                                        <span class="text-gray-400">/ 2k</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                                        <div class="bg-gray-400 h-1.5 rounded-full" style="width: 0%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-[#0d1b12] dark:text-white">0</td>
                                            <td class="py-4 px-6 text-sm font-medium text-[#0d1b12] dark:text-white">$0.99</td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-400 ring-1 ring-inset ring-gray-500/10">
                                                    <span class="size-1.5 rounded-full bg-gray-500"></span>
                                                    Out of Stock
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-right">
                                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">

                                                    <button onclick="openProductPanel('edit', 'Canned Tuna 180g', 'TN-180-ORG')" class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="Edit Details">
                                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                                    </button>
                                                    <button class="p-1.5 rounded-md text-red-500 hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="Transfer Stock">
                                                        <span class="material-symbols-outlined text-[20px] ">heart_broken</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Row 5 -->
                                        <tr class="group hover:bg-[#f6f8f6] dark:hover:bg-white/5 transition-colors cursor-pointer">
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <div class="size-10 rounded-lg bg-gray-100 dark:bg-gray-800 bg-center bg-cover" data-alt="Pack of spicy chips" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBjmZAe_jsT13WzEVhDGACVSLX89FqowcJEKwEApuH5CkhnDfwRcHZGz47m77TkLwuRQC3-3KT3G54oLUwQDBLjr1iJkUBDMGP0_cgH3ZeKeTPQOUkFzrPwvsgWM2iFl-y7_bJ94JkglBEW5xDPbbSblwpuaetb_Jk5s-uVoPtgS6cpRo_KkQV9Iirk5QOEeBtFC-MtkHh_9VKJ30HUaCEDOVf0x5PJSQF4LVtCnVHDjXsaE1q-0hIaoEMQrTs-8YrD-Bwmr49x8Kc");'></div>
                                                    <div>
                                                        <p class="text-sm font-bold text-[#0d1b12] dark:text-white">Spicy Chips 150g</p>
                                                        <p class="text-xs text-[#4c9a66] dark:text-gray-500">SKU: SN-150-SPC</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center rounded-md bg-[#e7f3eb] dark:bg-white/10 px-2 py-1 text-xs font-medium text-[#0d1b12] dark:text-white ring-1 ring-inset ring-gray-500/10">Snacks</span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex flex-col gap-1 w-24">
                                                    <div class="flex justify-between text-xs">
                                                        <span class="font-bold text-[#0d1b12] dark:text-white">1,200</span>
                                                        <span class="text-gray-400">/ 1.5k</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                                        <div class="bg-primary h-1.5 rounded-full" style="width: 80%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-[#0d1b12] dark:text-white">50</td>
                                            <td class="py-4 px-6 text-sm font-medium text-[#0d1b12] dark:text-white">$2.49</td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 dark:bg-green-900/20 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-400 ring-1 ring-inset ring-green-600/20">
                                                    <span class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"></span>
                                                    In Stock
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-right">
                                                <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">

                                                    <button onclick="openProductPanel('edit', 'Canned Tuna 180g', 'TN-180-ORG')" class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="Edit Details">
                                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                                    </button>
                                                    <button class="p-1.5 rounded-md text-red-500 hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 hover:text-primary transition-colors" title="Transfer Stock">
                                                        <span class="material-symbols-outlined text-[20px] ">heart_broken</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="flex items-center justify-between border-t border-[#cfe7d7] dark:border-white/10 bg-[#f8fcf9] dark:bg-surface-dark px-4 py-3 sm:px-6">
                                <div class="hidden sm:flex flex-1 items-center justify-between">
                                    <div>
                                        <p class="text-sm text-[#4c9a66] dark:text-gray-400">
                                            Showing <span class="font-medium text-[#0d1b12] dark:text-white">1</span> to <span class="font-medium text-[#0d1b12] dark:text-white">5</span> of <span class="font-medium text-[#0d1b12] dark:text-white">124</span> results
                                        </p>
                                    </div>
                                    <div>
                                        <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
                                            <a class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-white/10 hover:bg-gray-50 dark:hover:bg-white/5 focus:z-20 focus:outline-offset-0" href="#">
                                                <span class="sr-only">Previous</span>
                                                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                                            </a>
                                            <a aria-current="page" class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-[#0d1b12] focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary" href="#">1</a>
                                            <a class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-[#0d1b12] dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-white/10 hover:bg-gray-50 dark:hover:bg-white/5 focus:z-20 focus:outline-offset-0" href="#">2</a>
                                            <a class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-[#0d1b12] dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-white/10 hover:bg-gray-50 dark:hover:bg-white/5 focus:z-20 focus:outline-offset-0" href="#">3</a>
                                            <a class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-white/10 hover:bg-gray-50 dark:hover:bg-white/5 focus:z-20 focus:outline-offset-0" href="#">
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
            </div>
        </main>

        <!-- Right Panel: Product Form -->
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
                <form id="productForm" class="space-y-6">
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
                                <input id="productImage" type="file" class="hidden" accept="image/*" onchange="previewImage(event)">
                            </label>
                        </div>
                    </div>

                    <!-- Product Name -->
                    <div>
                        <label for="productName" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" id="productName" required class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., Premium Jasmine Rice">
                    </div>

                    <!-- SKU -->
                    <div>
                        <label for="productSKU" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">SKU <span class="text-red-500">*</span></label>
                        <input type="text" id="productSKU" required class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g., RC-5KG-JAS">
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="productCategory" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Category <span class="text-red-500">*</span></label>
                        <select id="productCategory" required class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select a category</option>
                            <option value="Beverages">Beverages</option>
                            <option value="Snacks">Snacks</option>
                            <option value="Staples">Staples</option>
                            <option value="Home Care">Home Care</option>
                        </select>
                    </div>

                    <!-- Stock Quantity and Max Level -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="stockLevel" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Stock Level <span class="text-red-500">*</span></label>
                            <input type="number" id="stockLevel" required min="0" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0">
                        </div>
                        <div>
                            <label for="maxLevel" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Max Level</label>
                            <input type="number" id="maxLevel" min="0" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0">
                        </div>
                    </div>

                    <!-- Allocated and Unit Price -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="allocated" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Allocated</label>
                            <input type="number" id="allocated" min="0" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0">
                        </div>
                        <div>
                            <label for="unitPrice" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Unit Price ($) <span class="text-red-500">*</span></label>
                            <input type="number" id="unitPrice" required min="0" step="0.01" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Description</label>
                        <textarea id="description" rows="3" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-[#102216] text-[#0d1b12] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-none" placeholder="Add product description..."></textarea>
                    </div>

                    <!-- Status -->
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
                </form>
            </div>

            <!-- Sticky Bottom Actions -->
            <div class="p-6 border-t border-[#e7f3eb] dark:border-gray-800 bg-white dark:bg-[#152e1e]">
                <div class="flex gap-3">
                    <button onclick="closeProductPanel()" type="button" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 dark:border-gray-700 text-[#0d1b12] dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 font-bold text-sm transition-colors">
                        Cancel
                    </button>
                    <button type="submit" form="productForm" class="flex-[2] flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-[#0ebf49] text-[#0d1b12] rounded-lg font-bold text-sm shadow-md shadow-primary/20 transition-all active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        <span id="submitButtonText">Add Product</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>

    <?php include '../components/scripts.php'; ?>
    <script src="js/scripts.js"></script>
</body>

</html>