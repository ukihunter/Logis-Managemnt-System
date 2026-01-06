function toggleUserMenu(event) {
  event.stopPropagation();
  document.getElementById("userMenu").classList.toggle("hidden");
}

// Close menu when clicking outside
document.addEventListener("click", function () {
  const menu = document.getElementById("userMenu");
  if (menu) {
    menu.classList.add("hidden");
  }
});
