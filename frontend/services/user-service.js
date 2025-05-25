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
    window.location.replace("login.html");
  }
};
