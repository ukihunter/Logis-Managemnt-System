// Dashboard Data Loader
document.addEventListener("DOMContentLoaded", function () {
  loadDashboardData();

  // Refresh data every 30 seconds
  setInterval(loadDashboardData, 30000);
});

async function loadDashboardData() {
  try {
    await Promise.all([
      loadKPIStats(),
      loadPendingOrders(),
      loadLowStockItems(),
      loadLogisticsSchedule(),
    ]);
  } catch (error) {
    console.error("Error loading dashboard data:", error);
  }
}

// Load KPI Statistics
async function loadKPIStats() {
  try {
    const response = await fetch("dashboard_handler.php?action=get_kpi_stats");
    const data = await response.json();

    if (data.error) {
      console.error("KPI Error:", data.error);
      return;
    }

    // Update Pending Orders
    document.getElementById("pending-orders-count").textContent =
      data.pending_orders.count;
    const pendingBadge = document.getElementById("pending-orders-badge");
    const pendingIcon = document.getElementById("pending-orders-icon");
    pendingBadge.textContent = Math.abs(data.pending_orders.change) + "%";

    if (data.pending_orders.trend === "up") {
      pendingBadge.className =
        "text-xs font-bold text-green-600 bg-green-100 dark:bg-green-900/30 dark:text-green-400 px-1.5 py-0.5 rounded flex items-center mb-1";
      pendingIcon.textContent = "trending_up";
    } else {
      pendingBadge.className =
        "text-xs font-bold text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400 px-1.5 py-0.5 rounded flex items-center mb-1";
      pendingIcon.textContent = "trending_down";
    }

    // Update Low Stock
    document.getElementById("low-stock-count").textContent =
      data.low_stock.count;
    document.getElementById("new-low-stock").textContent =
      data.low_stock.new_count + " New";

    // Update Active Trucks
    document.getElementById("active-trucks-count").textContent =
      data.active_trucks.count;

    // Update Today's Revenue
    document.getElementById("today-revenue").textContent =
      "Rs " + formatNumber(data.today_revenue.amount);
    const revenueBadge = document.getElementById("revenue-badge");
    const revenueIcon = document.getElementById("revenue-icon");
    revenueBadge.textContent = Math.abs(data.today_revenue.change) + "%";

    if (data.today_revenue.trend === "up") {
      revenueBadge.className =
        "text-xs font-bold text-green-600 bg-green-100 dark:bg-green-900/30 dark:text-green-400 px-1.5 py-0.5 rounded flex items-center mb-1";
      revenueIcon.textContent = "trending_up";
    } else {
      revenueBadge.className =
        "text-xs font-bold text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400 px-1.5 py-0.5 rounded flex items-center mb-1";
      revenueIcon.textContent = "trending_down";
    }
  } catch (error) {
    console.error("Error loading KPI stats:", error);
  }
}

// Load Pending Orders
async function loadPendingOrders() {
  try {
    const response = await fetch(
      "dashboard_handler.php?action=get_pending_orders"
    );
    const orders = await response.json();

    if (orders.error) {
      console.error("Orders Error:", orders.error);
      return;
    }

    const tbody = document.getElementById("pending-orders-table");
    tbody.innerHTML = "";

    orders.forEach((order) => {
      const statusConfig = getStatusConfig(order.status);

      const row = document.createElement("tr");
      row.className =
        "group hover:bg-background-light dark:hover:bg-[#22362b] transition-colors";
      row.innerHTML = `
                <td class="p-4 text-sm font-medium text-text-main-light dark:text-text-main-dark">${
                  order.order_number
                }</td>
                <td class="p-4 text-sm text-text-secondary-light dark:text-text-secondary-dark">${
                  order.customer
                }</td>
                <td class="p-4 text-sm text-text-secondary-light dark:text-text-secondary-dark">${
                  order.date
                }</td>
                <td class="p-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                      statusConfig.class
                    }">
                        ${statusConfig.label}
                    </span>
                </td>
                <td class="p-4 text-sm font-medium text-text-main-light dark:text-text-main-dark text-right">Rs ${formatNumber(
                  order.total
                )}</td>
                <td class="p-4 text-center">
                    <button class="bg-primary/10 hover:bg-primary hover:text-white text-primary rounded-lg px-3 py-1.5 text-xs font-bold transition-all" onclick="viewOrder('${
                      order.order_number
                    }')">
                        ${statusConfig.action}
                    </button>
                </td>
            `;
      tbody.appendChild(row);
    });
  } catch (error) {
    console.error("Error loading pending orders:", error);
  }
}

// Load Low Stock Items
async function loadLowStockItems() {
  try {
    const response = await fetch(
      "dashboard_handler.php?action=get_low_stock_items"
    );
    const items = await response.json();

    if (items.error) {
      console.error("Low Stock Error:", items.error);
      return;
    }

    const container = document.getElementById("low-stock-container");
    container.innerHTML = "";

    items.forEach((item) => {
      const severity =
        item.stock === 0
          ? "red"
          : item.stock < item.min_threshold * 0.5
          ? "red"
          : "orange";

      const div = document.createElement("div");
      div.className = `flex items-start gap-3 p-3 rounded-lg bg-${severity}-50 dark:bg-${severity}-900/10 border border-${severity}-100 dark:border-${severity}-900/30`;
      div.innerHTML = `
                <div class="bg-white dark:bg-surface-dark p-2 rounded-md shadow-sm shrink-0">
                    <div class="size-8 bg-cover bg-center rounded" style="background-image: url('../../../${item.image}')"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-text-main-light dark:text-text-main-dark truncate">${item.name}</p>
                    <p class="text-xs text-${severity}-600 dark:text-${severity}-400 font-medium">
                        Stock: ${item.stock} Units (Min: ${item.min_threshold})
                    </p>
                </div>
                <button class="text-primary hover:bg-primary/10 p-1.5 rounded-lg transition-colors" title="Quick Reorder" onclick="reorderProduct('${item.sku}')">
                    <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                </button>
            `;
      container.appendChild(div);
    });
  } catch (error) {
    console.error("Error loading low stock items:", error);
  }
}

// Load Logistics Schedule
async function loadLogisticsSchedule() {
  try {
    const response = await fetch(
      "dashboard_handler.php?action=get_logistics_schedule"
    );
    const schedules = await response.json();

    if (schedules.error) {
      console.error("Logistics Error:", schedules.error);
      return;
    }

    const container = document.getElementById("logistics-schedule");
    container.innerHTML = "";

    schedules.forEach((schedule) => {
      const startPercent = ((schedule.start_hour - 8) / 12) * 100;
      const duration = schedule.end_hour - schedule.start_hour;
      const widthPercent = (duration / 12) * 100;

      const row = document.createElement("div");
      row.className = "flex items-center group";
      row.innerHTML = `
                <div class="w-32 shrink-0 font-bold text-sm text-text-main-light dark:text-text-main-dark">${
                  schedule.vehicle_number
                }</div>
                <div class="w-24 shrink-0 text-sm text-text-secondary-light dark:text-text-secondary-dark flex items-center gap-2">
                    <div class="size-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                        ${schedule.driver_name.charAt(0)}
                    </div>
                    <span>${schedule.driver_name.split(" ")[0]}</span>
                </div>
                <div class="flex-1 relative h-10 bg-background-light dark:bg-[#22362b] rounded-lg">
                    <div class="absolute top-2 bottom-2 bg-primary/20 border border-primary text-primary rounded-md flex items-center px-2 text-xs font-bold overflow-hidden whitespace-nowrap" 
                         style="left: ${startPercent}%; width: ${widthPercent}%;" 
                         title="${schedule.destinations}">
                        ${schedule.deliveries} deliveries: ${
        schedule.destinations
      }
                    </div>
                </div>
            `;
      container.appendChild(row);
    });

    // If no schedules, show message
    if (schedules.length === 0) {
      container.innerHTML =
        '<div class="text-center py-8 text-text-secondary-light dark:text-text-secondary-dark">No active deliveries today</div>';
    }
  } catch (error) {
    console.error("Error loading logistics schedule:", error);
  }
}

// Helper Functions
function getStatusConfig(status) {
  const configs = {
    pending: {
      label: "Pending",
      class:
        "bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400",
      action: "Process",
    },
    processing: {
      label: "Processing",
      class: "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400",
      action: "View",
    },
    packed: {
      label: "Ready",
      class:
        "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400",
      action: "Manifest",
    },
    shipped: {
      label: "Shipped",
      class:
        "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400",
      action: "Track",
    },
    delivered: {
      label: "Delivered",
      class: "bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400",
      action: "View",
    },
    cancelled: {
      label: "Cancelled",
      class: "bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400",
      action: "View",
    },
  };

  return configs[status] || configs["pending"];
}

function formatNumber(num) {
  return new Intl.NumberFormat("en-US", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(num);
}

function viewOrder(orderNumber) {
  window.location.href = "../Orders/orders.php?order=" + orderNumber;
}

function reorderProduct(sku) {
  window.location.href = "../Inventory/inventory.php?sku=" + sku;
}
