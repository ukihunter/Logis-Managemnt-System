<?php
// Start session and include necessary files
require_once '../../../config/session_Detils.php';
require_once '../../../config/database.php';

// Create database connection
$conn = getDBConnection();
// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch cart items
$cart_query = "SELECT c.id as cart_id, c.quantity,
                      p.id as product_id, p.name, p.sku, p.image_path,
                      p.unit_price, p.carton_quantity, p.carton_price,
                      p.stock_level, p.discount_percentage, p.offer_label, p.is_featured
               FROM cart c
               JOIN products p ON c.product_id = p.id
               WHERE c.user_id = ? AND p.status = 'active'
               ORDER BY c.added_at DESC";

//  Prepare and execute statement
$stmt = $conn->prepare($cart_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$subtotal = 0;

// Process cart items
while ($row = $result->fetch_assoc()) {
    $unit_price = floatval($row['unit_price']);
    $discount_percentage = floatval($row['discount_percentage']);
    // disount  if there
    $discounted_price = $unit_price;
    if ($discount_percentage > 0) {
        $discounted_price = $unit_price * (1 - $discount_percentage / 100);
    }
    // quntity count
    $item_total = $discounted_price * $row['quantity'];
    $subtotal += $item_total;
    // stock level
    $is_low_stock = $row['stock_level'] < 50;
    $is_out_of_stock = $row['stock_level'] <= 0;
    // Build cart item array
    $cart_items[] = [
        'cart_id' => $row['cart_id'],
        'product_id' => $row['product_id'],
        'name' => $row['name'],
        'sku' => $row['sku'],
        'image_path' => $row['image_path'],
        'unit_price' => $unit_price,
        'discounted_price' => $discounted_price,
        'discount_percentage' => $discount_percentage,
        'quantity' => intval($row['quantity']),
        'stock_level' => intval($row['stock_level']),
        'is_low_stock' => $is_low_stock,
        'is_out_of_stock' => $is_out_of_stock,
        'is_featured' => boolval($row['is_featured']),
        'offer_label' => $row['offer_label'],
        'item_total' => $item_total
    ];
}

// if there is tax or other neccery 
$tax_rate = 0.0;
$shipping_fee = 0.0;
$tax_amount = $subtotal * $tax_rate;
$total = $subtotal + $tax_amount + $shipping_fee;
//  connction close 
$conn->close();
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
                    <a class="text-text-main dark:text-gray-200 text-sm font-medium hover:text-primary transition-colors" href="../Orders/order.php">Orders</a>
                    <!--  <a class="text-text-main dark:text-gray-200 text-sm font-medium hover:text-primary transition-colors" href="#">Invoices</a>-->
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
                <!-- bussiness and oter infornmation  -->
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
    <main class="flex-grow w-full px-4 py-8 md:px-8 lg:px-16 xl:px-32 max-w-[1600px] mx-auto">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 mb-6 text-sm">
            <a class="text-text-secondary hover:text-primary dark:text-gray-400" href="../Dashboard/dashboard.php">Dashboard</a>
            <span class="material-symbols-outlined text-sm text-gray-400">chevron_right</span>
            <a class="text-text-secondary hover:text-primary dark:text-gray-400" href="../Catalog/catalog.php">Catalog</a>
            <span class="material-symbols-outlined text-sm text-gray-400">chevron_right</span>
            <span class="font-semibold text-text-main dark:text-white">Shopping Cart</span>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span><?php echo htmlspecialchars($_SESSION['success']);
                            unset($_SESSION['success']); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined">error</span>
                    <span><?php echo htmlspecialchars($_SESSION['error']);
                            unset($_SESSION['error']); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Cart Items -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Page Heading -->
                <div class="flex flex-col gap-1 pb-4 border-b border-gray-200 dark:border-gray-800">
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-text-main dark:text-white">Your Cart (<span id="cartCount"><?php echo count($cart_items); ?></span> items)</h1>
                    <p class="text-text-secondary dark:text-gray-400">Review your items for island-wide distribution.</p>
                </div>

                <!-- Cart List -->
                <div id="cartItemsContainer" class="flex flex-col gap-4">
                    <?php if (empty($cart_items)): ?>
                        <div class="text-center py-20">
                            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">shopping_cart</span>
                            <p class="text-gray-500 text-lg mb-2">Your cart is empty</p>
                            <a href="../Catalog/catalog.php" class="text-primary hover:underline">Continue Shopping</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item group flex flex-col md:flex-row gap-6 bg-surface-light dark:bg-surface-dark p-5 rounded-xl shadow-sm border border-transparent hover:border-primary/20 transition-all" data-cart-id="<?php echo $item['cart_id']; ?>">
                                <div class="relative shrink-0">
                                    <!-- image path -->
                                    <?php if (!empty($item['image_path']) && file_exists('../../../' . $item['image_path'])): ?>
                                        <div class="bg-gray-100 dark:bg-white/5 rounded-lg w-full md:w-[120px] aspect-square bg-center bg-cover" style='background-image: url("../../../<?php echo htmlspecialchars($item['image_path']); ?>");'></div>
                                    <?php else: ?>
                                        <div class="bg-gray-100 dark:bg-white/5 rounded-lg w-full md:w-[120px] aspect-square flex items-center justify-center">
                                            <span class="material-symbols-outlined text-gray-400 text-4xl">inventory_2</span>
                                        </div>
                                    <?php endif; ?>
                                    <!-- offers label-->
                                    <?php if ($item['is_featured'] && !empty($item['offer_label'])): ?>
                                        <div class="absolute -top-2 -left-2 bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm"><?php echo htmlspecialchars($item['offer_label']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <!-- prodct inforamtion display -->
                                <div class="flex flex-1 flex-col justify-between gap-4">
                                    <div>
                                        <div class="flex justify-between items-start">
                                            <h3 class="text-lg font-bold text-text-main dark:text-white"><?php echo htmlspecialchars($item['name']); ?></h3>
                                            <span class="item-total text-lg font-bold text-text-main dark:text-white">Rs <?php echo number_format($item['item_total'], 2); ?></span>
                                        </div>
                                        <p class="text-sm text-text-secondary dark:text-gray-400 mt-1">SKU: <?php echo htmlspecialchars($item['sku']); ?> • Unit Price: Rs <?php echo number_format($item['discounted_price'], 2); ?></p>
                                        <?php if ($item['is_out_of_stock']): ?>
                                            <div class="flex items-center gap-1.5 mt-2 text-red-500 font-medium text-xs bg-red-50 dark:bg-red-900/20 w-fit px-2 py-1 rounded">
                                                <span class="material-symbols-outlined text-sm">error</span>
                                                Out of Stock
                                            </div>
                                        <?php elseif ($item['is_low_stock']): ?>
                                            <div class="flex items-center gap-1.5 mt-2 text-yellow-600 font-medium text-xs bg-yellow-50 dark:bg-yellow-900/20 w-fit px-2 py-1 rounded">
                                                <span class="material-symbols-outlined text-sm">inventory</span>
                                                Low Stock (Only <?php echo $item['stock_level']; ?> left)
                                            </div>
                                        <?php else: ?>
                                            <div class="flex items-center gap-1.5 mt-2 text-primary font-medium text-xs bg-primary/10 w-fit px-2 py-1 rounded">
                                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                                In Stock at Central Warehouse
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex justify-between items-end border-t border-gray-100 dark:border-white/5 pt-4">
                                        <button onclick="removeFromCart(<?php echo $item['cart_id']; ?>)" class="text-sm font-medium text-red-500 hover:text-red-600 flex items-center gap-1 transition-colors">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                            <span class="hidden sm:inline">Remove</span>
                                        </button>
                                        <div class="flex items-center gap-3 bg-[#f0f4f1] dark:bg-black/20 rounded-lg p-1">
                                            <button onclick="updateQuantity(<?php echo $item['cart_id']; ?>, -1)" class="w-8 h-8 flex items-center justify-center rounded bg-white dark:bg-white/10 shadow-sm hover:text-primary transition-colors text-text-main dark:text-white">
                                                <span class="material-symbols-outlined text-sm">remove</span>
                                            </button>
                                            <input class="input-stepper cart-quantity" data-cart-id="<?php echo $item['cart_id']; ?>" type="number" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_level']; ?>" readonly />
                                            <button onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 1)" class="w-8 h-8 flex items-center justify-center rounded bg-white dark:bg-white/10 shadow-sm hover:text-primary transition-colors text-text-main dark:text-white">
                                                <span class="material-symbols-outlined text-sm">add</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <!-- Cross Sell -->
                <!-- TO DO --->
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
                                    <span class="text-text-secondary dark:text-gray-400">Subtotal (<span id="itemCount"><?php echo count($cart_items); ?></span> items)</span>
                                    <span id="subtotalDisplay" class="font-semibold text-text-main dark:text-white">Rs <?php echo number_format($subtotal, 2); ?></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-secondary dark:text-gray-400">Distribution Fee</span>
                                    <span id="shippingDisplay" class="font-semibold text-text-main dark:text-white">Rs <?php echo number_format($shipping_fee, 2); ?></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-secondary dark:text-gray-400">Tax (12%)</span>
                                    <span id="taxDisplay" class="font-semibold text-text-main dark:text-white">Rs <?php echo number_format($tax_amount, 2); ?></span>
                                </div>
                            </div>
                            <div class="border-t border-dashed border-gray-200 dark:border-gray-700 my-4"></div>
                            <!-- Total -->
                            <div class="flex justify-between items-end mb-6">
                                <span class="text-base font-bold text-text-main dark:text-white">Total</span>
                                <div class="text-right">
                                    <span id="totalDisplay" class="block text-2xl font-black text-primary tracking-tight">Rs <?php echo number_format($total, 2); ?></span>
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
                            <?php if (!empty($cart_items)): ?>
                                <form action="checkout.php" method="POST">
                                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold text-lg py-4 rounded-xl shadow-lg shadow-primary/30 transition-all transform active:scale-[0.99] flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined">lock</span>
                                        Secure Checkout
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="../Catalog/catalog.php" class="w-full block text-center bg-gray-300 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-bold text-lg py-4 rounded-xl">
                                    Continue Shopping
                                </a>
                            <?php endif; ?>
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

        <!-- Mobile Bottom Nav Spacer -->
        <div class="h-16 lg:hidden"></div>
    </main>

    <!-- Mobile Bottom Nav -->
    <div class="fixed bottom-0 left-0 w-full bg-surface-light dark:bg-surface-dark border-t border-slate-200 dark:border-slate-800 flex lg:hidden justify-around py-3 px-2 z-40">
        <a href="../Dashboard/dashboard.php" class="flex flex-col items-center gap-1 text-text-secondary dark:text-emerald-400">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="text-[10px] font-medium">Home</span>
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
    <!-- footer -->
    <footer class="mt-auto border-t border-[#e7f3eb] dark:border-white/5 bg-surface-light dark:bg-surface-dark py-12">
        <div class="max-w-[1440px] mx-auto px-6 text-center">
            <p class="text-text-secondary dark:text-gray-500 text-sm">© <?php echo date("Y"); ?> DistriMgt Distribution Systems. All rights reserved.</p>
        </div>
    </footer>
</body>

<!-- script link  -->
<script src="../Cart/js/script.js"></script>

</html>