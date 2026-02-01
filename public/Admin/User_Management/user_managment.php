<?php
// user_managment.php - User & Role Management Page
// session and database includes
require_once '../../../config/admin_session.php';

// Fetch users from database
require_once '../../../config/database.php';
$conn = getDBConnection();

// Pagination settings
$records_per_page = 5;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page); // Ensure page is at least 1
$offset = ($current_page - 1) * $records_per_page;

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$zone_filter = isset($_GET['zone']) ? $_GET['zone'] : '';

// Build query with filters
$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(full_name LIKE ? OR email LIKE ? OR username LIKE ? OR business_name LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ssss';
}

if (!empty($role_filter)) {
    $where_conditions[] = "user_type = ?";
    $params[] = $role_filter;
    $types .= 's';
}

if (!empty($zone_filter)) {
    $where_conditions[] = "province = ?";
    $params[] = $zone_filter;
    $types .= 's';
}

// Count total records with filters
$count_query = "SELECT COUNT(*) as total FROM users";
if (!empty($where_conditions)) {
    $count_query .= " WHERE " . implode(" AND ", $where_conditions);
}

if (!empty($params)) {
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param($types, ...$params);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_records = $count_result->fetch_assoc()['total'];
} else {
    $count_result = $conn->query($count_query);
    $total_records = $count_result->fetch_assoc()['total'];
}

$total_pages = ceil($total_records / $records_per_page);

// Fetch users with pagination and filters
$query = "SELECT * FROM users";
if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}
$query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $params[] = $records_per_page;
    $params[] = $offset;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $records_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Count statistics (from all users, not filtered)
$stats_query = "SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN user_type = 'customer' AND status = 'active' THEN 1 ELSE 0 END) as active_retailers,
    SUM(CASE WHEN user_type IN ('staff', 'admin') THEN 1 ELSE 0 END) as rdc_staff,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_users
    FROM users";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();
$total_users = $stats['total_users'];
$active_retailers = $stats['active_retailers'];
$rdc_staff = $stats['rdc_staff'];
$pending_users = $stats['pending_users'];

// Get unique provinces for filter dropdown
$province_query = "SELECT DISTINCT province FROM users WHERE province IS NOT NULL AND province != '' ORDER BY province";
$province_result = $conn->query($province_query);
$provinces = [];
while ($row = $province_result->fetch_assoc()) {
    $provinces[] = $row['province'];
}
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>User &amp; Role Management - IslandDistro</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#11d452",
                        "background-light": "#f6f8f6",
                        "background-dark": "#102216",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a2e22",
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
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .icon-filled {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .detail-panel {
            transition: transform 0.3s ease-in-out;
        }
    </style>
    <?php include '../components/styles.php'; ?>
</head>

<body class="bg-background-light dark:bg-background-dark text-[#0d1b12] dark:text-gray-100 font-display transition-colors duration-200">
    <!-- Notification Toast -->
    <div id="notification" class="fixed top-4 right-4 z-[100] transform translate-x-[500px] transition-transform duration-300">
        <div id="notificationContent" class="flex items-center gap-3 min-w-[320px] px-4 py-3 rounded-lg shadow-lg">
            <span id="notificationIcon" class="material-symbols-outlined"></span>
            <span id="notificationMessage" class="text-sm font-medium"></span>
        </div>
    </div>

    <div class="relative flex h-screen w-full overflow-hidden">
        <?php include '../components/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="flex h-full flex-1 flex-col overflow-hidden bg-background-light dark:bg-background-dark">
            <!-- Top Header -->
            <header class="flex items-center justify-between border-b border-[#e7f3eb] dark:border-gray-800 bg-surface-light dark:bg-surface-dark px-8 py-3 shrink-0">
                <div class="flex items-center gap-4">
                    <h2 class="text-[#0d1b12] dark:text-white text-lg font-bold">User &amp; Role Management</h2>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative hidden md:block">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <span class="material-symbols-outlined" style="font-size: 20px;">search</span>
                        </span>
                        <input class="h-10 w-64 rounded-lg bg-[#f8fcf9] dark:bg-gray-800 pl-10 pr-4 text-sm outline-none border-none focus:ring-1 focus:ring-primary dark:text-white transition-all" placeholder="Global search..." type="text" disabled />
                    </div>
                    <!--  <button class="flex size-10 items-center justify-center rounded-lg hover:bg-[#e7f3eb] dark:hover:bg-primary/20 text-[#0d1b12] dark:text-white transition-colors relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2.5 right-2.5 size-2 rounded-full bg-red-500 border border-white dark:border-gray-900"></span>
                    </button> -->
                </div>
            </header>
            <!-- Scrollable Content -->
            <main id="mainContent" class="flex-1 overflow-y-auto p-8 transition-all duration-300">
                <div class="mx-auto max-w-[1200px] flex flex-col gap-6">
                    <!-- Breadcrumbs -->
                    <nav class="flex items-center text-sm font-medium">
                        <a class="text-gray-500 hover:text-primary transition-colors" href="#">Home</a>
                        <span class="mx-2 text-gray-400">/</span>
                        <a class="text-gray-500 hover:text-primary transition-colors" href="#">Settings</a>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-primary font-bold">Users</span>
                    </nav>
                    <!-- Page Header & Main Actions -->
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div class="flex flex-col gap-1">
                            <h1 class="text-3xl font-black text-[#0d1b12] dark:text-white tracking-tight">User Management</h1>
                            <p class="text-gray-500 dark:text-gray-400 text-base max-w-2xl">Manage system access, roles, and permissions across RDCs, retail partners, and logistics teams.</p>
                        </div>
                        <div class="flex gap-3">
                            <button class="flex items-center gap-2 px-4 h-10 rounded-lg border border-[#e7f3eb] bg-surface-light dark:bg-surface-dark dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-[#0d1b12] dark:text-white text-sm font-bold transition-all shadow-sm">
                                <span class="material-symbols-outlined" style="font-size: 20px;">download</span>
                                Export
                            </button>
                            <button onclick="openUserPanel()" class="flex items-center gap-2 px-4 h-10 rounded-lg bg-primary hover:bg-[#0ebf4a] text-white text-sm font-bold transition-all shadow-md shadow-primary/20">
                                <span class="material-symbols-outlined" style="font-size: 20px;">add</span>
                                Add New User
                            </button>
                        </div>
                    </div>
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#e7f3eb] dark:border-gray-700 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex items-center justify-between">
                                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Users</p>
                                <span class="material-symbols-outlined text-primary bg-primary/10 p-1 rounded-md" style="font-size: 20px;">group</span>
                            </div>
                            <div class="flex items-baseline gap-2 mt-2">
                                <p class="text-[#0d1b12] dark:text-white text-2xl font-bold"><?php echo $total_users; ?></p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#e7f3eb] dark:border-gray-700 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex items-center justify-between">
                                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Active Retailers</p>
                                <span class="material-symbols-outlined text-blue-500 bg-blue-500/10 p-1 rounded-md" style="font-size: 20px;">storefront</span>
                            </div>
                            <div class="flex items-baseline gap-2 mt-2">
                                <p class="text-[#0d1b12] dark:text-white text-2xl font-bold"><?php echo $active_retailers; ?></p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#e7f3eb] dark:border-gray-700 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex items-center justify-between">
                                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">RDC Staff</p>
                                <span class="material-symbols-outlined text-purple-500 bg-purple-500/10 p-1 rounded-md" style="font-size: 20px;">warehouse</span>
                            </div>
                            <div class="flex items-baseline gap-2 mt-2">
                                <p class="text-[#0d1b12] dark:text-white text-2xl font-bold"><?php echo $rdc_staff; ?></p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#e7f3eb] dark:border-gray-700 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex items-center justify-between">
                                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Pending</p>
                                <span class="material-symbols-outlined text-orange-500 bg-orange-500/10 p-1 rounded-md" style="font-size: 20px;">pending_actions</span>
                            </div>
                            <div class="flex items-baseline gap-2 mt-2">
                                <p class="text-[#0d1b12] dark:text-white text-2xl font-bold"><?php echo $pending_users; ?></p>
                                <?php if ($pending_users > 0): ?>
                                    <p class="text-orange-500 text-xs font-bold bg-orange-500/10 px-1.5 py-0.5 rounded">Action Req.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- Filters & Toolbar -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-between items-center bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-[#e7f3eb] dark:border-gray-700 shadow-sm">
                        <form method="GET" action="" class="flex flex-1 gap-4 w-full sm:w-auto">
                            <div class="relative flex-1 sm:max-w-xs">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">search</span>
                                </span>
                                <input name="search" value="<?php echo htmlspecialchars($search); ?>" class="w-full h-10 rounded-lg bg-[#f8fcf9] dark:bg-gray-800 border-none pl-10 pr-4 text-sm focus:ring-1 focus:ring-primary dark:text-white shadow-inner" placeholder="Search user, email, or ID..." type="text" />
                            </div>
                            <div class="hidden sm:block">
                                <select name="role" onchange="this.form.submit()" class="h-10 rounded-lg bg-[#f8fcf9] dark:bg-gray-800 border-none px-4 text-sm focus:ring-1 focus:ring-primary dark:text-white shadow-inner cursor-pointer text-gray-600 dark:text-gray-300">
                                    <option value="">All Roles</option>
                                    <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="staff" <?php echo $role_filter === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                    <option value="customer" <?php echo $role_filter === 'customer' ? 'selected' : ''; ?>>Customer</option>
                                </select>
                            </div>
                            <div class="hidden sm:block">
                                <select name="zone" onchange="this.form.submit()" class="h-10 rounded-lg bg-[#f8fcf9] dark:bg-gray-800 border-none px-4 text-sm focus:ring-1 focus:ring-primary dark:text-white shadow-inner cursor-pointer text-gray-600 dark:text-gray-300">
                                    <option value="">All Zones</option>
                                    <?php foreach ($provinces as $province): ?>
                                        <option value="<?php echo htmlspecialchars($province); ?>" <?php echo $zone_filter === $province ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($province); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="hidden sm:flex items-center gap-2 px-4 h-10 rounded-lg bg-primary hover:bg-[#0ebf4a] text-white text-sm font-bold transition-all">
                                <span class="material-symbols-outlined" style="font-size: 20px;">search</span>
                                Search
                            </button>
                            <?php if (!empty($search) || !empty($role_filter) || !empty($zone_filter)): ?>
                                <a href="user_managment.php" class="flex items-center gap-2 px-4 h-10 rounded-lg border border-[#e7f3eb] hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 text-sm font-bold transition-all">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
                                    Clear
                                </a>
                            <?php endif; ?>
                        </form>
                        <div class="flex gap-2">
                            <button class="flex items-center justify-center size-10 rounded-lg border border-[#e7f3eb] hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition-colors" title="Filter">
                                <span class="material-symbols-outlined" style="font-size: 20px;">filter_list</span>
                            </button>
                            <button class="flex items-center justify-center size-10 rounded-lg border border-[#e7f3eb] hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition-colors" title="Settings">
                                <span class="material-symbols-outlined" style="font-size: 20px;">settings</span>
                            </button>
                        </div>
                    </div>
                    <!-- Users Table -->
                    <div class="overflow-hidden rounded-xl border border-[#e7f3eb] dark:border-gray-700 bg-surface-light dark:bg-surface-dark shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-[#f8fcf9] dark:bg-gray-800/50 border-b border-[#e7f3eb] dark:border-gray-700">
                                        <th class="p-4 w-12 text-center">
                                            <input class="rounded border-gray-300 text-primary focus:ring-primary" type="checkbox" />
                                        </th>
                                        <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                                        <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Zone / RDC</th>
                                        <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Last Login</th>
                                        <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="p-4 w-12"></th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody" class="divide-y divide-[#e7f3eb] dark:divide-gray-700">
                                    <?php if (count($users) > 0): ?>
                                        <?php foreach ($users as $user): ?>
                                            <tr class="group hover:bg-[#f8fcf9] dark:hover:bg-gray-800/30 transition-colors">
                                                <td class="p-4 text-center">
                                                    <input class="rounded border-gray-300 text-primary focus:ring-primary" type="checkbox" />
                                                </td>
                                                <td class="p-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center">
                                                            <span class="text-primary font-bold text-sm"><?php echo strtoupper(substr($user['full_name'], 0, 2)); ?></span>
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <span class="font-bold text-[#0d1b12] dark:text-white text-sm"><?php echo htmlspecialchars($user['full_name']); ?></span>
                                                            <span class="text-xs text-gray-500"><?php echo htmlspecialchars($user['email']); ?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="p-4">
                                                    <?php
                                                    $roleColors = [
                                                        'admin' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                                                        'staff' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                                        'customer' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                                    ];
                                                    $colorClass = $roleColors[$user['user_type']] ?? 'bg-gray-100 text-gray-700';
                                                    ?>
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold <?php echo $colorClass; ?>">
                                                        <?php echo ucfirst($user['user_type']); ?>
                                                    </span>
                                                </td>
                                                <td class="p-4">
                                                    <div class="flex items-center gap-2">
                                                        <span class="material-symbols-outlined text-gray-400" style="font-size: 16px;">location_on</span>
                                                        <span class="text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($user['province'] ?? 'N/A'); ?></span>
                                                    </div>
                                                </td>
                                                <td class="p-4 text-sm text-gray-600 dark:text-gray-400">
                                                    <?php
                                                    $created = new DateTime($user['created_at']);
                                                    $now = new DateTime();
                                                    $diff = $now->diff($created);
                                                    if ($diff->days == 0) echo "Today";
                                                    else if ($diff->days == 1) echo "Yesterday";
                                                    else echo $diff->days . " days ago";
                                                    ?>
                                                </td>
                                                <td class="p-4">
                                                    <?php
                                                    $statusColors = [
                                                        'active' => ['bg' => 'bg-primary', 'text' => 'text-gray-700 dark:text-gray-300'],
                                                        'inactive' => ['bg' => 'bg-gray-400', 'text' => 'text-gray-500'],
                                                        'pending' => ['bg' => 'bg-orange-500', 'text' => 'text-orange-700 dark:text-orange-300']
                                                    ];
                                                    $statusStyle = $statusColors[$user['status']] ?? ['bg' => 'bg-gray-400', 'text' => 'text-gray-500'];
                                                    ?>
                                                    <div class="flex items-center gap-2">
                                                        <div class="size-2 rounded-full <?php echo $statusStyle['bg']; ?>"></div>
                                                        <span class="text-sm font-medium <?php echo $statusStyle['text']; ?>"><?php echo ucfirst($user['status']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="p-4 text-right">
                                                    <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                                        <button onclick="viewUserDetails(<?php echo $user['id']; ?>)" class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-blue-600 dark:text-blue-400 transition-colors" title="View Details">
                                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                                        </button>
                                                        <button onclick="editUser(<?php echo $user['id']; ?>)" class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-primary transition-colors" title="Edit User">
                                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                                        </button>
                                                        <button onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name']); ?>')" class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-red-500 transition-colors" title="Delete User">
                                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="p-8 text-center text-gray-500">
                                                No users found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="flex items-center justify-between px-4 py-3 border-t border-[#e7f3eb] dark:border-gray-700 bg-[#fcfdfd] dark:bg-gray-800/30">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Showing <span class="font-bold text-gray-800 dark:text-gray-200"><?php echo $offset + 1; ?>-<?php echo min($offset + $records_per_page, $total_records); ?></span> of <span class="font-bold text-gray-800 dark:text-gray-200"><?php echo $total_records; ?></span> users
                            </div>
                            <div class="flex gap-2">
                                <?php if ($current_page > 1): ?>
                                    <a href="?page=<?php echo $current_page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($role_filter) ? '&role=' . urlencode($role_filter) : ''; ?><?php echo !empty($zone_filter) ? '&zone=' . urlencode($zone_filter) : ''; ?>" class="px-3 py-1 rounded border border-[#e7f3eb] dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium transition-colors">Previous</a>
                                <?php else: ?>
                                    <button disabled class="px-3 py-1 rounded border border-[#e7f3eb] dark:border-gray-600 text-gray-400 cursor-not-allowed text-sm font-medium">Previous</button>
                                <?php endif; ?>

                                <!-- Page numbers -->
                                <div class="flex gap-1">
                                    <?php
                                    $start_page = max(1, $current_page - 2);
                                    $end_page = min($total_pages, $current_page + 2);

                                    for ($i = $start_page; $i <= $end_page; $i++):
                                    ?>
                                        <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($role_filter) ? '&role=' . urlencode($role_filter) : ''; ?><?php echo !empty($zone_filter) ? '&zone=' . urlencode($zone_filter) : ''; ?>"
                                            class="px-3 py-1 rounded border text-sm font-medium transition-colors <?php echo $i === $current_page ? 'bg-primary text-white border-primary' : 'border-[#e7f3eb] dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                </div>

                                <?php if ($current_page < $total_pages): ?>
                                    <a href="?page=<?php echo $current_page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($role_filter) ? '&role=' . urlencode($role_filter) : ''; ?><?php echo !empty($zone_filter) ? '&zone=' . urlencode($zone_filter) : ''; ?>" class="px-3 py-1 rounded border border-[#e7f3eb] dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium transition-colors">Next</a>
                                <?php else: ?>
                                    <button disabled class="px-3 py-1 rounded border border-[#e7f3eb] dark:border-gray-600 text-gray-400 cursor-not-allowed text-sm font-medium">Next</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Right Panel for Add/Edit User -->
            <div id="userPanel" class="detail-panel fixed right-0 top-0 h-full w-[450px] bg-surface-light dark:bg-surface-dark border-l border-[#e7f3eb] dark:border-gray-700 shadow-2xl overflow-y-auto translate-x-full z-50">
                <div class="flex flex-col h-full">
                    <!-- Panel Header -->
                    <div class="flex items-center justify-between p-6 border-b border-[#e7f3eb] dark:border-gray-700">
                        <h2 class="text-xl font-bold text-[#0d1b12] dark:text-white">Add New User</h2>
                        <button onclick="closeUserPanel()" class="text-gray-400 hover:text-[#0d1b12] dark:hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Panel Content -->
                    <form id="userForm" class="flex-1 p-6 space-y-6">
                        <input type="hidden" id="userId" name="user_id" value="">
                        <input type="hidden" id="formAction" name="action" value="add">
                        <!-- Account Type -->
                        <div>
                            <label class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Account Type</label>
                            <select id="userTypeSelect" name="user_type" onchange="toggleFieldsByUserType()" class="w-full px-4 py-2.5 rounded-lg border border-[#e7f3eb] dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0d1b12] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="customer">Customer / Retailer</option>
                                <option value="staff">Staff Member</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>

                        <!-- Business Name -->
                        <div id="businessNameField">
                            <label class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Business Name</label>
                            <input type="text" name="business_name" placeholder="e.g., ABC Grocers" class="w-full px-4 py-2.5 rounded-lg border border-[#e7f3eb] dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0d1b12] dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent" />
                        </div>

                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Full Name *</label>
                            <input type="text" name="full_name" required placeholder="saman" class="w-full px-4 py-2.5 rounded-lg border border-[#e7f3eb] dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0d1b12] dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent" />
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Email Address *</label>
                            <input type="email" name="email" required placeholder="saman@example.com" class="w-full px-4 py-2.5 rounded-lg border border-[#e7f3eb] dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0d1b12] dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent" />
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Username *</label>
                            <input type="text" name="username" required placeholder="saman123" class="w-full px-4 py-2.5 rounded-lg border border-[#e7f3eb] dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0d1b12] dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Password *</label>
                            <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-lg border border-[#e7f3eb] dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0d1b12] dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent" />
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Phone Number</label>
                            <input type="tel" name="phone_number" placeholder="+94 71 234 5678" class="w-full px-4 py-2.5 rounded-lg border border-[#e7f3eb] dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0d1b12] dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent" />
                        </div>

                        <!-- Address -->
                        <div id="addressField">
                            <label class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Address</label>
                            <textarea name="address" rows="2" placeholder="123 Main Street, City" class="w-full px-4 py-2.5 rounded-lg border border-[#e7f3eb] dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0d1b12] dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent resize-none"></textarea>
                        </div>

                        <!-- Province -->
                        <div id="provinceField">
                            <label class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Province / Zone</label>
                            <select name="province" class="w-full px-4 py-2.5 rounded-lg border border-[#e7f3eb] dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0d1b12] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Select Province</option>
                                <option value="Western">Western Province</option>
                                <option value="Central">Central Province</option>
                                <option value="Southern">Southern Province</option>
                                <option value="Northern">Northern Province</option>
                                <option value="Eastern">Eastern Province</option>
                                <option value="North Western">North Western Province</option>
                                <option value="North Central">North Central Province</option>
                                <option value="Uva">Uva Province</option>
                                <option value="Sabaragamuwa">Sabaragamuwa Province</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-bold text-[#0d1b12] dark:text-white mb-2">Account Status</label>
                            <select name="status" class="w-full px-4 py-2.5 rounded-lg border border-[#e7f3eb] dark:border-gray-600 bg-white dark:bg-gray-800 text-[#0d1b12] dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="active">Active</option>
                                <option value="pending">Pending Approval</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-4 border-t border-[#e7f3eb] dark:border-gray-700">
                            <button type="button" onclick="closeUserPanel()" class="flex-1 px-4 py-2.5 rounded-lg border border-[#e7f3eb] dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 text-[#0d1b12] dark:text-white font-bold transition-all">
                                Cancel
                            </button>
                            <button type="submit" class="flex-1 px-4 py-2.5 rounded-lg bg-primary hover:bg-[#0ebf4a] text-white font-bold transition-all shadow-md shadow-primary/20">
                                Add User
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- View User Details Panel -->
            <div id="viewUserPanel" class="detail-panel fixed right-0 top-0 h-full w-[450px] bg-surface-light dark:bg-surface-dark border-l border-[#e7f3eb] dark:border-gray-700 shadow-2xl overflow-y-auto translate-x-full z-50">
                <div class="flex flex-col h-full">
                    <!-- Panel Header -->
                    <div class="flex items-center justify-between p-6 border-b border-[#e7f3eb] dark:border-gray-700">
                        <h2 class="text-xl font-bold text-[#0d1b12] dark:text-white">User Details</h2>
                        <button onclick="closeViewPanel()" class="text-gray-400 hover:text-[#0d1b12] dark:hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Panel Content -->
                    <div id="userDetailsContent" class="flex-1 p-6 space-y-6">
                        <!-- User details will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-[#152e1e] rounded-xl shadow-2xl max-w-md w-full transform transition-all">
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-[28px]">warning</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-[#0d1b12] dark:text-white">Delete User</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">This action cannot be undone</p>
                            </div>
                        </div>
                        <p class="text-sm text-[#0d1b12] dark:text-white mb-6">
                            Are you sure you want to delete <strong id="deleteUserName"></strong>? All associated data will be permanently removed.
                        </p>
                        <div class="flex gap-3">
                            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 text-[#0d1b12] dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 font-medium text-sm transition-colors">
                                Cancel
                            </button>
                            <button onclick="confirmDelete()" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium text-sm transition-colors">
                                Delete User
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../components/scripts.php'; ?>
    <script src="./js/script.js"></script>
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
        <a href="../Logistics/logistics.php" class="flex items-center gap-3 px-4 py-3 hover:bg-background-light dark:hover:bg-[#2a4034] transition-colors border-b border-[#e7f3eb] dark:border-[#2a4034]">
            <span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark">local_shipping</span>
            <span class="text-sm font-medium">Logistics</span>
        </a>
        <a href="user_managment.php" class="flex items-center gap-3 px-4 py-3 bg-primary/10 text-primary transition-colors <?php echo ($user_type === 'admin') ? 'border-b border-[#e7f3eb] dark:border-[#2a4034]' : ''; ?>">
            <span class="material-symbols-outlined">account_child_invert</span>
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