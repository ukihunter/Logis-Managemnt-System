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
          `input[name="status"][value="${product.status}"]`,
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
let productToDelete = null;

function deleteProduct(productId) {
  productToDelete = productId;
  document.getElementById("deleteModal").classList.remove("hidden");
}

function closeDeleteModal() {
  document.getElementById("deleteModal").classList.add("hidden");
  productToDelete = null;
}

function confirmDelete() {
  if (!productToDelete) return;

  const formData = new FormData();
  formData.append("action", "delete");
  formData.append("product_id", productToDelete);

  // Close modal and show loading state
  closeDeleteModal();

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

// Filter by category
function filterByCategory(category) {
  const urlParams = new URLSearchParams(window.location.search);
  const search = urlParams.get("search") || "";

  if (category) {
    window.location.href = `?category=${encodeURIComponent(
      category,
    )}&search=${encodeURIComponent(search)}`;
  } else {
    window.location.href = `?search=${encodeURIComponent(search)}`;
  }
}

// AJAX Filter Products
let currentPage = 1;

function filterProducts(page = 1) {
  currentPage = page;
  const search = document.getElementById("searchInput").value;
  const category = document.getElementById("categoryFilter").value;
  const brand = document.getElementById("brandFilter").value;

  const params = new URLSearchParams();
  if (search) params.append("search", search);
  if (category) params.append("category", category);
  if (brand) params.append("brand", brand);
  params.append("page", page);

  fetch(`filter_products.php?${params.toString()}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("productTableBody").innerHTML = data.html;

        // Update pagination
        const paginationContainer = document.getElementById(
          "paginationContainer",
        );
        if (paginationContainer) {
          paginationContainer.innerHTML = data.pagination;
        }
      }
    })
    .catch((error) => {
      console.error("Filter error:", error);
    });
}

// Search functionality
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput");
  const categoryFilter = document.getElementById("categoryFilter");
  const brandFilter = document.getElementById("brandFilter");

  if (searchInput) {
    // Debounce search
    let searchTimeout;
    searchInput.addEventListener("keyup", function (e) {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        filterProducts(1);
      }, 500);
    });
  }

  if (categoryFilter) {
    categoryFilter.addEventListener("change", () => filterProducts(1));
  }

  if (brandFilter) {
    brandFilter.addEventListener("change", () => filterProducts(1));
  }
});

// Open Category Panel
function openCategoryPanel() {
  const sidebar = document.getElementById("sidebar");
  const categoryPanel = document.getElementById("categoryPanel");
  const mainContent = document.getElementById("mainContent");

  sidebar.classList.remove("sidebar-expanded");
  sidebar.classList.add("sidebar-collapsed");
  mainContent.style.marginRight = "28rem";

  document.getElementById("categoryForm").reset();
  categoryPanel.classList.remove("hidden");
  categoryPanel.classList.add("flex");
  setTimeout(() => categoryPanel.classList.add("active"), 10);
}

function closeCategoryPanel() {
  const sidebar = document.getElementById("sidebar");
  const categoryPanel = document.getElementById("categoryPanel");
  const mainContent = document.getElementById("mainContent");

  categoryPanel.classList.remove("active");
  setTimeout(() => {
    categoryPanel.classList.remove("flex");
    categoryPanel.classList.add("hidden");
    sidebar.classList.remove("sidebar-collapsed");
    sidebar.classList.add("sidebar-expanded");
    mainContent.style.marginRight = "0";
  }, 300);
}

function submitCategory() {
  const formData = new FormData();
  formData.append("action", "add_category");
  formData.append("name", document.getElementById("categoryName").value);

  fetch("category_handler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification(data.message, "success");
        setTimeout(() => location.reload(), 1000);
      } else {
        showNotification(data.message, "error");
      }
    })
    .catch((error) => {
      showNotification("An error occurred", "error");
      console.error("Error:", error);
    });
}

function deleteCategory(id) {
  if (!confirm("Delete this category? Products will not be deleted.")) return;

  const formData = new FormData();
  formData.append("action", "delete_category");
  formData.append("category_id", id);

  fetch("category_handler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification(data.message, "success");
        setTimeout(() => location.reload(), 1000);
      } else {
        showNotification(data.message, "error");
      }
    })
    .catch((error) => {
      showNotification("An error occurred", "error");
      console.error("Error:", error);
    });
}

// Open Brand Panel
function openBrandPanel() {
  const sidebar = document.getElementById("sidebar");
  const brandPanel = document.getElementById("brandPanel");
  const mainContent = document.getElementById("mainContent");

  sidebar.classList.remove("sidebar-expanded");
  sidebar.classList.add("sidebar-collapsed");
  mainContent.style.marginRight = "28rem";

  document.getElementById("brandForm").reset();
  brandPanel.classList.remove("hidden");
  brandPanel.classList.add("flex");
  setTimeout(() => brandPanel.classList.add("active"), 10);
}

function closeBrandPanel() {
  const sidebar = document.getElementById("sidebar");
  const brandPanel = document.getElementById("brandPanel");
  const mainContent = document.getElementById("mainContent");

  brandPanel.classList.remove("active");
  setTimeout(() => {
    brandPanel.classList.remove("flex");
    brandPanel.classList.add("hidden");
    sidebar.classList.remove("sidebar-collapsed");
    sidebar.classList.add("sidebar-expanded");
    mainContent.style.marginRight = "0";
  }, 300);
}

function submitBrand() {
  const formData = new FormData();
  formData.append("action", "add_brand");
  formData.append("name", document.getElementById("brandName").value);

  fetch("brand_handler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification(data.message, "success");
        setTimeout(() => location.reload(), 1000);
      } else {
        showNotification(data.message, "error");
      }
    })
    .catch((error) => {
      showNotification("An error occurred", "error");
      console.error("Error:", error);
    });
}

function deleteBrand(id) {
  if (!confirm("Delete this brand? Products will not be deleted.")) return;

  const formData = new FormData();
  formData.append("action", "delete_brand");
  formData.append("brand_id", id);

  fetch("brand_handler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification(data.message, "success");
        setTimeout(() => location.reload(), 1000);
      } else {
        showNotification(data.message, "error");
      }
    })
    .catch((error) => {
      showNotification("An error occurred", "error");
      console.error("Error:", error);
    });
}

// Open Details Panel
function openDetailsPanel(productId) {
  const sidebar = document.getElementById("sidebar");
  const detailsPanel = document.getElementById("detailsPanel");
  const mainContent = document.getElementById("mainContent");

  sidebar.classList.remove("sidebar-expanded");
  sidebar.classList.add("sidebar-collapsed");
  mainContent.style.marginRight = "28rem";

  // Fetch product details
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
        displayProductDetails(data.product);
      } else {
        showNotification(data.message, "error");
      }
    })
    .catch((error) => {
      showNotification("Error loading details", "error");
      console.error("Error:", error);
    });

  detailsPanel.classList.remove("hidden");
  detailsPanel.classList.add("flex");
  setTimeout(() => detailsPanel.classList.add("active"), 10);
}

function closeDetailsPanel() {
  const sidebar = document.getElementById("sidebar");
  const detailsPanel = document.getElementById("detailsPanel");
  const mainContent = document.getElementById("mainContent");

  detailsPanel.classList.remove("active");
  setTimeout(() => {
    detailsPanel.classList.remove("flex");
    detailsPanel.classList.add("hidden");
    sidebar.classList.remove("sidebar-collapsed");
    sidebar.classList.add("sidebar-expanded");
    mainContent.style.marginRight = "0";
  }, 300);
}

function displayProductDetails(product) {
  const container = document.getElementById("productDetails");
  const stockPercentage =
    product.max_level > 0 ? (product.stock_level / product.max_level) * 100 : 0;
  const isLowStock = product.stock_level < product.max_level * 0.2;

  container.innerHTML = `
    ${
      product.image_path
        ? `
      <div class="w-full h-48 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
        <img src="../../../${product.image_path}" alt="${product.name}" class="w-full h-full object-cover">
      </div>
    `
        : ""
    }
    
    <div>
      <h3 class="text-lg font-bold text-[#0d1b12] dark:text-white">${
        product.name
      }</h3>
      <p class="text-sm text-primary font-medium mt-1">SKU: ${product.sku}</p>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-lg">
        <p class="text-xs text-gray-500 dark:text-gray-400">Category</p>
        <p class="text-sm font-bold text-[#0d1b12] dark:text-white mt-1">${
          product.category
        }</p>
      </div>
      <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-lg">
        <p class="text-xs text-gray-500 dark:text-gray-400">Brand</p>
        <p class="text-sm font-bold text-[#0d1b12] dark:text-white mt-1">${
          product.brand || "N/A"
        }</p>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-lg">
        <p class="text-xs text-gray-500 dark:text-gray-400">Unit Price</p>
        <p class="text-lg font-bold text-[#0d1b12] dark:text-white mt-1">Rs ${parseFloat(
          product.unit_price,
        ).toFixed(2)}</p>
      </div>
      <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-lg">
        <p class="text-xs text-gray-500 dark:text-gray-400">Carton Price</p>
        <p class="text-lg font-bold text-[#0d1b12] dark:text-white mt-1">Rs ${parseFloat(
          product.carton_price,
        ).toFixed(2)}</p>
      </div>
    </div>

    <div class="p-4 bg-gradient-to-r from-primary/10 to-primary/5 dark:from-primary/20 dark:to-primary/10 rounded-lg border border-primary/20">
      <div class="flex justify-between items-center mb-2">
        <p class="text-sm font-bold text-[#0d1b12] dark:text-white">Stock Level</p>
        <span class="${
          isLowStock ? "text-red-600 dark:text-red-400" : "text-primary"
        } font-bold">${product.stock_level} / ${product.max_level}</span>
      </div>
      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
        <div class="${
          isLowStock ? "bg-red-500" : "bg-primary"
        } h-2 rounded-full transition-all" style="width: ${Math.min(
          stockPercentage,
          100,
        )}%"></div>
      </div>
    </div>

    <div class="grid grid-cols-3 gap-3">
      <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-lg text-center">
        <p class="text-xs text-gray-500 dark:text-gray-400">Carton Qty</p>
        <p class="text-lg font-bold text-[#0d1b12] dark:text-white mt-1">${
          product.carton_quantity
        }</p>
      </div>
      <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-lg text-center">
        <p class="text-xs text-gray-500 dark:text-gray-400">Allocated</p>
        <p class="text-lg font-bold text-[#0d1b12] dark:text-white mt-1">${
          product.allocated
        }</p>
      </div>
      <div class="p-3 bg-gray-50 dark:bg-white/5 rounded-lg text-center">
        <p class="text-xs text-gray-500 dark:text-gray-400">Available</p>
        <p class="text-lg font-bold text-primary mt-1">${
          product.stock_level - product.allocated
        }</p>
      </div>
    </div>

    ${
      product.description
        ? `
      <div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Description</p>
        <p class="text-sm text-[#0d1b12] dark:text-white">${product.description}</p>
      </div>
    `
        : ""
    }

    ${
      product.offer_label || product.discount_percentage > 0
        ? `
      <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
        ${
          product.offer_label
            ? `<p class="text-sm font-bold text-orange-700 dark:text-orange-400">${product.offer_label}</p>`
            : ""
        }
        ${
          product.discount_percentage > 0
            ? `<p class="text-xs text-orange-600 dark:text-orange-300 mt-1">${product.discount_percentage}% Discount</p>`
            : ""
        }
      </div>
    `
        : ""
    }

    <div class="flex gap-2 pt-4">
      <button onclick="closeDetailsPanel(); openProductPanel('edit', ${
        product.id
      })" class="flex-1 px-4 py-2 bg-primary hover:bg-[#0ebf49] text-[#0d1b12] rounded-lg font-bold text-sm transition-colors">
        Edit Product
      </button>
      <button onclick="deleteProduct(${
        product.id
      })" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-bold text-sm transition-colors">
        Delete
      </button>
    </div>
  `;
}

// Stock Transfer Modal Functions
let currentTransferProduct = null;

function openStockTransferModal(productId, productName, stockLevel) {
  console.log(
    "Opening stock transfer modal for:",
    productId,
    productName,
    stockLevel,
  );

  currentTransferProduct = {
    id: productId,
    name: productName,
    stock: stockLevel,
  };

  // Update modal content
  const transferProductId = document.getElementById("transferProductId");
  const transferProductName = document.getElementById("transferProductName");
  const availableStock = document.getElementById("availableStock");
  const maxQuantity = document.getElementById("maxQuantity");
  const transferQuantity = document.getElementById("transferQuantity");
  const modal = document.getElementById("stockTransferModal");

  if (!transferProductId || !modal) {
    console.error("Modal elements not found!");
    alert("Error: Stock transfer modal not found. Please refresh the page.");
    return;
  }

  transferProductId.value = productId;
  transferProductName.textContent = productName;
  availableStock.textContent = stockLevel;
  maxQuantity.textContent = stockLevel;
  transferQuantity.max = stockLevel;

  // Reset form
  document.getElementById("stockTransferForm").reset();
  transferProductId.value = productId;

  // Load branches
  loadBranches();

  // Show modal
  modal.classList.remove("hidden");
  console.log("Modal should now be visible");
}

function closeStockTransferModal() {
  document.getElementById("stockTransferModal").classList.add("hidden");
  currentTransferProduct = null;
}

function loadBranches() {
  const formData = new FormData();
  formData.append("action", "get_branches");

  fetch("stock_transfer_handler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const select = document.getElementById("transferBranch");
        select.innerHTML = '<option value="">Choose a branch...</option>';

        data.branches.forEach((branch) => {
          const option = document.createElement("option");
          option.value = branch.id;
          option.textContent = `${branch.name} - ${branch.location}`;
          select.appendChild(option);
        });
      } else {
        showNotification("Failed to load branches", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Error loading branches", "error");
    });
}

// Handle stock transfer form submission
document.addEventListener("DOMContentLoaded", function () {
  // Event delegation for stock transfer buttons
  document.addEventListener("click", function (e) {
    const transferBtn = e.target.closest(".stock-transfer-btn");
    if (transferBtn) {
      e.preventDefault();
      const productId = parseInt(transferBtn.dataset.productId);
      const productName = transferBtn.dataset.productName;
      const stockLevel = parseInt(transferBtn.dataset.stockLevel);

      console.log(
        "Stock transfer clicked:",
        productId,
        productName,
        stockLevel,
      );
      openStockTransferModal(productId, productName, stockLevel);
    }
  });

  const transferForm = document.getElementById("stockTransferForm");
  if (transferForm) {
    transferForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      formData.append("action", "transfer_stock");

      const quantity = parseInt(formData.get("quantity"));
      const maxStock = currentTransferProduct
        ? currentTransferProduct.stock
        : 0;

      // Validate quantity
      if (quantity <= 0) {
        showNotification("Quantity must be greater than 0", "error");
        return;
      }

      if (quantity > maxStock) {
        showNotification(
          `Cannot transfer more than ${maxStock} units`,
          "error",
        );
        return;
      }

      // Disable submit button
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = "Processing...";

      fetch("stock_transfer_handler.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            showNotification(data.message, "success");
            closeStockTransferModal();
            // Reload page to update stock levels
            setTimeout(() => {
              location.reload();
            }, 1000);
          } else {
            showNotification(data.message || "Transfer failed", "error");
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          showNotification("Error processing transfer", "error");
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        });
    });
  }
});
