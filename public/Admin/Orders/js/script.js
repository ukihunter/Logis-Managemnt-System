let currentOrderId = null;
let allDrivers = [];
let currentStatusFilter = "all";
let currentOrderData = null; // Store current order data for modal

// Load available drivers when page loads
document.addEventListener("DOMContentLoaded", async function () {
  await loadDrivers();
  setupSearchAndFilter();

  // Close dropdowns when clicking outside
  document.addEventListener("click", function (e) {
    if (
      !e.target.closest("#statusFilterDropdown") &&
      !e.target.closest('button[onclick="toggleStatusFilter()"]')
    ) {
      const dropdown = document.getElementById("statusFilterDropdown");
      if (dropdown && !dropdown.classList.contains("hidden")) {
        dropdown.classList.add("hidden");
      }
    }
  });
});

// Load available drivers from database
async function loadDrivers() {
  try {
    const response = await fetch(
      "../Orders/order_handler.php?action=get_available_drivers"
    );
    const result = await response.json();

    if (result.success) {
      allDrivers = result.drivers;
    }
  } catch (error) {
    console.error("Error loading drivers:", error);
  }
}

// Setup search and filter functionality
function setupSearchAndFilter() {
  const searchInput = document.getElementById("searchInput");

  searchInput.addEventListener("input", function (e) {
    const searchTerm = e.target.value.toLowerCase();
    filterOrders(searchTerm);
  });
}

// Filter orders based on search term
function filterOrders(searchTerm) {
  const rows = document.querySelectorAll(".order-row");

  rows.forEach((row) => {
    const orderId = row.dataset.orderId.toLowerCase();
    const customer = row.dataset.customer.toLowerCase();
    const zone = row.dataset.zone.toLowerCase();
    const status = row.dataset.status.toLowerCase();

    const matches =
      orderId.includes(searchTerm) ||
      customer.includes(searchTerm) ||
      zone.includes(searchTerm) ||
      status.includes(searchTerm);

    row.style.display = matches ? "" : "none";
  });
}

// Show order detail panel and collapse sidebar
async function showOrderDetail(orderId) {
  currentOrderId = orderId;

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

  // Load order details
  await loadOrderDetails(orderId);

  setTimeout(() => {
    detailPanel.classList.add("active");
  }, 10);
}

// Load order details from server
async function loadOrderDetails(orderId) {
  console.log("Loading order details for ID:", orderId);
  try {
    const response = await fetch(
      `../Orders/order_handler.php?action=get_order_details&order_id=${orderId}`
    );
    const result = await response.json();

    console.log("Order details response:", result);

    if (result.success) {
      displayOrderDetails(result.order);
    } else {
      console.error("Error loading order details:", result.message);
      alert("Error loading order details: " + result.message);
    }
  } catch (error) {
    console.error("Error loading order details:", error);
    alert("Error loading order details. Check console for details.");
  }
}

// Display order details in the panel
function displayOrderDetails(order) {
  console.log("Displaying order details:", order);

  // Store current order data for modal
  currentOrderData = order;

  // Update header
  const orderNumberEl = document.getElementById("orderNumber");
  const orderStatusEl = document.getElementById("orderStatus");
  const orderDateEl = document.getElementById("orderDate");

  console.log("Elements found:", {
    orderNumberEl,
    orderStatusEl,
    orderDateEl,
  });

  if (orderNumberEl) orderNumberEl.textContent = `#${order.order_number}`;
  if (orderStatusEl) {
    orderStatusEl.textContent = getStatusLabel(order.order_status);
    orderStatusEl.className = `inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide ${getStatusClass(
      order.order_status
    )}`;
  }
  if (orderDateEl)
    orderDateEl.textContent = `Created: ${formatDateTime(order.created_at)}`;

  // Update customer info
  const customerNameEl = document.getElementById("customerName");
  const customerTierEl = document.getElementById("customerTier");
  const contactPersonEl = document.getElementById("contactPerson");
  const contactPhoneEl = document.getElementById("contactPhone");
  const shippingAddressEl = document.getElementById("shippingAddress");

  if (customerNameEl)
    customerNameEl.textContent = order.business_name || order.customer_name;
  if (customerTierEl) customerTierEl.textContent = "Regular Customer • Tier 1";
  if (contactPersonEl) contactPersonEl.textContent = order.customer_name;
  if (contactPhoneEl) contactPhoneEl.textContent = order.customer_phone;
  if (shippingAddressEl)
    shippingAddressEl.textContent = `${order.shipping_address}, ${order.shipping_city}, ${order.shipping_province} ${order.shipping_postal_code}`;

  // Update order items
  displayOrderItems(order.items, order.subtotal, order.total_amount);

  // Update driver assignment dropdown
  populateDriverDropdown(order.driver_id);

  // Show appropriate action buttons based on order status
  showOrderActionButtons(order.order_status);

  // Update delivery notes and set editability based on status
  const deliveryNotesEl = document.getElementById("deliveryNotes");
  const saveNotesBtn = document.getElementById("saveNotesBtn");

  if (deliveryNotesEl) {
    // Show admin_notes, not customer_notes
    deliveryNotesEl.value = order.admin_notes || "";

    // Make readonly if order is shipped or delivered
    const isEditable = ["pending", "processing", "packed"].includes(
      order.order_status
    );
    deliveryNotesEl.readOnly = !isEditable;

    if (!isEditable) {
      deliveryNotesEl.classList.add(
        "bg-gray-100",
        "dark:bg-gray-800",
        "cursor-not-allowed"
      );
      deliveryNotesEl.placeholder = "Notes locked after packing";
    } else {
      deliveryNotesEl.classList.remove(
        "bg-gray-100",
        "dark:bg-gray-800",
        "cursor-not-allowed"
      );
      deliveryNotesEl.placeholder =
        "Add special instructions for the driver...";
    }

    // Show/hide save button
    if (saveNotesBtn) {
      saveNotesBtn.style.display = isEditable ? "flex" : "none";
    }
  }
}

// Display order items table
function displayOrderItems(items, subtotal, total) {
  const tbody = document.getElementById("orderItemsBody");
  tbody.innerHTML = "";

  let totalWeight = 0;

  items.forEach((item) => {
    const stockStatus = getStockStatus(item.stock_quantity, item.quantity);
    const row = `
            <tr>
                <td class="py-3 px-3">
                    <div class="font-bold text-[#0d1b12] dark:text-white">${
                      item.product_name
                    }</div>
                    <div class="text-[10px] ${
                      stockStatus.class
                    } flex items-center gap-1 mt-0.5">
                        <span class="material-symbols-outlined text-[10px]">${
                          stockStatus.icon
                        }</span> ${stockStatus.text}
                    </div>
                </td>
                <td class="py-3 px-3 text-center font-medium">${
                  item.quantity
                }</td>
                <td class="py-3 px-3 text-right font-medium">$${parseFloat(
                  item.subtotal
                ).toFixed(2)}</td>
            </tr>
        `;
    tbody.innerHTML += row;
  });

  // Update totals
  document.getElementById("orderItemCount").textContent = items.length;
  document.getElementById("orderGrandTotal").textContent = `$${parseFloat(
    total
  ).toFixed(2)}`;
}

// Get stock status info
function getStockStatus(stockQty, orderedQty) {
  if (!stockQty || stockQty === 0) {
    return {
      class: "text-red-500",
      icon: "block",
      text: "Out of Stock",
    };
  } else if (stockQty < orderedQty * 2) {
    return {
      class: "text-amber-500",
      icon: "warning",
      text: `Low Stock (${stockQty})`,
    };
  } else {
    return {
      class: "text-primary",
      icon: "check_circle",
      text: `In Stock (${stockQty})`,
    };
  }
}

// Populate driver dropdown
function populateDriverDropdown(selectedDriverId) {
  const select = document.getElementById("driverSelect");
  select.innerHTML = '<option value="">Select Delivery Partner...</option>';

  allDrivers.forEach((driver) => {
    const option = document.createElement("option");
    option.value = driver.id;
    option.textContent = `${driver.full_name} (${driver.employee_id}) - ${driver.license_plate}`;

    if (driver.id == selectedDriverId) {
      option.selected = true;
    }

    select.appendChild(option);
  });
}

// Save driver assignment when Save button is clicked
function saveDriverAssignment() {
  const select = document.getElementById("driverSelect");
  const driverId = select.value;

  if (!driverId) {
    showNotification("Please select a driver", "error");
    return;
  }

  assignDriver(currentOrderId, driverId);
}

// Save delivery notes
async function saveDeliveryNotes() {
  const notesTextarea = document.getElementById("deliveryNotes");
  const notes = notesTextarea.value;

  try {
    const formData = new FormData();
    formData.append("action", "save_delivery_notes");
    formData.append("order_id", currentOrderId);
    formData.append("notes", notes);

    const response = await fetch("../Orders/order_handler.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      showNotification("Delivery notes saved successfully!", "success");
    } else {
      showNotification("Error: " + result.message, "error");
    }
  } catch (error) {
    console.error("Error saving delivery notes:", error);
    showNotification("Error saving delivery notes", "error");
  }
}

// Assign driver to order
async function assignDriver(orderId, driverId) {
  try {
    const formData = new FormData();
    formData.append("action", "assign_driver");
    formData.append("order_id", orderId);
    formData.append("driver_id", driverId || "");

    const response = await fetch("../Orders/order_handler.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      showNotification("Driver assigned successfully!", "success");
      // Reload the page to update the table
      setTimeout(() => location.reload(), 1000);
    } else {
      showNotification("Error assigning driver: " + result.message, "error");
    }
  } catch (error) {
    console.error("Error assigning driver:", error);
    showNotification("Error assigning driver", "error");
  }
}

// Update order status
async function updateOrderStatus(status, notes = "") {
  try {
    const formData = new FormData();
    formData.append("action", "update_status");
    formData.append("order_id", currentOrderId);
    formData.append("status", status);
    formData.append("notes", notes);

    const response = await fetch("../Orders/order_handler.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      showNotification("Order status updated successfully!", "success");
      // Reload the page to update the table
      setTimeout(() => location.reload(), 1000);
    } else {
      showNotification(
        "Error updating order status: " + result.message,
        "error"
      );
    }
  } catch (error) {
    console.error("Error updating order status:", error);
    showNotification("Error updating order status", "error");
  }
}

// Show appropriate action buttons based on order status
function showOrderActionButtons(currentStatus) {
  const actionsDiv = document.getElementById("orderActions");
  let buttonsHTML = "";

  // Status progression: pending → processing → packed → shipped → delivered
  switch (currentStatus) {
    case "pending":
      buttonsHTML = `
                <div class="flex gap-3">
                    <button onclick="cancelOrder()" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 font-bold text-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">close</span> Cancel Order
                    </button>
                    <button onclick="moveToProcessing()" class="flex-[2] flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-[#0ebf49] text-white rounded-lg font-bold text-sm shadow-md shadow-primary/20 transition-all active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">check</span> Accept & Process
                    </button>
                </div>
            `;
      break;

    case "processing":
      buttonsHTML = `
                <div class="flex gap-3">
                    <button onclick="cancelOrder()" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 font-bold text-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">close</span> Cancel
                    </button>
                    <button onclick="moveToPacked()" class="flex-[2] flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-sm shadow-md shadow-blue-600/20 transition-all active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">inventory_2</span> Mark as Packed
                    </button>
                </div>
            `;
      break;

    case "packed":
      buttonsHTML = `
                <div class="flex gap-3">
                    <button onclick="moveToShipped()" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-sm shadow-md shadow-indigo-600/20 transition-all active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">local_shipping</span> Ship Order
                    </button>
                </div>
            `;
      break;

    case "shipped":
      buttonsHTML = `
                <div class="flex gap-3">
                    <button onclick="moveToDelivered()" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold text-sm shadow-md shadow-green-600/20 transition-all active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span> Mark as Delivered
                    </button>
                </div>
            `;
      break;

    case "delivered":
      buttonsHTML = `
                <div class="text-center py-4">
                    <div class="flex items-center justify-center gap-2 text-green-600 font-bold">
                        <span class="material-symbols-outlined">check_circle</span>
                        <span>Order Completed</span>
                    </div>
                </div>
            `;
      break;

    case "cancelled":
      buttonsHTML = `
                <div class="text-center py-4">
                    <div class="flex items-center justify-center gap-2 text-red-600 font-bold">
                        <span class="material-symbols-outlined">cancel</span>
                        <span>Order Cancelled</span>
                    </div>
                </div>
            `;
      break;
  }

  actionsDiv.innerHTML = buttonsHTML;
}

// Status progression functions
function moveToProcessing() {
  updateOrderStatus("processing", "Order approved and being processed");
}

function moveToPacked() {
  updateOrderStatus("packed", "Order has been packed and ready for shipment");
}

function moveToShipped() {
  const driverSelect = document.getElementById("driverSelect");
  if (!driverSelect.value) {
    if (
      confirm(
        "No driver assigned. Do you want to continue shipping without a driver?"
      )
    ) {
      updateOrderStatus("shipped", "Order dispatched for delivery");
    }
  } else {
    updateOrderStatus("shipped", "Order dispatched for delivery");
  }
}

function moveToDelivered() {
  // Open delivery confirmation modal
  openDeliveryConfirmModal();
}

function cancelOrder() {
  if (confirm("Are you sure you want to cancel this order?")) {
    updateOrderStatus("cancelled", "Order cancelled by admin");
  }
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

  currentOrderId = null;
}

// Helper function to get status label
function getStatusLabel(status) {
  const labels = {
    pending: "New Order",
    processing: "Processing",
    packed: "Packed",
    shipped: "Shipped",
    delivered: "Delivered",
    cancelled: "Cancelled",
  };
  return labels[status] || status;
}

// Helper function to get status class
function getStatusClass(status) {
  const classes = {
    pending: "bg-primary/10 text-primary border border-primary/20",
    processing: "bg-blue-100 text-blue-800 border border-blue-200",
    packed: "bg-purple-100 text-purple-800 border border-purple-200",
    shipped: "bg-indigo-100 text-indigo-800 border border-indigo-200",
    delivered: "bg-green-100 text-green-800 border border-green-200",
    cancelled: "bg-red-100 text-red-800 border border-red-200",
  };
  return classes[status] || "bg-gray-100 text-gray-800 border border-gray-200";
}

// Helper function to format date time
function formatDateTime(datetime) {
  const date = new Date(datetime);
  const options = {
    month: "short",
    day: "numeric",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  };
  return date.toLocaleDateString("en-US", options).replace(",", " •");
}

// Show notification
function showNotification(message, type = "success") {
  // Create notification element
  const notification = document.createElement("div");
  notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg ${
    type === "success" ? "bg-green-500" : "bg-red-500"
  } text-white font-medium`;
  notification.textContent = message;

  document.body.appendChild(notification);

  // Remove after 3 seconds
  setTimeout(() => {
    notification.remove();
  }, 3000);
}

// Toggle status filter dropdown
function toggleStatusFilter() {
  const dropdown = document.getElementById("statusFilterDropdown");
  dropdown.classList.toggle("hidden");
}

// Filter by status
function filterByStatus(status) {
  currentStatusFilter = status;
  const rows = document.querySelectorAll(".order-row");

  // Update label
  const label = document.getElementById("statusFilterLabel");
  if (status === "all") {
    label.textContent = "Status: All";
  } else {
    label.textContent = `Status: ${
      status.charAt(0).toUpperCase() + status.slice(1)
    }`;
  }

  // Close dropdown
  document.getElementById("statusFilterDropdown").classList.add("hidden");

  // Filter rows
  rows.forEach((row) => {
    const rowStatus = row.dataset.status;
    if (status === "all" || rowStatus === status) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

// Open delivery confirmation modal
function openDeliveryConfirmModal() {
  if (!currentOrderData) return;

  // Populate modal with order details
  document.getElementById("modalOrderNumber").textContent =
    currentOrderData.order_number;
  document.getElementById("modalCustomerName").textContent =
    currentOrderData.business_name || currentOrderData.customer_name;
  document.getElementById("modalDriverName").textContent =
    currentOrderData.driver_name || "Not assigned";

  // Show modal
  document.getElementById("deliveryConfirmModal").classList.remove("hidden");
}

// Close delivery confirmation modal
function closeDeliveryConfirmModal() {
  document.getElementById("deliveryConfirmModal").classList.add("hidden");
}

// Confirm delivery
function confirmDelivery() {
  closeDeliveryConfirmModal();
  updateOrderStatus("delivered", "Order successfully delivered to customer");
}

// Print Order Invoice function
function printOrderInvoice() {
  if (!currentOrderId) {
    alert("Please select an order first");
    return;
  }

  // Open invoice in new window for printing
  window.open(`order_invoice.php?order_id=${currentOrderId}`, "_blank");
}

// Download Order Invoice function
function downloadOrderInvoice() {
  if (!currentOrderId) {
    alert("Please select an order first");
    return;
  }

  // Create a hidden iframe to load and trigger download
  const iframe = document.createElement("iframe");
  iframe.style.display = "none";
  iframe.src = `order_invoice.php?order_id=${currentOrderId}&download=1`;
  document.body.appendChild(iframe);

  // Remove iframe after download starts (5 seconds)
  setTimeout(() => {
    document.body.removeChild(iframe);
  }, 5000);
}
