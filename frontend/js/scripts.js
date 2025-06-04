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

document.addEventListener("DOMContentLoaded", function () {
  const saveButton = document.querySelector(
    "#edit_profile_form button.btn-success"
  );
  if (saveButton) {
    saveButton.addEventListener("click", function () {
      const formData = new FormData();
      const imageInput = document.querySelector("#profile_picture");

      if (imageInput.files.length > 0) {
        formData.append("profile_picture", imageInput.files[0]);

        fetch("http://localhost/web_project/backend/rest/users/upload_image", {
          method: "POST",
          headers: {
            Authorization: "Bearer " + localStorage.getItem("token"),
          },
          body: formData,
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.status === "success") {
              // Update user image visually
              document.querySelector("#profile img").src = data.image_url;
              toastr.success("Profile picture updated!");
              display_user_profile(); // Refresh user info
            } else {
              toastr.error("Image upload failed.");
            }
          })
          .catch((err) => {
            console.error("Upload error:", err);
            toastr.error("Something went wrong while uploading image.");
          });
      } else {
        toastr.warning("Please choose an image file first.");
      }
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const user = JSON.parse(localStorage.getItem("user"));

  if (user && user.role_id == 1) {
    const dashboardLink = document.querySelector("#nav-dashboard");
    if (dashboardLink) {
      dashboardLink.setAttribute("href", "#admin_dashboard");
      dashboardLink.innerHTML = `
        <i class="fa-solid fa-screwdriver-wrench fa-lg my-2"></i>
        <span class="small">Admin Dashboard</span>
      `;
    }
  }
});

$(document).on('change', '.order-status-dropdown', function () {
  const orderId = $(this).data('order-id');
  const newStatusId = $(this).val();
  OrderService.updateOrderStatus(orderId, newStatusId);
});

$(document).off('click', '.delete-order-btn').on('click', '.delete-order-btn', function () {
  const orderId = $(this).data('order-id');
  OrderService.openDeleteConfirmationDialog(orderId);
});
