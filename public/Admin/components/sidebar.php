<!-- Sidebar Navigation Component -->
<aside id="sidebar" class="sidebar-expanded flex-shrink-0 flex flex-col bg-surface-light dark:bg-surface-dark border-r border-[#e7f3eb] dark:border-[#2a4034] hidden lg:flex">
    <div class="p-6 border-b border-[#e7f3eb] dark:border-[#2a4034]">
        <div class="flex items-center gap-3">
            <div class="bg-primary/20 p-2 rounded-lg text-primary">
                <span class="material-symbols-outlined text-3xl text-primary">shopping_bag_speed</span>
            </div>
            <div class="flex flex-col sidebar-text">
                <h1 class="text-base font-bold leading-none whitespace-nowrap"><?php echo htmlspecialchars($province) . " RDC"; ?></h1>
                <p class="text-text-secondary-light dark:text-text-secondary-dark text-xs font-medium mt-1">Staff Portal</p>
            </div>
        </div>
    </div>
    <nav class="flex-1 overflow-y-auto py-4 px-3 flex flex-col gap-5">
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'dasboard.php') ? 'bg-primary/10 text-primary' : 'text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034]'; ?> transition-colors font-medium group" href="<?php echo getRelativePath('Admin/Dasboard/dasboard.php'); ?>">
            <span class="material-symbols-outlined <?php echo (basename($_SERVER['PHP_SELF']) == 'dasboard.php') ? 'group-hover:scale-110' : 'text-text-secondary-light dark:text-text-secondary-dark group-hover:text-primary'; ?> transition-colors">dashboard</span>
            <span class="sidebar-text">Dashboard</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'bg-primary/10 text-primary' : 'text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034]'; ?> transition-colors font-medium group" href="<?php echo getRelativePath('Admin/Orders/orders.php'); ?>">
            <span class="material-symbols-outlined <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'group-hover:scale-110' : 'text-text-secondary-light dark:text-text-secondary-dark group-hover:text-primary'; ?> transition-colors">shopping_cart</span>
            <span class="sidebar-text">Orders</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'inventory.php') ? 'bg-primary/10 text-primary' : 'text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034]'; ?> transition-colors font-medium group" href="<?php echo getRelativePath('Admin/Inventory/inventory.php'); ?>">
            <span class="material-symbols-outlined <?php echo (basename($_SERVER['PHP_SELF']) == 'inventory.php') ? 'group-hover:scale-110' : 'text-text-secondary-light dark:text-text-secondary-dark group-hover:text-primary'; ?> transition-colors">inventory_2</span>
            <span class="sidebar-text">Inventory</span>
        </a>
        <div class="relative">
            <button type="button"
                onclick="toggleDropdown()"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['logistics.php', 'driver_mangemnt.php'])) ? 'bg-primary/10 text-primary' : 'text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034]'; ?> transition-colors font-medium group">
                <span class="material-symbols-outlined <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['logistics.php', 'driver_mangemnt.php'])) ? 'text-primary' : 'text-text-secondary-light dark:text-text-secondary-dark group-hover:text-primary'; ?> transition-colors">
                    local_shipping
                </span>
                <span class="sidebar-text">Logistics</span>
                <span id="arrow"
                    class="material-symbols-outlined ml-auto <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['logistics.php', 'driver_mangemnt.php'])) ? 'text-primary' : 'text-text-secondary-light dark:text-text-secondary-dark group-hover:text-primary'; ?> transition-transform duration-300 sidebar-text">
                    keyboard_arrow_down
                </span>
            </button>
            <!-- Dropdown -->
            <div id="logisticsDropdown"
                class="max-h-0 overflow-hidden transition-all duration-300 ml-10 sidebar-text <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['logistics.php', 'driver_mangemnt.php'])) ? 'max-h-[500px]' : 'hidden'; ?>">

                <div class="mt-1 space-y-1">

                    <!-- Live Tracking -->
                    <a href="<?php echo getRelativePath('Admin/Logistics/logistics.php'); ?>"
                        class="flex items-center gap-3 px-3 py-2 rounded-md text-sm <?php echo (basename($_SERVER['PHP_SELF']) == 'logistics.php') ? 'bg-primary/10 text-primary font-semibold' : 'text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034]'; ?> transition">

                        <span class="relative flex h-2.5 w-2.5">
                            <span class="<?php echo (basename($_SERVER['PHP_SELF']) == 'logistics.php') ? 'animate-ping' : ''; ?> absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-green-500"></span>
                        </span>

                        <span>Live Tracking</span>
                    </a>

                    <!-- Driver Management -->
                    <a href="<?php echo getRelativePath('Admin/Logistics/driver_mangemnt.php'); ?>"
                        class="flex items-center gap-3 px-3 py-2 rounded-md text-sm <?php echo (basename($_SERVER['PHP_SELF']) == 'driver_mangemnt.php') ? 'bg-primary/10 text-primary font-semibold' : 'text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034]'; ?> transition">

                        <span class="material-symbols-outlined <?php echo (basename($_SERVER['PHP_SELF']) == 'driver_mangemnt.php') ? 'text-primary' : 'text-text-secondary-light dark:text-text-secondary-dark'; ?> text-[18px]">
                            badge
                        </span>

                        <span>Driver Management</span>
                    </a>

                </div>
            </div>


        </div>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'user_managment.php') ? 'bg-primary/10 text-primary' : 'text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034]'; ?> transition-colors font-medium group" href="<?php echo getRelativePath('Admin/User_Management/user_managment.php'); ?>">
            <span class="material-symbols-outlined <?php echo (basename($_SERVER['PHP_SELF']) == 'user_managment.php') ? 'group-hover:scale-110' : 'text-text-secondary-light dark:text-text-secondary-dark group-hover:text-primary'; ?> transition-colors">account_child_invert</span>
            <span class="sidebar-text">User Management</span>
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'bg-primary/10 text-primary' : 'text-text-main-light dark:text-text-main-dark hover:bg-background-light dark:hover:bg-[#2a4034]'; ?> transition-colors font-medium group" href="<?php echo getRelativePath('Admin/Reports/reports.php'); ?>">
            <span class="material-symbols-outlined <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'group-hover:scale-110' : 'text-text-secondary-light dark:text-text-secondary-dark group-hover:text-primary'; ?> transition-colors">description</span>
            <span class="sidebar-text">Reports</span>
        </a>
    </nav>
    <div class="p-4 border-t border-[#e7f3eb] dark:border-[#2a4034] relative">
        <div class="flex items-center justify-between gap-3 p-2 rounded-lg hover:bg-background-light dark:hover:bg-[#2a4034]">
            <!-- User Info -->
            <div class="flex items-center gap-3 min-w-0">
                <div class="size-8 rounded-full bg-cover bg-center flex-shrink-0"
                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCHud_yjkjGz0pDgk3H4D4QYFsrO1s3InLEFvPAxiEQkwg2F8faGX2QWPTL1Xnz6nbukCyc6NjyJvbc997p3MJOzuvktnvnnByG5JwKvmnQyZygnoQBbmSGSu2aVDrbxPT9exPDEJ47vOpaj5hv_IcyKxCEaXMrHa3AdEfaM-Bm0Z3ablCWaVQf5UCa1raIfHzwXSaKoDYjNyzK4F6u1QMBAW5fvIqczinJn1QMGMwubGxZnlCQuyqOjuS2aOVX86NCpwnuBdGUrcU');">
                </div>
                <div class="flex flex-col sidebar-text min-w-0">
                    <p class="text-sm font-bold truncate">
                        <?php echo htmlspecialchars($full_name); ?>
                    </p>
                    <p class="text-xs text-text-secondary-light dark:text-text-secondary-dark truncate">
                        <?php echo htmlspecialchars($user_type); ?>
                    </p>
                </div>
            </div>
            <!-- Vertical Dots Menu -->
            <div class="relative sidebar-text flex-shrink-0">
                <button onclick="toggleUserMenu(event)"
                    class="p-2 rounded hover:bg-gray-200 dark:hover:bg-[#2a4034] focus:outline-none">
                    &#8942;
                </button>
                <!-- Dropdown -->
                <div id="userMenu"
                    class="hidden absolute right-0 bottom-full mb-2 w-32 bg-white dark:bg-[#1f2f26]
               border border-gray-200 dark:border-[#2a4034]
               rounded-lg shadow-lg z-[100]">
                    <a href="<?php echo getRelativePath('/public/logout/logout.php', true); ?>"
                        class="block px-4 py-2 text-sm text-red-600
                   hover:bg-gray-100 dark:hover:bg-[#2a4034]">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</aside>

<?php
// Helper function to get relative path based on current page location
function getRelativePath($targetPath, $isLogout = false)
{
    // Get current directory depth
    $currentPath = $_SERVER['PHP_SELF'];
    $currentDir = dirname($currentPath);

    // Count how many directories deep we are from /public/Admin/
    $depth = substr_count($currentDir, '/') - substr_count('/public/Admin', '/');

    // Build the base path
    $basePath = str_repeat('../', $depth);

    // For logout, we need to go back to public level
    if ($isLogout) {
        return $basePath . '../' . $targetPath;
    }

    return $basePath . $targetPath;
}
?>