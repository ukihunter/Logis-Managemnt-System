// Toggle user menu
function toggleUserMenu(event) {
  event.stopPropagation();
  const menu = document.getElementById("userMenu");
  menu.classList.toggle("hidden");
}

// Close menu when clicking outside
document.addEventListener("click", function (event) {
  const menu = document.getElementById("userMenu");
  if (!menu.classList.contains("hidden")) {
    menu.classList.add("hidden");
  }
});

// Open product panel (new or edit)
function openProductPanel(mode, productName = "", productSKU = "") {
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

  // Update panel title and button based on mode
  if (mode === "edit") {
    panelTitle.textContent = "Edit Product";
    submitButtonText.textContent = "Update Product";
    // Pre-fill form with product data
    document.getElementById("productName").value = productName;
    document.getElementById("productSKU").value = productSKU;
  } else {
    panelTitle.textContent = "New Stock Entry";
    submitButtonText.textContent = "Add Product";
    document.getElementById("productForm").reset();
    removeImage();
  }

  // Show product panel
  productPanel.classList.remove("hidden");
  productPanel.classList.add("flex");
  setTimeout(() => {
    productPanel.classList.add("active");
  }, 10);
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

// Handle form submission
document.getElementById("productForm").addEventListener("submit", function (e) {
  e.preventDefault();
  // Add your form submission logic here
  console.log("Form submitted");
  alert("Product saved successfully!");
  closeProductPanel();
});
