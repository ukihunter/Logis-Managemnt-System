<?php
require_once '../../../config/admin_session.php';
require_once '../../../config/database.php';

// Get database connection
$conn = getDBConnection();

// Fetch driver statistics
$total_drivers = 0;
$active_drivers = 0;
$inactive_drivers = 0;

$result = $conn->query("SELECT COUNT(*) as total FROM drivers");
if ($result) {
    $total_drivers = $result->fetch_assoc()['total'];
}

$result = $conn->query("SELECT COUNT(*) as active FROM drivers WHERE status = 'active'");
if ($result) {
    $active_drivers = $result->fetch_assoc()['active'];
}

$result = $conn->query("SELECT COUNT(*) as inactive FROM drivers WHERE status IN ('inactive', 'on_leave')");
if ($result) {
    $inactive_drivers = $result->fetch_assoc()['inactive'];
}

// Fetch all drivers
$drivers_query = "SELECT * FROM drivers ORDER BY created_at DESC";
$drivers_result = $conn->query($drivers_query);
$drivers = [];
if ($drivers_result) {
    while ($row = $drivers_result->fetch_assoc()) {
        $drivers[] = $row;
    }
}
?>

<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Driver Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&amp;family=Noto+Sans:wght@400;500;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <?php include '../components/styles.php'; ?>

</head>

<body class="bg-background-light dark:bg-background-dark font-display text-gray-900 dark:text-gray-100 min-h-screen flex flex-col transition-colors duration-200">
    <!-- Navigation Bar / Header Area -->
    <div class="flex h-screen w-full overflow-hidden">
        <?php include '../components/sidebar.php'; ?>
        <!-- Main Content -->
        <main id="mainContent" class="flex-1 w-full max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-8 transition-all duration-300">

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div class="flex flex-col gap-1">
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">Driver Management</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-base max-w-2xl">
                        Manage fleet personnel, vehicle assignments, and track active distribution status across all regional centres.
                    </p>
                </div>
                <button onclick="openDriverPanel()" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg font-bold shadow-sm hover:shadow-md transition-all active:scale-95 shrink-0">
                    <span class="material-symbols-outlined">person_add</span>
                    Add New Driver
                </button>
            </div>
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-primary">groups</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Drivers</p>
                    <div class="flex items-end gap-2 mt-2">
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $total_drivers; ?></p>
                        <span class="text-sm text-gray-500 mb-1">registered</span>
                    </div>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-green-600">local_shipping</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Active Now</p>
                    <div class="flex items-end gap-2 mt-2">
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $active_drivers; ?></p>
                        <span class="text-sm text-gray-500 mb-1">on the road</span>
                    </div>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-gray-400">person_off</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Inactive / Leave</p>
                    <div class="flex items-end gap-2 mt-2">
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $inactive_drivers; ?></p>
                        <span class="text-sm text-gray-500 mb-1">unavailable</span>
                    </div>
                </div>
            </div>
            <!-- Filters & Tools -->
            <div class="bg-surface-light dark:bg-surface-dark rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm mb-6">
                <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
                    <!-- Search & Dropdowns -->
                    <div class="flex flex-col sm:flex-row gap-4 flex-1 w-full lg:w-auto">
                        <div class="relative flex-1 min-w-[280px]">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <span class="material-symbols-outlined">search</span>
                            </span>
                            <input id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" placeholder="Search by Name, ID, or License Plate" type="text" />
                        </div>
                        <div class="relative w-full sm:w-[220px]">
                            <select id="centreFilter" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary appearance-none cursor-pointer">
                                <option value="">All Distribution Centres</option>
                                <option value="North RDC - Woodlands">North RDC - Woodlands</option>
                                <option value="East RDC - Changi">East RDC - Changi</option>
                                <option value="West RDC - Jurong">West RDC - Jurong</option>
                            </select>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                <span class="material-symbols-outlined text-[20px]">expand_more</span>
                            </span>
                        </div>
                    </div>
                    <!-- Chips Filter -->
                    <div class="flex gap-2 overflow-x-auto pb-2 lg:pb-0 w-full lg:w-auto no-scrollbar">
                        <button onclick="filterByStatus('')" data-status="" class="status-filter whitespace-nowrap px-4 py-1.5 rounded-full bg-primary/10 text-primary border border-primary/20 text-sm font-bold flex items-center gap-2">
                            All Statuses
                        </button>
                        <button onclick="filterByStatus('active')" data-status="active" class="status-filter whitespace-nowrap px-4 py-1.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-transparent hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium transition-colors">
                            Active Only
                        </button>
                        <button onclick="filterByStatus('inactive,on_leave')" data-status="inactive,on_leave" class="status-filter whitespace-nowrap px-4 py-1.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-transparent hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium transition-colors">
                            Inactive Only
                        </button>
                    </div>
                </div>
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Driver</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Emp ID</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vehicle Assigned</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assigned RDC</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            <?php if (empty($drivers)): ?>
                                <tr>
                                    <td colspan="6" class="py-8 px-6 text-center text-gray-500 dark:text-gray-400">
                                        No drivers found. Click "Add New Driver" to get started.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($drivers as $driver):
                                    // Determine status badge
                                    $status_class = '';
                                    $status_text = '';
                                    $status_dot = '';

                                    switch ($driver['status']) {
                                        case 'active':
                                            $status_class = 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400';
                                            $status_text = 'Active';
                                            $status_dot = 'bg-green-500 animate-pulse';
                                            break;
                                        case 'inactive':
                                            $status_class = 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400';
                                            $status_text = 'Inactive';
                                            $status_dot = 'bg-gray-400';
                                            break;
                                        case 'on_leave':
                                            $status_class = 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400';
                                            $status_text = 'On Leave';
                                            $status_dot = 'bg-yellow-500';
                                            break;
                                    }

                                    // Determine vehicle icon
                                    $vehicle_icon = 'local_shipping';
                                    $vehicle_color = 'blue';
                                    switch ($driver['vehicle_type']) {
                                        case 'truck':
                                            $vehicle_icon = 'local_shipping';
                                            $vehicle_color = 'blue';
                                            break;
                                        case 'van':
                                            $vehicle_icon = 'airport_shuttle';
                                            $vehicle_color = 'orange';
                                            break;
                                        case 'motorcycle':
                                            $vehicle_icon = 'two_wheeler';
                                            $vehicle_color = 'purple';
                                            break;
                                    }
                                ?>
                                    <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                                                    <?php if ($driver['profile_image']): ?>
                                                        <img alt="Driver Avatar" class="h-full w-full object-cover" src="<?php echo htmlspecialchars($driver['profile_image']); ?>" />
                                                    <?php else: ?>
                                                        <span class="material-symbols-outlined text-gray-400">person</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($driver['full_name']); ?></span>
                                                    <span class="text-xs text-gray-500"><?php echo htmlspecialchars($driver['phone_number']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="text-sm font-mono text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($driver['employee_id']); ?></span>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div class="p-1.5 rounded bg-<?php echo $vehicle_color; ?>-50 dark:bg-<?php echo $vehicle_color; ?>-900/20 text-<?php echo $vehicle_color; ?>-600 dark:text-<?php echo $vehicle_color; ?>-400">
                                                    <span class="material-symbols-outlined text-[18px]"><?php echo $vehicle_icon; ?></span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($driver['vehicle_model'] ?: ucfirst($driver['vehicle_type'])); ?></span>
                                                    <span class="text-xs text-gray-500"><?php echo htmlspecialchars($driver['license_plate']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($driver['distribution_centre']); ?></span>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold <?php echo $status_class; ?>">
                                                <span class="w-1.5 h-1.5 rounded-full <?php echo $status_dot; ?>"></span>
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button onclick="viewDriver(<?php echo $driver['id']; ?>)" class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="View Details">
                                                    <span class="material-symbols-outlined">visibility</span>
                                                </button>
                                                <button onclick="editDriver(<?php echo $driver['id']; ?>)" class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Edit">
                                                    <span class="material-symbols-outlined">edit</span>
                                                </button>
                                                <button onclick="deleteDriver(<?php echo $driver['id']; ?>, '<?php echo htmlspecialchars($driver['full_name']); ?>')" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Delete">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Showing <span class="font-bold text-gray-900 dark:text-white"><?php echo min(1, count($drivers)); ?></span> to <span class="font-bold text-gray-900 dark:text-white"><?php echo count($drivers); ?></span> of <span class="font-bold text-gray-900 dark:text-white"><?php echo count($drivers); ?></span> drivers
                    </p>
                    <div class="flex gap-2">
                        <button disabled class="px-3 py-1.5 text-sm font-medium text-gray-500 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            Previous
                        </button>
                        <button disabled class="px-3 py-1.5 text-sm font-medium text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Right Panel for Add/Edit Driver -->
        <aside id="driverPanel" class="detail-panel hidden flex-col w-[28rem] bg-surface-light dark:bg-surface-dark border-l border-gray-200 dark:border-gray-800 fixed right-0 top-0 h-full z-40 shadow-2xl">
            <!-- Panel Header -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-white dark:bg-surface-dark sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary">badge</span>
                    </div>
                    <h2 id="panelTitle" class="text-xl font-bold text-gray-900 dark:text-white">Add New Driver</h2>
                </div>
                <button onclick="closeDriverPanel()" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Panel Content - Scrollable -->
            <div id="driverFormContainer" class="flex-1 overflow-y-auto p-6">
                <form id="driverForm" class="space-y-6">
                    <input type="hidden" id="driver_id" name="id" value="">
                    <input type="hidden" id="form_action" name="action" value="add">

                    <!-- Personal Information -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Personal Information</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name *</label>
                            <input type="text" name="full_name" id="full_name" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" placeholder="Enter driver's full name" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contact Number *</label>
                            <input type="tel" name="phone_number" id="phone_number" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" placeholder="+65 9123 4567" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                            <input type="email" name="email" id="email" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" placeholder="driver@example.com" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Driver License Number *</label>
                            <input type="text" name="license_number" id="license_number" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" placeholder="DL-123456789" />
                        </div>
                    </div>

                    <!-- Vehicle Assignment -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vehicle Assignment</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vehicle Type *</label>
                            <select name="vehicle_type" id="vehicle_type" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary appearance-none cursor-pointer">
                                <option value="">Select vehicle type</option>
                                <option value="van">Delivery Van</option>
                                <option value="truck">Cargo Truck</option>
                                <option value="motorcycle">Motorcycle</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vehicle Model</label>
                            <input type="text" name="vehicle_model" id="vehicle_model" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" placeholder="e.g., Toyota Hiace" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">License Plate *</label>
                            <input type="text" name="license_plate" id="license_plate" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" placeholder="ABC-1234" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Distribution Centre *</label>
                            <select name="distribution_centre" id="distribution_centre" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary appearance-none cursor-pointer">
                                <option value="">Assign to centre</option>
                                <option value="North RDC - Woodlands">North RDC - Woodlands</option>
                                <option value="East RDC - Changi">East RDC - Changi</option>
                                <option value="West RDC - Jurong">West RDC - Jurong</option>
                            </select>
                        </div>
                    </div>

                    <!-- Employment Details -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employment Details</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Start Date *</label>
                            <input type="date" name="start_date" id="start_date" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                            <select name="status" id="status" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary appearance-none cursor-pointer">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="on_leave">On Leave</option>
                            </select>
                        </div>
                    </div>
                </form>

                <!-- View Driver Details Container (Hidden by default) -->
                <div id="driverViewContainer" class="hidden space-y-6"></div>
            </div>

            <!-- Sticky Bottom Actions -->
            <div id="panelActions" class="p-6 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-surface-dark">
                <div class="flex gap-3">
                    <button onclick="closeDriverPanel()" type="button" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 font-bold text-sm transition-colors">
                        Cancel
                    </button>
                    <button type="submit" form="driverForm" id="submitBtn" class="flex-[2] flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-primary/90 text-white rounded-lg font-bold text-sm shadow-md shadow-primary/20 transition-all active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        <span id="submitBtnText">Add Driver</span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-surface-dark rounded-xl shadow-2xl max-w-md w-full transform transition-all">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-[28px]">warning</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Delete Driver</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">This action cannot be undone</p>
                        </div>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 mb-6">
                        Are you sure you want to delete <strong id="deleteDriverName" class="text-gray-900 dark:text-white"></strong>?
                        All data associated with this driver will be permanently removed.
                    </p>
                    <div class="flex gap-3">
                        <button onclick="closeDeleteModal()" type="button" class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 font-medium text-sm transition-colors">
                            Cancel
                        </button>
                        <button onclick="confirmDelete()" type="button" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium text-sm transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                            Delete Driver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../components/scripts.php'; ?>
    <script>
        let currentDriverId = null;

        function openDriverPanel(isEdit = false) {
            const sidebar = document.getElementById("sidebar");
            const driverPanel = document.getElementById("driverPanel");
            const mainContent = document.getElementById("mainContent");

            // Reset form for adding new driver (only if not editing or viewing)
            if (!isEdit) {
                // Show form, hide view
                document.getElementById('driverForm').classList.remove('hidden');
                document.getElementById('driverViewContainer').classList.add('hidden');
                document.getElementById('panelActions').classList.remove('hidden');

                document.getElementById('driverForm').reset();
                document.getElementById('panelTitle').textContent = 'Add New Driver';
                document.getElementById('submitBtnText').textContent = 'Add Driver';
                document.getElementById('form_action').value = 'add';
                document.getElementById('driver_id').value = '';
                currentDriverId = null;
            }

            // Collapse sidebar to icon-only mode
            sidebar.classList.remove("sidebar-expanded");
            sidebar.classList.add("sidebar-collapsed");

            // Shift main content to the left to make room for panel
            mainContent.style.marginRight = "28rem";

            // Show driver panel
            driverPanel.classList.remove("hidden");
            driverPanel.classList.add("flex");
            setTimeout(() => {
                driverPanel.classList.add("active");
            }, 10);
        }

        function closeDriverPanel() {
            const sidebar = document.getElementById("sidebar");
            const driverPanel = document.getElementById("driverPanel");
            const mainContent = document.getElementById("mainContent");

            // Hide driver panel with animation
            driverPanel.classList.remove("active");

            // Reset main content to center
            mainContent.style.marginRight = "";

            // Expand sidebar back to full width
            sidebar.classList.remove("sidebar-collapsed");
            sidebar.classList.add("sidebar-expanded");

            // Remove panel from DOM after animation completes
            setTimeout(() => {
                driverPanel.classList.add("hidden");
                driverPanel.classList.remove("flex");
            }, 300);
        }

        // Handle form submission
        document.getElementById('driverForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            const originalBtnText = submitBtnText.textContent;

            // Disable button and show loading state
            submitBtn.disabled = true;
            submitBtnText.textContent = 'Saving...';

            try {
                const response = await fetch('driver_handler.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Show success message
                    showNotification('success', result.message);

                    // Close panel and reload page
                    closeDriverPanel();
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    showNotification('error', result.message || 'An error occurred');
                }
            } catch (error) {
                showNotification('error', 'Failed to save driver: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtnText.textContent = originalBtnText;
            }
        });

        // View driver function
        async function viewDriver(id) {
            try {
                const response = await fetch(`driver_handler.php?action=get_single&id=${id}`);
                const result = await response.json();

                if (result.success) {
                    const driver = result.driver;

                    // Hide form, show view container
                    document.getElementById('driverForm').classList.add('hidden');
                    document.getElementById('driverViewContainer').classList.remove('hidden');
                    document.getElementById('panelActions').classList.add('hidden');

                    // Update panel title
                    document.getElementById('panelTitle').textContent = 'Driver Details';

                    // Get vehicle icon
                    let vehicleIcon = 'local_shipping';
                    let vehicleColor = 'blue';
                    switch (driver.vehicle_type) {
                        case 'truck':
                            vehicleIcon = 'local_shipping';
                            vehicleColor = 'blue';
                            break;
                        case 'van':
                            vehicleIcon = 'airport_shuttle';
                            vehicleColor = 'orange';
                            break;
                        case 'motorcycle':
                            vehicleIcon = 'two_wheeler';
                            vehicleColor = 'purple';
                            break;
                    }

                    // Get status badge
                    let statusClass = '';
                    let statusText = '';
                    switch (driver.status) {
                        case 'active':
                            statusClass = 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400';
                            statusText = 'Active';
                            break;
                        case 'inactive':
                            statusClass = 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400';
                            statusText = 'Inactive';
                            break;
                        case 'on_leave':
                            statusClass = 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400';
                            statusText = 'On Leave';
                            break;
                    }

                    // Build view HTML
                    const viewHTML = `
                        <!-- Driver Profile -->
                        <div class="flex flex-col items-center text-center pb-6 border-b border-gray-200 dark:border-gray-800">
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-4">
                                <span class="material-symbols-outlined text-5xl text-primary">person</span>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">${driver.full_name}</h3>
                            <p class="text-sm font-mono text-gray-500 dark:text-gray-400 mb-3">${driver.employee_id}</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold ${statusClass}">
                                ${statusText}
                            </span>
                        </div>

                        <!-- Contact Information -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact Information</h4>
                            
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-gray-400 text-[20px] mt-0.5">phone</span>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Phone Number</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${driver.phone_number}</p>
                                </div>
                            </div>

                            ${driver.email ? `
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-gray-400 text-[20px] mt-0.5">email</span>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Email Address</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${driver.email}</p>
                                </div>
                            </div>
                            ` : ''}

                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-gray-400 text-[20px] mt-0.5">badge</span>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">License Number</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${driver.license_number}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Information -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vehicle Assignment</h4>
                            
                            <div class="p-4 bg-gradient-to-br from-${vehicleColor}-50 to-${vehicleColor}-50/50 dark:from-${vehicleColor}-900/20 dark:to-${vehicleColor}-900/10 rounded-xl border border-${vehicleColor}-100 dark:border-${vehicleColor}-800/30">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-12 h-12 rounded-lg bg-white dark:bg-gray-800 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-${vehicleColor}-600 dark:text-${vehicleColor}-400 text-[28px]">${vehicleIcon}</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">${driver.vehicle_model || driver.vehicle_type.charAt(0).toUpperCase() + driver.vehicle_type.slice(1)}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">${driver.vehicle_type.charAt(0).toUpperCase() + driver.vehicle_type.slice(1)}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-${vehicleColor}-100 dark:border-${vehicleColor}-800/30">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">License Plate</span>
                                    <span class="text-sm font-bold font-mono text-gray-900 dark:text-white">${driver.license_plate}</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-gray-400 text-[20px] mt-0.5">location_on</span>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Distribution Centre</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${driver.distribution_centre}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Employment Details -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employment Details</h4>
                            
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-gray-400 text-[20px] mt-0.5">calendar_today</span>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Start Date</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${new Date(driver.start_date).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-gray-400 text-[20px] mt-0.5">schedule</span>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Joined</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${new Date(driver.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-4">
                            <button onclick="editDriver(${driver.id})" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-primary/90 text-white rounded-lg font-bold text-sm transition-colors">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                Edit Driver
                            </button>
                            <button onclick="deleteDriver(${driver.id}, '${driver.full_name}')" class="px-4 py-3 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 font-bold text-sm transition-colors">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    `;

                    document.getElementById('driverViewContainer').innerHTML = viewHTML;

                    // Open panel in view mode
                    openDriverPanel(true);
                } else {
                    showNotification('error', result.message || 'Failed to load driver data');
                }
            } catch (error) {
                showNotification('error', 'Failed to load driver: ' + error.message);
            }
        }

        // Edit driver function
        async function editDriver(id) {
            try {
                const response = await fetch(`driver_handler.php?action=get_single&id=${id}`);
                const result = await response.json();

                if (result.success) {
                    const driver = result.driver;

                    // Show form, hide view
                    document.getElementById('driverForm').classList.remove('hidden');
                    document.getElementById('driverViewContainer').classList.add('hidden');
                    document.getElementById('panelActions').classList.remove('hidden');

                    // Populate form fields
                    document.getElementById('driver_id').value = driver.id;
                    document.getElementById('full_name').value = driver.full_name;
                    document.getElementById('phone_number').value = driver.phone_number;
                    document.getElementById('email').value = driver.email || '';
                    document.getElementById('license_number').value = driver.license_number;
                    document.getElementById('vehicle_type').value = driver.vehicle_type;
                    document.getElementById('vehicle_model').value = driver.vehicle_model || '';
                    document.getElementById('license_plate').value = driver.license_plate;
                    document.getElementById('distribution_centre').value = driver.distribution_centre;
                    document.getElementById('start_date').value = driver.start_date;
                    document.getElementById('status').value = driver.status;

                    // Update panel title and button
                    document.getElementById('panelTitle').textContent = 'Edit Driver';
                    document.getElementById('submitBtnText').textContent = 'Update Driver';
                    document.getElementById('form_action').value = 'edit';
                    currentDriverId = id;

                    // Open panel in edit mode
                    openDriverPanel(true);
                } else {
                    showNotification('error', result.message || 'Failed to load driver data');
                }
            } catch (error) {
                showNotification('error', 'Failed to load driver: ' + error.message);
            }
        }

        // Delete driver variables
        let deleteDriverId = null;
        let deleteDriverName = null;

        // Delete driver function - show modal
        function deleteDriver(id, name) {
            deleteDriverId = id;
            deleteDriverName = name;
            document.getElementById('deleteDriverName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        // Close delete modal
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            deleteDriverId = null;
            deleteDriverName = null;
        }

        // Confirm delete
        async function confirmDelete() {
            if (!deleteDriverId) return;

            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', deleteDriverId);

                const response = await fetch('driver_handler.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                closeDeleteModal();

                if (result.success) {
                    showNotification('success', result.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    showNotification('error', result.message || 'Failed to delete driver');
                }
            } catch (error) {
                closeDeleteModal();
                showNotification('error', 'Failed to delete driver: ' + error.message);
            }
        }

        // Search and filter functionality
        let currentStatusFilter = '';
        let currentCentreFilter = '';

        // Search input handler
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            filterTable(searchTerm, currentCentreFilter, currentStatusFilter);
        });

        // Centre filter handler
        document.getElementById('centreFilter').addEventListener('change', function(e) {
            currentCentreFilter = e.target.value;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            filterTable(searchTerm, currentCentreFilter, currentStatusFilter);
        });

        // Status filter function
        function filterByStatus(status) {
            currentStatusFilter = status;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            filterTable(searchTerm, currentCentreFilter, currentStatusFilter);

            // Update button styles
            document.querySelectorAll('.status-filter').forEach(btn => {
                if (btn.dataset.status === status) {
                    btn.className = 'status-filter whitespace-nowrap px-4 py-1.5 rounded-full bg-primary/10 text-primary border border-primary/20 text-sm font-bold flex items-center gap-2';
                } else {
                    btn.className = 'status-filter whitespace-nowrap px-4 py-1.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-transparent hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium transition-colors';
                }
            });
        }

        // Filter table function
        function filterTable(searchTerm, centre, status) {
            const rows = document.querySelectorAll('tbody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                // Skip the "no drivers" row
                if (row.querySelector('td[colspan]')) {
                    return;
                }

                const driverName = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const empId = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const vehicle = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const assignedCentre = row.querySelector('td:nth-child(4)').textContent.trim();
                const statusBadge = row.querySelector('td:nth-child(5)').textContent.trim().toLowerCase();

                // Check search term
                const matchesSearch = !searchTerm ||
                    driverName.includes(searchTerm) ||
                    empId.includes(searchTerm) ||
                    vehicle.includes(searchTerm);

                // Check centre filter
                const matchesCentre = !centre || assignedCentre === centre;

                // Check status filter
                let matchesStatus = true;
                if (status) {
                    const statuses = status.split(',');
                    matchesStatus = statuses.some(s => {
                        if (s === 'active') return statusBadge.includes('active');
                        if (s === 'inactive') return statusBadge.includes('inactive');
                        if (s === 'on_leave') return statusBadge.includes('leave');
                        return false;
                    });
                }

                // Show/hide row
                if (matchesSearch && matchesCentre && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update pagination text if needed
            // You can add pagination update logic here
        }

        // Notification function
        function showNotification(type, message) {
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2`;
            notification.innerHTML = `
                <span class="material-symbols-outlined">${type === 'success' ? 'check_circle' : 'error'}</span>
                <span>${message}</span>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
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