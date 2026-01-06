<?php
require_once '../../../config/session_Detils.php';
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Product Catalog - Distribution System</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts: Manrope -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet" />
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <!-- Theme Configuration -->
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
                        "surface-dark": "#1C2C23",
                        "text-main": "#0d1b12",
                        "text-muted": "#4c9a66",
                        "border-light": "#e7f3eb",
                        "border-dark": "#2A3C32"
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
        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-text-main font-display antialiased min-h-screen flex flex-col overflow-x-hidden">
    <!-- Top Navigation -->
    <header class="sticky top-0 z-50 bg-background-light dark:bg-background-dark border-b border-border-light dark:border-border-dark px-6 py-3 shadow-sm">
        <div class="mx-auto w-full max-w-[1440px] flex items-center justify-between gap-6">
            <!-- Brand & Search -->
            <div class="flex items-center gap-8 flex-1">
                <div class="flex items-center gap-3 text-text-main dark:text-white shrink-0">
                    <div class="size-8 bg-primary/70 rounded-lg flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-white">shopping_bag_speed</span>
                    </div>
                    <h2 class="text-lg font-bold leading-tight tracking-tight hidden md:flex">IslandDistro</h2>
                </div>
                <!-- Search Bar -->
                <label class="flex w-full max-w-lg items-center relative group">
                    <div class="absolute left-4 text-text-muted">
                        <span class="material-symbols-outlined">search</span>
                    </div>
                    <input class="w-full h-11 pl-12 pr-4 rounded-lg bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark focus:border-primary focus:ring-1 focus:ring-primary text-text-main dark:text-white placeholder:text-text-muted text-sm transition-all" placeholder="Search by product name, SKU, or brand..." value="" />
                    <div class="absolute right-3 hidden group-focus-within:block">
                        <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-500 px-2 py-1 rounded border border-gray-200 dark:border-gray-700">ESC</span>
                    </div>
                </label>
            </div>
            <!-- Right Actions -->
            <div class="flex items-center gap-6 shrink-0">
                <nav class="hidden lg:flex items-center gap-6">
                    <a class="text-text-main dark:text-gray-200 text-sm font-medium hover:text-primary transition-colors" href="../Dashboard/dashboard.php">Dashboard</a>
                    <a class="text-primary text-sm font-bold" href="#">Catalog</a>
                    <a class="text-text-main dark:text-gray-200 text-sm font-medium hover:text-primary transition-colors" href="#">Orders</a>
                    <a class="text-text-main dark:text-gray-200 text-sm font-medium hover:text-primary transition-colors" href="#">Invoices</a>
                </nav>
                <div class="flex items-center gap-4">
                    <button onclick="location.href='../../Customer/Cart/cart.php'" class="relative flex items-center justify-center h-10 px-4 bg-primary hover:bg-green-500 transition-colors text-text-main rounded-lg font-bold text-sm gap-2">
                        <span class="material-symbols-outlined text-[20px] text-white">shopping_cart</span>
                        <span class="hidden sm:inline text-white">Cart (0)</span>
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-black/10 text-[10px] font-bold text-white">0</span>
                    </button>
                    <button class="flex items-center gap-2">
                        <div class="relative ml-2">
                            <button id="profileMenuBtn" class="size-10 rounded-full bg-slate-300 dark:bg-slate-700 bg-cover bg-center border-2 border-slate-100 dark:border-slate-800 hover:border-primary dark:hover:border-primary transition-colors" data-alt="User profile avatar showing a store logo or generic user icon" style='background-image: url("https://avatar.iran.liara.run/username?username=<?php echo urlencode($business_name); ?>");'></button>
                            <!-- Dropdown Menu -->
                            <div id="profileDropdown" class="hidden absolute right-40 mt-60 w-48 bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-lg shadow-lg overflow-hidden z-50">
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
                    </button>
                </div>
            </div>
        </div>
    </header>
    <!-- Main Layout -->
    <div class="flex flex-1 mx-auto w-full max-w-[1440px]">
        <!-- Left Sidebar (Filters) -->
        <aside class="hidden lg:flex w-72 flex-col gap-6 border-r border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark py-6 pr-6 h-[calc(100vh-73px)] sticky top-[73px] custom-scrollbar overflow-y-auto">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-text-main dark:text-white font-bold text-lg flex items-center gap-2">
                    <span class="material-symbols-outlined">filter_list</span> Filters
                </h3>
                <button class="text-xs font-medium text-primary hover:text-green-600 transition-colors">Clear All</button>
            </div>
            <!-- Categories -->
            <div class="flex flex-col gap-1">
                <p class="px-2 text-xs font-bold text-text-muted uppercase tracking-wider mb-2">Categories</p>
                <button class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary font-bold">
                    <span class="material-symbols-outlined filled text-[20px]">grid_view</span>
                    <span class="text-sm">All Products</span>
                </button>
                <button class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-text-main dark:text-gray-300 hover:bg-border-light dark:hover:bg-border-dark transition-colors group">
                    <span class="material-symbols-outlined text-[20px] text-gray-400 group-hover:text-primary">toys</span>
                    <span class="text-sm font-medium">Toys</span>
                </button>
                <button class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-text-main dark:text-gray-300 hover:bg-border-light dark:hover:bg-border-dark transition-colors group">
                    <span class="material-symbols-outlined text-[20px] text-gray-400 group-hover:text-primary">devices_other</span>
                    <span class="text-sm font-medium">Electronics</span>
                </button>
                <button class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-text-main dark:text-gray-300 hover:bg-border-light dark:hover:bg-border-dark transition-colors group">
                    <span class="material-symbols-outlined text-[20px] text-gray-400 group-hover:text-primary">apparel</span>
                    <span class="text-sm font-medium">Clothes</span>
                </button>
                <button class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-text-main dark:text-gray-300 hover:bg-border-light dark:hover:bg-border-dark transition-colors group">
                    <span class="material-symbols-outlined text-[20px] text-gray-400 group-hover:text-primary">clean_hands</span>
                    <span class="text-sm font-medium">Cleaning</span>
                </button>
            </div>
            <hr class="border-border-light dark:border-border-dark mx-2" />
            <!-- Detailed Filters (Accordions) -->
            <div class="flex flex-col gap-3">
                <!-- Brand Filter -->
                <details class="group rounded-lg border border-border-light dark:border-border-dark bg-white dark:bg-surface-dark px-4 py-2" open="">
                    <summary class="flex cursor-pointer items-center justify-between gap-4 py-1 list-none">
                        <p class="text-text-main dark:text-white text-sm font-bold">Brand</p>
                        <span class="material-symbols-outlined text-text-main dark:text-white text-[20px] transition-transform group-open:rotate-180">expand_more</span>
                    </summary>
                    <div class="pt-3 pb-2 flex flex-col gap-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input checked="" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary" type="checkbox" />
                            <span class="text-sm text-text-main dark:text-gray-300">Coca-Cola</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary" type="checkbox" />
                            <span class="text-sm text-text-main dark:text-gray-300">Pepsi Co.</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary" type="checkbox" />
                            <span class="text-sm text-text-main dark:text-gray-300">Nestlé</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary" type="checkbox" />
                            <span class="text-sm text-text-main dark:text-gray-300">Unilever</span>
                        </label>
                    </div>
                </details>
                <!-- Stock Filter -->
                <details class="group rounded-lg border border-border-light dark:border-border-dark bg-white dark:bg-surface-dark px-4 py-2">
                    <summary class="flex cursor-pointer items-center justify-between gap-4 py-1 list-none">
                        <p class="text-text-main dark:text-white text-sm font-bold">Stock Status</p>
                        <span class="material-symbols-outlined text-text-main dark:text-white text-[20px] transition-transform group-open:rotate-180">expand_more</span>
                    </summary>
                    <div class="pt-3 pb-2 flex flex-col gap-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input checked="" class="w-4 h-4 border-gray-300 text-primary focus:ring-primary" name="stock" type="radio" />
                            <span class="text-sm text-text-main dark:text-gray-300">All Stock</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input class="w-4 h-4 border-gray-300 text-primary focus:ring-primary" name="stock" type="radio" />
                            <span class="text-sm text-text-main dark:text-gray-300">In Stock</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input class="w-4 h-4 border-gray-300 text-primary focus:ring-primary" name="stock" type="radio" />
                            <span class="text-sm text-text-main dark:text-gray-300">Low Stock (&lt; 50)</span>
                        </label>
                    </div>
                </details>
                <!-- Price Filter -->
                <details class="group rounded-lg border border-border-light dark:border-border-dark bg-white dark:bg-surface-dark px-4 py-2">
                    <summary class="flex cursor-pointer items-center justify-between gap-4 py-1 list-none">
                        <p class="text-text-main dark:text-white text-sm font-bold">Price Range</p>
                        <span class="material-symbols-outlined text-text-main dark:text-white text-[20px] transition-transform group-open:rotate-180">expand_more</span>
                    </summary>
                    <div class="pt-3 pb-2 flex flex-col gap-3">
                        <div class="flex items-center gap-2">
                            <div class="relative w-full">
                                <span class="absolute left-1 top-1.5 text-xs text-gray-500">Rs</span>
                                <input class="w-full pl-5 py-1 text-sm border border-gray-300 rounded focus:ring-primary focus:border-primary" placeholder="Min" type="number" />
                            </div>
                            <span class="text-gray-400">-</span>
                            <div class="relative w-full">
                                <span class="absolute left-1 top-1.5 text-xs text-gray-500">Rs</span>
                                <input class="w-full pl-5 py-1 text-sm border border-gray-300 rounded focus:ring-primary focus:border-primary" placeholder="Max" type="number" />
                            </div>
                        </div>
                        <button class="w-full bg-border-light dark:bg-border-dark text-text-main dark:text-white text-xs font-bold py-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Apply</button>
                    </div>
                </details>
            </div>
        </aside>
        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col p-6 min-w-0">
            <!-- Breadcrumbs & Heading -->
            <div class="mb-8">
                <div class="flex items-center gap-2 text-sm text-text-muted mb-2">
                    <span>Catalog</span>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span>All Products</span>
                </div>
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-black text-text-main dark:text-white tracking-tight">Fast-Moving Consumer Goods</h1>
                        <p class="text-text-muted mt-1">Showing 1-12 of 6 products</p>
                    </div>
                </div>
            </div>
            <!-- Toolbar -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6 bg-white dark:bg-surface-dark p-3 rounded-xl border border-border-light dark:border-border-dark shadow-sm">
                <div class="flex items-center gap-2">
                    <button class="p-2 rounded-lg bg-background-light dark:bg-background-dark text-text-main dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors" title="Filter Visibility">
                        <span class="material-symbols-outlined">tune</span>
                    </button>
                    <div class="h-6 w-px bg-border-light dark:bg-border-dark mx-1"></div>
                    <button class="p-2 rounded-lg bg-primary/10 text-primary transition-colors" title="Grid View">
                        <span class="material-symbols-outlined filled">grid_view</span>
                    </button>
                    <button class="p-2 rounded-lg text-text-muted hover:text-text-main transition-colors" title="List View">
                        <span class="material-symbols-outlined">view_list</span>
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-text-muted hidden sm:block">Sort by:</span>
                    <div class="relative">
                        <select class="appearance-none pl-4 pr-10 py-2 bg-background-light dark:bg-background-dark border-none rounded-lg text-sm font-bold text-text-main dark:text-white focus:ring-2 focus:ring-primary cursor-pointer">
                            <option>Popularity</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Newest Arrivals</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2 top-2 pointer-events-none text-text-main dark:text-white">expand_more</span>
                    </div>
                </div>
            </div>
            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Product Card 1 -->
                <div class="group flex flex-col bg-white dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-primary/50 relative">
                    <div class="absolute top-3 left-3 z-10 flex gap-2">
                        <span class="bg-primary text-text-main text-xs font-bold px-2.5 py-1 rounded shadow-sm">Best Seller</span>
                    </div>
                    <div class="aspect-[4/3] w-full bg-gray-50 dark:bg-gray-800 p-6 flex items-center justify-center relative overflow-hidden">
                        <div class="bg-center bg-no-repeat bg-contain w-full h-full transition-transform duration-500 group-hover:scale-105" data-alt="Red aluminum can of Coca-Cola soda" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCXNmZEmVdh19nADwKvvFlJlvQh9yGBMpedsFp1AJ1tl_E8QEySAiJQrm4AZGc_dDEdhOzSk5AAGk1wgUbaTyZTY8vLp_Obo-Q3SnMX2yS5hNjXyUqFZRNiTZrx2JyeT9AGTPja9VWjiYGIvn_K8T0cztJg__TGlvqmhxUEwq0_t_LLFIoPkMEKqUViLHq-AiU_4N_8-vzIZP46wjaVRLyuc8zY0yZd6oRx41BndfR4C79J3ipRd7nF3SsBbphLB6gDp_iBOxaJROs");'>
                        </div>
                    </div>
                    <div class="p-4 flex flex-col gap-2 flex-1">
                        <div class="flex justify-between items-start">
                            <p class="text-xs font-mono text-text-muted">SKU: BV-001-COKE</p>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                                <span class="text-xs font-bold text-green-700 dark:text-green-400">In Stock</span>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg text-text-main dark:text-white leading-tight line-clamp-2" title="Coca-Cola Original Taste 330ml Can">Coca-Cola Original Taste 330ml Can</h3>
                        <div class="mt-auto pt-4 flex flex-col gap-3">
                            <div class="flex items-baseline justify-between border-b border-dashed border-border-light dark:border-border-dark pb-3">
                                <div class="flex flex-col">
                                    <span class="text-2xl font-black text-text-main dark:text-white">Rs 0.85</span>
                                    <span class="text-[10px] uppercase text-text-muted font-bold tracking-wide">Per Unit</span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-bold text-text-main dark:text-gray-300">Rs 20.40</span>
                                    <span class="text-[10px] text-text-muted">Per Carton (24)</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <div class="flex items-center bg-background-light dark:bg-background-dark rounded-lg border border-border-light dark:border-border-dark h-10 w-24 shrink-0">
                                    <button class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-primary transition-colors hover:bg-gray-100 rounded-l-lg">-</button>
                                    <input class="w-full h-full bg-transparent text-center text-sm font-bold focus:ring-0 border-none p-0" type="number" value="24" />
                                    <button class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-primary transition-colors hover:bg-gray-100 rounded-r-lg">+</button>
                                </div>
                                <button class="flex-1 h-10 bg-primary hover:bg-green-500 text-text-main text-sm font-bold rounded-lg transition-colors flex items-center justify-center gap-2 shadow-sm">
                                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product Card 2 -->
                <div class="group flex flex-col bg-white dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-primary/50">
                    <div class="aspect-[4/3] w-full bg-gray-50 dark:bg-gray-800 p-6 flex items-center justify-center relative overflow-hidden">
                        <div class="bg-center bg-no-repeat bg-contain w-full h-full transition-transform duration-500 group-hover:scale-105" data-alt="Pack of Oreo cookies blue packaging" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDa3ZcDhwff6GyM9oh_nZq5GbVJM1mpTmBP54pxyRq0Exp7ZSPv_PJJ0sdxUL8mHzdg9a99uzGWvDRtko-SMBkhvEPhEh4TuJODSuCdqrLpDm2oDRydj8vRI0myxCzVlLqkjXT0pyv1PBzOH4Vnc9yxpfy8Braj5z7EthzBD6SsezTE8CtQEiu2t08Qdqb122zpF7WpFKRv8gc7YvZbBS2uhTCZdkAF13FATzeQ7TSbdq3VjSGOY7iHCEYpifLbtUXUq86MNwL6kzw");'>
                        </div>
                    </div>
                    <div class="p-4 flex flex-col gap-2 flex-1">
                        <div class="flex justify-between items-start">
                            <p class="text-xs font-mono text-text-muted">SKU: SN-OREO-154</p>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                                <span class="text-xs font-bold text-orange-600 dark:text-orange-400">Low Stock</span>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg text-text-main dark:text-white leading-tight line-clamp-2">Oreo Original Sandwich Cookies 154g</h3>
                        <div class="mt-auto pt-4 flex flex-col gap-3">
                            <div class="flex items-baseline justify-between border-b border-dashed border-border-light dark:border-border-dark pb-3">
                                <div class="flex flex-col">
                                    <span class="text-2xl font-black text-text-main dark:text-white">Rs 1.20</span>
                                    <span class="text-[10px] uppercase text-text-muted font-bold tracking-wide">Per Unit</span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-bold text-text-main dark:text-gray-300">Rs 19.20</span>
                                    <span class="text-[10px] text-text-muted">Per Carton (16)</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <div class="flex items-center bg-background-light dark:bg-background-dark rounded-lg border border-border-light dark:border-border-dark h-10 w-24 shrink-0">
                                    <button class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-primary transition-colors hover:bg-gray-100 rounded-l-lg">-</button>
                                    <input class="w-full h-full bg-transparent text-center text-sm font-bold focus:ring-0 border-none p-0" type="number" value="16" />
                                    <button class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-primary transition-colors hover:bg-gray-100 rounded-r-lg">+</button>
                                </div>
                                <button class="flex-1 h-10 bg-primary hover:bg-green-500 text-text-main text-sm font-bold rounded-lg transition-colors flex items-center justify-center gap-2 shadow-sm">
                                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product Card 3 -->
                <div class="group flex flex-col bg-white dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-primary/50 relative">
                    <div class="absolute top-3 left-3 z-10 flex gap-2">
                        <span class="bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded shadow-sm">-15% Off</span>
                    </div>
                    <div class="aspect-[4/3] w-full bg-gray-50 dark:bg-gray-800 p-6 flex items-center justify-center relative overflow-hidden">
                        <div class="bg-center bg-no-repeat bg-contain w-full h-full transition-transform duration-500 group-hover:scale-105" data-alt="Plastic bottle of Dove body wash" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAOki7_hUVVSC6m6B8OODIOGX5lAdj7c7HzunyT607HQYu4xmVyZ4_qsNikohGfjl9H3DoToQPnQs75rzTlGmSgxntZ5X6hPtyEXxFf4ahMuEbAboi2IURceLvMgTvh_eHrlcnNqpJJ4rmhYf1mWEivOn_eXJhJcg7h71b2ahft5kB4zpn2WeTnKb4lEqk7oFqZh9sKlSXxwjvFSjPwzBl5dwQ-Z_fwr-fYh6C6E31rOFifgerUZ25Iz6FyPneCqbVgKj2XqZM9KlI");'>
                        </div>
                    </div>
                    <div class="p-4 flex flex-col gap-2 flex-1">
                        <div class="flex justify-between items-start">
                            <p class="text-xs font-mono text-text-muted">SKU: PC-DOVE-500</p>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-primary"></span>
                                <span class="text-xs font-bold text-green-700 dark:text-green-400">In Stock</span>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg text-text-main dark:text-white leading-tight line-clamp-2">Dove Deeply Nourishing Body Wash 500ml</h3>
                        <div class="mt-auto pt-4 flex flex-col gap-3">
                            <div class="flex items-baseline justify-between border-b border-dashed border-border-light dark:border-border-dark pb-3">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl font-black text-text-main dark:text-white">Rs 4.50</span>
                                        <span class="text-xs text-red-500 line-through font-medium">Rs 5.30</span>
                                    </div>
                                    <span class="text-[10px] uppercase text-text-muted font-bold tracking-wide">Per Unit</span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-bold text-text-main dark:text-gray-300">Rs 54.00</span>
                                    <span class="text-[10px] text-text-muted">Per Carton (12)</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <div class="flex items-center bg-background-light dark:bg-background-dark rounded-lg border border-border-light dark:border-border-dark h-10 w-24 shrink-0">
                                    <button class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-primary transition-colors hover:bg-gray-100 rounded-l-lg">-</button>
                                    <input class="w-full h-full bg-transparent text-center text-sm font-bold focus:ring-0 border-none p-0" type="number" value="12" />
                                    <button class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-primary transition-colors hover:bg-gray-100 rounded-r-lg">+</button>
                                </div>
                                <button class="flex-1 h-10 bg-primary hover:bg-green-500 text-text-main text-sm font-bold rounded-lg transition-colors flex items-center justify-center gap-2 shadow-sm">
                                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product Card 4 -->
                <div class="group flex flex-col bg-white dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-primary/50">
                    <div class="aspect-[4/3] w-full bg-gray-50 dark:bg-gray-800 p-6 flex items-center justify-center relative overflow-hidden">
                        <div class="bg-center bg-no-repeat bg-contain w-full h-full transition-transform duration-500 group-hover:scale-105" data-alt="Pack of Barilla pasta spaghetti" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDk8I3BvPj8xSC804MimBduXpZhXdAgyMmA0ZgmAvXBi3kYIDOKlT_SgSw8ks9NTXd8WvhcZhUwzws5GNHIIu7ZBm-MX6KiMlPfUXiabvx7ExixLrq1SHwrwb3XKr6g2C31kVwB8_XkA52hvUPm-NtL0CFnr1Zl6sl6OvPLfY0vF8J1GBSHV5be4YU2Rngd-A2yh2NCs4bVBlG9fPT_KWHowr6PnhIjJfSdn6wtSsNmq93c_cj6j-7339S8qxZmo71e0fcRQsclKXg");'>
                        </div>
                    </div>
                    <div class="p-4 flex flex-col gap-2 flex-1">
                        <div class="flex justify-between items-start">
                            <p class="text-xs font-mono text-text-muted">SKU: PT-BAR-SPAG</p>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-primary"></span>
                                <span class="text-xs font-bold text-green-700 dark:text-green-400">In Stock</span>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg text-text-main dark:text-white leading-tight line-clamp-2">Barilla Spaghetti No.5 Pasta 500g</h3>
                        <div class="mt-auto pt-4 flex flex-col gap-3">
                            <div class="flex items-baseline justify-between border-b border-dashed border-border-light dark:border-border-dark pb-3">
                                <div class="flex flex-col">
                                    <span class="text-2xl font-black text-text-main dark:text-white">Rs 1.80</span>
                                    <span class="text-[10px] uppercase text-text-muted font-bold tracking-wide">Per Unit</span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-bold text-text-main dark:text-gray-300">Rs 36.00</span>
                                    <span class="text-[10px] text-text-muted">Per Carton (20)</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <div class="flex items-center bg-background-light dark:bg-background-dark rounded-lg border border-border-light dark:border-border-dark h-10 w-24 shrink-0">
                                    <button class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-primary transition-colors hover:bg-gray-100 rounded-l-lg">-</button>
                                    <input class="w-full h-full bg-transparent text-center text-sm font-bold focus:ring-0 border-none p-0" type="number" value="20" />
                                    <button class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-primary transition-colors hover:bg-gray-100 rounded-r-lg">+</button>
                                </div>
                                <button class="flex-1 h-10 bg-primary hover:bg-green-500 text-text-main text-sm font-bold rounded-lg transition-colors flex items-center justify-center gap-2 shadow-sm">
                                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product Card 5 -->
                <div class="group flex flex-col bg-white dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-primary/50">
                    <div class="aspect-[4/3] w-full bg-gray-50 dark:bg-gray-800 p-6 flex items-center justify-center relative overflow-hidden">
                        <div class="bg-center bg-no-repeat bg-contain w-full h-full transition-transform duration-500 group-hover:scale-105" data-alt="Packet of Lays classic potato chips" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuC0_GQGmEaF4oa6vguI2GCG-i-TtN8AuB_e8vQoXNcbY77GmAGL9FRDiRbKrYioHpnIHBAuFt-6GPLk2NbPmPf0nD7cX7e0Tm4uGyVtbtKbzNmDIVYAVfdTDTYyN-spghKXXhObFWP0wCOlw-sUNLpsZ_Xq4q7lZrgo3Ov-tSw_bChXlC-IvawCPOCjKDXMSWdD0Iao3ZTSYOkyBP6svx-6SNycW53VgIYBnx0ftkCCdamknXXOUe6ReI8JvKei7jlaZgSGbzKMrS0");'>
                        </div>
                    </div>
                    <div class="p-4 flex flex-col gap-2 flex-1">
                        <div class="flex justify-between items-start">
                            <p class="text-xs font-mono text-text-muted">SKU: SN-LAYS-CLS</p>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                <span class="text-xs font-bold text-gray-500">Out of Stock</span>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg text-text-main dark:text-white leading-tight line-clamp-2">Lays Classic Salted Chips 180g</h3>
                        <div class="mt-auto pt-4 flex flex-col gap-3">
                            <div class="flex items-baseline justify-between border-b border-dashed border-border-light dark:border-border-dark pb-3">
                                <div class="flex flex-col">
                                    <span class="text-2xl font-black text-text-main dark:text-white">Rs 2.10</span>
                                    <span class="text-[10px] uppercase text-text-muted font-bold tracking-wide">Per Unit</span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-bold text-text-main dark:text-gray-300">Rs 25.20</span>
                                    <span class="text-[10px] text-text-muted">Per Carton (12)</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg border border-border-light dark:border-border-dark h-10 w-24 shrink-0 opacity-50 cursor-not-allowed">
                                    <button class="w-8 h-full flex items-center justify-center text-gray-500" disabled="">-</button>
                                    <input class="w-full h-full bg-transparent text-center text-sm font-bold focus:ring-0 border-none p-0" disabled="" type="number" value="0" />
                                    <button class="w-8 h-full flex items-center justify-center text-gray-500" disabled="">+</button>
                                </div>
                                <button class="flex-1 h-10 bg-gray-200 dark:bg-gray-700 text-gray-500 text-sm font-bold rounded-lg flex items-center justify-center gap-2 cursor-not-allowed" disabled="">
                                    Notify Me
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Product Card 6 -->
                <div class="group flex flex-col bg-white dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-primary/50">
                    <div class="aspect-[4/3] w-full bg-gray-50 dark:bg-gray-800 p-6 flex items-center justify-center relative overflow-hidden">
                        <div class="bg-center bg-no-repeat bg-contain w-full h-full transition-transform duration-500 group-hover:scale-105" data-alt="Carton of almond milk" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDLjMA90_sUNVCtenNPaoJuVAyorc0UgNwXDM8hpXQ_Fmg0bDLItQfVc0sZ96Q9yzC7mZygwMJnWZIgGa2BPave8-DvtI3OYiVgwRhULGMOOAbNIr9kZiiF1QDF_ekT4sX8d-HU8RqYavLfn0dI4r5CTFPtXDrwUD1KLbdO1wJrxxGJKL6G8OouOBLQncWTJ2LQ3AAfr8nZ6xPLzt2O53kA4iK7-H9FQv_88y1Tp4QziUyuhJMicsVg9NYAl-6eEA_UlMpiTgRx3zk");'>
                        </div>
                    </div>
                    <div class="p-4 flex flex-col gap-2 flex-1">
                        <div class="flex justify-between items-start">
                            <p class="text-xs font-mono text-text-muted">SKU: DY-ALM-1L</p>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-primary"></span>
                                <span class="text-xs font-bold text-green-700 dark:text-green-400">In Stock</span>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg text-text-main dark:text-white leading-tight line-clamp-2">Almond Breeze Unsweetened Milk 1L</h3>
                        <div class="mt-auto pt-4 flex flex-col gap-3">
                            <div class="flex items-baseline justify-between border-b border-dashed border-border-light dark:border-border-dark pb-3">
                                <div class="flex flex-col">
                                    <span class="text-2xl font-black text-text-main dark:text-white">Rs 3.25</span>
                                    <span class="text-[10px] uppercase text-text-muted font-bold tracking-wide">Per Unit</span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-bold text-text-main dark:text-gray-300">Rs 32.50</span>
                                    <span class="text-[10px] text-text-muted">Per Carton (10)</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <div class="flex items-center bg-background-light dark:bg-background-dark rounded-lg border border-border-light dark:border-border-dark h-10 w-24 shrink-0">
                                    <button class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-primary transition-colors hover:bg-gray-100 rounded-l-lg">-</button>
                                    <input class="w-full h-full bg-transparent text-center text-sm font-bold focus:ring-0 border-none p-0" type="number" value="10" />
                                    <button class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-primary transition-colors hover:bg-gray-100 rounded-r-lg">+</button>
                                </div>
                                <button class="flex-1 h-10 bg-primary hover:bg-green-500 text-text-main text-sm font-bold rounded-lg transition-colors flex items-center justify-center gap-2 shadow-sm">
                                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pagination -->
            <div class="mt-12 flex items-center justify-center gap-3">
                <button class="flex items-center justify-center w-10 h-10 rounded-lg bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-main dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <button class="w-10 h-10 rounded-lg bg-primary text-text-main font-bold shadow-md">1</button>
                <button class="w-10 h-10 rounded-lg bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-main dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium">2</button>
                <button class="w-10 h-10 rounded-lg bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-main dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium">3</button>
                <span class="text-gray-400">...</span>
                <button class="w-10 h-10 rounded-lg bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-main dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium">10</button>
                <button class="flex items-center justify-center w-10 h-10 rounded-lg bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-main dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
            </div>
        </main>
    </div>
</body>

<script src="../Cart/js/script.js"></script>

</html>