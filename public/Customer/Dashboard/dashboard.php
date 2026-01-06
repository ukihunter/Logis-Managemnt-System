<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: ../../login/login.php');
    exit;
}

$business_name = $_SESSION['business_name'] ?? 'Customer';
$full_name = $_SESSION['full_name'] ?? 'User';
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>IslandDistro Portal Dashboard</title>
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
                        "surface-dark": "#1a2e22", // Slightly lighter than bg-dark
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
</head>

<body class="bg-background-light dark:bg-background-dark text-text-main dark:text-white font-display antialiased transition-colors duration-200">
    <div class="flex min-h-screen w-full flex-col overflow-x-hidden">
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-50 w-full border-b border-slate-200 dark:border-slate-800 bg-background-light/95 dark:bg-background-dark/95 backdrop-blur-sm">
            <div class="px-4 md:px-10 py-3 flex items-center justify-between gap-4">
                <div class="flex items-center gap-8">
                    <!-- Logo & Brand -->
                    <div class="flex items-center gap-3">
                        <div class="size-8 text-primary">
                            <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd" d="M24 0.757355L47.2426 24L24 47.2426L0.757355 24L24 0.757355ZM21 35.7574V12.2426L9.24264 24L21 35.7574Z" fill="currentColor" fill-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold tracking-tight hidden sm:block">IslandDistro</h2>
                    </div>
                    <!-- Search Bar -->
                    <div class="hidden md:flex items-center w-64 lg:w-96">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-text-secondary dark:text-emerald-400">
                                <span class="material-symbols-outlined text-[20px]">search</span>
                            </div>
                            <input class="block w-full rounded-lg border-none bg-slate-200/50 dark:bg-white/10 py-2.5 pl-10 pr-3 text-sm placeholder:text-text-secondary dark:placeholder:text-emerald-400/70 focus:ring-2 focus:ring-primary focus:bg-white dark:focus:bg-black/20" placeholder="Search products, orders..." type="text" />
                        </div>
                    </div>
                </div>
                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <!-- Nav Links (Desktop) -->
                    <nav class="hidden lg:flex items-center gap-6 mr-4">
                        <a class="text-sm font-bold text-primary" href="#">Dashboard</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors" href="../Catalog/catalog.php">Catalog</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors" href="#">Orders</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors" href="#">Invoices</a>
                    </nav>
                    <!-- Action Icons -->
                    <div class="flex gap-2">
                        <button class="flex items-center justify-center size-10 rounded-lg bg-slate-200/50 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">notifications</span>
                        </button>
                        <button class="flex items-center justify-center size-10 rounded-lg bg-slate-200/50 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 transition-colors relative">
                            <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
                            <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full"></span>
                        </button>
                        <button class="hidden sm:flex h-10 px-4 items-center justify-center rounded-lg bg-primary text-black font-bold text-sm hover:brightness-110 transition-all">
                            <span class="mr-2 material-symbols-outlined text-[18px]">add</span> New Order
                        </button>
                        <div class="size-10 rounded-full bg-slate-300 dark:bg-slate-700 bg-cover bg-center ml-2 border-2 border-slate-100 dark:border-slate-800" data-alt="User profile avatar showing a store logo or generic user icon" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuD6z3y47DuCRcPJllBKIiAhuhtpN2_42Z9uxlOYlGHOU4jeQTvBLaaxjdKHmjXogo5XI-sBMnoNOWMgeDToQ95Fa0OiKUb9fhCu0lgqjbWSrHQ-ieYR3FExD0rzZKE9tJ6HSepBmObRcbQxodXApJ7EJ0T9sCH1g7P21qDrFil2VdMAOC_o1zC_m623BuqRtA0YXQNNk00YsIDsLJDAwgK9bLehPWLnV5ttF-NOpfFnPh3k5urWISRG8dS24xnHe33X2WRfZh3wcqo");'></div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main Content -->
        <main class="flex-1 px-4 md:px-10 py-8 w-full max-w-[1440px] mx-auto">
            <!-- Page Heading -->
            <div class="mb-8">
                <h1 class="text-2xl md:text-4xl font-bold tracking-tight mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['business_name'] ?? 'Customer'); ?>
                </h1>
                <p class="text-green-500 md:text-sm font-semibold dark:text-emerald-400 ">Here is your account overview for today, <?php echo date('F jS'); ?></p>
            </div>
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col gap-1">
                    <p class="text-sm font-medium text-text-secondary dark:text-emerald-400">Active Orders</p>
                    <div class="flex items-baseline justify-between">
                        <p class="text-3xl font-bold">0</p>
                        <span class="bg-primary/20 text-emerald-800 dark:text-primary text-xs font-bold px-2 py-1 rounded-full">0 Arriving</span>
                    </div>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col gap-1">
                    <p class="text-sm font-medium text-text-secondary dark:text-emerald-400">Pending Payment</p>
                    <p class="text-3xl font-bold">Rs.0</p>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col gap-1">
                    <p class="text-sm font-medium text-text-secondary dark:text-emerald-400">Total Spend YTD</p>
                    <p class="text-3xl font-bold">Rs.0</p>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col gap-1">
                    <p class="text-sm font-medium text-text-secondary dark:text-emerald-400">Loyalty Points</p>
                    <div class="flex items-baseline justify-between">
                        <p class="text-3xl font-bold">0</p>
                        <span class="text-xs font-bold text-primary cursor-pointer hover:underline">Redeem</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
                <!-- Left Column (Track Delivery & Orders) -->
                <div class="lg:col-span-8 flex flex-col gap-8">
                    <!-- Track Delivery Widget -->
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold">Track my Delivery</h2>
                            <a class="text-primary text-sm font-bold hover:underline" href="#">View All</a>
                        </div>
                        <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
                            <div class="flex flex-col md:flex-row">
                                <!-- Map Image -->
                                <div class="w-full md:w-1/3 h-48 md:h-auto bg-slate-200 dark:bg-slate-800 relative">
                                    <div class="absolute inset-0 bg-cover bg-center opacity-80" data-alt="Map showing delivery route in Kingston" data-location="Kingston" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAciHolyBMnId5Pk6xtowzRZhgUEfPI_8azy3xn2Bxx8nMhLWsa9tDtNlIZg6khtZwF-N7NB-Vyuzl-oFMKnd_yA5hkkOYkFQrohTgE3XsRo5wxwXJg50WZcDQ9GDPpLqiYd_H7yckTX8Dn8AdyDwmWah5yBGeDcgx7iHFojQgKcps1B9fbOcrggSoZQXYROEWElqQcLgUsU8jTyaupBImmq2rJxT1942sq1tm3y154Vq8WplpiV5DP2vLdhezJVx62jim4N9V5MrQ");'></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="bg-primary text-white p-2 rounded-full shadow-lg">
                                            <span class="material-symbols-outlined block">local_shipping</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Delivery Details -->
                                <div class="p-6 flex flex-col justify-center flex-1">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-lg font-bold mb-1">Order #0001 - Out for Delivery</h3>
                                            <p class="text-text-secondary dark:text-emerald-400 text-sm">Processed at Distribution Center A</p>
                                        </div>
                                        <span class="px-3 py-1 bg-primary text-black text-xs font-bold rounded-full animate-pulse">Live</span>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="relative w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full mb-4">
                                        <div class="absolute left-0 top-0 h-full w-3/4 bg-primary rounded-full"></div>
                                    </div>
                                    <div class="flex flex-wrap items-end justify-between gap-4">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2 text-sm font-medium">
                                                <span class="material-symbols-outlined text-[18px] text-primary">schedule</span>
                                                <span>Est. arrival: 2:00 PM today</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-sm text-text-secondary dark:text-emerald-400">
                                                <span class="material-symbols-outlined text-[18px]">person</span>
                                                <span>Driver: Mahinda</span>
                                            </div>
                                        </div>
                                        <button class="px-4 py-2 bg-slate-200 dark:bg-white/10 text-sm font-bold rounded-lg hover:bg-green-500 hover:text-white dark:hover:bg-white/20 transition-colors">
                                            Contact Driver
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Recent Orders Table -->
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold">Recent Orders</h2>
                            <a class="text-primary text-sm font-bold hover:underline" href="#">View All</a>
                        </div>
                        <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm overflow-x-auto">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-slate-50 dark:bg-white/5 border-b border-slate-200 dark:border-slate-800">
                                    <tr>
                                        <th class="px-6 py-4 font-bold text-text-secondary dark:text-emerald-400">Order ID</th>
                                        <th class="px-6 py-4 font-bold text-text-secondary dark:text-emerald-400">Date</th>
                                        <th class="px-6 py-4 font-bold text-text-secondary dark:text-emerald-400">Status</th>
                                        <th class="px-6 py-4 font-bold text-text-secondary dark:text-emerald-400">Total</th>
                                        <th class="px-6 py-4 font-bold text-text-secondary dark:text-emerald-400 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 font-medium">#0000</td>
                                        <td class="px-6 py-4 text-text-secondary dark:text-emerald-400">jan , 2026</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                <span class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"></span> Delivered
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-bold">Rs.0</td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-primary hover:text-green-400 font-bold text-sm">Reorder</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 font-medium">#0001</td>
                                        <td class="px-6 py-4 text-text-secondary dark:text-emerald-400">jan , 2026</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                <span class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"></span> Delivered
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-bold">Rs.0</td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-primary hover:text-green-400 font-bold text-sm">Reorder</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 font-medium">#0002</td>
                                        <td class="px-6 py-4 text-text-secondary dark:text-emerald-400">jan , 2026</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                <span class="size-1.5 rounded-full bg-yellow-600 dark:bg-yellow-400"></span> Processing
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-bold">Rs.0</td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-primary hover:text-green-400 font-bold text-sm">View</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 font-medium">#0002</td>
                                        <td class="px-6 py-4 text-text-secondary dark:text-emerald-400">jan , 2026</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                <span class="size-1.5 rounded-full bg-yellow-600 dark:bg-yellow-400"></span> Processing
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-bold">Rs.0</td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-primary hover:text-green-400 font-bold text-sm">View</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 font-medium">#0003</td>
                                        <td class="px-6 py-4 text-text-secondary dark:text-emerald-400">jan , 2026</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                <span class="size-1.5 rounded-full bg-yellow-600 dark:bg-yellow-400"></span> Processing
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-bold">Rs.0</td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-primary hover:text-green-400 font-bold text-sm">View</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 font-medium">#0004</td>
                                        <td class="px-6 py-4 text-text-secondary dark:text-emerald-400">jan , 2026</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                <span class="size-1.5 rounded-full bg-yellow-600 dark:bg-yellow-400"></span> Processing
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-bold">Rs.0</td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-primary hover:text-green-400 font-bold text-sm">View</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Right Column (Quick Actions & Promos) -->
                <div class="lg:col-span-4 flex flex-col gap-8">
                    <!-- Quick Categories -->
                    <div class="flex flex-col gap-4">
                        <h2 class="text-xl font-bold">Quick Categories</h2>
                        <div class="grid grid-cols-2 gap-3">
                            <button class="flex flex-col items-center justify-center p-4 rounded-xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 hover:border-primary dark:hover:border-primary transition-all group">
                                <span class="material-symbols-outlined text-3xl mb-2 text-text-secondary dark:text-emerald-400 group-hover:text-primary transition-colors">toys</span>
                                <span class="text-sm font-bold">Toys</span>
                            </button>
                            <button class="flex flex-col items-center justify-center p-4 rounded-xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 hover:border-primary dark:hover:border-primary transition-all group">
                                <span class="material-symbols-outlined text-3xl mb-2 text-text-secondary dark:text-emerald-400 group-hover:text-primary transition-colors">apparel</span>
                                <span class="text-sm font-bold">Cloth</span>
                            </button>
                            <button class="flex flex-col items-center justify-center p-4 rounded-xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 hover:border-primary dark:hover:border-primary transition-all group">
                                <span class="material-symbols-outlined text-3xl mb-2 text-text-secondary dark:text-emerald-400 group-hover:text-primary transition-colors">devices_other</span>
                                <span class="text-sm font-bold">Dry Goods</span>
                            </button>
                            <button class="flex flex-col items-center justify-center p-4 rounded-xl bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 hover:border-primary dark:hover:border-primary transition-all group">
                                <span class="material-symbols-outlined text-3xl mb-2 text-text-secondary dark:text-emerald-400 group-hover:text-primary transition-colors">clean_hands</span>
                                <span class="text-sm font-bold">Cleaning</span>
                            </button>
                        </div>
                    </div>
                    <!-- Personalized Promotion -->
                    <div class="flex flex-col gap-4 grow">
                        <h2 class="text-xl font-bold">Exclusive for You</h2>
                        <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm flex flex-col h-full">
                            <div class="h-40 bg-cover bg-center" data-alt="Gradient background representing a special discount offer" style='background-image: url("https://static.vecteezy.com/system/resources/thumbnails/060/192/542/small/discount-a-red-sale-label-with-large-50-off-text-attached-to-a-clothing-hanger-free-photo.jpg");'></div>
                            <div class="p-5 flex flex-col grow">
                                <div class="mb-4">
                                    <span class="text-xs font-bold tracking-wider uppercase text-primary mb-1 block">Limited Time</span>
                                    <h3 class="text-xl font-black leading-tight mb-2">15% Off Bulk Beverage Cases</h3>
                                    <p class="text-sm text-text-secondary dark:text-emerald-400">Stock up for the holiday season. Offer valid until Oct 30th for all wholesale partners.</p>
                                </div>
                                <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800">
                                    <button class="w-full py-2.5 rounded-lg bg-black dark:bg-white text-white dark:text-black font-bold text-sm hover:opacity-90 transition-opacity">
                                        Shop Deal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Mobile Bottom Nav Spacer -->
            <div class="h-16 lg:hidden"></div>
        </main>
        <!-- Mobile Bottom Nav -->
        <div class="fixed bottom-0 left-0 w-full bg-surface-light dark:bg-surface-dark border-t border-slate-200 dark:border-slate-800 flex lg:hidden justify-around py-3 px-2 z-40">
            <button class="flex flex-col items-center gap-1 text-primary">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-[10px] font-bold">Home</span>
            </button>
            <button class="flex flex-col items-center gap-1 text-text-secondary dark:text-emerald-400">
                <span class="material-symbols-outlined">manage_search</span>
                <span class="text-[10px] font-medium">Catalog</span>
            </button>
            <button class="flex flex-col items-center gap-1 text-text-secondary dark:text-emerald-400">
                <span class="material-symbols-outlined">receipt_long</span>
                <span class="text-[10px] font-medium">Orders</span>
            </button>
            <button class="flex flex-col items-center gap-1 text-text-secondary dark:text-emerald-400">
                <span class="material-symbols-outlined">person</span>
                <span class="text-[10px] font-medium">Account</span>
            </button>
        </div>
    </div>
</body>

</html>