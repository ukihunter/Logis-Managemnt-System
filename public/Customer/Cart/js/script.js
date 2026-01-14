// Profile dropdown toggle
document.addEventListener("DOMContentLoaded", function () {
  const profileMenuBtn = document.getElementById("profileMenuBtn");
  const profileDropdown = document.getElementById("profileDropdown");
  const mobileProfileBtn = document.getElementById("mobileProfileBtn");
  // Toggle dropdown on button click
  if (profileMenuBtn && profileDropdown) {
    profileMenuBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      profileDropdown.classList.toggle("hidden");
    });
  }
  // Mobile toggle
  if (mobileProfileBtn && profileDropdown) {
    mobileProfileBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      profileDropdown.classList.toggle("hidden");
    });
  }

  // Close dropdown when clicking outside
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
});

//notifications dropdown toggle
document.addEventListener("DOMContentLoaded", function () {
  const notificationsBtn = document.getElementById("notificationsBtn");
  const notificationsDropdown = document.getElementById(
    "notificationsDropdown"
  );
  // Toggle dropdown on button click
  if (notificationsBtn && notificationsDropdown) {
    notificationsBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      notificationsDropdown.classList.toggle("hidden");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (e) {
      if (
        !notificationsBtn.contains(e.target) &&
        !notificationsDropdown.contains(e.target)
      ) {
        notificationsDropdown.classList.add("hidden");
      }
    });
  }
});

// Cart Management Functions

// Update cart item quantity
async function updateQuantity(cartId, change) {
  const quantityInput = document.querySelector(
    `.cart-quantity[data-cart-id="${cartId}"]`
  );
  if (!quantityInput) return;
  // Get current quantity
  const currentQty = parseInt(quantityInput.value);
  const newQty = currentQty + change;
  // If new quantity is less than 1, confirm removal
  if (newQty < 1) {
    if (confirm("Remove this item from cart?")) {
      await removeFromCart(cartId);
    }
    return;
  }
  // Update quantity via AJAX
  try {
    const formData = new FormData();
    formData.append("action", "update");
    formData.append("cart_id", cartId);
    formData.append("quantity", newQty);
    // Send request
    const response = await fetch("cart_handler.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      // Update UI
      quantityInput.value = newQty;
      updateCartUI(data.cart);
      showNotification("Cart updated successfully", "success");
    } else {
      showNotification(data.message || "Failed to update cart", "error");
    }
  } catch (error) {
    console.error("Error updating cart:", error);
    showNotification("An error occurred. Please try again.", "error");
  }
}

// Remove item from cart
async function removeFromCart(cartId) {
  try {
    const formData = new FormData();
    formData.append("action", "remove");
    formData.append("cart_id", cartId);
    // Send request
    const response = await fetch("cart_handler.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      // Remove item from DOM
      const cartItem = document.querySelector(
        `.cart-item[data-cart-id="${cartId}"]`
      );
      if (cartItem) {
        cartItem.style.opacity = "0";
        cartItem.style.transform = "scale(0.95)";
        setTimeout(() => {
          cartItem.remove();

          // Check if cart is empty
          const remainingItems = document.querySelectorAll(".cart-item");
          if (remainingItems.length === 0) {
            location.reload(); // Reload to show empty cart message
          } else {
            updateCartUI(data.cart);
          }
        }, 300);
      }
      //  item remove success massgae
      showNotification("Item removed from cart", "success");
    } else {
      showNotification(data.message || "Failed to remove item", "error");
    }
  } catch (error) {
    console.error("Error removing from cart:", error);
    showNotification("An error occurred. Please try again.", "error");
  }
}

// Update cart UI with new totals
function updateCartUI(cart) {
  if (!cart) return;

  // Update counts
  const cartCount = document.getElementById("cartCount");
  const itemCount = document.getElementById("itemCount");
  if (cartCount) cartCount.textContent = cart.item_count;
  if (itemCount) itemCount.textContent = cart.item_count;

  // Update amounts
  const subtotalDisplay = document.getElementById("subtotalDisplay");
  const taxDisplay = document.getElementById("taxDisplay");
  const shippingDisplay = document.getElementById("shippingDisplay");
  const totalDisplay = document.getElementById("totalDisplay");
  // Update amounts
  if (subtotalDisplay)
    subtotalDisplay.textContent = `Rs ${formatCurrency(cart.subtotal)}`;
  if (taxDisplay) taxDisplay.textContent = `Rs ${formatCurrency(cart.tax)}`;
  if (shippingDisplay)
    shippingDisplay.textContent = `Rs ${formatCurrency(cart.shipping)}`;
  if (totalDisplay)
    totalDisplay.textContent = `Rs ${formatCurrency(cart.total)}`;

  // Update individual item totals
  cart.items.forEach((item) => {
    const itemTotalEl = document.querySelector(
      `.cart-item[data-cart-id="${item.cart_id}"] .item-total`
    );
    if (itemTotalEl) {
      itemTotalEl.textContent = `Rs ${formatCurrency(item.item_total)}`;
    }
  });
}

// Format currency
function formatCurrency(amount) {
  return parseFloat(amount).toLocaleString("en-LK", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

// Show notification
function showNotification(message, type = "info") {
  // Create notification element
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

  // Animate in
  setTimeout(() => {
    notification.style.opacity = "1";
    notification.style.transform = "translateY(0)";
  }, 10);

  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.opacity = "0";
    notification.style.transform = "translateY(-20px)";
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}
