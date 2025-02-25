var RestClient = {
  get: function (url, callback, error_callback) {
    let token = localStorage.getItem("token");    
    $.ajax({
      url: Constants.API_BASE_URL + url,
      type: "GET",
      headers: {
        Authentication: token,
      },
      success: function (response) {
        if (callback) callback(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        if (error_callback) error_callback(jqXHR);
      },
    });
  },
  request: function (url, method, data, callback, error_callback) {
    let token = localStorage.getItem("token");
    $.ajax({
      url: Constants.API_BASE_URL + url,
      type: method,
      headers: {
        Authentication: token,
      },
      data: data,
    })
      .done(function (response, status, jqXHR) {
        if (callback) callback(response);
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        if (error_callback) {
          error_callback(jqXHR);
        } else {
          toastr.error(jqXHR.responseJSON.message);
        }
      });
  },
  post: function (url, data, callback, error_callback) {
    // method used for creating a new entity
    RestClient.request(url, "POST", data, callback, error_callback);
  },
  delete: function (url, data, callback, error_callback) {
    // method used for deleting an entity
    RestClient.request(url, "DELETE", data, callback, error_callback);
  },
  put: function (url, data, callback, error_callback) {
    //  method used for updating an entity
    RestClient.request(url, "PUT", data, callback, error_callback);
  },
  patch: function (url, data, callback, error_callback) {
    // method used for updating an entity
    RestClient.request(url, "PATCH", data, callback, error_callback);
  },
};