<?php
// Start session and include database configuration
require_once '../../../config/session_Detils.php';
require_once '../../../config/database.php';
//get db cnnection 
$conn = getDBConnection();

// Fetch categories
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = $conn->query($categories_query);
$categories = [];
if ($categories_result) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch brands
$brands_query = "SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' ORDER BY brand";
$brands_result = $conn->query($brands_query);
$brands = [];
if ($brands_result) {
    while ($row = $brands_result->fetch_assoc()) {
        $brands[] = $row['brand'];
    }
}


// cart items count
$user_id = $_SESSION['user_id'];

// Fetch cart items from database
$cart_items = [];
$cart_count = 0;
if ($user_id) {
    $cart_query = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_count = $result->fetch_assoc()['count'];
}

// Get total product count
$total_query = "SELECT COUNT(*) as total FROM products WHERE status = 'active'";
$total_result = $conn->query($total_query);
$total_products = $total_result->fetch_assoc()['total'];
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
                    <input id="searchInput" class="w-full h-11 pl-12 pr-4 rounded-lg bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark focus:border-primary focus:ring-1 focus:ring-primary text-text-main dark:text-white placeholder:text-text-muted text-sm transition-all" placeholder="Search by product name, SKU, or brand..." value="" />
                    <div class="absolute right-3 hidden group-focus-within:block">
                        <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-500 px-2 py-1 rounded border border-gray-200 dark:border-gray-700">ESC</span>
                    </div>
                </label>
            </div>
            <!-- Right Actions -->
            <div class="flex items-center gap-6 shrink-0">
                <nav class="hidden lg:flex items-center gap-6">
                    <a class="text-text-main dark:text-gray-200 text-sm  font-bold hover:text-primary transition-colors" href="../Dashboard/dashboard.php">Dashboard</a>
                    <a class="text-primary text-sm font-bold" href="#">Catalog</a>
                    <a class="text-text-main dark:text-gray-200 text-sm font-bold hover:text-primary transition-colors" href="../Orders/order.php">Orders</a>
                    <!--    <a class="text-text-main dark:text-gray-200 text-sm font-medium hover:text-primary transition-colors" href="#">Invoices</a> -->
                </nav>
                <div class="flex items-center gap-4">
                    <button onclick="location.href='../../Customer/Cart/cart.php'" class="relative flex items-center justify-center h-10 px-4 bg-primary hover:bg-green-500 transition-colors text-text-main rounded-lg font-bold text-sm gap-2">
                        <span class="material-symbols-outlined text-[20px] text-white">shopping_cart</span>
                        <span class="hidden sm:inline text-white">Cart </span>
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-black/10 text-[10px] font-bold text-white" id="cartCount"><?php echo $cart_count; ?></span>
                    </button>
                    <button class="flex items-center gap-2">
                        <div class="relative ml-2">
                            <button id="profileMenuBtn" class="size-10 rounded-full bg-slate-300 dark:bg-slate-700 bg-cover bg-center border-2 border-slate-100 dark:border-slate-800 hover:border-primary dark:hover:border-primary transition-colors" data-alt="User profile avatar showing a store logo or generic user icon" style='background-image: url("https://ui-avatars.com/api/?&background=0D8ABC&color=fff&name=<?php echo urlencode($business_name); ?>");'></button>
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
                <button id="clearFilters" class="text-xs font-medium text-primary hover:text-green-600 transition-colors">Clear All</button>
            </div>
            <!-- Categories -->
            <div class="flex flex-col gap-1">
                <p class="px-2 text-xs font-bold text-text-muted uppercase tracking-wider mb-2">Categories</p>
                <button data-category="all" class="category-filter flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary font-bold">
                    <span class="material-symbols-outlined filled text-[20px]">grid_view</span>
                    <span class="text-sm">All Products</span>
                </button>
                <!-- getting catogories names-->
                <?php foreach ($categories as $cat): ?>

                    <button data-category="<?php echo htmlspecialchars($cat['name']); ?>" class="category-filter flex items-center gap-3 px-3 py-2.5 rounded-lg text-text-main dark:text-gray-300 hover:bg-border-light dark:hover:bg-border-dark transition-colors group">
                        <span class="material-symbols-outlined text-[20px] text-gray-400 group-hover:text-primary"><?php echo htmlspecialchars($cat['icon'] ?? 'category'); ?></span>
                        <span class="text-sm font-medium"><?php echo htmlspecialchars($cat['name']); ?></span>
                    </button>
                <?php endforeach; ?>
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
                    <div class="pt-3 pb-2 flex flex-col gap-2" id="brandFilterContainer">
                        <!-- getting brand names-->
                        <?php foreach ($brands as $brand): ?>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input class="brand-checkbox w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary" type="checkbox" value="<?php echo htmlspecialchars($brand); ?>" />
                                <span class="text-sm text-text-main dark:text-gray-300"><?php echo htmlspecialchars($brand); ?></span>
                            </label>
                        <?php endforeach; ?>
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
                            <input checked="" class="stock-radio w-4 h-4 border-gray-300 text-primary focus:ring-primary" name="stock" type="radio" value="" />
                            <span class="text-sm text-text-main dark:text-gray-300">All Stock</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input class="stock-radio w-4 h-4 border-gray-300 text-primary focus:ring-primary" name="stock" type="radio" value="in_stock" />
                            <span class="text-sm text-text-main dark:text-gray-300">In Stock</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input class="stock-radio w-4 h-4 border-gray-300 text-primary focus:ring-primary" name="stock" type="radio" value="low_stock" />
                            <span class="text-sm text-text-main dark:text-gray-300">Low Stock</span>
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
                                <input id="minPrice" class="w-full pl-5 py-1 text-sm border border-gray-300 rounded focus:ring-primary focus:border-primary" placeholder="Min" type="number" min="0" />
                            </div>
                            <span class="text-gray-400">-</span>
                            <div class="relative w-full">
                                <span class="absolute left-1 top-1.5 text-xs text-gray-500">Rs</span>
                                <input id="maxPrice" class="w-full pl-5 py-1 text-sm border border-gray-300 rounded focus:ring-primary focus:border-primary" placeholder="Max" type="number" min="0" />
                            </div>
                        </div>
                        <button id="applyPriceFilter" class="w-full bg-border-light dark:bg-border-dark text-text-main dark:text-white text-xs font-bold py-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Apply</button>
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
                        <p class="text-text-muted mt-1" id="productCount">Showing 0 of <?php echo $total_products; ?> products</p>
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
                        <select id="sortBy" class="appearance-none pl-4 pr-10 py-2 bg-background-light dark:bg-background-dark border-none rounded-lg text-sm font-bold text-text-main dark:text-white focus:ring-2 focus:ring-primary cursor-pointer">
                            <option value="popularity">Popularity</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="newest">Newest Arrivals</option>
                            <option value="name">Name A-Z</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-2 top-2 pointer-events-none text-text-main dark:text-white">expand_more</span>
                    </div>
                </div>
            </div>
            <!-- Product Grid -->
            <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Products will be loaded here -->
                <div class="col-span-full flex justify-center items-center py-20">
                    <div class="animate-pulse text-text-muted">Loading products...</div>
                </div>
            </div>
            <!-- Pagination -->
            <div id="paginationContainer" class="mt-12 flex items-center justify-center gap-3">
                <!-- Pagination will be loaded here -->
            </div>

            <!-- Mobile Bottom Nav Spacer -->
            <div class="h-16 lg:hidden"></div>
        </main>

        <!-- Mobile Bottom Nav -->
        <div class="fixed bottom-0 left-0 w-full bg-surface-light dark:bg-surface-dark border-t border-slate-200 dark:border-slate-800 flex lg:hidden justify-around py-3 px-2 z-40">
            <a href="../Dashboard/dashboard.php" class="flex flex-col items-center gap-1 text-text-secondary dark:text-emerald-400">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-[10px] font-medium">Home</span>
            </a>
            <a href="catalog.php" class="flex flex-col items-center gap-1 text-primary">
                <span class="material-symbols-outlined">manage_search</span>
                <span class="text-[10px] font-bold">Catalog</span>
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
    <script>
        // Example: After successful add-to-cart AJAX
        function updateCartCount() {
            fetch("../Catalog/cart_count.php")
                .then((response) => response.json())
                .then((data) => {
                    const cartCountEl = document.getElementById("cartCount");
                    if (cartCountEl) {
                        cartCountEl.textContent = data.count;
                    }
                })
                .catch((error) => console.error("Error updating cart count:", error));
        }
    </script>
</body>
<!-- link the script -->
<script src="js/script.js"></script>

</html>