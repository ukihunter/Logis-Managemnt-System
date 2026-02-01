<!-- db connection and the session handling -->
<?php
require_once '../../../config/session_Detils.php';
require_once 'dashboard_handler.php';
?>

<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <!--title -->
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
                            <span class="material-symbols-outlined">shopping_bag_speed</span>
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
                    <nav class="hidden lg:flex items-center gap-6 mr-4 font-medium">
                        <a class="text-sm font-bold text-primary" href="#">Dashboard</a>
                        <a class="text-sm font-bold hover:text-primary transition-colors" href="../Catalog/catalog.php">Catalog</a>
                        <a class="text-sm font-bold hover:text-primary transition-colors" href="../Orders/order.php">Orders</a>
                        <!--    <a class="text-sm font-medium hover:text-primary transition-colors" href="#">Invoices</a>-->
                    </nav>
                    <!-- Action Icons -->
                    <div class="flex gap-2">
                        <div class="relative">
                            <button
                                id="notificationsBtn"
                                class="flex items-center justify-center size-10 rounded-lg bg-slate-200/50 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 transition-colors">

                                <span class="material-symbols-outlined text-[20px]">notifications</span>
                            </button>
                            <!-- Notification drop down menu -->
                            <div
                                id="notificationsDropdown"
                                class="hidden absolute right-0 mt-5 w-48 bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-lg shadow-lg overflow-hidden z-50">

                                <div class="px-4 py-3 border-b border-slate-200 text-text-secondary dark:border-slate-800 flex flex-col items-center gap-1">
                                    <span class="material-symbols-outlined text-3xl">
                                        deceased
                                    </span>
                                    <span class="text-sm text-text-secondary dark:text-emerald-400/70">
                                        No notifications
                                    </span>
                                </div>

                            </div>
                        </div>
                        <!-- Navigation buttons for the catalog and the cart -->
                        <button onclick="location.href='../../Customer/Cart/cart.php'" class="flex items-center justify-center size-10 rounded-lg bg-slate-200/50 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 transition-colors relative">
                            <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
                            <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full"></span>
                        </button>
                        <button onclick="location.href='../../Customer/Catalog/catalog.php'" class="hidden sm:flex h-10 px-4 items-center justify-center rounded-lg bg-primary text-black font-bold text-sm hover:brightness-110 transition-all">
                            <span class="mr-2 material-symbols-outlined text-[18px]">add</span> New Order
                        </button>
                        <!-- Profile Dropdown -->
                        <div class="relative ml-2">
                            <button id="profileMenuBtn" class="size-10 rounded-full bg-slate-300 dark:bg-slate-700 bg-cover bg-center border-2 border-slate-100 dark:border-slate-800 hover:border-primary dark:hover:border-primary transition-colors" data-alt="User profile avatar showing a store logo or generic user icon" style='background-image: url("https://ui-avatars.com/api/?&background=0D8ABC&color=fff&name=<?php echo urlencode($business_name); ?>");'></button>
                            <!-- Dropdown Menu -->
                            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-lg shadow-lg overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800">
                                    <p class="text-sm font-bold"><?php echo htmlspecialchars($full_name); ?></p>
                                    <p class="text-xs text-text-secondary dark:text-emerald-400"><?php echo htmlspecialchars($business_name); ?></p>
                                </div>
                                <!--  <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">person</span>
                                    <span class="text-sm font-medium">Edit Profile</span>
                                </a> -->
                                <a href="../../logout/logout.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-100 dark:hover:bg-white/5 transition-colors text-red-600 dark:text-red-400">
                                    <span class="material-symbols-outlined text-[20px]">logout</span>
                                    <span class="text-sm font-medium">Logout</span>
                                </a>
                            </div>
                        </div>
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
                        <!--fetch data from the db  -->
                        <p class="text-3xl font-bold"><?php echo $stats['active_orders']; ?></p>
                        <span class="bg-primary/20 text-emerald-800 dark:text-primary text-xs font-bold px-2 py-1 rounded-full"><?php echo $stats['arriving_today']; ?> Arriving</span>
                    </div>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col gap-1">
                    <p class="text-sm font-medium text-text-secondary dark:text-emerald-400">Completed Orders</p>
                    <!--fetch data from the db  -->
                    <p class="text-3xl font-bold"><?php echo $stats['completed_orders']; ?></p>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col gap-1">
                    <p class="text-sm font-medium text-text-secondary dark:text-emerald-400">Total Spend YTD</p>
                    <!--fetch data from the db  -->
                    <p class="text-3xl font-bold">Rs.<?php echo number_format($stats['total_spend_ytd'], 2); ?></p>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col gap-1">
                    <p class="text-sm font-medium text-text-secondary dark:text-emerald-400">Loyalty Points</p>
                    <div class="flex items-baseline justify-between">
                        <!--fetch data from the db  -->
                        <p class="text-3xl font-bold"><?php echo $stats['loyalty_points']; ?></p>
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
                            <a class="text-primary text-sm font-bold hover:underline" href="../Orders/order.php">View All</a>
                        </div>
                        <!--fetch data from the db  -->
                        <?php if ($tracking_order): ?>
                            <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
                                <div class="flex flex-col md:flex-row">
                                    <!-- Map Image -->
                                    <div class="w-full md:w-1/3 h-48 md:h-auto bg-slate-200 dark:bg-slate-800 relative">
                                        <div class="absolute inset-0 bg-cover bg-center opacity-80" data-alt="Map showing delivery route" data-location="<?php echo htmlspecialchars($tracking_order['shipping_city'] ?? 'Kingston'); ?>" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAciHolyBMnId5Pk6xtowzRZhgUEfPI_8azy3xn2Bxx8nMhLWsa9tDtNlIZg6khtZwF-N7NB-Vyuzl-oFMKnd_yA5hkkOYkFQrohTgE3XsRo5wxwXJg50WZcDQ9GDPpLqiYd_H7yckTX8Dn8AdyDwmWah5yBGeDcgx7iHFojQgKcps1B9fbOcrggSoZQXYROEWElqQcLgUsU8jTyaupBImmq2rJxT1942sq1tm3y154Vq8WplpiV5DP2vLdhezJVx62jim4N9V5MrQ");'></div>
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
                                                <!--fetch data from the db acording to the order number  -->
                                                <h3 class="text-lg font-bold mb-1">Order <?php echo htmlspecialchars($tracking_order['order_number']); ?> -
                                                    <?php
                                                    //delivary status
                                                    // color will define acording to the status 
                                                    $status_text = ucfirst($tracking_order['order_status']);
                                                    if ($tracking_order['order_status'] === 'shipped') {
                                                        echo 'Out for Delivery';
                                                    } elseif ($tracking_order['order_status'] === 'packed') {
                                                        echo 'Ready to Ship';
                                                    } else {
                                                        echo $status_text;
                                                    }
                                                    ?></h3>
                                                <p class="text-text-secondary dark:text-emerald-400 text-sm">
                                                    <?php
                                                    if ($tracking_order['order_status'] === 'shipped') {
                                                        echo 'En route to your location';
                                                    } elseif ($tracking_order['order_status'] === 'packed') {
                                                        echo 'Package is packed and ready';
                                                    } else {
                                                        echo 'Being processed at distribution center';
                                                    }
                                                    ?>
                                                </p>
                                            </div>
                                            <?php if ($tracking_order['order_status'] === 'shipped'): ?>
                                                <span class="px-3 py-1 bg-primary text-black text-xs font-bold rounded-full animate-pulse">Live</span>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Progress Bar -->
                                        <!-- This will indicate the progress of the delivery -->
                                        <div class="relative w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full mb-4">
                                            <div class="absolute left-0 top-0 h-full <?php
                                                                                        if ($tracking_order['order_status'] === 'shipped') echo 'w-3/4';
                                                                                        elseif ($tracking_order['order_status'] === 'packed') echo 'w-1/2';
                                                                                        else echo 'w-1/4';
                                                                                        ?> bg-primary rounded-full"></div>
                                        </div>
                                        <div class="flex flex-wrap items-end justify-between gap-4">
                                            <div class="flex flex-col gap-1">
                                                <!-- Estimated Arrival & Driver Info -->

                                                <?php if ($tracking_order['order_status'] === 'shipped'): ?>
                                                    <div class="flex items-center gap-2 text-sm font-medium">
                                                        <span class="material-symbols-outlined text-[18px] text-primary">schedule</span>
                                                        <span>Est. arrival: Today</span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($tracking_order['driver_name']): ?>
                                                    <div class="flex items-center gap-2 text-sm text-text-secondary dark:text-emerald-400">
                                                        <span class="material-symbols-outlined text-[18px]">person</span>
                                                        <span>Driver: <?php echo htmlspecialchars($tracking_order['driver_name']); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($tracking_order['driver_phone'] && $tracking_order['order_status'] === 'shipped'): ?>
                                                <a href="tel:<?php echo htmlspecialchars($tracking_order['driver_phone']); ?>" class="px-4 py-2 bg-slate-200 dark:bg-white/10 text-sm font-bold rounded-lg hover:bg-green-500 hover:text-white dark:hover:bg-white/20 transition-colors">
                                                    Contact Driver
                                                </a>
                                            <?php else: ?>
                                                <button class="px-4 py-2 bg-slate-200 dark:bg-white/10 text-sm font-bold rounded-lg hover:bg-green-500 hover:text-white dark:hover:bg-white/20 transition-colors" onclick="location.href='../Orders/order.php'">
                                                    View Details
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--if there is nothing in the database it will show this  -->
                        <?php else: ?>
                            <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm p-8 text-center">
                                <span class="material-symbols-outlined text-5xl text-text-secondary dark:text-emerald-400 mb-3 block">local_shipping</span>
                                <p class="text-text-secondary dark:text-emerald-400 mb-4">No active deliveries at the moment</p>
                                <button class="px-4 py-2 bg-primary text-black font-bold text-sm rounded-lg hover:brightness-110 transition-all" onclick="location.href='../Catalog/catalog.php'">
                                    Browse Catalog
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Recent Orders Table -->
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold">Recent Orders</h2>
                            <a class="text-primary text-sm font-bold hover:underline" href="../Orders/order.php">View All</a>
                        </div>
                        <!--fetch data from the db  -->
                        <div class="bg-surface-light dark:bg-surface-dark border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm overflow-x-auto">
                            <?php if (count($recent_orders) > 0): ?>
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
                                        <?php foreach ($recent_orders as $order):
                                            // Status badge configuration
                                            $status_config = [
                                                'delivered' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-800 dark:text-green-400', 'dot' => 'bg-green-600 dark:bg-green-400', 'label' => 'Delivered'],
                                                'shipped' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-800 dark:text-blue-400', 'dot' => 'bg-blue-600 dark:bg-blue-400', 'label' => 'Shipped'],
                                                'packed' => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-800 dark:text-purple-400', 'dot' => 'bg-purple-600 dark:bg-purple-400', 'label' => 'Packed'],
                                                'processing' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'text' => 'text-yellow-800 dark:text-yellow-400', 'dot' => 'bg-yellow-600 dark:bg-yellow-400', 'label' => 'Processing'],
                                                'pending' => ['bg' => 'bg-gray-100 dark:bg-gray-900/30', 'text' => 'text-gray-800 dark:text-gray-400', 'dot' => 'bg-gray-600 dark:bg-gray-400', 'label' => 'Pending'],
                                                'cancelled' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-800 dark:text-red-400', 'dot' => 'bg-red-600 dark:bg-red-400', 'label' => 'Cancelled']
                                            ];
                                            $status = $status_config[$order['order_status']] ?? $status_config['pending'];
                                        ?>
                                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                                <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($order['order_number']); ?></td>
                                                <td class="px-6 py-4 text-text-secondary dark:text-emerald-400"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                                <td class="px-6 py-4">
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold <?php echo $status['bg'] . ' ' . $status['text']; ?>">
                                                        <span class="size-1.5 rounded-full <?php echo $status['dot']; ?>"></span> <?php echo $status['label']; ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 font-bold">Rs.<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td class="px-6 py-4 text-right">
                                                    <?php if ($order['order_status'] === 'delivered'): ?>
                                                        <button class="text-primary hover:text-green-400 font-bold text-sm" onclick="location.href='../Orders/order.php'">Reorder</button>
                                                    <?php else: ?>
                                                        <button class="text-primary hover:text-green-400 font-bold text-sm" onclick="location.href='../Orders/order.php'">View</button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <!-- if not it will show this to place your first order -->
                                <div class="p-8 text-center">
                                    <span class="material-symbols-outlined text-5xl text-text-secondary dark:text-emerald-400 mb-3 block">receipt_long</span>
                                    <p class="text-text-secondary dark:text-emerald-400 mb-4">No orders yet</p>
                                    <button class="px-4 py-2 bg-primary text-black font-bold text-sm rounded-lg hover:brightness-110 transition-all" onclick="location.href='../Catalog/catalog.php'">
                                        Place Your First Order
                                    </button>
                                </div>
                            <?php endif; ?>
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
                                <span class="text-sm font-bold">Electronics</span>
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
            <a href="dashboard.php" class="flex flex-col items-center gap-1 text-primary">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-[10px] font-bold">Home</span>
            </a>
            <a href="../Catalog/catalog.php" class="flex flex-col items-center gap-1 text-text-secondary dark:text-emerald-400">
                <span class="material-symbols-outlined">manage_search</span>
                <span class="text-[10px] font-medium">Catalog</span>
            </a>
            <a href="../Orders/order.php" class="flex flex-col items-center gap-1 text-text-secondary dark:text-emerald-400">
                <span class="material-symbols-outlined">receipt_long</span>
                <span class="text-[10px] font-medium">Orders</span>
            </a>
            <button id="mobileProfileBtn" class="flex flex-col items-center gap-1 text-text-secondary dark:text-emerald-400">
                <span class="material-symbols-outlined">person</span>
                <span class="text-[10px] font-medium">Account</span>
            </button>
        </div>
    </div>

</body>
<!-- script link -->
<script src="../Dashboard/js/script.js"></script>

</html>