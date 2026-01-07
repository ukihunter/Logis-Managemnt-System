// Show order detail panel and collapse sidebar
function showOrderDetail() {
  const sidebar = document.getElementById("sidebar");
  const detailPanel = document.getElementById("detailPanel");
  const orderListSection = document.getElementById("orderListSection");
  const dropdown = document.getElementById("logisticsDropdown");
  const arrow = document.getElementById("arrow");

  // Collapse sidebar to icon-only mode
  sidebar.classList.remove("sidebar-expanded");
  sidebar.classList.add("sidebar-collapsed");

  // Close dropdown if it's open
  if (dropdown && !dropdown.classList.contains("hidden")) {
    dropdown.classList.add("hidden");
    dropdown.classList.remove("max-h-[500px]");
    if (arrow) arrow.classList.remove("rotate-180");
  }

  // Adjust order list width
  orderListSection.classList.remove("w-full");
  orderListSection.classList.add("w-7/12", "xl:w-8/12");

  // Show detail panel
  detailPanel.classList.remove("hidden");
  detailPanel.classList.add("flex");
  setTimeout(() => {
    detailPanel.classList.add("active");
  }, 10);
}

// Close order detail panel and expand sidebar
function closeOrderDetail() {
  const sidebar = document.getElementById("sidebar");
  const detailPanel = document.getElementById("detailPanel");
  const orderListSection = document.getElementById("orderListSection");

  // Hide detail panel
  detailPanel.classList.remove("active");
  setTimeout(() => {
    detailPanel.classList.add("hidden");
    detailPanel.classList.remove("flex");
  }, 300);

  // Restore order list to full width
  orderListSection.classList.remove("w-7/12", "xl:w-8/12");
  orderListSection.classList.add("w-full");

  // Expand sidebar back to full width
  sidebar.classList.remove("sidebar-collapsed");
  sidebar.classList.add("sidebar-expanded");
}
