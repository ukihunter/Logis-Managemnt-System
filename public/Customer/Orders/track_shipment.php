<?php
require_once '../../../config/session_Detils.php';
require_once '../../../config/database.php';

// Get order_id from URL
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$user_id = $_SESSION['user_id'];

if ($order_id === 0) {
    header('Location: order.php');
    exit;
}

$conn = getDBConnection();

// Fetch order details with driver info
$order_query = "SELECT o.*, d.full_name as driver_name, d.phone_number as driver_phone, d.vehicle_model
                FROM orders o
                LEFT JOIN drivers d ON o.driver_id = d.id
                WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Order not found';
    header('Location: order.php');
    exit;
}

$order = $result->fetch_assoc();

// Get order status history
$history_query = "SELECT * FROM order_status_history 
                  WHERE order_id = ? 
                  ORDER BY created_at ASC";
$stmt = $conn->prepare($history_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$history_result = $stmt->get_result();
$status_history = [];
while ($row = $history_result->fetch_assoc()) {
    $status_history[] = $row;
}

// Fetch order items
$items_query = "SELECT oi.*, p.name as product_name, p.sku as SKU 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_query);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
$order_items = [];
while ($item = $items_result->fetch_assoc()) {
    $order_items[] = $item;
}
$items_stmt->close();

// Close database connection
$conn->close();

// Determine progress percentage
$progress_map = [
    'pending' => 20,
    'processing' => 40,
    'packed' => 60,
    'shipped' => 80,
    'delivered' => 100
];
$progress = $progress_map[$order['order_status']] ?? 20;

// Status labels
$status_labels = [
    'pending' => 'Order Placed',
    'processing' => 'Processing',
    'packed' => 'Packed',
    'shipped' => 'In Transit',
    'delivered' => 'Delivered'
];
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Order Tracking - Island Distribution Portal</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
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
                        "card-light": "#ffffff",
                        "card-dark": "#1a2e22",
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
                        "2xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        /* Custom scrollbar for order list if needed */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="font-display bg-background-light dark:bg-background-dark text-text-main dark:text-gray-100 transition-colors duration-200">
    <div class="flex min-h-screen flex-col">
        <!-- Top Navigation -->
        <header class="sticky top-0 z-50 flex items-center justify-between border-b border-[#e7f3eb] dark:border-[#1e3a29] bg-white/80 dark:bg-card-dark/90 backdrop-blur-md px-6 py-3 lg:px-10">
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-4">
                    <div class="size-8 bg-primary/70 rounded-lg flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-white">shopping_bag_speed</span>
                    </div>
                    <h2 class="text-lg font-bold leading-tight tracking-tight hidden md:flex">IslandDistro</h2>
                </div>
                <!--  <nav class="hidden md:flex items-center gap-6">
                    <a class="text-sm font-medium text-text-secondary hover:text-primary transition-colors" href="#">Dashboard</a>
                    <a class="text-sm font-medium text-text-secondary hover:text-primary transition-colors" href="#">Orders</a>
                    <a class="text-sm font-medium text-text-secondary hover:text-primary transition-colors" href="#">Inventory</a>
                    <a class="text-sm font-medium text-text-secondary hover:text-primary transition-colors" href="#">Reports</a>
                </nav> -->
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button id="profileMenuBtn" class="size-10 rounded-full bg-slate-300 dark:bg-slate-700 bg-cover bg-center border-2 border-slate-100 dark:border-slate-800 hover:border-primary dark:hover:border-primary transition-colors" data-alt="User profile avatar showing a store logo or generic user icon" style='background-image: url("https://avatar.iran.liara.run/username?username=<?php echo urlencode($business_name); ?>");'></button>
                    <!-- Dropdown Menu -->
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-lg shadow-lg overflow-hidden z-50">
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
        </header>
        <!-- Main Content -->
        <main class="flex-1 w-full max-w-[1440px] mx-auto p-4 lg:p-8">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">

                <div>
                    <div class="flex flex-row justify-center rounded-full bg-primary/90 text-white px-3 py-1 mb-4 w-max cursor-pointer hover:bg-primary/20 transition-colors" onclick="history.back()">
                        <span class="material-symbols-outlined">chevron_right</span>
                        <button class="text-white">Go back</button>
                    </div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight text-text-main dark:text-white">Order <?php echo htmlspecialchars($order['order_number']); ?></h1>
                        <span class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-xs font-bold text-primary ring-1 ring-inset ring-primary/20">
                            <?php echo ucfirst($order['payment_status']); ?>
                        </span>
                    </div>
                    <p class="text-text-secondary dark:text-gray-400">Placed on <?php echo date('M d, Y', strtotime($order['created_at'])); ?> at <?php echo date('h:i A', strtotime($order['created_at'])); ?> • Wholesale Account: <?php echo htmlspecialchars($business_name); ?></p>
                </div>
                <div class="flex gap-3">
                    <?php if ($order['payment_status'] === 'paid' && $order['order_status'] !== 'cancelled'): ?>
                        <button onclick="window.open('download_invoice.php?order_id=<?php echo $order['id']; ?>', '_blank')" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-card-dark border border-[#e7f3eb] dark:border-[#253f30] text-sm font-bold text-text-main dark:text-white hover:bg-gray-50 dark:hover:bg-[#253f30] transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-lg">download</span>
                            <span>Invoice</span>
                        </button>
                    <?php endif; ?>
                    <button class="flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-card-dark border border-[#e7f3eb] dark:border-[#253f30] text-sm font-bold text-text-main dark:text-white hover:bg-gray-50 dark:hover:bg-[#253f30] transition-colors shadow-sm">
                        <span class="material-symbols-outlined text-lg">support_agent</span>
                        <span>Support</span>
                    </button>
                </div>
            </div>
            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full">
                <!-- Left Column: Status & Details -->
                <div class="lg:col-span-5 flex flex-col gap-6">
                    <!-- Estimated Delivery Card -->
                    <div class="rounded-xl bg-white dark:bg-card-dark p-6 shadow-sm border border-[#e7f3eb] dark:border-[#1e3a29]">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-text-secondary uppercase tracking-wider">
                                    <?php echo $order['order_status'] === 'delivered' ? 'Delivered' : 'Estimated Delivery'; ?>
                                </p>
                                <p class="mt-1 text-2xl font-bold text-text-main dark:text-white">
                                    <?php
                                    if ($order['order_status'] === 'delivered') {
                                        echo date('M d, Y', strtotime($order['updated_at']));
                                    } elseif ($order['order_status'] === 'shipped') {
                                        echo 'Today';
                                    } else {
                                        echo 'Pending';
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <span class="material-symbols-outlined text-2xl">local_shipping</span>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2 text-sm text-text-main dark:text-gray-300">
                            <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">location_on</span>
                            <span>Arriving at: <?php echo htmlspecialchars($address); ?> <span class="font-bold"></span></span>
                        </div>
                    </div>
                    <!-- Timeline -->
                    <div class="rounded-xl bg-white dark:bg-card-dark p-6 shadow-sm border border-[#e7f3eb] dark:border-[#1e3a29] flex-1">
                        <h3 class="text-lg font-bold text-text-main dark:text-white mb-6">Tracking History</h3>
                        <div class="relative pl-2">
                            <?php
                            $status_icons = [
                                'pending' => 'schedule',
                                'processing' => 'sync',
                                'packed' => 'inventory_2',
                                'shipped' => 'local_shipping',
                                'delivered' => 'check_circle',
                                'cancelled' => 'cancel'
                            ];

                            $total_steps = count($status_history);
                            foreach ($status_history as $index => $history):
                                $is_last = ($index === $total_steps - 1);
                                $is_current = ($history['status'] === $order['order_status']);
                                $date_time = date('M d, h:i A', strtotime($history['created_at']));
                                $status_label = $status_labels[$history['status']] ?? ucfirst($history['status']);
                                $icon = $status_icons[$history['status']] ?? 'circle';
                            ?>
                                <div class="flex gap-4 <?php echo !$is_last ? 'pb-8' : ''; ?> relative group">
                                    <?php if (!$is_last): ?>
                                        <div class="absolute left-[11px] top-8 bottom-0 w-[2px] bg-primary"></div>
                                    <?php endif; ?>
                                    <div class="relative z-10 flex h-6 w-6 shrink-0 items-center justify-center rounded-full <?php echo $is_current ? 'ring-4 ring-primary/20 bg-primary text-white animate-pulse' : 'bg-primary text-white'; ?>">
                                        <span class="material-symbols-outlined text-sm <?php echo $is_current ? '' : 'font-bold'; ?>"><?php echo $icon; ?></span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold <?php echo $is_current ? 'text-primary' : 'text-text-main dark:text-white'; ?>"><?php echo htmlspecialchars($status_label); ?></p>
                                        <p class="text-xs text-text-secondary"><?php echo $date_time; ?>
                                            <?php if (!empty($history['notes'])): ?>
                                                • <span class="font-medium"><?php echo htmlspecialchars($history['notes']); ?></span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Order Summary -->
                    <div class="rounded-xl bg-white dark:bg-card-dark overflow-hidden shadow-sm border border-[#e7f3eb] dark:border-[#1e3a29]">
                        <div class="px-6 py-4 border-b border-[#e7f3eb] dark:border-[#1e3a29] flex justify-between items-center">
                            <h3 class="text-lg font-bold text-text-main dark:text-white">Order Items</h3>
                            <span class="text-xs font-bold bg-[#e7f3eb] dark:bg-[#253f30] text-text-main dark:text-white px-2 py-1 rounded"><?php echo count($order_items); ?> Items</span>
                        </div>
                        <div class="divide-y divide-[#e7f3eb] dark:divide-[#1e3a29]">
                            <?php foreach ($order_items as $item): ?>
                                <div class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-[#253f30]/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-text-secondary">inventory_2</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-text-main dark:text-white"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                            <p class="text-xs text-text-secondary">SKU: <?php echo htmlspecialchars($item['SKU']); ?> • <?php echo (int)$item['quantity']; ?> Units</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold text-text-main dark:text-white">Rs <?php echo number_format($item['subtotal'], 2); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="bg-gray-50 dark:bg-[#15261c] px-6 py-4 flex justify-between items-center">
                            <span class="text-sm font-medium text-text-secondary">Total Amount</span>
                            <span class="text-xl font-black text-text-main dark:text-white">Rs <?php echo number_format($order['total_amount'], 2); ?></span>
                        </div>
                    </div>
                </div>
                <!-- Right Column: Interactive Map -->
                <div class="lg:col-span-7 h-[600px] lg:h-auto min-h-[500px] flex flex-col rounded-xl overflow-hidden shadow-lg border border-[#e7f3eb] dark:border-[#1e3a29] relative bg-gray-200 dark:bg-gray-800 group">
                    <!-- Map Placeholder Image -->
                    <div class="relative w-full h-full overflow-hidden rounded-xl">
                        <div id="mapPlaceholder" class="absolute inset-0 bg-cover bg-center cursor-pointer z-10" data-alt="Map of island showing roads and green terrain with a route highlighted" data-location="Sri Lanaka" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBLw5AQ0a3d_zcn_RcOp8Mq2r8PGR323nfvU3Yyff91FSB7HCY6-wX-FfCEPjdpiWnaGPlJqCceP-mEFBHHLxHoq7EMxTr_cLTelRA9XXIhaLmpFy5SL6nuiOcQsF0Cd1SROKvgG1bEpxjD9Z1Pt7_b7s5dNFy1SjLdDPwOSR4eYDr3nwtQGkBLiumZnHL6ADPAQRJUqv1h-32wcDffdMXbs41j0Bm15LFzdLywgypVwnvrWWoAQKa84tawOD1QYr6SvgbcZ3pgGQg');"></div>
                        <div id="googleMap" class="absolute inset-0 hidden z-10">
                            <iframe
                                class="w-full h-full border-0"
                                loading="lazy"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.469178660075!2d80.3588519!3d7.4884773!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2e9287ae39ecd%3A0x8f7945f6d12103c8!2sESOFT%20Metro%20Campus%20-%20Kurunegala!5e0!3m2!1sen!2slk!4v1704620000000!5m2!1sen!2slk">
                            </iframe>
                        </div>
                    </div>


                    <!-- Map Gradient/Overlay for readability -->
                    <div class="absolute inset-0 bg-gradient-to-b from-black/10 to-transparent pointer-events-none z-20"></div>
                    <!-- Map UI Controls -->
                    <div class="absolute top-4 right-4 flex flex-col gap-2 z-30">
                        <button class="bg-white dark:bg-card-dark text-text-main dark:text-white p-2 rounded-lg shadow-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <span class="material-symbols-outlined">add</span>
                        </button>
                        <button class="bg-white dark:bg-card-dark text-text-main dark:text-white p-2 rounded-lg shadow-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <span class="material-symbols-outlined">remove</span>
                        </button>
                    </div>
                    <!-- Live Indicator -->
                    <div class="absolute top-4 left-4 bg-white/90 dark:bg-card-dark/90 backdrop-blur px-3 py-1.5 rounded-full shadow-sm border border-white/20 flex items-center gap-2 z-30">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-primary"></span>
                        </span>
                        <span class="text-xs font-bold text-text-main dark:text-white uppercase tracking-wide">Live Tracking</span>
                    </div>
                    <!-- Driver Card Overlay -->
                    <?php if (!empty($order['driver_name'])): ?>
                        <div class="absolute bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-80 bg-white dark:bg-card-dark p-4 rounded-xl shadow-xl border border-[#e7f3eb] dark:border-[#1e3a29] backdrop-blur-sm bg-opacity-95 dark:bg-opacity-95 z-30">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="relative">
                                    <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-gray-600">person</span>
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 h-5 w-5 bg-primary rounded-full border-2 border-white dark:border-card-dark flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[10px] text-white font-bold">local_shipping</span>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-text-main dark:text-white"><?php echo htmlspecialchars($order['driver_name']); ?></h4>
                                    <p class="text-xs text-text-secondary">Your delivery driver</p>
                                </div>
                                <?php if (!empty($order['driver_phone'])): ?>
                                    <div class="ml-auto">
                                        <a href="tel:<?php echo htmlspecialchars($order['driver_phone']); ?>" class="h-10 w-10 flex items-center justify-center rounded-full bg-[#e7f3eb] dark:bg-[#253f30] text-primary hover:bg-primary hover:text-white transition-all">
                                            <span class="material-symbols-outlined">call</span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="space-y-3">
                                <?php if (!empty($order['vehicle_model'])): ?>
                                    <div class="flex items-center justify-between text-sm p-3 bg-background-light dark:bg-[#15261c] rounded-lg">
                                        <div class="flex items-center gap-2 text-text-secondary">
                                            <span class="material-symbols-outlined text-lg">directions_car</span>
                                            <span>Vehicle</span>
                                        </div>
                                        <span class="font-bold text-text-main dark:text-white"><?php echo htmlspecialchars($order['vehicle_model']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($order['driver_phone'])): ?>
                                    <div class="flex items-center justify-between text-sm p-3 bg-background-light dark:bg-[#15261c] rounded-lg">
                                        <div class="flex items-center gap-2 text-text-secondary">
                                            <span class="material-symbols-outlined text-lg">phone</span>
                                            <span>Contact</span>
                                        </div>
                                        <span class="font-bold text-text-main dark:text-white"><?php echo htmlspecialchars($order['driver_phone']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
<script src="../Orders/js/script.js"></script>

</html>