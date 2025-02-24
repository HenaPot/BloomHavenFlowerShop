display_user_profile = function (user_id) {
    RestClient.get("users/" + user_id, function (user) {
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

  // app.route({
  //   view: "login",
  //   load: "login.html",
  //   onCreate: function () {},
  //   onReady: function () {},
  // });
  
  // loginForm = function () {

  // };