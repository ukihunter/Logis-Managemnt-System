// Profile dropdown toggle
document.addEventListener("DOMContentLoaded", function () {
  const profileMenuBtn = document.getElementById("profileMenuBtn");
  const profileDropdown = document.getElementById("profileDropdown");
  const mobileProfileBtn = document.getElementById("mobileProfileBtn");
  // Desktop profile button
  if (profileMenuBtn && profileDropdown) {
    profileMenuBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      profileDropdown.classList.toggle("hidden");
    });
  }
  // Mobile profile button
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
  // Notifications button
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
