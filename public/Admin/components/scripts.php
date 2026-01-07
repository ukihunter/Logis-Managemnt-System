<script>
    // Toggle user menu
    function toggleUserMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('userMenu');
        menu.classList.toggle('hidden');
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('userMenu');
        if (!menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
        }
    });

    // Toggle logistics dropdown
    function toggleDropdown() {
        const dropdown = document.getElementById("logisticsDropdown");
        const arrow = document.getElementById("arrow");

        dropdown.classList.toggle("hidden");
        dropdown.classList.toggle("max-h-[500px]");
        arrow.classList.toggle("rotate-180");
    }

    // Close dropdown when sidebar collapses
    function closeDropdownOnCollapse() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar && sidebar.classList.contains('sidebar-collapsed')) {
            const dropdown = document.getElementById("logisticsDropdown");
            const arrow = document.getElementById("arrow");
            if (dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add("hidden");
                dropdown.classList.remove("max-h-[500px]");
                arrow.classList.remove("rotate-180");
            }
        }
    }

    // Watch for sidebar collapse and close dropdown
    const sidebarObserver = new MutationObserver(closeDropdownOnCollapse);
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebarObserver.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
</script>