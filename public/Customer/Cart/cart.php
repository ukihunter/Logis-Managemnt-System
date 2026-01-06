<?php
require_once '../../../config/session_Detils.php';
?>

<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Shopping Cart - DistriMgt</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet" />
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Theme Configuration -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#11d452",
                        "primary-dark": "#0eb545",
                        "background-light": "#f6f8f6",
                        "background-dark": "#102216",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a2e22",
                        "text-main": "#0d1b12",
                        "text-secondary": "#4c9a66",
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
    <style type="text/tailwindcss">
        @layer utilities {
            .icon-btn {
                @apply flex items-center justify-center rounded-lg p-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10;
            }
            .input-stepper {
                @apply text-base font-medium leading-normal w-12 p-0 text-center bg-transparent focus:outline-0 focus:ring-0 focus:border-none border-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none text-text-main dark:text-white;
            }
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-text-main dark:text-gray-100 min-h-screen flex flex-col">
    <!-- TopNavBar -->
    <header class="sticky top-0 z-50 w-full border-b border-[#e7f3eb] dark:border-white/10 bg-surface-light/95 dark:bg-surface-dark/95 backdrop-blur-sm px-6 py-3">
        <div class="max-w-[1440px] mx-auto flex items-center justify-between">
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-3 text-text-main dark:text-white">
                    <div class="size-8 bg-primary/70 rounded-lg flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-white">shopping_bag_speed</span>
                    </div>
                    <h2 class="text-lg font-bold leading-tight tracking-tight hidden md:flex">IslandDistro</h2>
                </div>
                <!-- Search Bar -->
                <div class="hidden md:flex items-center w-80 h-10 bg-[#e7f3eb] dark:bg-black/20 rounded-lg group focus-within:ring-2 focus-within:ring-primary/50 transition-all">
                    <div class="pl-3 text-text-secondary dark:text-gray-400">
                        <span class="material-symbols-outlined">search</span>
                    </div>
                    <input class="w-full bg-transparent border-none text-sm text-text-main dark:text-white placeholder:text-text-secondary dark:placeholder:text-gray-500 focus:ring-0 h-full" placeholder="Search products, SKUs..." />
                </div>
            </div>
            <div class="flex items-center gap-6">
                <nav class="hidden lg:flex items-center gap-6">
                    <a class="text-text-main dark:text-gray-200 text-sm font-medium hover:text-primary transition-colors" href="../Dashboard/dashboard.php">Dashboard</a>
                    <a class="text-text-main dark:text-gray-200 text-sm font-bold" href="#">Catalog</a>
                    <a class="text-text-main dark:text-gray-200 text-sm font-medium hover:text-primary transition-colors" href="#">Orders</a>
                    <a class="text-text-main dark:text-gray-200 text-sm font-medium hover:text-primary transition-colors" href="#">Invoices</a>
                </nav>
                <div class="relative flex gap-2 border-l border-[#e7f3eb] dark:border-white/10 pl-6">

                    <!-- Notifications -->
                    <button
                        id="notificationsBtn"
                        class="icon-btn text-text-main dark:text-white relative">

                        <span class="material-symbols-outlined">notifications</span>

                        <!-- red dot -->
                        <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- Notification menu -->
                    <div
                        id="notificationsDropdown"
                        class="hidden absolute right-0 top-12 w-64 bg-white dark:bg-surface-dark
               border border-slate-200 dark:border-slate-800
               rounded-lg shadow-lg z-50">

                        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 font-semibold">
                            Notifications
                        </div>

                        <div class="px-4 py-6 flex flex-col items-center gap-2 text-center text-sm text-text-secondary">
                            <span class="material-symbols-outlined text-3xl text-emerald-400">
                                deceased
                            </span>
                            <span>No notifications</span>
                        </div>
                    </div>

                    <!-- Cart -->
                    <button class="icon-btn text-primary bg-primary/10 dark:bg-primary/20">
                        <span class="material-symbols-outlined">shopping_cart</span>
                    </button>

                </div>

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
    <main class="flex-grow w-full px-4 py-8 md:px-8 lg:px-16 xl:px-32 max-w-[1600px] mx-auto">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 mb-6 text-sm">
            <a class="text-text-secondary hover:text-primary dark:text-gray-400" href="../Dashboard/dashboard.php">Dashboard</a>
            <span class="material-symbols-outlined text-sm text-gray-400">chevron_right</span>
            <a class="text-text-secondary hover:text-primary dark:text-gray-400" href="../Catalog/catalog.php">Catalog</a>
            <span class="material-symbols-outlined text-sm text-gray-400">chevron_right</span>
            <span class="font-semibold text-text-main dark:text-white">Shopping Cart</span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Cart Items -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Page Heading -->
                <div class="flex flex-col gap-1 pb-4 border-b border-gray-200 dark:border-gray-800">
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight text-text-main dark:text-white">Your Cart (2 items)</h1>
                    <p class="text-text-secondary dark:text-gray-400">Review your items for island-wide distribution.</p>
                </div>
                <!-- Cart List -->
                <div class="flex flex-col gap-4">
                    <!-- Item 1 -->
                    <div class="group flex flex-col md:flex-row gap-6 bg-surface-light dark:bg-surface-dark p-5 rounded-xl shadow-sm border border-transparent hover:border-primary/20 transition-all">
                        <div class="relative shrink-0">
                            <div class="bg-gray-100 dark:bg-white/5 rounded-lg w-full md:w-[120px] aspect-square bg-center bg-cover" data-alt="Sack of premium white rice with branding" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCZSrNtcItsSg7e_nZ0SDY4BdbZJcLDQ-K6qOyKDKGohoRCawc7n1vB-LcUay2sLJ8FzU9RonzyMO9zHyfomEevq2rGx1ZKrY3mSN7xc3DbfOpU1FBQUgS134xKyCxGO6b2RqbygPfIh-GGJcqdUJZB2CjQxVVJmRTLL2tUH-iGPxyWSwX0HsdwG4yd_MZS0BIw1ZB4RTsTnpIq5MCgcnjW_uWLwsZlrkQ_2oaP4gZACQLF2ZqZrWxNnjFGbfXfYsQ5MzRXNb1Cjk8");'></div>
                            <div class="absolute -top-2 -left-2 bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">BESTSELLER</div>
                        </div>
                        <div class="flex flex-1 flex-col justify-between gap-4">
                            <div>
                                <div class="flex justify-between items-start">
                                    <h3 class="text-lg font-bold text-text-main dark:text-white">Premium Rice 25kg Sack</h3>
                                    <span class="text-lg font-bold text-text-main dark:text-white">Rs 450.00</span>
                                </div>
                                <p class="text-sm text-text-secondary dark:text-gray-400 mt-1">SKU: RICE-001 • Unit Price: Rs 450.00</p>
                                <div class="flex items-center gap-1.5 mt-2 text-primary font-medium text-xs bg-primary/10 w-fit px-2 py-1 rounded">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    In Stock at Central Warehouse
                                </div>
                            </div>
                            <div class="flex justify-between items-end border-t border-gray-100 dark:border-white/5 pt-4">
                                <button class="text-sm font-medium text-red-500 hover:text-red-600 flex items-center gap-1 transition-colors">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                    <span class="hidden sm:inline">Remove</span>
                                </button>
                                <div class="flex items-center gap-3 bg-[#f0f4f1] dark:bg-black/20 rounded-lg p-1">
                                    <button class="w-8 h-8 flex items-center justify-center rounded bg-white dark:bg-white/10 shadow-sm hover:text-primary transition-colors text-text-main dark:text-white">
                                        <span class="material-symbols-outlined text-sm">remove</span>
                                    </button>
                                    <input class="input-stepper" type="number" value="10" />
                                    <button class="w-8 h-8 flex items-center justify-center rounded bg-white dark:bg-white/10 shadow-sm hover:text-primary transition-colors text-text-main dark:text-white">
                                        <span class="material-symbols-outlined text-sm">add</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Item 2 -->
                    <div class="group flex flex-col md:flex-row gap-6 bg-surface-light dark:bg-surface-dark p-5 rounded-xl shadow-sm border border-transparent hover:border-primary/20 transition-all">
                        <div class="shrink-0">
                            <div class="bg-gray-100 dark:bg-white/5 rounded-lg w-full md:w-[120px] aspect-square bg-center bg-cover" data-alt="Clear plastic bottle of sunflower cooking oil" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCu_uWU592BHT-rTolvjaFpkq4qw1JrxfELn6tV9nqlRsDCIXxJsgvRpQziHDnvlltT6-xs46fGQj2Je698eD75YFZG-pxDyLU_N_cpuREmaxvqMrC2Sioi3-nkT46yeydKMc54wdxX0bYxkDCodtmlYioVQ5GSI3CY90DNYJmQbXa8yyrghRdqMzX5r7K3BDRkpBCD1gtFfuxJjJjLrQyt1sq-7DxRXvR2Hl_9j8W7vAdblKue5KFVbHOtQ72v_iq3PbUIWhXjw-0");'></div>
                        </div>
                        <div class="flex flex-1 flex-col justify-between gap-4">
                            <div>
                                <div class="flex justify-between items-start">
                                    <h3 class="text-lg font-bold text-text-main dark:text-white">Sunflower Oil 5L Case</h3>
                                    <span class="text-lg font-bold text-text-main dark:text-white">Rs 150.00</span>
                                </div>
                                <p class="text-sm text-text-secondary dark:text-gray-400 mt-1">SKU: OIL-552 • Unit Price: Rs 30.00</p>
                                <div class="flex items-center gap-1.5 mt-2 text-text-secondary dark:text-gray-400 font-medium text-xs">
                                    <span class="material-symbols-outlined text-sm text-yellow-500">inventory</span>
                                    Low Stock (Only 12 left)
                                </div>
                            </div>
                            <div class="flex justify-between items-end border-t border-gray-100 dark:border-white/5 pt-4">
                                <button class="text-sm font-medium text-red-500 hover:text-red-600 flex items-center gap-1 transition-colors">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                    <span class="hidden sm:inline">Remove</span>
                                </button>
                                <div class="flex items-center gap-3 bg-[#f0f4f1] dark:bg-black/20 rounded-lg p-1">
                                    <button class="w-8 h-8 flex items-center justify-center rounded bg-white dark:bg-white/10 shadow-sm hover:text-primary transition-colors text-text-main dark:text-white">
                                        <span class="material-symbols-outlined text-sm">remove</span>
                                    </button>
                                    <input class="input-stepper" type="number" value="5" />
                                    <button class="w-8 h-8 flex items-center justify-center rounded bg-white dark:bg-white/10 shadow-sm hover:text-primary transition-colors text-text-main dark:text-white">
                                        <span class="material-symbols-outlined text-sm">add</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Cross Sell -->
                <div class="mt-12">
                    <h3 class="text-xl font-bold mb-6 text-text-main dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">recommend</span>
                        Frequently bought together
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Cross Sell Item 1 -->
                        <div class="bg-surface-light dark:bg-surface-dark p-3 rounded-xl border border-transparent hover:border-gray-200 dark:hover:border-white/10 transition-all cursor-pointer">
                            <div class="aspect-square rounded-lg bg-gray-100 dark:bg-white/5 mb-3 bg-center bg-cover" data-alt="Pack of brown sugar" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAn3Kvsu2_QgIptdeF1clJ0S5oh2VYDp5ECp7iBSrf2Il_M_gOu1SPY83qxnmCN4ItBuuEQMAUew89WAlWz3HgRV6oSitRG3NC435-nN7gu26l1HGO2lWtX4-u_EmEZWtBJRFGUsFksZ2qIwMMbjTd4eGrR8dQrREle7mhzLUKwhLPWOZ8koIMZlNEDR7xyAo1uGUXU6H42-y-L68h7o3bayI-Yk5owPHtbHiSb6rzcVFcTnpLY9EPT5RkE5Lwh0y9vVkeKz7ryOzA");'></div>
                            <p class="font-bold text-sm text-text-main dark:text-white truncate">Brown Sugar 1kg</p>
                            <p class="text-xs text-text-secondary dark:text-gray-400 mb-3">Rs2.50 / unit</p>
                            <button class="w-full py-1.5 rounded-lg border border-[#e7f3eb] dark:border-white/10 text-primary text-xs font-bold hover:bg-primary hover:text-white transition-colors">Add</button>
                        </div>
                        <!-- Cross Sell Item 2 -->
                        <div class="bg-surface-light dark:bg-surface-dark p-3 rounded-xl border border-transparent hover:border-gray-200 dark:hover:border-white/10 transition-all cursor-pointer">
                            <div class="aspect-square rounded-lg bg-gray-100 dark:bg-white/5 mb-3 bg-center bg-cover" data-alt="Canned tuna stack" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDzM0IrVx9dt_KClOpV0D_1JjUiwEJMUl6S6S6_WWMHibllWOPVERiv4j2XK_FbNJeX4z_mprXrIH-iWzKugbc_FoCPX5fuhD1DgIptcBjrhe7fvMB2LT3mfEvHNHvP77j-vEzmDuPS4sfo3Acpr8z5cfnHWfLXC46yBDUIkBXUjN6UU0HBPqW4e2zKJjpaXPojNWCc1RneXohBgSq4yN_pJktY8dQbIkR2orTci2-NWaXtDiu6b5bfKTKya19vEYkJKMP5OT1Zuj8");'></div>
                            <p class="font-bold text-sm text-text-main dark:text-white truncate">Canned Tuna 180g</p>
                            <p class="text-xs text-text-secondary dark:text-gray-400 mb-3">Rs 1.80 / unit</p>
                            <button class="w-full py-1.5 rounded-lg border border-[#e7f3eb] dark:border-white/10 text-primary text-xs font-bold hover:bg-primary hover:text-white transition-colors">Add</button>
                        </div>
                        <!-- Cross Sell Item 3 -->
                        <div class="bg-surface-light dark:bg-surface-dark p-3 rounded-xl border border-transparent hover:border-gray-200 dark:hover:border-white/10 transition-all cursor-pointer">
                            <div class="aspect-square rounded-lg bg-gray-100 dark:bg-white/5 mb-3 bg-center bg-cover" data-alt="Red lentils in a bowl" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDvmodpS-w9q-3jLv8gt0SUO9hD41mKcEFPt2Pyfnbuv1-OjDUGIXqkR6Q2edDm_CiV3aG309PuZQfvrPmsCY9jIvu-c0M7vIlVXC653xAnSjAgvFdHl_oMDfJZpN-GZ6QV4OqNLs8agB8SJ5Lz5q9G8wJf6E__fSyXZHBiOP77hA7TjY0zjrLg93_CCSQDr5ymBBQ9QVq9PS6YWLDadeqXSHjVXRtvKh-ZW4r_o8RcFfl9IS_pvEFQQG1KsxaLIF6j3HoLl0Pk1aA");'></div>
                            <p class="font-bold text-sm text-text-main dark:text-white truncate">Red Lentils 500g</p>
                            <p class="text-xs text-text-secondary dark:text-gray-400 mb-3">Rs 3.20 / unit</p>
                            <button class="w-full py-1.5 rounded-lg border border-[#e7f3eb] dark:border-white/10 text-primary text-xs font-bold hover:bg-primary hover:text-white transition-colors">Add</button>
                        </div>
                        <!-- Cross Sell Item 4 -->
                        <div class="bg-surface-light dark:bg-surface-dark p-3 rounded-xl border border-transparent hover:border-gray-200 dark:hover:border-white/10 transition-all cursor-pointer">
                            <div class="aspect-square rounded-lg bg-gray-100 dark:bg-white/5 mb-3 bg-center bg-cover" data-alt="Pack of pasta spaghetti" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAOMEa3U9Py40K6wGqCa84G83119e66yPRT3Ibig5aSCOYJ-TFR6ekDTyzijjHPk337sZP4XPQamq8t924-Sg_2fFiuRCBt-Dpp_-F-9MZwR2nASJwYOhE_QVy753a23TtRFMIvz-LzhcnojarXxqKYaUmMGW6zgii0pkyHRaj39wDx9uKpONAnZ3SRl2rLIF6siOnBNS0x38mZD18LqRsOscAMq1JYyIRcXfY2hP1oj_osEgvN6evP8ajX0U1ryOJn2tmBxDbnfM4");'></div>
                            <p class="font-bold text-sm text-text-main dark:text-white truncate">Spaghetti 500g</p>
                            <p class="text-xs text-text-secondary dark:text-gray-400 mb-3">Rs 1.20 / unit</p>
                            <button class="w-full py-1.5 rounded-lg border border-[#e7f3eb] dark:border-white/10 text-primary text-xs font-bold hover:bg-primary hover:text-white transition-colors">Add</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right Column: Summary -->
            <div class="lg:col-span-4">
                <div class="sticky top-24 space-y-4">
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-[#e7f3eb] dark:border-white/5 overflow-hidden">
                        <div class="p-6">
                            <h2 class="text-xl font-bold mb-6 text-text-main dark:text-white">Order Summary</h2>
                            <!-- Costs Breakdown -->
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-secondary dark:text-gray-400">Subtotal (2 items)</span>
                                    <span class="font-semibold text-text-main dark:text-white">Rs 600.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-secondary dark:text-gray-400">Distribution Fee</span>
                                    <span class="font-semibold text-text-main dark:text-white">Rs 25.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-secondary dark:text-gray-400">Tax (8%)</span>
                                    <span class="font-semibold text-text-main dark:text-white">Rs 48.00</span>
                                </div>
                            </div>
                            <div class="border-t border-dashed border-gray-200 dark:border-gray-700 my-4"></div>
                            <!-- Total -->
                            <div class="flex justify-between items-end mb-6">
                                <span class="text-base font-bold text-text-main dark:text-white">Total</span>
                                <div class="text-right">
                                    <span class="block text-2xl font-black text-primary tracking-tight">Rs 673.00</span>
                                    <span class="text-xs text-text-secondary dark:text-gray-500">LKR, inclusive of all taxes</span>
                                </div>
                            </div>
                            <!-- Promo Code -->
                            <div class="mb-6">
                                <label class="block text-xs font-medium text-text-secondary dark:text-gray-400 mb-2">Promotional Code</label>
                                <div class="flex gap-2">
                                    <input class="flex-1 rounded-lg border-none bg-gray-50 dark:bg-black/20 text-sm focus:ring-1 focus:ring-primary dark:text-white" placeholder="Enter code" type="text" />
                                    <button class="px-4 py-2 bg-gray-200 dark:bg-white/10 text-text-main dark:text-white text-sm font-bold rounded-lg hover:bg-gray-300 dark:hover:bg-white/20 transition-colors">Apply</button>
                                </div>
                            </div>
                            <!-- Checkout Button -->
                            <button class="w-full bg-primary hover:bg-primary-dark text-white font-bold text-lg py-4 rounded-xl shadow-lg shadow-primary/30 transition-all transform active:scale-[0.99] flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">lock</span>
                                Secure Checkout
                            </button>
                            <!-- Trust Badges -->
                            <div class="mt-6 flex justify-center gap-4 opacity-60 grayscale">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="material-symbols-outlined text-2xl">verified_user</span>
                                    <span class="text-[10px] text-center font-medium">Secure Payment</span>
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <span class="material-symbols-outlined text-2xl">local_shipping</span>
                                    <span class="text-[10px] text-center font-medium">Fast Delivery</span>
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <span class="material-symbols-outlined text-2xl">support_agent</span>
                                    <span class="text-[10px] text-center font-medium">24/7 Support</span>
                                </div>
                            </div>
                        </div>
                        <!-- Note -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 px-6 py-3 border-t border-blue-100 dark:border-blue-800/30">
                            <p class="text-xs text-blue-800 dark:text-blue-300 text-center leading-relaxed">
                                <span class="font-bold">Note:</span> Please contact Island Distro if you have any problem.
                            </p>
                        </div>
                    </div>
                    <a class="block text-center text-sm font-medium text-green-600 hover:text-text-secondary dark:text-gray-500 transition-colors" href="../Catalog/catalog.php">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </main>
    <footer class="mt-auto border-t border-[#e7f3eb] dark:border-white/5 bg-surface-light dark:bg-surface-dark py-12">
        <div class="max-w-[1440px] mx-auto px-6 text-center">
            <p class="text-text-secondary dark:text-gray-500 text-sm">© <?php echo date("Y"); ?> DistriMgt Distribution Systems. All rights reserved.</p>
        </div>
    </footer>
</body>


<script src="../Cart/js/script.js"></script>

</html>