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
                    "surface-dark": "#1c2e24",
                    "text-main-light": "#0d1b12",
                    "text-main-dark": "#e0ece4",
                    "text-secondary-light": "#4c9a66",
                    "text-secondary-dark": "#8dbca0",
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
    body {
        font-family: 'Manrope', sans-serif;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .dark ::-webkit-scrollbar-thumb {
        background: #2d4a3e;
    }

    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    /* Sidebar transition */
    .sidebar-collapsed {
        width: 5rem;
    }

    .sidebar-expanded {
        width: 18rem;
    }

    aside {
        transition: width 0.3s ease-in-out;
    }

    .sidebar-text {
        opacity: 1;
        transition: opacity 0.2s ease-in-out;
    }

    .sidebar-collapsed .sidebar-text {
        opacity: 0;
        width: 0;
        overflow: hidden;
    }

    /* Right panel slide-in animation */
    .detail-panel {
        transform: translateX(100%);
        transition: transform 0.3s ease-in-out;
    }

    .detail-panel.active {
        transform: translateX(0);
    }

    /* Image preview */
    .image-preview {
        display: none;
    }

    .image-preview.active {
        display: block;
    }
</style>