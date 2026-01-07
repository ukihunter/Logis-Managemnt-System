<?php
require_once '../../../config/admin_session.php';
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
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">142</p>
                        <span class="text-sm text-green-600 font-medium mb-1 flex items-center">
                            <span class="material-symbols-outlined text-base">trending_up</span> +4%
                        </span>
                    </div>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-green-600">local_shipping</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Active Now</p>
                    <div class="flex items-end gap-2 mt-2">
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">88</p>
                        <span class="text-sm text-gray-500 mb-1">on the road</span>
                    </div>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-gray-400">person_off</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Inactive / Leave</p>
                    <div class="flex items-end gap-2 mt-2">
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">12</p>
                        <span class="text-sm text-gray-500 mb-1">available</span>
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
                            <input class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" placeholder="Search by Name, ID, or License Plate" type="text" />
                        </div>
                        <div class="relative w-full sm:w-[220px]">
                            <select class="w-full pl-4 pr-10 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary appearance-none cursor-pointer">
                                <option value="">All Distribution Centres</option>
                                <option value="north">North RDC - Woodlands</option>
                                <option value="east">East RDC - Changi</option>
                                <option value="west">West RDC - Jurong</option>
                            </select>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                <span class="material-symbols-outlined text-[20px]">expand_more</span>
                            </span>
                        </div>
                    </div>
                    <!-- Chips Filter -->
                    <div class="flex gap-2 overflow-x-auto pb-2 lg:pb-0 w-full lg:w-auto no-scrollbar">
                        <button class="whitespace-nowrap px-4 py-1.5 rounded-full bg-primary/10 text-primary border border-primary/20 text-sm font-bold flex items-center gap-2">
                            All Statuses
                        </button>
                        <button class="whitespace-nowrap px-4 py-1.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-transparent hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium transition-colors">
                            Active Only
                        </button>
                        <button class="whitespace-nowrap px-4 py-1.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-transparent hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium transition-colors">
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
                            <!-- Row 1 -->
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200">
                                            <img alt="Driver Avatar" class="h-full w-full object-cover" data-alt="Profile photo of driver Michael Chen" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAE2LDs-6nTDRzn8l6c03BAZAxbSAbN0-xlRZ9DR-t2-mzs0LdV55-ibCOAdHnhKE9pi0DTsmiVU8Pg0SskkMbVlmavjz3OslW3IfbGJRBU9Y0Tobv6630mp2nLxvaE29edUA5ofdtAkGxL4OPuAYfHaeCEnRovj9EonGz4TF6qrzDlPmqsFwktnUOCdI56kVo3049KrC_PyjDH8tgnCqwPxAYS8kyQTrlteQSzbCxrm_bHyZSCFpD7dn5rZcTP-8hbSgB2TTBEyv0" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">Michael Chen</span>
                                            <span class="text-xs text-gray-500">+65 9123 4567</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-sm font-mono text-gray-600 dark:text-gray-400">EMP-2024-042</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 rounded bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                                            <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Hino 300</span>
                                            <span class="text-xs text-gray-500">GBA 1234 A</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">West RDC - Jurong</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                        Active
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-right">
                                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <span class="material-symbols-outlined">more_vert</span>
                                    </button>
                                </td>
                            </tr>
                            <!-- Row 2 -->
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200">
                                            <img alt="Driver Avatar" class="h-full w-full object-cover" data-alt="Profile photo of driver Sarah Tan" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC0xRWFkVoVF92j0963R59MSB0DKMebaWkeyWMNT4Dvcs51HKFjR1qhoR7GFZXzJOxCnExIn8clH3EhQUGR3Xwh1PMrOqRb5xd5jTq9PZr6kumhTFod9tWbuMEDsgZaE--PT5sf_gU85ZTQTfEP_zGZZn4f8y9WDTWvVSo2nX0Ad7vNxLG0qDAnamw11Pha--Is4krrtYBfgQVrjdG8CaFkRmMZcxrUYb0kiaKH0e49gXhtP_zWy38USTn6_sta0StijBgojikiu-I" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">Sarah Tan</span>
                                            <span class="text-xs text-gray-500">+65 8876 5432</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-sm font-mono text-gray-600 dark:text-gray-400">EMP-2023-118</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 rounded bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400">
                                            <span class="material-symbols-outlined text-[18px]">airport_shuttle</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Toyota Hiace</span>
                                            <span class="text-xs text-gray-500">GBD 5678 Z</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">North RDC - Woodlands</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-right">
                                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <span class="material-symbols-outlined">more_vert</span>
                                    </button>
                                </td>
                            </tr>
                            <!-- Row 3 -->
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200">
                                            <img alt="Driver Avatar" class="h-full w-full object-cover" data-alt="Profile photo of driver John Doe" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAlO9rzAL91dzLtE_ou7QFhopp4BZmruP8rJgMoty7eM7UdI4Buk2oNb9vZJhyMX1du1PMSdFxctkPoP-9u5x7InMOJKQ7U3NeAvdNBFnoU4HJWsm-oxy2mYn0a5vfYrsktT4P-fVfz_y46z1eVGntKFLQRPlgqMs8qwMhMJ8H6PmTVt0uPVuqjm_pEO63SRDWHmRY_-9bjkGPmzB-ERS2Boakco_HW4QsxsDXERO5ovdXZBSmxhaXwHCmdboZ61xkwEcbV2qUSaec" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">John Lim</span>
                                            <span class="text-xs text-gray-500">+65 9988 7766</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-sm font-mono text-gray-600 dark:text-gray-400">EMP-2022-092</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 rounded bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400">
                                            <span class="material-symbols-outlined text-[18px]">two_wheeler</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Honda PCX</span>
                                            <span class="text-xs text-gray-500">FBJ 9999 X</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">East RDC - Changi</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Inactive
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-right">
                                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <span class="material-symbols-outlined">more_vert</span>
                                    </button>
                                </td>
                            </tr>
                            <!-- Row 4 -->
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200">
                                            <img alt="Driver Avatar" class="h-full w-full object-cover" data-alt="Profile photo of driver David Ng" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCWx_vcIgq1WIQyCWBHJd_BF7_x3Tx_Y13TAbxPxGm8Ts3GB_z-Wk3mUM7W33VzKGyQo4lp6tbuJhxDBOWu8nEM3hIJKB9IgKYDf9_23KjyMbeVilRzKExm9ILXPBGGQv0wkcGJK38GKuazfIxns8J88VjW6Rl_syLgvHl9ii9di35CeJ9UtC3sgh8y_JywBL7hNr7T07ebKXPja_o4eGsc2LHwgy7ZnpSrMuW2TbJd2ASr9Ne6-6nPWam06S8YiLiZ_1Ohgc0hVGo" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">David Ng</span>
                                            <span class="text-xs text-gray-500">+65 8123 9876</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-sm font-mono text-gray-600 dark:text-gray-400">EMP-2024-005</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 rounded bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                                            <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Isuzu N-Series</span>
                                            <span class="text-xs text-gray-500">GBC 4321 B</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">West RDC - Jurong</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-right">
                                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <span class="material-symbols-outlined">more_vert</span>
                                    </button>
                                </td>
                            </tr>
                            <!-- Row 5 -->
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200">
                                            <img alt="Driver Avatar" class="h-full w-full object-cover" data-alt="Profile photo of driver Emily Wong" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB8trgdRexyVKoNySsCeSlqzCrX3mx7NikcA7Oq7BBKPN7wUOytK7JcYNdOf7d8lRuR0kM8a3XWyANkj0Qw32fdPvib_R9haMSiU4klycJ64AvepGQ-wQUc-3asPIvxZvJ5B4K6uM2hSRMWV3yYEf-KeOJWdn2D6AEyJEw1GYF16_OwjbVVz6NrskeMs-MHtS1Vz_haspV3Rsj715MA_U78-rWBfFT6Ykt1w2EjaJn9e78-3Oep-CPpu6kmCHVi-_aoaRi5oTYWyGg" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">Emily Wong</span>
                                            <span class="text-xs text-gray-500">+65 9654 3210</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-sm font-mono text-gray-600 dark:text-gray-400">EMP-2023-221</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 rounded bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400">
                                            <span class="material-symbols-outlined text-[18px]">airport_shuttle</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Nissan NV350</span>
                                            <span class="text-xs text-gray-500">GBA 5678 M</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">North RDC - Woodlands</span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                        On Leave
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-right">
                                    <button class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <span class="material-symbols-outlined">more_vert</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Showing <span class="font-bold text-gray-900 dark:text-white">1</span> to <span class="font-bold text-gray-900 dark:text-white">5</span> of <span class="font-bold text-gray-900 dark:text-white">142</span> drivers
                    </p>
                    <div class="flex gap-2">
                        <button class="px-3 py-1.5 text-sm font-medium text-gray-500 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            Previous
                        </button>
                        <button class="px-3 py-1.5 text-sm font-medium text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
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
            <div class="flex-1 overflow-y-auto p-6">
                <form id="driverForm" class="space-y-6">
                    <!-- Personal Information -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Personal Information</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name *</label>
                            <input type="text" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" placeholder="Enter driver's full name" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contact Number *</label>
                            <input type="tel" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" placeholder="+1 (555) 123-4567" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                            <input type="email" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" placeholder="driver@example.com" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Driver License Number *</label>
                            <input type="text" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" placeholder="DL-123456789" />
                        </div>
                    </div>

                    <!-- Vehicle Assignment -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vehicle Assignment</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Vehicle Type *</label>
                            <select required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary appearance-none cursor-pointer">
                                <option value="">Select vehicle type</option>
                                <option value="van">Delivery Van</option>
                                <option value="truck">Cargo Truck</option>
                                <option value="motorcycle">Motorcycle</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">License Plate *</label>
                            <input type="text" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" placeholder="ABC-1234" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Distribution Centre *</label>
                            <select required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary appearance-none cursor-pointer">
                                <option value="">Assign to centre</option>
                                <option value="north">North RDC - Woodlands</option>
                                <option value="east">East RDC - Changi</option>
                                <option value="west">West RDC - Jurong</option>
                            </select>
                        </div>
                    </div>

                    <!-- Employment Details -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employment Details</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Start Date *</label>
                            <input type="date" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                            <select class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary appearance-none cursor-pointer">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="leave">On Leave</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sticky Bottom Actions -->
            <div class="p-6 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-surface-dark">
                <div class="flex gap-3">
                    <button onclick="closeDriverPanel()" type="button" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 font-bold text-sm transition-colors">
                        Cancel
                    </button>
                    <button type="submit" form="driverForm" class="flex-[2] flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-primary/90 text-white rounded-lg font-bold text-sm shadow-md shadow-primary/20 transition-all active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        <span>Add Driver</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>
    <?php include '../components/scripts.php'; ?>
    <script>
        function openDriverPanel() {
            const sidebar = document.getElementById("sidebar");
            const driverPanel = document.getElementById("driverPanel");
            const mainContent = document.getElementById("mainContent");

            // Collapse sidebar to icon-only mode
            sidebar.classList.remove("sidebar-expanded");
            sidebar.classList.add("sidebar-collapsed");

            // Adjust main content width
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

            // Hide driver panel
            driverPanel.classList.remove("active");
            setTimeout(() => {
                driverPanel.classList.add("hidden");
                driverPanel.classList.remove("flex");
            }, 300);

            // Reset main content width
            mainContent.style.marginRight = "0";

            // Expand sidebar back to full width
            sidebar.classList.remove("sidebar-collapsed");
            sidebar.classList.add("sidebar-expanded");
        }
    </script>
</body>

</html>