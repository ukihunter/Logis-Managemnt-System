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

// Map placeholder click to load Google Map

document.getElementById("mapPlaceholder").addEventListener("click", () => {
  document.getElementById("mapPlaceholder").classList.add("hidden");
  document.getElementById("googleMap").classList.remove("hidden");
});

// Check local storage or system preference
if (
  localStorage.theme === "dark" ||
  (!("theme" in localStorage) &&
    window.matchMedia("(prefers-color-scheme: dark)").matches)
) {
  document.documentElement.classList.add("white");
} else {
  document.documentElement.classList.remove("dark");
}
