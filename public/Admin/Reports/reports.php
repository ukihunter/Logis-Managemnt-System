<?php
require_once '../../../config/admin_session.php';
?>

<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Head Office Management Dashboard</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;display=swap" rel="stylesheet" />
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <?php include '../components/styles.php'; ?>
    <style>
        /* Custom scrollbar for table containers */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #e2e8f0;
            border-radius: 20px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #334155;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-text-main-light dark:text-text-main-dark transition-colors duration-200 min-h-screen flex">
    <div class="flex h-screen w-full overflow-hidden">
        <?php include '../components/sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="sticky top-0 z-50 bg-card-light dark:bg-card-dark border-b border-border-light dark:border-border-dark px-6 py-3 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="size-8 text-primary flex items-center justify-center">
                            <svg class="w-full h-full" fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_6_319)">
                                    <path d="M8.57829 8.57829C5.52816 11.6284 3.451 15.5145 2.60947 19.7452C1.76794 23.9758 2.19984 28.361 3.85056 32.3462C5.50128 36.3314 8.29667 39.7376 11.8832 42.134C15.4698 44.5305 19.6865 45.8096 24 45.8096C28.3135 45.8096 32.5302 44.5305 36.1168 42.134C39.7033 39.7375 42.4987 36.3314 44.1494 32.3462C45.8002 28.361 46.2321 23.9758 45.3905 19.7452C44.549 15.5145 42.4718 11.6284 39.4217 8.57829L24 24L8.57829 8.57829Z" fill="currentColor"></path>
                                </g>
                                <defs>
                                    <clippath id="clip0_6_319">
                                        <rect fill="white" height="48" width="48"></rect>
                                    </clippath>
                                </defs>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold tracking-tight">Distribution Command Center</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <!-- Date Range Picker -->
                        <button class="hidden md:flex items-center gap-2 px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-sm font-medium hover:bg-border-light dark:hover:bg-border-dark transition-colors">
                            <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                            <span>Last 30 Days</span>
                            <span class="material-symbols-outlined text-[16px]">expand_more</span>
                        </button>
                        <div class="h-6 w-px bg-border-light dark:bg-border-dark mx-2"></div>
                        <div class="flex gap-2">
                            <button class="flex items-center justify-center rounded-lg size-10 bg-background-light dark:bg-background-dark hover:bg-border-light dark:hover:bg-border-dark transition-colors text-text-sec-light dark:text-text-sec-dark">
                                <span class="material-symbols-outlined">search</span>
                            </button>
                            <button class="flex items-center justify-center rounded-lg size-10 bg-background-light dark:bg-background-dark hover:bg-border-light dark:hover:bg-border-dark transition-colors text-text-sec-light dark:text-text-sec-dark relative">
                                <span class="material-symbols-outlined">notifications</span>
                                <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border-2 border-white dark:border-card-dark"></span>
                            </button>
                            <button class="flex items-center justify-center rounded-lg size-10 bg-background-light dark:bg-background-dark hover:bg-border-light dark:hover:bg-border-dark transition-colors text-text-sec-light dark:text-text-sec-dark">
                                <span class="material-symbols-outlined">settings</span>
                            </button>
                        </div>
                        <div class="flex items-center gap-3 ml-2 pl-2 border-l border-border-light dark:border-border-dark">
                            <div class="text-right hidden lg:block">
                                <p class="text-sm font-bold leading-none">Alex Morgan</p>
                                <p class="text-xs text-text-sec-light dark:text-text-sec-dark mt-1">Head of Logistics</p>
                            </div>
                            <div class="size-10 rounded-full bg-cover bg-center border-2 border-white dark:border-background-dark shadow-sm" data-alt="Profile picture of a male manager smiling" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCt8xhLwK3_DfjqoJjShSAOrgc9vWb4NMZrYOMDemPYwJwCntaW7ChiS02CigVCUZiREjPlKMyTjwzqlbVpAT6Lrm19na1uEQeKVOQ-UAiE1zxRMtMz7IZ3bPqlf0q0t7YfchT_AhB_0VDfAdP8LQCqmBk-d9LG85fyovgGJutQOpey5u9RzNJpN2SYNabrwu7bwBWvR9OVx2RbDyh9tXul8HNrBmUiNkAyqSPa1iq2I_58dbqZVYofWP8yh9XS2xCYpUCkC0cI-28');"></div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Main Content -->
            <main class="flex-1 p-6 md:px-10 lg:px-12 max-w-[1600px] mx-auto w-full">
                <!-- Header Section with Actions -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight mb-2">Dashboard Overview</h1>
                        <p class="text-text-sec-light dark:text-text-sec-dark">Consolidated analytics for island-wide distribution.</p>
                    </div>
                    <div class="flex gap-3">
                        <button class="flex items-center gap-2 px-4 py-2.5 bg-primary text-black font-bold text-sm rounded-lg hover:bg-primary-hover shadow-lg shadow-primary/20 transition-all">
                            <span class="material-symbols-outlined text-[20px]">download</span>
                            Generate Report
                        </button>
                    </div>
                </div>
                <!-- Filters (Chips) -->
                <div class="flex flex-wrap gap-3 mb-8">
                    <button class="flex items-center gap-2 px-4 py-2 rounded-full bg-primary text-black text-sm font-bold shadow-sm ring-2 ring-primary ring-offset-2 ring-offset-background-light dark:ring-offset-background-dark">
                        All Regions
                    </button>
                    <button class="flex items-center gap-2 px-4 py-2 rounded-full bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark text-text-sec-light dark:text-text-sec-dark text-sm font-medium hover:border-primary dark:hover:border-primary hover:text-primary transition-colors">
                        Western Province
                    </button>
                    <button class="flex items-center gap-2 px-4 py-2 rounded-full bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark text-text-sec-light dark:text-text-sec-dark text-sm font-medium hover:border-primary dark:hover:border-primary hover:text-primary transition-colors">
                        Central Province
                    </button>
                    <button class="flex items-center gap-2 px-4 py-2 rounded-full bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark text-text-sec-light dark:text-text-sec-dark text-sm font-medium hover:border-primary dark:hover:border-primary hover:text-primary transition-colors">
                        Southern Province
                    </button>
                    <button class="flex items-center gap-2 px-4 py-2 rounded-full bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark text-text-sec-light dark:text-text-sec-dark text-sm font-medium hover:border-primary dark:hover:border-primary hover:text-primary transition-colors">
                        Eastern Province
                    </button>
                    <button class="flex items-center gap-2 px-3 py-2 rounded-full border-2 border-dashed border-border-light dark:border-border-dark text-text-sec-light dark:text-text-sec-dark hover:border-primary hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                    </button>
                </div>
                <!-- KPI Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                    <!-- KPI 1 -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark group hover:border-primary/50 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-primary/10 rounded-lg text-primary">
                                <span class="material-symbols-outlined">payments</span>
                            </div>
                            <span class="flex items-center text-primary text-sm font-bold bg-primary/5 px-2 py-1 rounded">
                                <span class="material-symbols-outlined text-[16px] mr-1">trending_up</span> +12.5%
                            </span>
                        </div>
                        <p class="text-text-sec-light dark:text-text-sec-dark text-sm font-medium">Total Revenue</p>
                        <h3 class="text-2xl font-bold mt-1">$4,250,000</h3>
                        <p class="text-xs text-text-sec-light dark:text-text-sec-dark mt-2">Vs. previous period ($3.8M)</p>
                    </div>
                    <!-- KPI 2 -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark group hover:border-primary/50 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-600 dark:text-blue-400">
                                <span class="material-symbols-outlined">inventory_2</span>
                            </div>
                            <span class="flex items-center text-primary text-sm font-bold bg-primary/5 px-2 py-1 rounded">
                                <span class="material-symbols-outlined text-[16px] mr-1">trending_up</span> +5.2%
                            </span>
                        </div>
                        <p class="text-text-sec-light dark:text-text-sec-dark text-sm font-medium">Total Units Sold</p>
                        <h3 class="text-2xl font-bold mt-1">124,500</h3>
                        <p class="text-xs text-text-sec-light dark:text-text-sec-dark mt-2">Vs. previous period (118k)</p>
                    </div>
                    <!-- KPI 3 -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark group hover:border-primary/50 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-purple-600 dark:text-purple-400">
                                <span class="material-symbols-outlined">local_shipping</span>
                            </div>
                            <span class="flex items-center text-text-sec-light dark:text-text-sec-dark text-sm font-bold bg-background-light dark:bg-background-dark px-2 py-1 rounded border border-border-light dark:border-border-dark">
                                <span class="material-symbols-outlined text-[16px] mr-1">remove</span> 0%
                            </span>
                        </div>
                        <p class="text-text-sec-light dark:text-text-sec-dark text-sm font-medium">Active Deliveries</p>
                        <h3 class="text-2xl font-bold mt-1">45 Trucks</h3>
                        <p class="text-xs text-text-sec-light dark:text-text-sec-dark mt-2">92% On-time rate</p>
                    </div>
                    <!-- KPI 4 -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark group hover:border-primary/50 transition-colors">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg text-orange-600 dark:text-orange-400">
                                <span class="material-symbols-outlined">health_and_safety</span>
                            </div>
                            <span class="flex items-center text-primary text-sm font-bold bg-primary/5 px-2 py-1 rounded">
                                <span class="material-symbols-outlined text-[16px] mr-1">trending_up</span> +1.4%
                            </span>
                        </div>
                        <p class="text-text-sec-light dark:text-text-sec-dark text-sm font-medium">Stock Health Score</p>
                        <h3 class="text-2xl font-bold mt-1">92%</h3>
                        <p class="text-xs text-text-sec-light dark:text-text-sec-dark mt-2">Low stock alerts: 3</p>
                    </div>
                </div>
                <!-- Bento Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Left Column (Main Charts) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Sales Trend Chart -->
                        <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h3 class="text-lg font-bold">Sales Performance Trend</h3>
                                    <p class="text-sm text-text-sec-light dark:text-text-sec-dark">Comparison: Current vs Previous Period</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="flex items-center gap-2 text-xs font-medium">
                                        <span class="size-2 rounded-full bg-primary"></span> Current
                                    </span>
                                    <span class="flex items-center gap-2 text-xs font-medium text-text-sec-light dark:text-text-sec-dark">
                                        <span class="size-2 rounded-full bg-gray-300 dark:bg-gray-600"></span> Previous
                                    </span>
                                </div>
                            </div>
                            <div class="h-[280px] w-full relative">
                                <!-- Reusing the SVG chart with responsive sizing -->
                                <svg class="w-full h-full" fill="none" preserveaspectratio="none" viewbox="0 0 478 150" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Gradient Area -->
                                    <path d="M0 109C18.1538 109 18.1538 21 36.3077 21C54.4615 21 54.4615 41 72.6154 41C90.7692 41 90.7692 93 108.923 93C127.077 93 127.077 33 145.231 33C163.385 33 163.385 101 181.538 101C199.692 101 199.692 61 217.846 61C236 61 236 45 254.154 45C272.308 45 272.308 121 290.462 121C308.615 121 308.615 149 326.769 149C344.923 149 344.923 1 363.077 1C381.231 1 381.231 81 399.385 81C417.538 81 417.538 129 435.692 129C453.846 129 453.846 25 472 25V149H326.769H0V109Z" fill="url(#paint0_linear_1131_5935)"></path>
                                    <!-- Line Path -->
                                    <path d="M0 109C18.1538 109 18.1538 21 36.3077 21C54.4615 21 54.4615 41 72.6154 41C90.7692 41 90.7692 93 108.923 93C127.077 93 127.077 33 145.231 33C163.385 33 163.385 101 181.538 101C199.692 101 199.692 61 217.846 61C236 61 236 45 254.154 45C272.308 45 272.308 121 290.462 121C308.615 121 308.615 149 326.769 149C344.923 149 344.923 1 363.077 1C381.231 1 381.231 81 399.385 81C417.538 81 417.538 129 435.692 129C453.846 129 453.846 25 472 25" stroke="#11d452" stroke-linecap="round" stroke-width="3"></path>
                                    <!-- Dashed Line (Previous Period) -->
                                    <path d="M0 120C18 120 18 40 36 40C54 40 54 60 72 60C90 60 90 100 108 100C127 100 127 50 145 50C163 50 163 110 181 110C199 110 199 80 217 80C236 80 236 60 254 60C272 60 272 130 290 130C308 130 308 140 326 140C344 140 344 20 363 20C381 20 381 90 399 90C417 90 417 110 435 110C453 110 453 40 472 40" stroke="#cbd5e1" stroke-dasharray="4 4" stroke-linecap="round" stroke-width="2"></path>
                                    <defs>
                                        <lineargradient gradientunits="userSpaceOnUse" id="paint0_linear_1131_5935" x1="236" x2="236" y1="1" y2="149">
                                            <stop stop-color="#11d452" stop-opacity="0.15"></stop>
                                            <stop offset="1" stop-color="#11d452" stop-opacity="0"></stop>
                                        </lineargradient>
                                    </defs>
                                </svg>
                                <!-- X-Axis Labels Overlay -->
                                <div class="absolute bottom-0 left-0 right-0 flex justify-between text-xs font-bold text-text-sec-light dark:text-text-sec-dark px-2 translate-y-full pt-2">
                                    <span>Week 1</span>
                                    <span>Week 2</span>
                                    <span>Week 3</span>
                                    <span>Week 4</span>
                                </div>
                            </div>
                        </div>
                        <!-- RDC Map Widget -->
                        <div class="bg-card-light dark:bg-card-dark rounded-xl p-0 shadow-sm border border-border-light dark:border-border-dark overflow-hidden relative min-h-[400px]">
                            <div class="absolute top-6 left-6 z-10">
                                <h3 class="text-lg font-bold bg-white/80 dark:bg-black/50 backdrop-blur-sm px-3 py-1 rounded-lg">Island-wide Distribution</h3>
                                <p class="text-sm text-text-sec-light dark:text-text-sec-dark mt-1 bg-white/80 dark:bg-black/50 backdrop-blur-sm px-3 py-1 rounded-lg inline-block">Real-time heatmap of active nodes</p>
                            </div>
                            <!-- Interactive Map Controls Overlay -->
                            <div class="absolute bottom-6 right-6 z-10 flex flex-col gap-2">
                                <button class="size-8 flex items-center justify-center bg-white dark:bg-card-dark shadow-lg rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <span class="material-symbols-outlined text-[20px]">add</span>
                                </button>
                                <button class="size-8 flex items-center justify-center bg-white dark:bg-card-dark shadow-lg rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <span class="material-symbols-outlined text-[20px]">remove</span>
                                </button>
                            </div>
                            <!-- Placeholder for Map Visualization -->
                            <div class="w-full h-full bg-blue-50 dark:bg-slate-900 bg-cover bg-center relative group" data-alt="Abstract map background showing a stylized vector map of an island or region with data nodes" data-location="Island Map" style="background-image: url('https://placeholder.pics/svg/300');">
                                <div class="absolute inset-0 bg-white/50 dark:bg-black/60 mix-blend-overlay"></div>
                                <!-- Map Hotspots (Simulated) -->
                                <div class="absolute top-1/3 left-1/4 group/marker cursor-pointer">
                                    <div class="size-6 bg-primary/30 rounded-full animate-ping absolute"></div>
                                    <div class="size-4 bg-primary border-2 border-white dark:border-black rounded-full relative z-10 shadow-lg"></div>
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-32 bg-card-light dark:bg-card-dark rounded-lg shadow-xl p-2 text-xs opacity-0 group-hover/marker:opacity-100 transition-opacity pointer-events-none transform translate-y-2 group-hover/marker:translate-y-0 duration-200">
                                        <p class="font-bold">Western RDC</p>
                                        <p class="text-primary font-medium">$1.2M Sales</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-1/3 right-1/3 group/marker cursor-pointer">
                                    <div class="size-4 bg-orange-500 border-2 border-white dark:border-black rounded-full relative z-10 shadow-lg"></div>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-32 bg-card-light dark:bg-card-dark rounded-lg shadow-xl p-2 text-xs opacity-0 group-hover/marker:opacity-100 transition-opacity pointer-events-none transform translate-y-2 group-hover/marker:translate-y-0 duration-200">
                                        <p class="font-bold">Central RDC</p>
                                        <p class="text-orange-500 font-medium">Delayed Stock</p>
                                    </div>
                                </div>
                                <div class="absolute top-1/2 right-1/4 group/marker cursor-pointer">
                                    <div class="size-4 bg-primary border-2 border-white dark:border-black rounded-full relative z-10 shadow-lg"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Right Column (Stats & Lists) -->
                    <div class="lg:col-span-4 space-y-6 flex flex-col">
                        <!-- Efficiency Gauges -->
                        <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark">
                            <h3 class="text-lg font-bold mb-6">Delivery Efficiency</h3>
                            <div class="flex items-center justify-around">
                                <!-- Gauge 1 -->
                                <div class="flex flex-col items-center gap-2">
                                    <div class="relative size-24">
                                        <svg class="size-full" viewbox="0 0 36 36">
                                            <path class="text-gray-200 dark:text-gray-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3"></path>
                                            <path class="text-primary" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="85, 100" stroke-linecap="round" stroke-width="3"></path>
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                                            <span class="text-xl font-bold">85%</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium text-text-sec-light dark:text-text-sec-dark text-center">On-Time<br />Rate</span>
                                </div>
                                <div class="w-px h-24 bg-border-light dark:bg-border-dark"></div>
                                <!-- Gauge 2 -->
                                <div class="flex flex-col items-center gap-2">
                                    <div class="relative size-24">
                                        <svg class="size-full" viewbox="0 0 36 36">
                                            <path class="text-gray-200 dark:text-gray-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3"></path>
                                            <path class="text-blue-500" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="62, 100" stroke-linecap="round" stroke-width="3"></path>
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                                            <span class="text-xl font-bold">4.2h</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium text-text-sec-light dark:text-text-sec-dark text-center">Avg.<br />Turnaround</span>
                                </div>
                            </div>
                        </div>
                        <!-- RDC Leaderboard -->
                        <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark flex-1 overflow-hidden flex flex-col">
                            <div class="p-6 pb-2">
                                <h3 class="text-lg font-bold">Regional Leaderboard</h3>
                                <p class="text-sm text-text-sec-light dark:text-text-sec-dark mt-1">Top performing distribution centers.</p>
                            </div>
                            <div class="overflow-y-auto custom-scrollbar flex-1 p-6 pt-2">
                                <table class="w-full text-left text-sm">
                                    <thead>
                                        <tr class="text-text-sec-light dark:text-text-sec-dark border-b border-border-light dark:border-border-dark">
                                            <th class="pb-3 font-medium">RDC Name</th>
                                            <th class="pb-3 font-medium text-right">Sales</th>
                                            <th class="pb-3 font-medium text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border-light dark:divide-border-dark">
                                        <tr class="group">
                                            <td class="py-3 font-medium">Western Main</td>
                                            <td class="py-3 text-right font-bold">$420k</td>
                                            <td class="py-3 text-right"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">On Track</span></td>
                                        </tr>
                                        <tr class="group">
                                            <td class="py-3 font-medium">Southern Hub</td>
                                            <td class="py-3 text-right font-bold">$385k</td>
                                            <td class="py-3 text-right"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">On Track</span></td>
                                        </tr>
                                        <tr class="group">
                                            <td class="py-3 font-medium">Central East</td>
                                            <td class="py-3 text-right font-bold">$210k</td>
                                            <td class="py-3 text-right"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Review</span></td>
                                        </tr>
                                        <tr class="group">
                                            <td class="py-3 font-medium">Northern Tip</td>
                                            <td class="py-3 text-right font-bold">$195k</td>
                                            <td class="py-3 text-right"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Behind</span></td>
                                        </tr>
                                        <tr class="group">
                                            <td class="py-3 font-medium">City Center</td>
                                            <td class="py-3 text-right font-bold">$150k</td>
                                            <td class="py-3 text-right"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">On Track</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-4 border-t border-border-light dark:border-border-dark text-center">
                                <button class="text-sm text-primary font-bold hover:underline">View All RDCs</button>
                            </div>
                        </div>
                        <!-- Inventory Alerts -->
                        <div class="bg-card-light dark:bg-card-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold">Inventory Alerts</h3>
                                <span class="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold px-2 py-1 rounded-full">3 Critical</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex gap-3 items-start p-3 bg-red-50 dark:bg-red-900/10 rounded-lg border border-red-100 dark:border-red-900/20">
                                    <span class="material-symbols-outlined text-red-500 mt-0.5 text-[20px]">warning</span>
                                    <div>
                                        <p class="text-sm font-bold text-red-900 dark:text-red-200">Low Stock: Premium Rice 5kg</p>
                                        <p class="text-xs text-red-700 dark:text-red-300 mt-1">Only 45 units remaining at Central RDC.</p>
                                    </div>
                                </div>
                                <div class="flex gap-3 items-start p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg border border-yellow-100 dark:border-yellow-900/20">
                                    <span class="material-symbols-outlined text-yellow-600 mt-0.5 text-[20px]">schedule</span>
                                    <div>
                                        <p class="text-sm font-bold text-yellow-900 dark:text-yellow-200">Expiring Batch: Milk Powder</p>
                                        <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">Batch #4029 expires in 7 days.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../components/scripts.php'; ?>
</body>

</html>