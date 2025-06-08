document.addEventListener("DOMContentLoaded", function () {
  function updateNavbar() {
    const hash = window.location.hash;
    const isAuthPage =
      hash === "#login" || hash === "#signup" || hash === "#landing";

    document
      .getElementById("navbarLinks")
      .classList.toggle("d-none", isAuthPage);
    document
      .getElementById("authButtons")
      .classList.toggle("d-none", !isAuthPage);
    document
      .getElementById("navbarSearchForm")
      .classList.toggle("d-none", isAuthPage);
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
    navLinks.forEach((link) => {
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

function displaySelectedImage(event, elementId) {
  const selectedImage = document.getElementById(elementId);
  const fileInput = event.target;

  if (fileInput.files && fileInput.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      selectedImage.src = e.target.result;
    };

    reader.readAsDataURL(fileInput.files[0]);
  }
}

$(document).on('change', '.order-status-dropdown', function () {
  const orderId = $(this).data('order-id');
  const newStatusId = $(this).val();
  OrderService.updateOrderStatus(orderId, newStatusId);
});

$(document).off('click', '.delete-order-btn').on('click', '.delete-order-btn', function () {
  const orderId = $(this).data('order-id');
  OrderService.openDeleteConfirmationDialog(orderId);
});
