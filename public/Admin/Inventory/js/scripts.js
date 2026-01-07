// Open product panel (new or edit)
function openProductPanel(mode, productId = null) {
  const sidebar = document.getElementById("sidebar");
  const productPanel = document.getElementById("productPanel");
  const mainContent = document.getElementById("mainContent");
  const panelTitle = document.getElementById("panelTitle");
  const submitButtonText = document.getElementById("submitButtonText");

  // Collapse sidebar to icon-only mode
  sidebar.classList.remove("sidebar-expanded");
  sidebar.classList.add("sidebar-collapsed");

  // Adjust main content width
  mainContent.style.marginRight = "28rem";

  // Reset form
  document.getElementById("productForm").reset();
  removeImage();

  // Update panel title and button based on mode
  if (mode === "edit" && productId) {
    panelTitle.textContent = "Edit Product";
    submitButtonText.textContent = "Update Product";
    document.getElementById("formAction").value = "update";
    document.getElementById("productId").value = productId;

    // Fetch product data
    loadProductData(productId);
  } else {
    panelTitle.textContent = "New Stock Entry";
    submitButtonText.textContent = "Add Product";
    document.getElementById("formAction").value = "add";
    document.getElementById("productId").value = "";
  }

  // Show product panel
  productPanel.classList.remove("hidden");
  productPanel.classList.add("flex");
  setTimeout(() => {
    productPanel.classList.add("active");
  }, 10);
}

// Load product data for editing
function loadProductData(productId) {
  const formData = new FormData();
  formData.append("action", "get");
  formData.append("product_id", productId);

  fetch("product_handler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const product = data.product;

        // Populate form fields
        document.getElementById("productName").value = product.name;
        document.getElementById("productSKU").value = product.sku;
        document.getElementById("productCategory").value = product.category;
        document.getElementById("productBrand").value = product.brand || "";
        document.getElementById("unitPrice").value = product.unit_price;
        document.getElementById("cartonQuantity").value =
          product.carton_quantity;
        document.getElementById("stockLevel").value = product.stock_level;
        document.getElementById("maxLevel").value = product.max_level;
        document.getElementById("allocated").value = product.allocated;
        document.getElementById("offerLabel").value = product.offer_label || "";
        document.getElementById("discount").value = product.discount_percentage;
        document.getElementById("description").value =
          product.description || "";
        document.querySelector(
          `input[name="status"][value="${product.status}"]`
        ).checked = true;
        document.getElementById("isFeatured").checked =
          product.is_featured == 1;

        // Show existing image if available
        if (product.image_path) {
          document.getElementById("imagePreview").src =
            "../../../" + product.image_path;
          document
            .getElementById("imagePreviewContainer")
            .classList.add("active");
        }
      } else {
        showNotification(data.message, "error");
      }
    })
    .catch((error) => {
      showNotification("Error loading product data", "error");
      console.error("Error:", error);
    });
}

// Close product panel
function closeProductPanel() {
  const sidebar = document.getElementById("sidebar");
  const productPanel = document.getElementById("productPanel");
  const mainContent = document.getElementById("mainContent");

  // Hide product panel
  productPanel.classList.remove("active");
  setTimeout(() => {
    productPanel.classList.add("hidden");
    productPanel.classList.remove("flex");
  }, 300);

  // Restore main content width
  mainContent.style.marginRight = "0";

  // Expand sidebar back to full width
  sidebar.classList.remove("sidebar-collapsed");
  sidebar.classList.add("sidebar-expanded");
}

// Preview uploaded image
function previewImage(event) {
  const file = event.target.files[0];
  if (file) {
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
      showNotification("File size must be less than 5MB", "error");
      event.target.value = "";
      return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      document.getElementById("imagePreview").src = e.target.result;
      document.getElementById("imagePreviewContainer").classList.add("active");
    };
    reader.readAsDataURL(file);
  }
}

// Remove image preview
function removeImage() {
  document.getElementById("productImage").value = "";
  document.getElementById("imagePreview").src = "";
  document.getElementById("imagePreviewContainer").classList.remove("active");
}

// Show notification
function showNotification(message, type = "success") {
  // Create notification element if it doesn't exist
  let notification = document.getElementById("notification");
  if (!notification) {
    notification = document.createElement("div");
    notification.id = "notification";
    notification.className =
      "fixed top-4 right-4 z-[100] transform translate-x-[500px] transition-transform duration-300";
    document.body.appendChild(notification);
  }

  const bgColor =
    type === "success"
      ? "bg-primary"
      : type === "error"
      ? "bg-red-500"
      : "bg-orange-500";
  const icon =
    type === "success"
      ? "check_circle"
      : type === "error"
      ? "error"
      : "warning";

  notification.innerHTML = `
    <div class="flex items-center gap-3 min-w-[320px] px-4 py-3 rounded-lg shadow-lg ${bgColor} text-white">
      <span class="material-symbols-outlined">${icon}</span>
      <span class="text-sm font-medium">${message}</span>
    </div>
  `;

  notification.classList.remove("translate-x-[500px]");
  notification.classList.add("translate-x-0");

  setTimeout(() => {
    notification.classList.remove("translate-x-0");
    notification.classList.add("translate-x-[500px]");
  }, 3000);
}

// Submit product form
function submitProduct() {
  const form = document.getElementById("productForm");
  const formData = new FormData(form);

  // Validate required fields
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  // Submit form
  fetch("product_handler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        showNotification(data.message, "success");
        closeProductPanel();
        // Reload page after short delay
        setTimeout(() => {
          location.reload();
        }, 1000);
      } else {
        showNotification(data.message || "An error occurred", "error");
        console.error("Server error:", data);
      }
    })
    .catch((error) => {
      showNotification("An error occurred. Please try again.", "error");
      console.error("Error:", error);
    });
}

// Delete product
function deleteProduct(productId) {
  if (
    !confirm(
      "Are you sure you want to delete this product? This action cannot be undone."
    )
  ) {
    return;
  }

  const formData = new FormData();
  formData.append("action", "delete");
  formData.append("product_id", productId);

  fetch("product_handler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification(data.message, "success");
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
}
