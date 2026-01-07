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
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-[#0d1b12] dark:text-gray-100 font-display transition-colors duration-200">
    <div class="relative flex h-screen w-full overflow-hidden">
        <!-- Sidebar -->
        <aside class="flex w-64 flex-col border-r border-[#e7f3eb] dark:border-gray-800 bg-surface-light dark:bg-surface-dark transition-all duration-300">
            <div class="flex h-16 items-center px-6 border-b border-[#e7f3eb] dark:border-gray-800">
                <div class="flex flex-col">
                    <h1 class="text-[#0d1b12] dark:text-white text-lg font-bold leading-normal">IslandDistro</h1>
                    <p class="text-primary text-xs font-medium leading-normal tracking-wide">ADMIN PORTAL</p>
                </div>
            </div>
            <div class="flex flex-1 flex-col justify-between overflow-y-auto p-4">
                <nav class="flex flex-col gap-2">
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-[#e7f3eb] dark:hover:bg-primary/10 hover:text-primary transition-colors" href="#">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-[#e7f3eb] dark:bg-primary/20 text-primary dark:text-primary" href="#">
                        <span class="material-symbols-outlined icon-filled">group</span>
                        <span class="text-sm font-bold">User Management</span>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-[#e7f3eb] dark:hover:bg-primary/10 hover:text-primary transition-colors" href="#">
                        <span class="material-symbols-outlined">inventory_2</span>
                        <span class="text-sm font-medium">Inventory</span>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-[#e7f3eb] dark:hover:bg-primary/10 hover:text-primary transition-colors" href="#">
                        <span class="material-symbols-outlined">local_shipping</span>
                        <span class="text-sm font-medium">Logistics</span>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-[#e7f3eb] dark:hover:bg-primary/10 hover:text-primary transition-colors" href="#">
                        <span class="material-symbols-outlined">analytics</span>
                        <span class="text-sm font-medium">Reports</span>
                    </a>
                </nav>
                <div class="flex flex-col gap-2 border-t border-[#e7f3eb] dark:border-gray-800 pt-4">
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-[#e7f3eb] dark:hover:bg-primary/10 hover:text-primary transition-colors" href="#">
                        <span class="material-symbols-outlined">settings</span>
                        <span class="text-sm font-medium">Settings</span>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-[#e7f3eb] dark:hover:bg-primary/10 hover:text-primary transition-colors" href="#">
                        <span class="material-symbols-outlined">help</span>
                        <span class="text-sm font-medium">Support</span>
                    </a>
                </div>
            </div>
            <div class="p-4 border-t border-[#e7f3eb] dark:border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="bg-center bg-no-repeat bg-cover rounded-full size-10 border-2 border-primary/30" data-alt="User avatar of logged in admin" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCc0tPvuQMFwD4pkSPA3BBDJ-zewt2rFw3bHrygo8kTCJu5F57DF6qT4dhlMd3MLkGONreF3VpyRAtur8hrSCAEuUX_0wl-MKRrbfpwZDQayvHW_oFy9YlWa41OcEwTeSGeW6JzX_S7fq69gjFEyImry2NSX8MZixrCTtX-oNOd5TJuCPjjKRzkPhVfBWNmKA-H1Kp4sJgurx-Xa_OLJ9-qB_ngeYUbEkrddlRtfdQUK_sBicYj50Ar6s8KEZ0wDrgDZ9BFyttXjCY");'></div>
                    <div class="flex flex-col">
                        <p class="text-sm font-bold text-[#0d1b12] dark:text-white">James K.</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Super Admin</p>
                    </div>
                </div>
            </div>
        </aside>
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
                        <input class="h-10 w-64 rounded-lg bg-[#f8fcf9] dark:bg-gray-800 pl-10 pr-4 text-sm outline-none border-none focus:ring-1 focus:ring-primary dark:text-white transition-all" placeholder="Global search..." type="text" />
                    </div>
                    <button class="flex size-10 items-center justify-center rounded-lg hover:bg-[#e7f3eb] dark:hover:bg-primary/20 text-[#0d1b12] dark:text-white transition-colors relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2.5 right-2.5 size-2 rounded-full bg-red-500 border border-white dark:border-gray-900"></span>
                    </button>
                </div>
            </header>
            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto p-8">
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
                            <button class="flex items-center gap-2 px-4 h-10 rounded-lg bg-primary hover:bg-[#0ebf4a] text-white text-sm font-bold transition-all shadow-md shadow-primary/20">
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
                                <p class="text-[#0d1b12] dark:text-white text-2xl font-bold">1,240</p>
                                <p class="text-primary text-xs font-bold bg-primary/10 px-1.5 py-0.5 rounded">+12%</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#e7f3eb] dark:border-gray-700 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex items-center justify-between">
                                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Active Retailers</p>
                                <span class="material-symbols-outlined text-blue-500 bg-blue-500/10 p-1 rounded-md" style="font-size: 20px;">storefront</span>
                            </div>
                            <div class="flex items-baseline gap-2 mt-2">
                                <p class="text-[#0d1b12] dark:text-white text-2xl font-bold">850</p>
                                <p class="text-primary text-xs font-bold bg-primary/10 px-1.5 py-0.5 rounded">+5%</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#e7f3eb] dark:border-gray-700 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex items-center justify-between">
                                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">RDC Staff</p>
                                <span class="material-symbols-outlined text-purple-500 bg-purple-500/10 p-1 rounded-md" style="font-size: 20px;">warehouse</span>
                            </div>
                            <div class="flex items-baseline gap-2 mt-2">
                                <p class="text-[#0d1b12] dark:text-white text-2xl font-bold">120</p>
                                <p class="text-gray-400 text-xs font-medium">Stable</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 rounded-xl p-5 border border-[#e7f3eb] dark:border-gray-700 bg-surface-light dark:bg-surface-dark shadow-sm">
                            <div class="flex items-center justify-between">
                                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Pending</p>
                                <span class="material-symbols-outlined text-orange-500 bg-orange-500/10 p-1 rounded-md" style="font-size: 20px;">pending_actions</span>
                            </div>
                            <div class="flex items-baseline gap-2 mt-2">
                                <p class="text-[#0d1b12] dark:text-white text-2xl font-bold">15</p>
                                <p class="text-orange-500 text-xs font-bold bg-orange-500/10 px-1.5 py-0.5 rounded">Action Req.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Filters & Toolbar -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-between items-center bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-[#e7f3eb] dark:border-gray-700 shadow-sm">
                        <div class="flex flex-1 gap-4 w-full sm:w-auto">
                            <div class="relative flex-1 sm:max-w-xs">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">search</span>
                                </span>
                                <input class="w-full h-10 rounded-lg bg-[#f8fcf9] dark:bg-gray-800 border-none pl-10 pr-4 text-sm focus:ring-1 focus:ring-primary dark:text-white shadow-inner" placeholder="Search user, email, or ID..." type="text" />
                            </div>
                            <div class="hidden sm:block">
                                <select class="h-10 rounded-lg bg-[#f8fcf9] dark:bg-gray-800 border-none px-4 text-sm focus:ring-1 focus:ring-primary dark:text-white shadow-inner cursor-pointer text-gray-600 dark:text-gray-300">
                                    <option value="">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="rdc">RDC Staff</option>
                                    <option value="retail">Retailer</option>
                                    <option value="logistics">Logistics</option>
                                </select>
                            </div>
                            <div class="hidden sm:block">
                                <select class="h-10 rounded-lg bg-[#f8fcf9] dark:bg-gray-800 border-none px-4 text-sm focus:ring-1 focus:ring-primary dark:text-white shadow-inner cursor-pointer text-gray-600 dark:text-gray-300">
                                    <option value="">All Zones</option>
                                    <option value="north">North Zone</option>
                                    <option value="south">South Zone</option>
                                    <option value="central">Central Hub</option>
                                </select>
                            </div>
                        </div>
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
                                <tbody class="divide-y divide-[#e7f3eb] dark:divide-gray-700">
                                    <!-- Row 1 -->
                                    <tr class="group hover:bg-[#f8fcf9] dark:hover:bg-gray-800/30 transition-colors">
                                        <td class="p-4 text-center">
                                            <input class="rounded border-gray-300 text-primary focus:ring-primary" type="checkbox" />
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="size-10 rounded-full bg-cover bg-center" data-alt="Portrait of Sarah Jenkins" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuA70Z4ZtExmiEMLpzjUv8Hxe3lyjLTN_Cxj8vnM8gDz97nnBKt4d2US5crA1W7_cnXiXVrPTY0JIAvywme0CfYenhcB8X3FDcyV1mKI1ruyR_MdsZx5nURRzo3yq9l_IOug4Yar7UX0bT9Svcfbvd4WWuyqntHzceCcBC_AZCeJXfSKESY1ccnsUCtXNPbA1-hpBRq2nS009IN6O-TjzSZ1Fp6rBgEF43OHri-sn-vFWKG5R-2f3zUIKBE7KaJlSA2L08vPeJ0pZKk');"></div>
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-[#0d1b12] dark:text-white text-sm">Sarah Jenkins</span>
                                                    <span class="text-xs text-gray-500">sarah.j@islanddistro.com</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                                RDC Manager
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-gray-400" style="font-size: 16px;">location_on</span>
                                                <span class="text-sm text-gray-700 dark:text-gray-300">North RDC</span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-sm text-gray-600 dark:text-gray-400">
                                            2 hours ago
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <div class="size-2 rounded-full bg-primary"></div>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-right">
                                            <button class="text-gray-400 hover:text-[#0d1b12] dark:hover:text-white transition-colors">
                                                <span class="material-symbols-outlined">more_vert</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Row 2 -->
                                    <tr class="group hover:bg-[#f8fcf9] dark:hover:bg-gray-800/30 transition-colors">
                                        <td class="p-4 text-center">
                                            <input class="rounded border-gray-300 text-primary focus:ring-primary" type="checkbox" />
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="size-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-sm bg-gradient-to-br from-purple-100 to-purple-200" data-alt="Initials avatar for Sunshine Grocers">SG</div>
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-[#0d1b12] dark:text-white text-sm">Sunshine Grocers</span>
                                                    <span class="text-xs text-gray-500">orders@sunshine.co</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                                Retail Partner
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-gray-400" style="font-size: 16px;">location_on</span>
                                                <span class="text-sm text-gray-700 dark:text-gray-300">South Coast Zone</span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-sm text-gray-600 dark:text-gray-400">
                                            Yesterday
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <div class="size-2 rounded-full bg-primary"></div>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-right">
                                            <button class="text-gray-400 hover:text-[#0d1b12] dark:hover:text-white transition-colors">
                                                <span class="material-symbols-outlined">more_vert</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Row 3 -->
                                    <tr class="group hover:bg-[#f8fcf9] dark:hover:bg-gray-800/30 transition-colors">
                                        <td class="p-4 text-center">
                                            <input class="rounded border-gray-300 text-primary focus:ring-primary" type="checkbox" />
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="size-10 rounded-full bg-cover bg-center" data-alt="Portrait of Michael Chen" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBvzo-TpEZvcWh9I_VnwDyJjQ5OX44BPDwheZZ-lTYZORpw4uxOnuV4TSB2qjOZ7ywlp-KuUFxLlK3OY9uRZjGpAa3NVB7ewymjndudsT7KEOpbIref0c_bHbRN0gAu0nu4qUXl75RimyrsgjT89CgfW9s19JGjhhuVZ3Lklnb3sYdsANgutZajhm98HpGhPh0NychV218nrwEODvs5AL_WLaZ_Px4WZDf5kXUdy8m7KeHmrK1iy2bsMGHZAg7Acxhv-lOW6ow2YxE');"></div>
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-[#0d1b12] dark:text-white text-sm">Michael Chen</span>
                                                    <span class="text-xs text-gray-500">m.chen@logistics.net</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                                                Logistics Lead
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-gray-400" style="font-size: 16px;">location_on</span>
                                                <span class="text-sm text-gray-700 dark:text-gray-300">Central Hub</span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-sm text-gray-600 dark:text-gray-400">
                                            5 mins ago
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <div class="size-2 rounded-full bg-primary"></div>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-right">
                                            <button class="text-gray-400 hover:text-[#0d1b12] dark:hover:text-white transition-colors">
                                                <span class="material-symbols-outlined">more_vert</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Row 4 -->
                                    <tr class="group hover:bg-[#f8fcf9] dark:hover:bg-gray-800/30 transition-colors">
                                        <td class="p-4 text-center">
                                            <input class="rounded border-gray-300 text-primary focus:ring-primary" type="checkbox" />
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="size-10 rounded-full bg-cover bg-center grayscale" data-alt="Portrait of David Ross" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAiTBXUjHufx4dsJ6B_hJUHtnIstK0HoaQ1w8RXphgTtsFdpeFK1d3zO80UueTyL31xpZkuMeMwq_FAlRijknNz04BNSKJXlWjG6vOjHbhB1Hd1fAuXcbN2n68WO6QJvz9_hA6R0LyPeF_JXpbxwzCVP9FG40lqBYfeItNqMrJr85xbsDMz-M0M_O8jIXsYD23EqkaBdRcb-UKIzdazG7wyfhnn2seRFLEAGcEYpkOGyzMKM7wVm0PqgvGnksOlJqd715-KUorT-BM');"></div>
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-gray-500 text-sm">David Ross</span>
                                                    <span class="text-xs text-gray-400">david.r@islanddistro.com</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                                RDC Staff
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-gray-300" style="font-size: 16px;">location_on</span>
                                                <span class="text-sm text-gray-500">East Wing</span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-sm text-gray-500">
                                            14 days ago
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <div class="size-2 rounded-full bg-gray-300"></div>
                                                <span class="text-sm font-medium text-gray-500">Inactive</span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-right">
                                            <button class="text-gray-400 hover:text-[#0d1b12] dark:hover:text-white transition-colors">
                                                <span class="material-symbols-outlined">more_vert</span>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="flex items-center justify-between px-4 py-3 border-t border-[#e7f3eb] dark:border-gray-700 bg-[#fcfdfd] dark:bg-gray-800/30">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Showing <span class="font-bold text-gray-800 dark:text-gray-200">1-4</span> of <span class="font-bold text-gray-800 dark:text-gray-200">1,240</span> users
                            </div>
                            <div class="flex gap-2">
                                <button class="px-3 py-1 rounded border border-[#e7f3eb] dark:border-gray-600 text-gray-400 cursor-not-allowed text-sm font-medium">Previous</button>
                                <button class="px-3 py-1 rounded border border-[#e7f3eb] dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium transition-colors">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>