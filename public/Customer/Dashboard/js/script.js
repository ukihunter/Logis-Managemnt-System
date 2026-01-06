// Profile dropdown toggle
document.addEventListener("DOMContentLoaded", function () {
  const profileMenuBtn = document.getElementById("profileMenuBtn");
  const profileDropdown = document.getElementById("profileDropdown");

  if (profileMenuBtn && profileDropdown) {
    profileMenuBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      profileDropdown.classList.toggle("hidden");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (e) {
      if (
        !profileMenuBtn.contains(e.target) &&
        !profileDropdown.contains(e.target)
      ) {
        profileDropdown.classList.add("hidden");
      }
    });
  }
});

//notifications dropdown toggle
document.addEventListener("DOMContentLoaded", function () {
  const notificationsBtn = document.getElementById("notificationsBtn");
  const notificationsDropdown = document.getElementById(
    "notificationsDropdown"
  );
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
