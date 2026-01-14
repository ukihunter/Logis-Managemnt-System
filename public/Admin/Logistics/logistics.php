<?php
// Logistics Management Page - Delivery Tracking & Route Optimization
// db and session includes
require_once '../../../config/admin_session.php';
require_once '../../../config/database.php';

// Get all orders with drivers and calculate counts
$conn = getDBConnection();

// Get order counts by status
$statsQuery = "SELECT 
                COUNT(CASE WHEN order_status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN order_status IN ('processing', 'packed') THEN 1 END) as in_progress_count,
                COUNT(CASE WHEN order_status IN ('shipped') THEN 1 END) as shipped_count,
                COUNT(CASE WHEN order_status = 'delivered' THEN 1 END) as completed_count
            FROM orders";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();

// Get orders with driver and location info
$ordersQuery = "SELECT 
                o.id,
                o.order_number,
                o.customer_name,
                o.business_name,
                o.shipping_address,
                o.shipping_city,
                o.shipping_province,
                o.shipping_postal_code,
                o.order_status,
                o.total_amount,
                o.created_at,
                o.updated_at,
                d.full_name as driver_name,
                d.employee_id as driver_employee_id,
                d.phone_number as driver_phone,
                COUNT(oi.id) as total_items
            FROM orders o
            LEFT JOIN drivers d ON o.driver_id = d.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.order_status IN ('processing', 'packed', 'shipped', 'delivered')
            GROUP BY o.id
            ORDER BY 
                CASE o.order_status
                    WHEN 'shipped' THEN 1
                    WHEN 'packed' THEN 2
                    WHEN 'processing' THEN 3
                    WHEN 'delivered' THEN 4
                END,
                o.updated_at DESC";
$ordersResult = $conn->query($ordersQuery);
?>

<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Delivery Tracking &amp; Route Optimization</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <?php include '../components/styles.php'; ?>
    <style>
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px
        }

        ::-webkit-scrollbar-track {
            background: transparent
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #334155
        }

        .map-bg {
            background-color: #e5e7eb;
            background-image: url(https://lh3.googleusercontent.com/aida-public/AB6AXuD6AG-7AWoW5WKDq-B5h9S3y3rHVl5WrNev-_GrF46y_F3XwJyfo6ybaSgVSaNfQJsg7sRmGT7KiTZAg8VlwBRQtMMauF99hivOwJ01iHopE9y7gQOteSvw6don4zpyq161B6TAj4m3SlYQTr9EFRCKFYqJGNmizSvT5Ot4BngD24PFSL1vbN2RWPYFh7oAJaKaZOgY4bT8jxWvMTh52oK3A-q5khkoBHTMQPGOez9TOeo45dydM7Fskr_HGlFoorOnyQOe8ihLqxA);
            background-size: cover;
            background-position: center
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 0.5;
            }

            100% {
                transform: scale(2.5);
                opacity: 0;
            }
        }

        .ring-pulse::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: inherit;
            border-radius: 50%;
            z-index: -1;
            animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
    </style>
</head>


<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white font-display overflow-hidden h-screen w-screen flex">
    <?php include '../components/sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <header class="h-16 bg-surface-light dark:bg-surface-dark border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 shrink-0 z-10">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-bold">Live Tracker</h2>
                <div class="h-4 w-px bg-slate-300 dark:bg-slate-700 mx-2"></div>
                <div class="flex items-center text-sm text-slate-500">
                    <span class="material-symbols-outlined text-[18px] mr-1">location_on</span>
                    <span><?php echo htmlspecialchars($province) . " RDC"; ?></span>
                </div>
            </div>
            <div class="flex items-center gap-4">

                <button class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
                </button>
            </div>
        </header>
        <div class="flex flex-1 overflow-hidden">
            <div id="deliveryListSection" class="w-full transition-all duration-300 bg-surface-light dark:bg-surface-dark border-r border-slate-200 dark:border-slate-800 flex flex-col overflow-hidden z-10 shadow-xl">
                <div class="flex border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-surface-dark shrink-0">
                    <button onclick="filterByTab('pending')" id="tabPending" class="flex-1 pb-3 pt-4 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium text-xs uppercase tracking-wide transition-colors">
                        Pending (<?php echo $stats['pending_count']; ?>)
                    </button>
                    <button onclick="filterByTab('in_progress')" id="tabInProgress" class="flex-1 pb-3 pt-4 border-b-2 border-primary text-primary font-bold text-xs uppercase tracking-wide transition-colors bg-primary/5">
                        In Progress (<?php echo $stats['in_progress_count']; ?>)
                    </button>
                    <button onclick="filterByTab('shipped')" id="tabShipped" class="flex-1 pb-3 pt-4 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium text-xs uppercase tracking-wide transition-colors">
                        Shipped (<?php echo $stats['shipped_count']; ?>)
                    </button>
                    <button onclick="filterByTab('completed')" id="tabCompleted" class="flex-1 pb-3 pt-4 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium text-xs uppercase tracking-wide transition-colors">
                        Completed (<?php echo $stats['completed_count']; ?>)
                    </button>
                </div>
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-surface-dark shrink-0">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[20px]">search</span>
                        <input id="searchInput" onkeyup="searchDeliveries()" class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-slate-700 dark:text-slate-200 placeholder-slate-400" placeholder="Search order ID, driver, or destination..." type="text" />
                    </div>
                </div>
                <div id="deliveryList" class="flex-1 overflow-y-auto bg-slate-50 dark:bg-[#122218] p-4 space-y-3">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Active Deliveries</h3>
                    <?php
                    // Get status badge HTML
                    function getStatusBadge($status)
                    {
                        $badges = [
                            'pending' => '<span class="inline-flex items-center gap-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold px-2 py-0.5 rounded-full">Pending</span>',
                            'processing' => '<span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-bold px-2 py-0.5 rounded-full">Processing</span>',
                            'packed' => '<span class="inline-flex items-center gap-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded-full">Packed</span>',
                            'shipped' => '<span class="inline-flex items-center gap-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[10px] font-bold px-2 py-0.5 rounded-full"><span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> In Transit</span>',
                            'delivered' => '<span class="inline-flex items-center gap-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold px-2 py-0.5 rounded-full"><span class="material-symbols-outlined text-[12px]">check</span> Delivered</span>'
                        ];
                        return $badges[$status] ?? $badges['pending'];
                    }
                    // Get driver initials
                    function getDriverInitials($name)
                    {
                        if (empty($name)) return '?';
                        $words = explode(' ', $name);
                        if (count($words) >= 2) {
                            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                        }
                        return strtoupper(substr($name, 0, 2));
                    }
                    // Get tab group based on status
                    function getTabGroup($status)
                    {
                        if ($status === 'pending') return 'pending';
                        if (in_array($status, ['processing', 'packed'])) return 'in_progress';
                        if ($status === 'shipped') return 'shipped';
                        if ($status === 'delivered') return 'completed';
                        return 'pending';
                    }
                    // Display each order card
                    $hasOrders = false;
                    while ($order = $ordersResult->fetch_assoc()):
                        $hasOrders = true;
                        $fullAddress = $order['shipping_address'] . ', ' . $order['shipping_city'] . ', ' . $order['shipping_province'];
                        $isHighlighted = $order['order_status'] === 'shipped';
                        $borderClass = $isHighlighted ? 'border-2 border-primary shadow-lg shadow-primary/5' : 'border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 shadow-sm';
                        $tabGroup = getTabGroup($order['order_status']);
                    ?>

                        <div onclick='showDeliveryMap(<?php echo json_encode([
                                                            "id" => $order["id"],
                                                            "orderNumber" => $order["order_number"],
                                                            "customer" => $order["business_name"] ?? $order["customer_name"],
                                                            "address" => $fullAddress,
                                                            "status" => $order["order_status"],
                                                            "driver" => $order["driver_name"],
                                                            "totalAmount" => $order["total_amount"]
                                                        ]); ?>)' class="delivery-card bg-white dark:bg-surface-dark p-4 rounded-xl <?php echo $borderClass; ?> cursor-pointer transition-all hover:shadow-md" data-status="<?php echo $order['order_status']; ?>" data-tab="<?php echo $tabGroup; ?>" data-search="<?php echo strtolower($order['order_number'] . ' ' . ($order['business_name'] ?? $order['customer_name']) . ' ' . $order['driver_name'] . ' ' . $order['shipping_city']); ?>">
                            <?php if ($isHighlighted): ?>
                                <div class="absolute top-0 left-0 w-1.5 h-full bg-primary"></div>
                            <?php endif; ?>
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">#<?php echo htmlspecialchars($order['order_number']); ?></span>
                                    <?php echo getStatusBadge($order['order_status']); ?>
                                </div>
                                <span class="text-xs text-slate-400"><?php echo date('g:i A', strtotime($order['updated_at'])); ?></span>
                            </div>
                            <h4 class="font-bold text-<?php echo $isHighlighted ? 'base' : 'sm'; ?> text-slate-900 dark:text-white"><?php echo htmlspecialchars($order['business_name'] ?? $order['customer_name']); ?></h4>
                            <p class="text-xs text-slate-500 mt-0.5 mb-3 truncate"><?php echo htmlspecialchars($fullAddress); ?></p>
                            <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 pt-3 mt-1">
                                <div class="flex items-center gap-2">
                                    <?php if ($order['driver_name']): ?>
                                        <div class="w-6 h-6 rounded-full bg-slate-800 dark:bg-slate-700 text-white flex items-center justify-center text-[10px] font-bold ring-2 ring-slate-100 dark:ring-slate-700">
                                            <?php echo getDriverInitials($order['driver_name']); ?>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-slate-400 font-bold uppercase leading-none mb-0.5">Driver</span>
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 leading-none"><?php echo htmlspecialchars($order['driver_name']); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-6 h-6 rounded-full border border-dashed border-slate-300 dark:border-slate-600 flex items-center justify-center text-slate-400">
                                            <span class="material-symbols-outlined text-[14px]">person_add</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-slate-400 font-bold uppercase leading-none mb-0.5">Driver</span>
                                            <span class="text-xs font-medium text-slate-400 italic leading-none">Unassigned</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button class="text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <?php if (!$hasOrders): ?>
                        <!-- no delivery -->
                        <div class="text-center py-12">
                            <span class="material-symbols-outlined text-slate-300 dark:text-slate-700 text-5xl">package_2</span>
                            <p class="text-slate-400 mt-2">No deliveries found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div id="mapSection" class="flex-1 relative bg-slate-200 dark:bg-slate-800 overflow-hidden transition-all duration-300 hidden">
                <!-- Close Map Button -->
                <button onclick="closeDeliveryMap()" class="absolute top-4 right-4 z-50 w-10 h-10 bg-white dark:bg-surface-dark rounded-lg shadow-lg flex items-center justify-center text-slate-600 hover:text-red-500 dark:text-slate-400 dark:hover:text-red-400 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>

                <!-- Static Map Background -->
                <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900">
                    <!-- Grid Pattern -->
                    <div class="absolute inset-0" style="background-image: linear-gradient(rgba(148, 163, 184, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(148, 163, 184, 0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>

                    <!-- Map Roads/Streets Illustration -->
                    <svg class="absolute inset-0 w-full h-full opacity-20">
                        <line x1="0" y1="30%" x2="100%" y2="30%" stroke="#94a3b8" stroke-width="3" />
                        <line x1="0" y1="60%" x2="100%" y2="60%" stroke="#94a3b8" stroke-width="3" />
                        <line x1="20%" y1="0" x2="20%" y2="100%" stroke="#94a3b8" stroke-width="2" />
                        <line x1="50%" y1="0" x2="50%" y2="100%" stroke="#94a3b8" stroke-width="4" />
                        <line x1="80%" y1="0" x2="80%" y2="100%" stroke="#94a3b8" stroke-width="2" />
                    </svg>

                    <!-- Delivery Zone Marker -->
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10">
                        <div class="relative">
                            <div class="w-16 h-16 bg-primary rounded-full shadow-2xl flex items-center justify-center animate-pulse">
                                <span class="material-symbols-outlined text-white text-3xl">location_on</span>
                            </div>
                            <div class="absolute inset-0 bg-primary rounded-full animate-ping opacity-20"></div>
                        </div>
                    </div>
                </div>

                <!-- Map Info Overlay -->
                <div class="absolute top-4 left-4 z-10 bg-white/95 dark:bg-surface-dark/95 backdrop-blur px-4 py-3 rounded-lg shadow-lg border border-slate-200 dark:border-slate-700 max-w-sm">
                    <h3 id="mapOrderNumber" class="font-bold text-slate-900 dark:text-white text-sm mb-1">Select an order</h3>
                    <p id="mapCustomer" class="text-xs text-slate-600 dark:text-slate-300 mb-1"></p>
                    <p id="mapAddress" class="text-xs text-slate-500 mb-2"></p>
                    <div id="mapStatus" class="inline-flex items-center gap-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold px-2 py-0.5 rounded-full"></div>
                </div>

                <!-- Delivery Zone Label -->
                <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 bg-white/90 dark:bg-surface-dark/90 backdrop-blur px-4 py-2 rounded-lg shadow-lg border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-sm">info</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Delivery Zone View</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Bottom Nav Spacer -->
        <div class="h-16 lg:hidden"></div>
    </main>

    <?php include '../components/scripts.php'; ?>
    <script>
        let map;
        let marker;
        let currentOrderData = null;
        let currentTab = 'in_progress';

        // Initialize Google Map
        function initMap() {
            // Default center - general map view
            const defaultCenter = {
                lat: 40.7128,
                lng: -74.0060
            };

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: defaultCenter,
                mapTypeControl: true,
                streetViewControl: false,
                fullscreenControl: false,
                styles: [{
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{
                        visibility: "off"
                    }]
                }]
            });

            // Add a general marker for visual purposes
            marker = new google.maps.Marker({
                map: map,
                position: defaultCenter,
                title: "Delivery Zone"
            });
        }

        // Show delivery on map with order data
        function showDeliveryMap(orderData) {
            currentOrderData = orderData;
            const mapSection = document.getElementById('mapSection');
            const deliveryListSection = document.getElementById('deliveryListSection');

            // Show map
            mapSection.classList.remove('hidden');

            // Shrink delivery list to fixed width
            deliveryListSection.classList.remove('w-full');
            deliveryListSection.classList.add('w-[420px]');

            // Update map info
            document.getElementById('mapOrderNumber').textContent = '#' + orderData.orderNumber;
            document.getElementById('mapCustomer').textContent = orderData.customer;
            document.getElementById('mapAddress').textContent = orderData.address;
            document.getElementById('mapStatus').textContent = orderData.status.charAt(0).toUpperCase() + orderData.status.slice(1);

            // Just show general map view without actual location
            // Update marker title with customer name
            if (marker) {
                marker.setTitle(orderData.customer + ' - Delivery Zone');
            }

            console.log('Showing delivery map for:', orderData);
        }

        // Close map
        function closeDeliveryMap() {
            const mapSection = document.getElementById('mapSection');
            const deliveryListSection = document.getElementById('deliveryListSection');

            // Hide map
            mapSection.classList.add('hidden');

            // Expand delivery list to full width
            deliveryListSection.classList.remove('w-[420px]');
            deliveryListSection.classList.add('w-full');

            currentOrderData = null;
        }

        // Center map on current marker
        function centerMap() {
            if (marker && marker.getPosition()) {
                map.setCenter(marker.getPosition());
                map.setZoom(12);
            }
        }

        // Filter by tab
        function filterByTab(tab) {
            currentTab = tab;
            const cards = document.querySelectorAll('.delivery-card');

            // Update tab buttons
            const tabs = ['Pending', 'InProgress', 'Shipped', 'Completed'];
            tabs.forEach(t => {
                const btn = document.getElementById('tab' + t);
                if (btn) {
                    const tabValue = t.toLowerCase().replace('inprogress', 'in_progress');
                    if (tabValue === tab) {
                        btn.classList.add('border-primary', 'text-primary', 'font-bold', 'bg-primary/5');
                        btn.classList.remove('border-transparent', 'text-slate-500', 'font-medium');
                    } else {
                        btn.classList.remove('border-primary', 'text-primary', 'font-bold', 'bg-primary/5');
                        btn.classList.add('border-transparent', 'text-slate-500', 'font-medium');
                    }
                }
            });

            // Filter cards
            cards.forEach(card => {
                const cardTab = card.dataset.tab;
                if (cardTab === tab || tab === 'all') {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Search deliveries
        function searchDeliveries() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.delivery-card');

            cards.forEach(card => {
                const searchData = card.dataset.search;
                const cardTab = card.dataset.tab;

                const matchesSearch = searchData.includes(searchTerm);
                const matchesTab = (cardTab === currentTab);

                if (matchesSearch && matchesTab) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Initialize map when page loads
        window.addEventListener('load', function() {
            if (typeof google !== 'undefined') {
                initMap();
            } else {
                console.warn('Google Maps API not loaded. Please add your API key.');
            }

            // Filter to in_progress tab by default
            filterByTab('in_progress');
        });
    </script>

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
        <a href="logistics.php" class="flex items-center gap-3 px-4 py-3 bg-primary/10 text-primary transition-colors border-b border-[#e7f3eb] dark:border-[#2a4034]">
            <span class="material-symbols-outlined">local_shipping</span>
            <span class="text-sm font-medium">Logistics</span>
        </a>
        <a href="../User_Management/user_managment.php" class="flex items-center gap-3 px-4 py-3 hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors <?php echo ($user_type === 'admin') ? 'border-b border-[#e7f3eb] dark:border-[#2a4034]' : ''; ?>">
            <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark">account_child_invert</span>
            <span class="text-sm font-medium">Users</span>
        </a>
        <?php if ($user_type === 'admin'): ?>
            <a href="../Reports/reports.php" class="flex items-center gap-3 px-4 py-3 hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors">
                <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark">description</span>
                <span class="text-sm font-medium">Reports</span>
            </a>
        <?php endif; ?>
    </div>

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