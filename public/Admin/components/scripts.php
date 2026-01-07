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
        const dropdown = document.getElementById('logisticsDropdown');
        const arrow = document.getElementById('arrow');

        if (dropdown && arrow) {
            dropdown.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        }
    }
</script>