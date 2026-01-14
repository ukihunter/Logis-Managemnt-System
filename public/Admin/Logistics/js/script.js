let currentDriverId = null;

function openDriverPanel(isEdit = false) {
  const sidebar = document.getElementById("sidebar");
  const driverPanel = document.getElementById("driverPanel");
  const mainContent = document.getElementById("mainContent");

  // Reset form for adding new driver (only if not editing or viewing)
  if (!isEdit) {
    // Show form, hide view
    document.getElementById("driverForm").classList.remove("hidden");
    document.getElementById("driverViewContainer").classList.add("hidden");
    document.getElementById("panelActions").classList.remove("hidden");

    document.getElementById("driverForm").reset();
    document.getElementById("panelTitle").textContent = "Add New Driver";
    document.getElementById("submitBtnText").textContent = "Add Driver";
    document.getElementById("form_action").value = "add";
    document.getElementById("driver_id").value = "";
    currentDriverId = null;
  }

  // Collapse sidebar to icon-only mode
  sidebar.classList.remove("sidebar-expanded");
  sidebar.classList.add("sidebar-collapsed");

  // Shift main content to the left to make room for panel
  mainContent.style.marginRight = "28rem";

  // Show driver panel
  driverPanel.classList.remove("hidden");
  driverPanel.classList.add("flex");
  setTimeout(() => {
    driverPanel.classList.add("active");
  }, 10);
}

function closeDriverPanel() {
  const sidebar = document.getElementById("sidebar");
  const driverPanel = document.getElementById("driverPanel");
  const mainContent = document.getElementById("mainContent");

  // Hide driver panel with animation
  driverPanel.classList.remove("active");

  // Reset main content to center
  mainContent.style.marginRight = "";

  // Expand sidebar back to full width
  sidebar.classList.remove("sidebar-collapsed");
  sidebar.classList.add("sidebar-expanded");

  // Remove panel from DOM after animation completes
  setTimeout(() => {
    driverPanel.classList.add("hidden");
    driverPanel.classList.remove("flex");
  }, 300);
}

// Handle form submission
document
  .getElementById("driverForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = document.getElementById("submitBtn");
    const submitBtnText = document.getElementById("submitBtnText");
    const originalBtnText = submitBtnText.textContent;

    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtnText.textContent = "Saving...";

    try {
      const response = await fetch("driver_handler.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        // Show success message
        showNotification("success", result.message);

        // Close panel and reload page
        closeDriverPanel();
        setTimeout(() => {
          window.location.reload();
        }, 500);
      } else {
        showNotification("error", result.message || "An error occurred");
      }
    } catch (error) {
      showNotification("error", "Failed to save driver: " + error.message);
    } finally {
      submitBtn.disabled = false;
      submitBtnText.textContent = originalBtnText;
    }
  });

// View driver function
async function viewDriver(id) {
  try {
    const response = await fetch(
      `driver_handler.php?action=get_single&id=${id}`
    );
    const result = await response.json();

    if (result.success) {
      const driver = result.driver;

      // Hide form, show view container
      document.getElementById("driverForm").classList.add("hidden");
      document.getElementById("driverViewContainer").classList.remove("hidden");
      document.getElementById("panelActions").classList.add("hidden");

      // Update panel title
      document.getElementById("panelTitle").textContent = "Driver Details";

      // Get vehicle icon
      let vehicleIcon = "local_shipping";
      let vehicleColor = "blue";
      switch (driver.vehicle_type) {
        case "truck":
          vehicleIcon = "local_shipping";
          vehicleColor = "blue";
          break;
        case "van":
          vehicleIcon = "airport_shuttle";
          vehicleColor = "orange";
          break;
        case "motorcycle":
          vehicleIcon = "two_wheeler";
          vehicleColor = "purple";
          break;
      }

      // Get status badge
      let statusClass = "";
      let statusText = "";
      switch (driver.status) {
        case "active":
          statusClass =
            "bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400";
          statusText = "Active";
          break;
        case "inactive":
          statusClass =
            "bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400";
          statusText = "Inactive";
          break;
        case "on_leave":
          statusClass =
            "bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400";
          statusText = "On Leave";
          break;
      }

      // Build view HTML
      const viewHTML = `
                        <!-- Driver Profile -->
                        <div class="flex flex-col items-center text-center pb-6 border-b border-gray-200 dark:border-gray-800">
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-4">
                                <span class="material-symbols-outlined text-5xl text-primary">person</span>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">${
                              driver.full_name
                            }</h3>
                            <p class="text-sm font-mono text-gray-500 dark:text-gray-400 mb-3">${
                              driver.employee_id
                            }</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold ${statusClass}">
                                ${statusText}
                            </span>
                        </div>

                        <!-- Contact Information -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact Information</h4>
                            
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-gray-400 text-[20px] mt-0.5">phone</span>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Phone Number</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${
                                      driver.phone_number
                                    }</p>
                                </div>
                            </div>

                            ${
                              driver.email
                                ? `
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-gray-400 text-[20px] mt-0.5">email</span>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Email Address</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${driver.email}</p>
                                </div>
                            </div>
                            `
                                : ""
                            }

                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-gray-400 text-[20px] mt-0.5">badge</span>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">License Number</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${
                                      driver.license_number
                                    }</p>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Information -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vehicle Assignment</h4>
                            
                            <div class="p-4 bg-gradient-to-br from-${vehicleColor}-50 to-${vehicleColor}-50/50 dark:from-${vehicleColor}-900/20 dark:to-${vehicleColor}-900/10 rounded-xl border border-${vehicleColor}-100 dark:border-${vehicleColor}-800/30">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-12 h-12 rounded-lg bg-white dark:bg-gray-800 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-${vehicleColor}-600 dark:text-${vehicleColor}-400 text-[28px]">${vehicleIcon}</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">${
                                          driver.vehicle_model ||
                                          driver.vehicle_type
                                            .charAt(0)
                                            .toUpperCase() +
                                            driver.vehicle_type.slice(1)
                                        }</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">${
                                          driver.vehicle_type
                                            .charAt(0)
                                            .toUpperCase() +
                                          driver.vehicle_type.slice(1)
                                        }</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-${vehicleColor}-100 dark:border-${vehicleColor}-800/30">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">License Plate</span>
                                    <span class="text-sm font-bold font-mono text-gray-900 dark:text-white">${
                                      driver.license_plate
                                    }</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-gray-400 text-[20px] mt-0.5">location_on</span>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Distribution Centre</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${
                                      driver.distribution_centre
                                    }</p>
                                </div>
                            </div>
                        </div>

                        <!-- Employment Details -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employment Details</h4>
                            
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-gray-400 text-[20px] mt-0.5">calendar_today</span>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Start Date</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${new Date(
                                      driver.start_date
                                    ).toLocaleDateString("en-US", {
                                      year: "numeric",
                                      month: "long",
                                      day: "numeric",
                                    })}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-gray-400 text-[20px] mt-0.5">schedule</span>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Joined</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${new Date(
                                      driver.created_at
                                    ).toLocaleDateString("en-US", {
                                      year: "numeric",
                                      month: "long",
                                      day: "numeric",
                                    })}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-4">
                            <button onclick="editDriver(${
                              driver.id
                            })" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-primary/90 text-white rounded-lg font-bold text-sm transition-colors">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                Edit Driver
                            </button>
                            <button onclick="deleteDriver(${driver.id}, '${
        driver.full_name
      }')" class="px-4 py-3 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 font-bold text-sm transition-colors">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    `;

      document.getElementById("driverViewContainer").innerHTML = viewHTML;

      // Open panel in view mode
      openDriverPanel(true);
    } else {
      showNotification("error", result.message || "Failed to load driver data");
    }
  } catch (error) {
    showNotification("error", "Failed to load driver: " + error.message);
  }
}

// Edit driver function
async function editDriver(id) {
  try {
    const response = await fetch(
      `driver_handler.php?action=get_single&id=${id}`
    );
    const result = await response.json();

    if (result.success) {
      const driver = result.driver;

      // Show form, hide view
      document.getElementById("driverForm").classList.remove("hidden");
      document.getElementById("driverViewContainer").classList.add("hidden");
      document.getElementById("panelActions").classList.remove("hidden");

      // Populate form fields
      document.getElementById("driver_id").value = driver.id;
      document.getElementById("full_name").value = driver.full_name;
      document.getElementById("phone_number").value = driver.phone_number;
      document.getElementById("email").value = driver.email || "";
      document.getElementById("license_number").value = driver.license_number;
      document.getElementById("vehicle_type").value = driver.vehicle_type;
      document.getElementById("vehicle_model").value =
        driver.vehicle_model || "";
      document.getElementById("license_plate").value = driver.license_plate;
      document.getElementById("distribution_centre").value =
        driver.distribution_centre;
      document.getElementById("start_date").value = driver.start_date;
      document.getElementById("status").value = driver.status;

      // Update panel title and button
      document.getElementById("panelTitle").textContent = "Edit Driver";
      document.getElementById("submitBtnText").textContent = "Update Driver";
      document.getElementById("form_action").value = "edit";
      currentDriverId = id;

      // Open panel in edit mode
      openDriverPanel(true);
    } else {
      showNotification("error", result.message || "Failed to load driver data");
    }
  } catch (error) {
    showNotification("error", "Failed to load driver: " + error.message);
  }
}

// Delete driver variables
let deleteDriverId = null;
let deleteDriverName = null;

// Delete driver function - show modal
function deleteDriver(id, name) {
  deleteDriverId = id;
  deleteDriverName = name;
  document.getElementById("deleteDriverName").textContent = name;
  document.getElementById("deleteModal").classList.remove("hidden");
}

// Close delete modal
function closeDeleteModal() {
  document.getElementById("deleteModal").classList.add("hidden");
  deleteDriverId = null;
  deleteDriverName = null;
}

// Confirm delete
async function confirmDelete() {
  if (!deleteDriverId) return;

  try {
    const formData = new FormData();
    formData.append("action", "delete");
    formData.append("id", deleteDriverId);

    const response = await fetch("driver_handler.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    closeDeleteModal();

    if (result.success) {
      showNotification("success", result.message);
      setTimeout(() => {
        window.location.reload();
      }, 500);
    } else {
      showNotification("error", result.message || "Failed to delete driver");
    }
  } catch (error) {
    closeDeleteModal();
    showNotification("error", "Failed to delete driver: " + error.message);
  }
}

// Search and filter functionality
let currentStatusFilter = "";
let currentCentreFilter = "";

// Search input handler
document.getElementById("searchInput").addEventListener("input", function (e) {
  const searchTerm = e.target.value.toLowerCase();
  filterTable(searchTerm, currentCentreFilter, currentStatusFilter);
});

// Centre filter handler
document
  .getElementById("centreFilter")
  .addEventListener("change", function (e) {
    currentCentreFilter = e.target.value;
    const searchTerm = document
      .getElementById("searchInput")
      .value.toLowerCase();
    filterTable(searchTerm, currentCentreFilter, currentStatusFilter);
  });

// Status filter function
function filterByStatus(status) {
  currentStatusFilter = status;
  const searchTerm = document.getElementById("searchInput").value.toLowerCase();
  filterTable(searchTerm, currentCentreFilter, currentStatusFilter);

  // Update button styles
  document.querySelectorAll(".status-filter").forEach((btn) => {
    if (btn.dataset.status === status) {
      btn.className =
        "status-filter whitespace-nowrap px-4 py-1.5 rounded-full bg-primary/10 text-primary border border-primary/20 text-sm font-bold flex items-center gap-2";
    } else {
      btn.className =
        "status-filter whitespace-nowrap px-4 py-1.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-transparent hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium transition-colors";
    }
  });
}

// Filter table function
function filterTable(searchTerm, centre, status) {
  const rows = document.querySelectorAll("tbody tr");
  let visibleCount = 0;

  rows.forEach((row) => {
    // Skip the "no drivers" row
    if (row.querySelector("td[colspan]")) {
      return;
    }

    const driverName = row
      .querySelector("td:nth-child(1)")
      .textContent.toLowerCase();
    const empId = row
      .querySelector("td:nth-child(2)")
      .textContent.toLowerCase();
    const vehicle = row
      .querySelector("td:nth-child(3)")
      .textContent.toLowerCase();
    const assignedCentre = row
      .querySelector("td:nth-child(4)")
      .textContent.trim();
    const statusBadge = row
      .querySelector("td:nth-child(5)")
      .textContent.trim()
      .toLowerCase();

    // Check search term
    const matchesSearch =
      !searchTerm ||
      driverName.includes(searchTerm) ||
      empId.includes(searchTerm) ||
      vehicle.includes(searchTerm);

    // Check centre filter
    const matchesCentre = !centre || assignedCentre === centre;

    // Check status filter
    let matchesStatus = true;
    if (status) {
      const statuses = status.split(",");
      matchesStatus = statuses.some((s) => {
        if (s === "active") return statusBadge.includes("active");
        if (s === "inactive") return statusBadge.includes("inactive");
        if (s === "on_leave") return statusBadge.includes("leave");
        return false;
      });
    }

    // Show/hide row
    if (matchesSearch && matchesCentre && matchesStatus) {
      row.style.display = "";
      visibleCount++;
    } else {
      row.style.display = "none";
    }
  });

  // Update pagination text if needed
  // You can add pagination update logic here
}

// Notification function
function showNotification(type, message) {
  const bgColor = type === "success" ? "bg-green-500" : "bg-red-500";
  const notification = document.createElement("div");
  notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2`;
  notification.innerHTML = `
                <span class="material-symbols-outlined">${
                  type === "success" ? "check_circle" : "error"
                }</span>
                <span>${message}</span>
            `;
  document.body.appendChild(notification);

  setTimeout(() => {
    notification.remove();
  }, 3000);
}
