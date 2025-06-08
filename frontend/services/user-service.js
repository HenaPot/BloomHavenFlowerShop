var UserService = {
  init: function () {
    const token = localStorage.getItem("token");

    FormValidation.validate(
      "#login-form",
      {
        email: {
          required: true,
          email: true,
        },
        password: {
          required: true,
          minlength: 3,
          maxlength: 10,
        },
      },
      {
        email: {
          required: "Please enter your email address.",
          email: "Please enter a valid email address.",
        },
        password: {
          required: "Please provide a password.",
          minlength: "Password must be at least 3 characters long.",
          maxlength: "Password cannot exceed 10 characters.",
        },
      },
      UserService.login
    );

    // Signup form validation
    FormValidation.validate(
      "#signup-form",
      {
        username: "required",
        date_of_birth: "required",
        name: "required",
        email: {
          required: true,
          email: true,
        },
        address: {
          required: true,
        },
        password: {
          required: true,
          minlength: 3,
          maxlength: 10,
        },
        repeat_password_signup: {
          required: true,
          equalTo: "#password",
        },
      },
      {
        username: "Please enter your username.",
        date_of_birth: "Please enter your date of birth.",
        name: "Please enter your full name.",
        email: {
          required: "Please enter your email address.",
          email: "Please enter a valid email address.",
        },
        address: {
          required: "Please enter your address.",
        },
        password: {
          required: "Please provide a password.",
          minlength: "Password must be at least 3 characters long.",
          maxlength: "Password cannot exceed 10 characters.",
        },
        repeat_password_signup: {
          required: "Please repeat your password.",
          equalTo: "Passwords do not match. Please try again.",
        },
      },
      UserService.signup
    );

    FormValidation.validate(
      "#edit_profile_form",
      {
        edit_username: "required",
        edit_name: "required",
        edit_email: {
          required: true,
          email: true,
        },
        edit_date_of_birth: "required",
        edit_address: "required",
      },
      {
        edit_username: "Please enter your username.",
        edit_name: "Please enter your full name.",
        edit_email: {
          required: "Please enter your email address.",
          email: "Please enter a valid email address.",
        },
        edit_date_of_birth: "Please enter your date of birth.",
        edit_address: "Please enter your address.",
      },
      UserService.editProfile
    );
  },

  login: function (data) {
    Utils.block_ui("#login-form");

    RestClient.post(
      "auth/login",
      data,
      function (response) {
        localStorage.setItem("token", response.token);
        localStorage.setItem("user_id", response.id);
        localStorage.setItem("user", JSON.stringify(response));

        toastr.success("You logged in successfully.");

        if (response.role_id == 2) {
          window.location.hash = "#profile";
        } else {
          window.location.hash = "#admin_dashboard";
        }

        Utils.unblock_ui("#login-form");
      },
      function (error) {
        Utils.unblock_ui("#login-form");
        toastr.error("Error occurred while logging into your account.");
      }
    );
  },

  signup: function (data) {
    Utils.block_ui("#signup-form");

    RestClient.post(
        "auth/register",
        data,
        function (response) {
        const loginData = {
            email: data.email,
            password: data.password
        };

        UserService.login(loginData);
        },
        function (xhr) {
        Utils.unblock_ui("#signup-form");
        toastr.error("Sorry, something went wrong during registration.");
        }
    );
  },

  logout: function () {
    localStorage.clear();
    window.location.replace("#landing");
  },

  deleteAccount: function () {
  const userId = localStorage.getItem("user_id");

  if (!userId) {
    toastr.error("User ID not found.");
    return;
  }

  if (!confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
    return;
  }

  Utils.block_ui("#profile");

  RestClient.delete(
    `users/delete/${userId}`,
    {}, // No body is needed for DELETE
    function (response) {
      toastr.success("Your account has been deleted.");
      UserService.logout();
    },
    function (error) {
      toastr.error("Error deleting account.");
    });

    Utils.unblock_ui("#profile");
  },

  getUserData: function () {
    RestClient.get(
    "users/current",
    function (response) {
      // Update Profile Picture (Use default if null)
      let profileImg = document.querySelector("#profile img");
      profileImg.src = response.image
        ? Constants.get_api_base_url() + response.image
        : "assets/images/kvalitetno_cvijece.webp";

      // Update Profile Information in the card
      document.querySelector("#profile h5").textContent =
        response.name || "N/A";
      document.querySelector("#profile p.text-muted.mb-4").textContent =
        response.role_id === "2" ? "Customer" : "Administrator";

      // Update Detailed Profile Information
      let profileFields = document.querySelectorAll("#profile .col-sm-9 p");
      profileFields[0].textContent = response.name || "N/A"; // Full Name
      profileFields[1].textContent = response.username || "N/A"; // Username
      profileFields[2].textContent = response.email || "N/A"; // Email
      profileFields[3].textContent = response.date_of_birth || "N/A"; // Date of Birth
      profileFields[4].textContent = response.address || "N/A"; // Address

      // Update Edit Modal Form Fields
      document.querySelector("#edit_name").value = response.name || "";
      document.querySelector("#edit_username").value = response.username || "";
      document.querySelector("#edit_email").value = response.email || "";
      document.querySelector("#edit_date_of_birth").value =
        response.date_of_birth || "";
      document.querySelector("#edit_address").value = response.address || "";
    },
    function (error) {
      console.error("Error fetching user data:", error);
    });
  }, 

  editProfile: function () {
  const form = document.getElementById("edit_profile_form");
  const formData = new FormData(form);


  const data = {
    username: formData.get("edit_username"),
    name: formData.get("edit_name"),
    email: formData.get("edit_email"),
    date_of_birth: formData.get("edit_date_of_birth"),
    address: formData.get("edit_address")
  };


  RestClient.put(
    "users/update",
    data,
    function (response) {      
      const fileInput = document.getElementById("profile_picture");
      const imageFile = fileInput.files[0];

      if (imageFile) {
  const imageFormData = new FormData();
  imageFormData.append("profile_picture", imageFile);

  RestClient.uploadFile(
    "users/upload_image",
    imageFormData,
    function (imgResponse) {
      toastr.success("Profile and image updated successfully.");
      $("#edit_modal").modal("hide");
      UserService.getUserData();
    },
    function () {
      toastr.error("Profile updated, but image upload failed.");
    }
  );
} else {
  toastr.success("Profile updated successfully.");
  $("#edit_modal").modal("hide");
  UserService.getUserData();
}
    },
    function (xhr) {
      const response = xhr.responseJSON || {};
      const msg = response.message || "Something went wrong while updating your profile.";
      toastr.error(msg);
    });
  },

  updateDashboardLink: function () {
    const user = JSON.parse(localStorage.getItem("user"));
    const dashboardLink = document.querySelector("#nav-dashboard");
    if (!dashboardLink) return;

    if (user && user.role_id == 1) {
      dashboardLink.setAttribute("href", "#admin_dashboard");
      dashboardLink.innerHTML = `
        <i class="fa-solid fa-screwdriver-wrench fa-lg my-2"></i>
        <span class="small">Admin Dashboard</span>
      `;
    } else {
      dashboardLink.setAttribute("href", "#dashboard");
      dashboardLink.innerHTML = `
        <i class="fa-solid fa-house fa-lg my-2"></i>
        <span class="small">Dashboard</span>
      `;
    }
  },
};