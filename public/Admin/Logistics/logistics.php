<?php
require_once '../../../config/admin_session.php';
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
                <div class="flex items-center gap-2 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-full border border-red-100 dark:border-red-800/50">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    <span class="text-xs font-bold">High Traffic Alert: Highway 4</span>
                </div>
                <button class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
                </button>
            </div>
        </header>
        <div class="flex flex-1 overflow-hidden">
            <div id="deliveryListSection" class="w-full transition-all duration-300 bg-surface-light dark:bg-surface-dark border-r border-slate-200 dark:border-slate-800 flex flex-col overflow-hidden z-10 shadow-xl">
                <div class="flex border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-surface-dark shrink-0">
                    <button class="flex-1 pb-3 pt-4 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium text-xs uppercase tracking-wide transition-colors">Pending (12)</button>
                    <button class="flex-1 pb-3 pt-4 border-b-2 border-primary text-primary font-bold text-xs uppercase tracking-wide transition-colors bg-primary/5">In Progress (5)</button>
                    <button class="flex-1 pb-3 pt-4 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium text-xs uppercase tracking-wide transition-colors">Completed (8)</button>
                </div>
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-surface-dark shrink-0">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[20px]">search</span>
                        <input class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 text-slate-700 dark:text-slate-200 placeholder-slate-400" placeholder="Search order ID, driver, or destination..." type="text" />
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto bg-slate-50 dark:bg-[#122218] p-4 space-y-3">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 ml-1">Individual Deliveries</h3>
                    <div onclick="showDeliveryMap('ORD-408')" class="bg-white dark:bg-surface-dark p-4 rounded-xl border-2 border-primary shadow-lg shadow-primary/5 cursor-pointer relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-primary"></div>
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-900 dark:text-white">#ORD-408</span>
                                <span class="inline-flex items-center gap-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> In Transit
                                </span>
                            </div>
                            <span class="text-xs font-bold text-primary">ETA: 5 min</span>
                        </div>
                        <h4 class="font-bold text-base text-slate-900 dark:text-white">Central Grocery Supply</h4>
                        <p class="text-xs text-slate-500 mt-0.5 mb-3 truncate">128 Marina Blvd, District 4</p>
                        <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 pt-3 mt-1">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-cover bg-center ring-2 ring-slate-100 dark:ring-slate-700" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAFbrEdpJF8UuvRaHlLx5INK_mvTClWd-CzE0JEC57TlmK_KmFQJRKCIzOYPDcW29zfLr_1XaYqo11M146z0sKFge_90NkBxQli1g3qac98Opx_9l47rM5JqroVUp3Xxo7N5nsXdDNKihNgCXRqyCeri8am0IC3cn38PzYkqoiQ3m5REMgWKSI4rBu_G1soN-QTn6vQ_DfhfLBvg5ZGQHESyWg6t8WogDCmySs_ZglsCZIHS0AIrfo5uC2QprCszkIxkUldSZAn1yg');"></div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase leading-none mb-0.5">Driver</span>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200 leading-none">Mike Thompson</span>
                                </div>
                            </div>
                            <button class="text-slate-400 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                            </button>
                        </div>
                    </div>
                    <div onclick="showDeliveryMap('ORD-412')" class="bg-white dark:bg-surface-dark p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 shadow-sm cursor-pointer transition-all hover:shadow-md">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-500">#ORD-412</span>
                                <span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                    Picking Up
                                </span>
                            </div>
                            <span class="text-xs text-slate-400">10:45 AM</span>
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">TechHub Warehouse</h4>
                        <p class="text-xs text-slate-500 mt-0.5 mb-3 truncate">45 Industrial Park Rd</p>
                        <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 pt-3 mt-1">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-500 dark:text-slate-300">SJ</div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase leading-none mb-0.5">Driver</span>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200 leading-none">Sarah Jenkins</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div onclick="showDeliveryMap('ORD-415')" class="bg-white dark:bg-surface-dark p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 shadow-sm cursor-pointer transition-all hover:shadow-md">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-500">#ORD-415</span>
                                <span class="inline-flex items-center gap-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                    Pending
                                </span>
                            </div>
                            <span class="text-xs text-slate-400">--:--</span>
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">Fresh Market Co.</h4>
                        <p class="text-xs text-slate-500 mt-0.5 mb-3 truncate">88 Ocean Drive</p>
                        <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 pt-3 mt-1">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full border border-dashed border-slate-300 dark:border-slate-600 flex items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-[14px]">person_add</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase leading-none mb-0.5">Driver</span>
                                    <span class="text-xs font-medium text-slate-400 italic leading-none">Unassigned</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div onclick="showDeliveryMap('ORD-421')" class="bg-white dark:bg-surface-dark p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 shadow-sm cursor-pointer transition-all hover:shadow-md">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-500">#ORD-421</span>
                                <span class="inline-flex items-center gap-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> In Transit
                                </span>
                            </div>
                            <span class="text-xs font-bold text-slate-500">ETA: 45 min</span>
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">Northside Hospital</h4>
                        <p class="text-xs text-slate-500 mt-0.5 mb-3 truncate">12 Medical Center Dr</p>
                        <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 pt-3 mt-1">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-800 text-white flex items-center justify-center text-[10px] font-bold">DK</div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase leading-none mb-0.5">Driver</span>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200 leading-none">David Kim</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="mapSection" class="flex-1 relative bg-slate-200 dark:bg-slate-800 overflow-hidden transition-all duration-300 hidden">
                <!-- Close Map Button -->
                <button onclick="closeDeliveryMap()" class="absolute top-4 right-4 z-50 w-10 h-10 bg-white dark:bg-surface-dark rounded-lg shadow-lg flex items-center justify-center text-slate-600 hover:text-red-500 dark:text-slate-400 dark:hover:text-red-400 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>

                <div class="absolute inset-0 z-0">
                    <img class="w-full h-full object-cover opacity-80 mix-blend-multiply dark:mix-blend-overlay" data-alt="Stylized map background showing city streets" data-location="City Map" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBaE897LTkuARXY-HinVU11cfdZ4spwIY8dKVMUq-UWrzxOTdc5h806doa3NUJPq5VSVHZTBSRURqlArkIclxV6jSZ-40sezJTPJHDzlUb4agA1Dfsj8_TWdrmJoAjLN2b6Qx5-5iASed4eWKUm3etE8_ScOMNNORbqEvm6tibauMFJqhtRgGWssCkeHbWl6Bs6T3JuHPtdyiYdkBD0mNrU5sqG8lChBve4HiYFWfjY6UyAdQe0iDbzTYONyzKQIA_yPo0r4rtrDhE" />
                    <div class="absolute inset-0 bg-slate-200/10 dark:bg-slate-900/30"></div>
                </div>
                <div class="absolute top-4 left-4 flex gap-3 z-10">
                    <div class="bg-white/90 dark:bg-surface-dark/90 backdrop-blur px-3 py-2 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 uppercase">Traffic</span>
                        <div class="flex gap-1 h-1.5 w-16 bg-slate-200 rounded-full overflow-hidden">
                            <div class="w-1/3 bg-green-500"></div>
                            <div class="w-1/3 bg-orange-500"></div>
                            <div class="w-1/3 bg-red-500"></div>
                        </div>
                        <span class="text-xs font-bold text-orange-500">Moderate</span>
                    </div>
                </div>
                <div class="absolute right-6 bottom-8 flex flex-col gap-2 z-10">
                    <button class="w-10 h-10 bg-white dark:bg-surface-dark rounded-lg shadow-lg flex items-center justify-center text-slate-600 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">my_location</span>
                    </button>
                    <div class="h-2"></div>
                    <button class="w-10 h-10 bg-white dark:bg-surface-dark rounded-lg shadow-lg flex items-center justify-center text-slate-600 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">add</span>
                    </button>
                    <button class="w-10 h-10 bg-white dark:bg-surface-dark rounded-lg shadow-lg flex items-center justify-center text-slate-600 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">remove</span>
                    </button>
                    <button class="w-10 h-10 bg-white dark:bg-surface-dark rounded-lg shadow-lg flex items-center justify-center text-slate-600 hover:text-primary transition-colors mt-2">
                        <span class="material-symbols-outlined">layers</span>
                    </button>
                </div>
                <svg class="absolute inset-0 w-full h-full pointer-events-none z-0">
                    <filter id="glow">
                        <feGaussianBlur result="coloredBlur" stdDeviation="2.5"></feGaussianBlur>
                        <feMerge>
                            <feMergeNode in="coloredBlur"></feMergeNode>
                            <feMergeNode in="SourceGraphic"></feMergeNode>
                        </feMerge>
                    </filter>
                    <path d="M 300 150 Q 350 200 400 300" fill="none" stroke="#94a3b8" stroke-dasharray="8,4" stroke-opacity="0.5" stroke-width="4"></path>
                    <path d="M 400 300 L 480 340" fill="none" filter="url(#glow)" stroke="#11d452" stroke-width="5"></path>
                    <path d="M 480 340 Q 550 400 600 250" fill="none" stroke="#f97316" stroke-dasharray="10,0" stroke-width="5"></path>
                    <path d="M 600 250 T 800 350" fill="none" stroke="#11d452" stroke-width="5"></path>
                </svg>
                <div class="absolute top-[150px] left-[300px] -translate-x-1/2 -translate-y-1/2 z-10 flex flex-col items-center">
                    <div class="w-10 h-10 bg-slate-800 text-white rounded-full shadow-xl flex items-center justify-center border-2 border-white dark:border-slate-700">
                        <span class="material-symbols-outlined text-sm">warehouse</span>
                    </div>
                </div>
                <div class="absolute top-[340px] left-[480px] z-20 flex flex-col items-center -translate-x-1/2 -translate-y-1/2 transition-all duration-1000">
                    <div class="relative">
                        <div class="w-10 h-10 bg-white dark:bg-surface-dark rounded-full shadow-2xl flex items-center justify-center border-2 border-primary ring-pulse text-primary z-20">
                            <span class="material-symbols-outlined rotate-45 filled">navigation</span>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-surface-dark px-2 py-0.5 rounded shadow mt-1 border border-slate-100 dark:border-slate-700">
                        <span class="text-[10px] font-bold whitespace-nowrap">Mike T. (45 km/h)</span>
                    </div>
                </div>
                <div class="absolute top-[250px] left-[600px] -translate-x-1/2 -translate-y-1/2 z-30 group">
                    <div class="w-12 h-12 bg-primary text-surface-dark rounded-full shadow-glow flex items-center justify-center font-bold text-sm border-4 border-white dark:border-slate-800 cursor-pointer hover:scale-110 transition-transform">
                        1
                    </div>
                    <div class="absolute top-1/2 left-full ml-4 -mt-12 w-72 bg-white dark:bg-surface-dark rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all opacity-100 scale-100 origin-left">
                        <div class="bg-gradient-to-r from-primary/10 to-transparent p-3 border-b border-primary/10 flex justify-between items-center">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-primary text-sm">package_2</span>
                                <span class="text-xs font-bold text-primary uppercase">Current Stop</span>
                            </div>
                            <span class="text-xs font-mono font-bold bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300">ETA: 5 min</span>
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white">Central Grocery Supply</h4>
                            <p class="text-xs text-slate-500">Order #408 • 450kg</p>
                            <div class="mt-4 space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Update Status</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button class="flex items-center justify-center gap-1 bg-primary hover:bg-green-600 text-white text-xs font-bold py-2 rounded transition-colors col-span-2">
                                        <span class="material-symbols-outlined text-sm">local_shipping</span> Out for Delivery
                                    </button>
                                    <button class="flex items-center justify-center gap-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold py-2 rounded transition-colors">
                                        <span class="material-symbols-outlined text-sm">check_circle</span> Delivered
                                    </button>
                                    <button class="flex items-center justify-center gap-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold py-2 rounded transition-colors">
                                        <span class="material-symbols-outlined text-sm">close</span> Attempted
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-12 -left-2 w-4 h-4 bg-white dark:bg-surface-dark border-l border-b border-slate-200 dark:border-slate-700 transform rotate-45"></div>
                    </div>
                </div>
                <div class="absolute top-[350px] left-[800px] -translate-x-1/2 -translate-y-1/2 z-10 group cursor-pointer">
                    <div class="w-8 h-8 bg-white dark:bg-surface-dark text-slate-600 dark:text-slate-300 rounded-full shadow-lg flex items-center justify-center font-bold text-xs border-2 border-slate-300 dark:border-slate-600 transition-transform group-hover:scale-110">
                        2
                    </div>
                    <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-surface-dark text-white text-[10px] py-1 px-2 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                        TechHub Warehouse
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../components/scripts.php'; ?>
    <script>
        function showDeliveryMap(orderId) {
            const mapSection = document.getElementById('mapSection');
            const deliveryListSection = document.getElementById('deliveryListSection');

            // Show map
            mapSection.classList.remove('hidden');

            // Shrink delivery list to fixed width
            deliveryListSection.classList.remove('w-full');
            deliveryListSection.classList.add('w-[420px]');

            console.log('Showing delivery map for:', orderId);
        }

        function closeDeliveryMap() {
            const mapSection = document.getElementById('mapSection');
            const deliveryListSection = document.getElementById('deliveryListSection');

            // Hide map
            mapSection.classList.add('hidden');

            // Expand delivery list to full width
            deliveryListSection.classList.remove('w-[420px]');
            deliveryListSection.classList.add('w-full');
        }
    </script>
</body>

</html>