display_user_profile = function () {
    RestClient.get("users/current", function (user) {
      const userContainer = document.getElementById("profile-page");
      userContainer.innerHTML = `
      <div
      class="col-md-4 gradient-custom text-center text-white"
      style="
        border-top-left-radius: 0.5rem;
        border-bottom-left-radius: 0.5rem;
      "
    >
      <img
        src="/../../frontend/assets/img/user-image.jpeg"
        alt="Avatar"
        class="img-fluid my-4"
        style="width: 80px"
      />
      <h5 style="color: #0d6efd">${user.first_name} ${user.last_name}</h5>
    </div>
    <div class="col-md-8">
      <div class="card-body p-4">
        <h6>Information</h6>
        <hr class="mt-0 mb-4" />
        <div class="row pt-1">
          <h6>Email</h6>
          <p class="text-muted">${user.email}</p>
        </div>
      </div>
    </div>
      `;
    });
  };

document.addEventListener("DOMContentLoaded", function () {
  function updateNavbar() {
      const hash = window.location.hash;
      const isAuthPage = hash === "#login" || hash === "#signup" || hash === "#landing";

      document.getElementById("navbarLinks").classList.toggle("d-none", isAuthPage);
      document.getElementById("authButtons").classList.toggle("d-none", !isAuthPage);
      document.getElementById("navbarSearchForm").classList.toggle("d-none", isAuthPage);
  }

  // Run on page load
  updateNavbar();

  // Run when navigation occurs
  window.addEventListener("hashchange", updateNavbar);

  // Logout functionality
  document.getElementById("logoutBtn").addEventListener("click", function () {
      localStorage.clear(); // Clears stored data
      window.location.hash = "#landing"; // Redirect to landing page
      updateNavbar(); // Update UI immediately
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const navLinks = document.querySelectorAll(".nav-link");

  function updateActiveLink() {
      const currentPage = window.location.hash || "#dashboard"; // Default to dashboard
      navLinks.forEach(link => {
          link.classList.remove("active");
          if (link.getAttribute("href") === currentPage) {
              link.classList.add("active");
          }
      });
  }

  // Update active link when page loads
  updateActiveLink();

  // Listen for hash changes (SPA navigation)
  window.addEventListener("hashchange", updateActiveLink);
});
