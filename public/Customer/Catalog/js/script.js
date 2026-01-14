// Catalog filter and product loading functionality
let currentPage = 1;
let currentFilters = {
  search: "",
  category: "all",
  brands: [],
  stock_status: "",
  min_price: 0,
  max_price: 0,
  sort_by: "popularity",
};

// Load products on page load
document.addEventListener("DOMContentLoaded", function () {
  loadProducts();
  setupEventListeners();
  setupProfileDropdown();
});

// Setup profile dropdown
function setupProfileDropdown() {
  const profileMenuBtn = document.getElementById("profileMenuBtn");
  const profileDropdown = document.getElementById("profileDropdown");
  const mobileProfileBtn = document.getElementById("mobileProfileBtn");

  //  cathing the action
  if (profileMenuBtn && profileDropdown) {
    profileMenuBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      profileDropdown.classList.toggle("hidden");
    });
  }

  // mobiele preview settings
  if (mobileProfileBtn && profileDropdown) {
    mobileProfileBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      profileDropdown.classList.toggle("hidden");
    });
  }

  // cathing the action
  document.addEventListener("click", function (e) {
    if (
      profileDropdown &&
      !profileMenuBtn?.contains(e.target) &&
      !mobileProfileBtn?.contains(e.target) &&
      !profileDropdown.contains(e.target)
    ) {
      profileDropdown.classList.add("hidden");
    }
  });
}

// Setup all event listeners
function setupEventListeners() {
  // Search input with debounce
  const searchInput = document.getElementById("searchInput");
  let searchTimeout;
  searchInput.addEventListener("input", function () {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      currentFilters.search = this.value;
      currentPage = 1;
      loadProducts();
    }, 500);
  });

  // ESC key to clear search
  searchInput.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      this.value = "";
      currentFilters.search = "";
      currentPage = 1;
      loadProducts();
    }
  });

  // Category filters
  const categoryButtons = document.querySelectorAll(".category-filter");
  categoryButtons.forEach((button) => {
    button.addEventListener("click", function () {
      categoryButtons.forEach((btn) => {
        btn.classList.remove("bg-primary/10", "text-primary", "font-bold");
        btn.classList.add("text-text-main", "dark:text-gray-300");
      });

      this.classList.add("bg-primary/10", "text-primary", "font-bold");
      this.classList.remove("text-text-main", "dark:text-gray-300");

      currentFilters.category = this.dataset.category;
      currentPage = 1;
      loadProducts();
    });
  });

  // Brand checkboxes
  const brandCheckboxes = document.querySelectorAll(".brand-checkbox");
  brandCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      if (this.checked) {
        currentFilters.brands.push(this.value);
      } else {
        currentFilters.brands = currentFilters.brands.filter(
          (b) => b !== this.value
        );
      }
      currentPage = 1;
      loadProducts();
    });
  });

  // Stock status radio buttons
  const stockRadios = document.querySelectorAll(".stock-radio");
  stockRadios.forEach((radio) => {
    radio.addEventListener("change", function () {
      currentFilters.stock_status = this.value;
      currentPage = 1;
      loadProducts();
    });
  });

  // Price filter
  const applyPriceBtn = document.getElementById("applyPriceFilter");
  applyPriceBtn.addEventListener("click", function () {
    const minPrice = document.getElementById("minPrice").value;
    const maxPrice = document.getElementById("maxPrice").value;
    currentFilters.min_price = minPrice ? parseFloat(minPrice) : 0;
    currentFilters.max_price = maxPrice ? parseFloat(maxPrice) : 0;
    currentPage = 1;
    loadProducts();
  });

  // Sort dropdown
  const sortSelect = document.getElementById("sortBy");
  sortSelect.addEventListener("change", function () {
    currentFilters.sort_by = this.value;
    currentPage = 1;
    loadProducts();
  });

  // Clear all filters
  const clearFiltersBtn = document.getElementById("clearFilters");
  clearFiltersBtn.addEventListener("click", function () {
    currentFilters = {
      search: "",
      category: "all",
      brands: [],
      stock_status: "",
      min_price: 0,
      max_price: 0,
      sort_by: "popularity",
    };
    currentPage = 1;

    searchInput.value = "";
    brandCheckboxes.forEach((cb) => (cb.checked = false));
    stockRadios[0].checked = true;
    document.getElementById("minPrice").value = "";
    document.getElementById("maxPrice").value = "";
    document.getElementById("sortBy").value = "popularity";

    categoryButtons.forEach((btn) => {
      btn.classList.remove("bg-primary/10", "text-primary", "font-bold");
      btn.classList.add("text-text-main", "dark:text-gray-300");
    });
    categoryButtons[0].classList.add(
      "bg-primary/10",
      "text-primary",
      "font-bold"
    );
    categoryButtons[0].classList.remove("text-text-main", "dark:text-gray-300");

    loadProducts();
  });
}

// Load products from server
function loadProducts() {
  const productGrid = document.getElementById("productGrid");

  productGrid.innerHTML = `
        <div class="col-span-full flex justify-center items-center py-20">
            <div class="animate-pulse text-text-muted">Loading products...</div>
        </div>
    `;

  const params = new URLSearchParams({
    page: currentPage,
    per_page: 12,
    search: currentFilters.search,
    category: currentFilters.category,
    stock_status: currentFilters.stock_status,
    min_price: currentFilters.min_price,
    max_price: currentFilters.max_price,
    sort_by: currentFilters.sort_by,
  });

  currentFilters.brands.forEach((brand) => {
    params.append("brands[]", brand);
  });

  fetch(`filter_products.php?${params.toString()}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        renderProducts(data.products);
        renderPagination(data.pagination);
        updateProductCount(data.pagination);
      } else {
        showError("Failed to load products");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showError("An error occurred while loading products");
    });
}

// Render products
function renderProducts(products) {
  const productGrid = document.getElementById("productGrid");

  if (products.length === 0) {
    productGrid.innerHTML = `
            <div class="col-span-full flex flex-col items-center justify-center py-20">
                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">inventory_2</span>
                <p class="text-gray-500 text-lg">No products found</p>
                <p class="text-gray-400 text-sm mt-2">Try adjusting your filters</p>
            </div>
        `;
    return;
  }

  productGrid.innerHTML = products
    .map((product) => createProductCard(product))
    .join("");
}

// Create product card
function createProductCard(product) {
  const imagePath = product.image_path ? `../../../${product.image_path}` : "";
  const hasImage = imagePath && imagePath.includes("assest/");

  //  color will define with the
  const stockColors = {
    green: {
      dot: "bg-primary",
      text: "text-green-700 dark:text-green-400",
      pulse: "animate-pulse",
    },
    orange: {
      dot: "bg-orange-400",
      text: "text-orange-600 dark:text-orange-400",
      pulse: "",
    },
    gray: { dot: "bg-gray-400", text: "text-gray-500", pulse: "" },
  };

  // defining the stock color
  const stockColor =
    stockColors[product.stock_status.color] || stockColors.gray;
  const isOutOfStock = product.stock_status.status === "out_of_stock";
  const hasDiscount = product.discount_percentage > 0;

  // define the tags
  let labelHTML = "";
  if (product.is_featured && !hasDiscount) {
    labelHTML = `<div class="absolute top-3 left-3 z-10"><span class="bg-primary text-text-main text-xs font-bold px-2.5 py-1 rounded shadow-sm">${
      product.offer_label || "Best Seller"
    }</span></div>`;
  } else if (hasDiscount) {
    labelHTML = `<div class="absolute top-3 left-3 z-10"><span class="bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded shadow-sm">-${product.discount_percentage}% Off</span></div>`;
  }
  // price html
  let priceHTML = hasDiscount
    ? // this will show the prive acording to the discount that in the product
      `<div class="flex items-center gap-2"><span class="text-2xl font-black text-text-main dark:text-white">Rs ${product.discounted_price.toFixed(
        2
      )}</span><span class="text-xs text-red-500 line-through font-medium">Rs ${product.unit_price.toFixed(
        2
      )}</span></div>`
    : `<span class="text-2xl font-black text-text-main dark:text-white">Rs ${product.unit_price.toFixed(
        2
      )}</span>`;
  // cart button html
  let cartButtonHTML = isOutOfStock
    ? `<div class="flex gap-2"><div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg border border-border-light dark:border-border-dark h-10 w-24 shrink-0 opacity-50 cursor-not-allowed"><button class="w-8 h-full flex items-center justify-center text-gray-500" disabled>-</button><input class="w-full h-full bg-transparent text-center text-sm font-bold focus:ring-0 border-none p-0" disabled type="number" value="0" /><button class="w-8 h-full flex items-center justify-center text-gray-500" disabled>+</button></div><button class="flex-1 h-10 bg-gray-200 dark:bg-gray-700 text-gray-500 text-sm font-bold rounded-lg flex items-center justify-center gap-2 cursor-not-allowed" disabled>Notify Me</button></div>`
    : `<div class="flex gap-2"><div class="flex items-center bg-background-light dark:bg-background-dark rounded-lg border border-border-light dark:border-border-dark h-10 w-24 shrink-0"><button onclick="decrementQuantity(${product.id})" class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-primary transition-colors hover:bg-gray-100 rounded-l-lg">-</button><input id="qty-${product.id}" class="w-full h-full bg-transparent text-center text-sm font-bold focus:ring-0 border-none p-0" type="number" value="${product.carton_quantity}" min="${product.min_order_quantity}" /><button onclick="incrementQuantity(${product.id})" class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-primary transition-colors hover:bg-gray-100 rounded-r-lg">+</button></div><button onclick="addToCart(${product.id})" class="flex-1 h-10 bg-primary hover:bg-green-500 text-text-main text-sm font-bold rounded-lg transition-colors flex items-center justify-center gap-2 shadow-sm"><span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>Add</button></div>`;
  // return the final html
  return `
        <div class="group flex flex-col bg-white dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-primary/50 relative">
            ${labelHTML}
            <div class="aspect-[4/3] w-full bg-gray-50 dark:bg-gray-800 p-6 flex items-center justify-center relative overflow-hidden">
                ${
                  hasImage
                    ? `<div class="bg-center bg-no-repeat bg-contain w-full h-full transition-transform duration-500 group-hover:scale-105" style="background-image: url('${imagePath}');"></div>`
                    : `<span class="material-symbols-outlined text-gray-300 text-6xl">inventory_2</span>`
                }
            </div>
            <div class="p-4 flex flex-col gap-2 flex-1">
                <div class="flex justify-between items-start">
                    <p class="text-xs font-mono text-text-muted">SKU: ${
                      product.sku
                    }</p>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full ${stockColor.dot} ${
    stockColor.pulse
  }"></span>
                        <span class="text-xs font-bold ${stockColor.text}">${
    product.stock_status.label
  }</span>
                    </div>
                </div>
                <h3 class="font-bold text-lg text-text-main dark:text-white leading-tight line-clamp-2" title="${
                  product.name
                }">${product.name}</h3>
                <div class="mt-auto pt-4 flex flex-col gap-3">
                    <div class="flex items-baseline justify-between border-b border-dashed border-border-light dark:border-border-dark pb-3">
                        <div class="flex flex-col">
                            ${priceHTML}
                            <span class="text-[10px] uppercase text-text-muted font-bold tracking-wide">Per Unit</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-sm font-bold text-text-main dark:text-gray-300">Rs ${product.carton_price.toFixed(
                              2
                            )}</span>
                            <span class="text-[10px] text-text-muted">Per Carton (${
                              product.carton_quantity
                            })</span>
                        </div>
                    </div>
                    ${cartButtonHTML}
                </div>
            </div>
        </div>
    `;
}

// Render pagination
function renderPagination(pagination) {
  const container = document.getElementById("paginationContainer");
  if (pagination.total_pages <= 1) {
    container.innerHTML = "";
    return;
  }

  let html = "";
  html +=
    pagination.current_page > 1
      ? `<button onclick="changePage(${
          pagination.current_page - 1
        })" class="flex items-center justify-center w-10 h-10 rounded-lg bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-main dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"><span class="material-symbols-outlined">chevron_left</span></button>`
      : `<button disabled class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 border border-border-light dark:border-border-dark text-gray-400 cursor-not-allowed"><span class="material-symbols-outlined">chevron_left</span></button>`;

  const maxPages = 5;
  let start = Math.max(1, pagination.current_page - Math.floor(maxPages / 2));
  let end = Math.min(pagination.total_pages, start + maxPages - 1);
  if (end - start < maxPages - 1) start = Math.max(1, end - maxPages + 1);

  if (start > 1) {
    html += `<button onclick="changePage(1)" class="w-10 h-10 rounded-lg bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-main dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium">1</button>`;
    if (start > 2) html += `<span class="text-gray-400">...</span>`;
  }

  for (let i = start; i <= end; i++) {
    html +=
      i === pagination.current_page
        ? `<button class="w-10 h-10 rounded-lg bg-primary text-text-main font-bold shadow-md">${i}</button>`
        : `<button onclick="changePage(${i})" class="w-10 h-10 rounded-lg bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-main dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium">${i}</button>`;
  }

  if (end < pagination.total_pages) {
    if (end < pagination.total_pages - 1)
      html += `<span class="text-gray-400">...</span>`;
    html += `<button onclick="changePage(${pagination.total_pages})" class="w-10 h-10 rounded-lg bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-main dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium">${pagination.total_pages}</button>`;
  }

  html +=
    pagination.current_page < pagination.total_pages
      ? `<button onclick="changePage(${
          pagination.current_page + 1
        })" class="flex items-center justify-center w-10 h-10 rounded-lg bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark text-text-main dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"><span class="material-symbols-outlined">chevron_right</span></button>`
      : `<button disabled class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 border border-border-light dark:border-border-dark text-gray-400 cursor-not-allowed"><span class="material-symbols-outlined">chevron_right</span></button>`;

  container.innerHTML = html;
}

// Update product count display

function updateProductCount(pagination) {
  document.getElementById(
    "productCount"
  ).textContent = `Showing ${pagination.showing_from}-${pagination.showing_to} of ${pagination.total_products} products`;
}

// Change page
function changePage(page) {
  currentPage = page;
  loadProducts();
  window.scrollTo({ top: 0, behavior: "smooth" });
}

// show the error
function showError(message) {
  document.getElementById(
    "productGrid"
  ).innerHTML = `<div class="col-span-full flex flex-col items-center justify-center py-20"><span class="material-symbols-outlined text-6xl text-red-300 mb-4">error</span><p class="text-red-500 text-lg">${message}</p></div>`;
}

// conntatty  incressing  funtion

function incrementQuantity(id) {
  const input = document.getElementById(`qty-${id}`);
  input.value = parseInt(input.value) + 1;
}

//  countty decrese duntion

function decrementQuantity(id) {
  const input = document.getElementById(`qty-${id}`);
  const min = parseInt(input.min) || 1;
  if (parseInt(input.value) > min) input.value = parseInt(input.value) - 1;
}

// add to cart funtion

function addToCart(id) {
  const quantity = parseInt(document.getElementById(`qty-${id}`).value);
  console.log(`Adding product ${id} with quantity ${quantity} to cart`);

  // Send to cart handler
  const formData = new FormData();
  formData.append("action", "add");
  formData.append("product_id", id);
  formData.append("quantity", quantity);

  // cart handelr location

  fetch("../Cart/cart_handler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification(`Added ${quantity} units to cart!`, "success");
        // Update cart count in header if exists
        updateCartCount();
      } else {
        // error massage
        showNotification(data.message || "Failed to add to cart", "error");
      }
    })
    .catch((error) => {
      console.error("Error adding to cart:", error);
      showNotification("An error occurred. Please try again.", "error");
    });
}

// Update cart count in header
function updateCartCount() {
  fetch("../Cart/cart_handler.php?action=get")
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.cart) {
        const cartCountEl = document.getElementById("cartCount");
        if (cartCountEl) {
          cartCountEl.textContent = data.cart.item_count;
        }
      }
    })
    .catch((error) => console.error("Error updating cart count:", error));
}

// Show notification
function showNotification(message, type = "info") {
  const notification = document.createElement("div");
  notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium transform transition-all duration-300 ${
    type === "success"
      ? "bg-green-500"
      : type === "error"
      ? "bg-red-500"
      : "bg-blue-500"
  }`;
  notification.textContent = message;
  notification.style.opacity = "0";
  notification.style.transform = "translateY(-20px)";

  document.body.appendChild(notification);

  // set the time out
  setTimeout(() => {
    notification.style.opacity = "1";
    notification.style.transform = "translateY(0)";
  }, 10);

  setTimeout(() => {
    notification.style.opacity = "0";
    notification.style.transform = "translateY(-20px)";
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}
