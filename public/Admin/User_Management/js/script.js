function toggleFieldsByUserType() {
  const userType = document.getElementById("userTypeSelect").value;
  const businessNameField = document.getElementById("businessNameField");
  const addressField = document.getElementById("addressField");
  const provinceField = document.getElementById("provinceField");

  // Hide business name and address for staff and admin
  if (userType === "staff" || userType === "admin") {
    businessNameField.style.display = "none";
    addressField.style.display = "none";
    // Clear values when hidden
    businessNameField.querySelector("input").value = "";
    addressField.querySelector("textarea").value = "";
  } else {
    businessNameField.style.display = "block";
    addressField.style.display = "block";
  }

  // Hide province for admin only
  if (userType === "admin") {
    provinceField.style.display = "none";
    provinceField.querySelector("select").value = "";
  } else {
    provinceField.style.display = "block";
  }
}

function openUserPanel() {
  // Collapse sidebar
  const sidebar = document.getElementById("sidebar");
  sidebar.classList.add("sidebar-collapsed");
  sidebar.classList.remove("sidebar-expanded");

  // Shrink main content
  const mainContent = document.getElementById("mainContent");
  mainContent.style.width = "calc(100% - 450px)";

  // Show panel
  const panel = document.getElementById("userPanel");
  panel.classList.remove("translate-x-full");
  panel.classList.add("translate-x-0");
}

function closeUserPanel() {
  // Reset main content
  const mainContent = document.getElementById("mainContent");
  mainContent.style.width = "100%";

  // Hide panel
  const panel = document.getElementById("userPanel");
  panel.classList.remove("translate-x-0");
  panel.classList.add("translate-x-full");

  // Reset form
  document.getElementById("userForm").reset();
  document.getElementById("userId").value = "";
  document.getElementById("formAction").value = "add";
  document.querySelector("#userPanel h2").textContent = "Add New User";
}

// Show notification
function showNotification(message, type = "success") {
  const notification = document.getElementById("notification");
  const content = document.getElementById("notificationContent");
  const icon = document.getElementById("notificationIcon");
  const msg = document.getElementById("notificationMessage");

  // Set message
  msg.textContent = message;

  // Set style based on type
  if (type === "success") {
    content.className =
      "flex items-center gap-3 min-w-[320px] px-4 py-3 rounded-lg shadow-lg bg-primary text-white";
    icon.textContent = "check_circle";
  } else if (type === "error") {
    content.className =
      "flex items-center gap-3 min-w-[320px] px-4 py-3 rounded-lg shadow-lg bg-red-500 text-white";
    icon.textContent = "error";
  } else if (type === "warning") {
    content.className =
      "flex items-center gap-3 min-w-[320px] px-4 py-3 rounded-lg shadow-lg bg-orange-500 text-white";
    icon.textContent = "warning";
  }

  // Show notification
  notification.classList.remove("translate-x-[500px]");
  notification.classList.add("translate-x-0");

  // Hide after 3 seconds
  setTimeout(() => {
    notification.classList.remove("translate-x-0");
    notification.classList.add("translate-x-[500px]");
  }, 3000);
}

// Edit user
function editUser(userId) {
  // Fetch user data
  const formData = new FormData();
  formData.append("action", "get");
  formData.append("user_id", userId);

  fetch("user_handler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.text();
    })
    .then((text) => {
      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        console.error("Invalid JSON response:", text);
        throw new Error(
          "Server returned invalid JSON. Check console for details."
        );
      }
      if (data.success) {
        const user = data.user;

        // Populate form
        document.getElementById("userId").value = user.id;
        document.getElementById("formAction").value = "update";
        document.querySelector("#userPanel h2").textContent = "Edit User";

        document.querySelector('[name="user_type"]').value = user.user_type;
        document.querySelector('[name="business_name"]').value =
          user.business_name || "";
        document.querySelector('[name="full_name"]').value = user.full_name;
        document.querySelector('[name="email"]').value = user.email;
        document.querySelector('[name="username"]').value = user.username;
        document.querySelector('[name="phone_number"]').value =
          user.phone_number || "";
        document.querySelector('[name="address"]').value = user.address || "";
        document.querySelector('[name="province"]').value = user.province || "";
        document.querySelector('[name="status"]').value = user.status;

        // Password is optional for edit
        document.querySelector('[name="password"]').removeAttribute("required");
        document.querySelector('[name="password"]').placeholder =
          "Leave blank to keep current password";

        // Toggle fields based on user type
        toggleFieldsByUserType();

        // Open panel
        openUserPanel();
      } else {
        showNotification(data.message, "error");
      }
    })
    .catch((error) => {
      showNotification(error.message || "Error loading user data", "error");
      console.error("Error:", error);
    });
}

// Handle form submission
document.getElementById("userForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const action = formData.get("action");

  fetch("user_handler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification(data.message, "success");
        closeUserPanel();
        // Reload page after short delay to show updated data
        setTimeout(() => {
          location.reload();
        }, 1000);
      } else {
        showNotification(data.message, "error");
      }
    })
    .catch((error) => {
      showNotification("An error occurred. Please try again.", "error");
      console.error("Error:", error);
    });
});
