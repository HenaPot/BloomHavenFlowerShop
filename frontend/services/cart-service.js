var CartService = {
  data: [],

  getCart: function () {
    RestClient.get("cart", function (cartItems) {
      CartService.data = cartItems;
      CartService.renderCart(cartItems);
    }, function (xhr, status, error) {
      toastr.error("Failed to load cart.");
      console.error(error);
    });
  },

  renderCart: function (items) {
    const container = document.getElementById("cartItems");
    container.innerHTML = "";

    if (!items || items.length === 0) {
      container.innerHTML = `<div class="text-center">Your cart is empty.</div>`;
      return;
    }

    items.forEach(item => {
      let rawImageUrl = (item.images && item.images.length > 0)
        ? item.images[0].image
        : null;

      if (rawImageUrl && rawImageUrl.startsWith("https//")) {
        rawImageUrl = rawImageUrl.replace("https//", "https://");
      }

      const imageUrl = rawImageUrl || 'assets/images/kvalitetno_cvijece.webp';

      const html = `
        <div class="card mb-3 border-success bg-white">
          <div class="card-body">
            <div class="row align-items-center g-2">
              <div class="col-4 col-md-2 d-flex justify-content-center">
                <img src="${imageUrl}" class="img-fluid rounded" style="width: 90px; height: 90px; object-fit: cover;" alt="${item.name}">
              </div>
              <div class="col-8 col-md-5">
                <h5 class="card-title mb-1">${item.name}</h5>
                <div class="mb-2">
                  <span class="fw-bold">$${item.price}</span>
                </div>
                <p class="small mb-0">${item.description || "No description"}</p>
              </div>
              <div class="col-6 col-md-2 d-flex flex-column justify-content-center align-items-center">
                <label class="form-label d-none d-md-block">Quantity</label>
                <div class="input-group">
                  <input type="number" class="form-control text-center quantity-input" value="${item.cart_quantity}" min="1" data-product-id="${item.product_id}">
                </div>
              </div>
              <div class="col-6 col-md-3 d-flex flex-column justify-content-center align-items-center">
                <button class="btn btn-outline-danger w-100 mb-2 remove-item" data-product-id="${item.product_id}">
                  <i class="fas fa-trash-alt"></i> Remove
                </button>
              </div>
            </div>
          </div>
        </div>`;
      container.innerHTML += html;
    });

    CartService.attachEvents();

    // const checkoutBtn = document.getElementById("checkoutBtn");
    // if (checkoutBtn) {
    //   checkoutBtn.onclick = function () {
    //     const form = document.getElementById("purchase_form");
    //     if (!form) return toastr.error("Checkout form not found.");

    //     const data = {
    //       name: form.name.value,
    //       surname: form.surname.value,
    //       address: form.address.value,
    //       city: form.city.value,
    //       country: form.country.value,
    //       phone_number: form.phone_number.value
    //     };

    //     RestClient.post("order/add", data, function (orderResponse) {
    //       toastr.success(orderResponse.message || "Purchase made successfully!");
    //       CartService.clearCart();
    //     }, function () {
    //       toastr.error("Failed to create order.");
    //     });
    //   };
    // }

    const clearBtn = document.getElementById("clearCartBtn");
    if (clearBtn) {
      clearBtn.onclick = function () {
        CartService.clearCart();
      };
    }

    CartService.loadSummary();
  },

  attachEvents: function () {
    document.querySelectorAll('.quantity-input').forEach(input => {
      input.addEventListener('change', function () {
        let newQuantity = parseInt(this.value);
        if (isNaN(newQuantity) || newQuantity < 1) {
          this.value = 1;
          toastr.warning("Quantity must be a positive number.");
          newQuantity = 1;
        }
        this.value = Math.floor(newQuantity); // Remove decimals if any
        CartService.updateQuantity(this.getAttribute('data-product-id'), this.value);
      });
      input.addEventListener('input', function () {
        // Prevent non-numeric input
        this.value = this.value.replace(/[^0-9]/g, '');
      });
    });

    document.querySelectorAll('.remove-item').forEach(button => {
      button.addEventListener('click', function () {
        const productId = this.getAttribute('data-product-id');
        CartService.removeFromCart(productId);
      });
    });
  },

  updateQuantity: function (productId, newQuantity) {
    RestClient.put("cart/update", {
      product_id: parseInt(productId),
      quantity: parseInt(newQuantity)
    }, function () {
      toastr.success("Quantity updated.");
      CartService.getCart();
    }, function () {
      toastr.error("Failed to update cart.");
    });
  },

  removeFromCart: function (productId) {
    RestClient.delete(`cart/remove/${productId}`, {}, function () {
      toastr.success("Item removed.");
      CartService.getCart();
      CartService.loadSummary();
    }, function () {
      toastr.error("Failed to remove item.");
    });
  },

  loadSummary: function () {
    RestClient.get("cart/summary", function (summary) {
      document.getElementById("cart-total-value").textContent = summary.total_value || 0;
      document.getElementById("cart-total-count").textContent = summary.total_count || 0;
    }, function () {
      document.getElementById("cart-total-value").textContent = 0;
      document.getElementById("cart-total-count").textContent = 0;
    });
  },

  clearCart: function () {
    if (!confirm("Are you sure you want to clear your cart?")) return;
    RestClient.delete("cart/clear", {}, function () {
      toastr.success("Cart cleared.");
      CartService.getCart();
      CartService.loadSummary();
    }, function () {
      toastr.error("Failed to clear cart.");
    });
  },

  initPurchaseFormValidation: function () {
    if (typeof $.validator !== "undefined" && !$.validator.methods.phonePlusDigits) {
      $.validator.addMethod("phonePlusDigits", function(value, element) {
        return this.optional(element) || /^\+\d+$/.test(value);
      }, "Phone number must start with '+' and contain only digits after it.");
    }

    FormValidation.validate(
      "#purchase_form",
      {
        name: "required",
        surname: "required",
        address: "required",
        city: "required",
        country: "required",
        phone_number: {
          required: true,
          minlength: 6,
          phonePlusDigits: true
        }
      },
      {
        name: "Please enter your name.",
        surname: "Please enter your surname.",
        address: "Please enter your address.",
        city: "Please enter your city.",
        country: "Please enter your country.",
        phone_number: {
          required: "Please enter your phone number.",
          minlength: "Phone number must be at least 6 digits.",
          phonePlusDigits: "Phone number must start with '+' and contain only digits after it."
        }
      },
      function (data) {
        // This is called only if the form is valid!
        RestClient.post("order/add", data, function (orderResponse) {
          toastr.success(orderResponse.message || "Purchase made successfully!");
          CartService.clearCart();
        }, function () {
          toastr.error("Failed to create order.");
        });
      }
    );
  }
};

const clearBtn = document.getElementById("clearCartBtn");
if (clearBtn) {
  clearBtn.onclick = function () {
    CartService.clearCart();
  };
}