// Login form handling
document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const loginBtn = document.getElementById("loginBtn");
  const loginBtnText = document.getElementById("loginBtnText");
  const loginBtnLoader = document.getElementById("loginBtnLoader");
  const errorMessage = document.getElementById("errorMessage");
  const successMessage = document.getElementById("successMessage");
  const passwordInput = document.getElementById("password");
  const toggleBtn = document.querySelector('button[type="button"]');
  const toggleIcon = toggleBtn?.querySelector("span");

  // Password toggle logic
  if (toggleBtn && passwordInput && toggleIcon) {
    toggleBtn.addEventListener("click", () => {
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.textContent = "visibility_off";
      } else {
        passwordInput.type = "password";
        toggleIcon.textContent = "visibility";
      }
    });
  }

  // Form submission
  if (loginForm) {
    loginForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      // Hide messages
      hideMessage(errorMessage);
      hideMessage(successMessage);

      // Show loading state
      loginBtn.disabled = true;
      loginBtnText.classList.add("hidden");
      loginBtnLoader.classList.remove("hidden");

      // Get form data
      const formData = new FormData(loginForm);

      try {
        const response = await fetch("login_handler.php", {
          method: "POST",
          body: formData,
        });

        const data = await response.json();

        if (data.success) {
          showMessage(successMessage, data.message);
          // Redirect after 1 second
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 1000);
        } else {
          showMessage(errorMessage, data.message);
          // Reset button state
          loginBtn.disabled = false;
          loginBtnText.classList.remove("hidden");
          loginBtnLoader.classList.add("hidden");
        }
      } catch (error) {
        showMessage(errorMessage, "An error occurred. Please try again.");
        // Reset button state
        loginBtn.disabled = false;
        loginBtnText.classList.remove("hidden");
        loginBtnLoader.classList.add("hidden");
      }
    });
  }

  // Update copyright year
  const yearSpan = document.getElementById("year");
  if (yearSpan) {
    yearSpan.textContent = new Date().getFullYear();
  }
});

function showMessage(element, message) {
  const p = element.querySelector("p");
  p.textContent = message;
  element.classList.remove("hidden");
}

function hideMessage(element) {
  element.classList.add("hidden");
}
