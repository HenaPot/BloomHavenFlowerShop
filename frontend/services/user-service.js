var UserService = {
  init: function () {
    const token = localStorage.getItem("token");
    if (token) {
      // Redirect if already logged in
      window.location.hash = "#profile"; // or "#admin_dashboard" based on role if needed
    }

    // Attach validation and login handler
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
      AuthService.login
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

  logout: function () {
    localStorage.clear();
    window.location.replace("login.html");
  },

  generateMenuItems: function () {
    const token = localStorage.getItem("token");
    if (!token) {
      window.location.replace("login.html");
      return;
    }

    const user = Utils.parseJwt(token); // You may need to implement `parseJwt`

    if (!user || !user.role_id) {
      window.location.replace("login.html");
      return;
    }

    let nav = "";
    let main = "";

    if (user.role_id == 2) {
      // Regular user
      nav += `
        <li><a href="#profile">Profile</a></li>
        <li><button onclick="AuthService.logout()" class="btn btn-sm btn-primary">Logout</button></li>
      `;
      main += `
        <section id="profile" data-load="profile.html"></section>
      `;
    } else {
      // Admin
      nav += `
        <li><a href="#admin_dashboard">Dashboard</a></li>
        <li><a href="#users">Users</a></li>
        <li><button onclick="AuthService.logout()" class="btn btn-sm btn-primary">Logout</button></li>
      `;
      main += `
        <section id="admin_dashboard" data-load="admin_dashboard.html"></section>
        <section id="users" data-load="users.html"></section>
      `;
    }

    $("#tabs").html(nav);
    $("#spapp").html(main);
  },
};
