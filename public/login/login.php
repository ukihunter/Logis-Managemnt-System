<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Login - Centralised Sales Distribution System</title>
    <!-- Google Fonts: Manrope -->
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
                        "background-light": "#f6f8f6",
                        "background-dark": "#102216",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1c3024",
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
        /* Custom scrollbar for better aesthetic */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 20px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-[#0d1b12] dark:text-gray-100 transition-colors duration-200">
    <div class="min-h-screen flex w-full">
        <!-- Left Section: Visual / Branding (Desktop only) -->
        <div class="hidden lg:flex w-1/2 relative bg-surface-dark overflow-hidden flex-col justify-between">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-gradient-to-t from-background-dark/90 via-background-dark/40 to-transparent z-10"></div>
                <div
                    class="w-full h-full bg-cover bg-center opacity-80"
                    data-alt="Modern logistics warehouse and distribution map visualization"
                    style="background-image: url('../../assest/login.png');">
                </div>

            </div>
            <!-- Top Brand -->
            <div class="relative z-20 px-12 py-10">
                <div class="flex items-center gap-3 text-white">
                    <div class="size-8 bg-primary rounded-lg flex items-center justify-center">
                        <img
                            src="../../assest/icons/store.svg"
                            alt="Inventory"
                            class="w-5 h-5 object-contain filter invert brightness-0" />
                    </div>

                    <span class="text-xl font-bold tracking-tight">IslandDistribute</span>
                </div>
            </div>
            <!-- Bottom Caption -->
            <div class="relative z-20 px-12 py-12 max-w-2xl">
                <div class="mb-6 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 border border-primary/30 backdrop-blur-sm">
                    <span class="flex h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="text-xs font-semibold text-primary uppercase tracking-wider">System Online</span>
                </div>
                <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight mb-4">
                    Connecting Logistics Across the Island
                </h1>
                <p class="text-gray-300 text-lg leading-relaxed max-w-md">
                    The centralised hub for retail customers, RDC staff, logistics teams, and management.
                </p>
            </div>
        </div>
        <!-- Right Section: Login Form -->
        <div class="flex-1 flex flex-col justify-center items-center p-6 sm:p-12 lg:p-16 relative w-full bg-background-light dark:bg-background-dark">
            <!-- Mobile Brand Header (Visible only on small screens) -->
            <div class="lg:hidden absolute top-6 left-6 flex items-center gap-2 text-[#0d1b12] dark:text-white">
                <div class="size-8 bg-primary rounded-lg flex items-center justify-center">
                    <img
                        src="../../assest/icons/store.svg"
                        alt="Inventory"
                        class="w-5 h-5 object-contain filter invert brightness-0">
                </div>
                <span class="text-lg font-bold">IslandDistribute</span>
            </div>
            <!-- Help Links -->
            <div class="absolute top-6 right-6 flex items-center gap-6">
                <a class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Help</a>
                <a class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Support</a>
            </div>
            <div class="w-full max-w-[440px] flex flex-col">
                <!-- Headline Component (Styled) -->
                <div class="mb-10 text-center lg:text-left">
                    <h1 class="text-3xl lg:text-[32px] font-bold leading-tight text-[#0d1b12] dark:text-white tracking-tight mb-2">
                        Welcome Back
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 text-base font-normal leading-normal">
                        Please enter your details to sign in.
                    </p>
                </div>
                <!-- Error Message -->
                <div id="errorMessage" class="hidden mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                    <p class="text-sm text-red-600 dark:text-red-400 text-center"></p>
                </div>
                <!-- Success Message -->
                <div id="successMessage" class="hidden mb-4 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                    <p class="text-sm text-green-600 dark:text-green-400 text-center"></p>
                </div>
                <!-- Form Section -->
                <form id="loginForm" action="login_handler.php" class="flex flex-col gap-5" method="POST">
                    <!-- Username Field -->
                    <div class="flex flex-col gap-2">
                        <label class="text-[#0d1b12] dark:text-gray-200 text-sm font-medium leading-normal" for="username">
                            Username or Email
                        </label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">
                                <img
                                    src="../../assest/icons/person.svg"
                                    alt="person"
                                    class="w-5 h-5 object-contain filter grayscale brightness-75" />
                            </span>
                            <input autofocus="" class="form-input flex w-full min-w-0 resize-none rounded-xl border border-[#cfe7d7] dark:border-gray-700 bg-surface-light dark:bg-surface-dark focus:border-primary dark:focus:border-primary focus:ring-1 focus:ring-primary h-14 placeholder:text-gray-400 dark:placeholder:text-gray-500 pl-11 pr-4 text-base text-[#0d1b12] dark:text-white font-normal leading-normal transition-all" id="username" name="username" placeholder="Enter your username or email" type="text" required />
                        </div>
                    </div>
                    <!-- Password Field -->
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <label class="text-[#0d1b12] dark:text-gray-200 text-sm font-medium leading-normal" for="password">
                                Password
                            </label>
                        </div>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">
                                <img
                                    src="../../assest/icons/lock.svg"
                                    alt="lock"
                                    class="w-5 h-5 object-contain filter grayscale brightness-75" />
                            </span>
                            <input class="form-input flex w-full min-w-0 resize-none rounded-xl border border-[#cfe7d7] dark:border-gray-700 bg-surface-light dark:bg-surface-dark focus:border-primary dark:focus:border-primary focus:ring-1 focus:ring-primary h-14 placeholder:text-gray-400 dark:placeholder:text-gray-500 pl-11 pr-12 text-base text-[#0d1b12] dark:text-white font-normal leading-normal transition-all" id="password" name="password" placeholder="Enter your password" type="password" required />
                            <button class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#0d1b12] dark:hover:text-white transition-colors cursor-pointer flex items-center justify-center" type="button">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                        <div class="flex justify-end mt-1">
                            <a class="text-sm font-medium text-primary hover:text-green-600 transition-colors" href="#">Forgot Password?</a>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <button type="submit" id="loginBtn" class="mt-4 flex w-full items-center justify-center rounded-xl bg-primary h-14 px-5 text-base font-bold leading-normal text-white shadow-lg shadow-green-500/20 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-background-dark transition-all transform active:scale-[0.99]">
                        <span id="loginBtnText">Log In</span>
                        <span id="loginBtnLoader" class="hidden">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </form>
                <!-- Footer / Extra Actions -->
                <div class="mt-8 flex flex-col items-center gap-4 border-t border-gray-100 dark:border-gray-800 pt-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                        Don't have access? <a class="text-[#0d1b12] dark:text-gray-200 font-bold hover:underline" href="../register/register.php">Create an account</a>
                    </p>
                    <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-600">
                        <img
                            src="../../assest/icons/lock.svg"
                            alt="lock"
                            class="w-4 h-4 object-contain filter" />
                        <span>Authorized person only</span>
                    </div>
                </div>
            </div>
            <!-- Bottom Legal (Desktop) -->
            <div class="absolute bottom-6 w-full text-center lg:text-left px-16 hidden lg:block">
                <p class="text-xs text-gray-400 dark:text-gray-600">
                    © <span id="year"></span> Distribution Co. All rights reserved.
                    <a class="hover:underline" href="#">Privacy Policy</a>
                </p>
            </div>
        </div>
    </div>
    <!-- java scripts-->
    <script src="js/script.js"></script>
</body>

</html>