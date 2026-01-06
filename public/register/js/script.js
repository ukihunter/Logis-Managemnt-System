// Registration form handling
document.addEventListener("DOMContentLoaded", function () {
  const registerForm = document.getElementById("registerForm");
  const registerBtn = document.getElementById("registerBtn");
  const registerBtnText = document.getElementById("registerBtnText");
  const registerBtnLoader = document.getElementById("registerBtnLoader");
  const errorMessage = document.getElementById("errorMessage");
  const successMessage = document.getElementById("successMessage");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirm_password");
  const togglePassword = document.getElementById("togglePassword");
  const toggleConfirmPassword = document.getElementById(
    "toggleConfirmPassword"
  );

  // Password toggle for password field
  if (togglePassword && passwordInput) {
    togglePassword.addEventListener("click", () => {
      const icon = togglePassword.querySelector("span");
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.textContent = "visibility_off";
      } else {
        passwordInput.type = "password";
        icon.textContent = "visibility";
      }
    });
  }

  // Password toggle for confirm password field
  if (toggleConfirmPassword && confirmPasswordInput) {
    toggleConfirmPassword.addEventListener("click", () => {
      const icon = toggleConfirmPassword.querySelector("span");
      if (confirmPasswordInput.type === "password") {
        confirmPasswordInput.type = "text";
        icon.textContent = "visibility_off";
      } else {
        confirmPasswordInput.type = "password";
        icon.textContent = "visibility";
      }
    });
  }

  // Form submission
  if (registerForm) {
    registerForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      // Hide messages
      hideMessage(errorMessage);
      hideMessage(successMessage);

      // Client-side validation
      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;

      if (password !== confirmPassword) {
        showMessage(errorMessage, "Passwords do not match");
        return;
      }

      if (password.length < 6) {
        showMessage(
          errorMessage,
          "Password must be at least 6 characters long"
        );
        return;
      }

      // Show loading state
      registerBtn.disabled = true;
      registerBtnText.classList.add("hidden");
      registerBtnLoader.classList.remove("hidden");

      // Get form data
      const formData = new FormData(registerForm);

      try {
        const response = await fetch("register_handler.php", {
          method: "POST",
          body: formData,
        });

        const data = await response.json();

        if (data.success) {
          showMessage(successMessage, data.message);
          registerForm.reset();
          // Redirect after 1.5 seconds
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 1500);
        } else {
          showMessage(errorMessage, data.message);
          // Reset button state
          registerBtn.disabled = false;
          registerBtnText.classList.remove("hidden");
          registerBtnLoader.classList.add("hidden");
        }
      } catch (error) {
        showMessage(errorMessage, "An error occurred. Please try again.");
        // Reset button state
        registerBtn.disabled = false;
        registerBtnText.classList.remove("hidden");
        registerBtnLoader.classList.add("hidden");
      }
    });
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
