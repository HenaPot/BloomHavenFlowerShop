var WishlistService = {
  data: [],

  getWishlist: function () {
    RestClient.get("wishlist", function (wishlistData) {
      WishlistService.data = wishlistData;
      WishlistService.renderWishlist(wishlistData);
    }, function (xhr, status, error) {
      toastr.error("Failed to load wishlist.");
      console.error(error);
    });
  },

  renderWishlist: function (items) {
    const container = document.getElementById("wishlistItems");
    container.innerHTML = "";

    if (!items || items.length === 0) {
      container.innerHTML = `<div class="text-muted text-center">Your wishlist is empty.</div>`;
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
        <div class="card mb-3 border-success">
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
              </div>
              <div class="col-6 col-md-2 d-flex flex-column justify-content-center align-items-center">
                <label class="form-label d-none d-md-block">Quantity</label>
                <div class="input-group">
                  <input type="number" class="form-control text-center quantity-input" value="${item.cart_quantity || 1}" min="1" data-product-id="${item.product_id}">
                </div>
              </div>
              <div class="col-6 col-md-3 d-flex flex-column justify-content-center align-items-center">
                <button class="btn btn-success mb-2 w-100 add-to-cart-btn" data-product-id="${item.product_id}">
                  <i class="bi bi-cart-plus"></i> Add to Cart
                </button>
                <button class="btn btn-outline-danger w-100 mb-2 remove-from-wishlist-btn" onclick="WishlistService.removeItemFromWishlist(${item.product_id})">
                  <i class="bi bi-trash"></i> Remove
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
      container.innerHTML += html;
    });

    WishlistService.attachQuantityEvents();
    const clearBtn = document.getElementById("clearWishlistBtn");
    if (clearBtn) {
      clearBtn.onclick = function () {
        WishlistService.clearWishlist();
      };
    }
    const addAllBtn = document.getElementById("addAllToCartBtn");
    if (addAllBtn) {
      addAllBtn.onclick = function () {
        WishlistService.addAllToCart();
        WishlistService.clearWishlist();
      };
    }
    WishlistService.loadSummary();

    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
      button.addEventListener('click', function () {
        const productId = this.getAttribute('data-product-id');
        const item = WishlistService.data.find(i => i.product_id == productId);
        const quantity = item && item.cart_quantity ? item.cart_quantity : 1;

        RestClient.post("cart/add", {
          product_id: productId,
          quantity: quantity
        }, function () {
          toastr.success("Added to cart!");
          WishlistService.removeItemFromWishlist(productId);
        }, function () {
          toastr.error("Failed to add to cart.");
        });
      });
    });
  },

  attachQuantityEvents: function () {
    document.querySelectorAll('.quantity-input').forEach(input => {
      input.addEventListener('change', function () {
        let newQuantity = parseInt(this.value);
        if (isNaN(newQuantity) || newQuantity < 1) {
          this.value = 1;
          toastr.warning("Quantity must be a positive number.");
          newQuantity = 1;
        }
        this.value = Math.floor(newQuantity);
        const productId = this.getAttribute('data-product-id');
        WishlistService.updateQuantity(productId, this.value);
      });
      input.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
      });
    });
  },

  clearWishlist: function () {
    if (!confirm("Are you sure you want to clear your wishlist?")) return;

    RestClient.delete("wishlist/clear", {}, function () {
      toastr.success("Wishlist cleared successfully.");
      WishlistService.getWishlist();
      WishlistService.loadSummary(); 
    }, function () {
      toastr.error("Failed to clear wishlist.");
    });
  },

  removeItemFromWishlist: function (productId) {
    if (!productId) return;

    if (!confirm("Remove this item from your wishlist?")) return;

    RestClient.delete(`wishlist/remove/${productId}`, {}, function () {
      toastr.success("Item removed from wishlist.");
      WishlistService.getWishlist();
      WishlistService.loadSummary(); // <-- Add this line
    }, function () {
      toastr.error("Failed to remove item.");
    });
  },

  addToWishlist: function (productId, quantity = 1) {
    if (!productId) {
      toastr.error("No product selected.");
      return;
    }

    RestClient.post("wishlist/add", { product_id: productId, quantity: quantity }, function () {
      toastr.success("Product added to wishlist.");
    }, function () {
      toastr.error("Failed to add product to wishlist.");
    });
  },

  updateQuantity: function (productId, newQuantity) {
    if (!productId || isNaN(newQuantity) || newQuantity < 1) {
      toastr.error("Invalid quantity.");
      return;
    }

    RestClient.put("wishlist/update", {
      product_id: parseInt(productId),
      quantity: parseInt(newQuantity)
    }, function () {
      toastr.success("Quantity updated.");
      WishlistService.getWishlist(); // Refresh total and values
    }, function () {
      toastr.error("Failed to update quantity.");
    });
  },

  loadSummary: function () {
    RestClient.get("wishlist/summary", function (summary) {
      document.getElementById("wishlist-total-value").textContent = summary.total_value || 0;
      document.getElementById("wishlist-total-count").textContent = summary.total_count || 0;
    }, function () {
      document.getElementById("wishlist-total-value").textContent = 0;
      document.getElementById("wishlist-total-count").textContent = 0;
    });
  },

  addAllToCart: function () {
    // Add all wishlist items to cart
    WishlistService.data.forEach(item => {
      RestClient.post("cart/add", {
        product_id: item.product_id,
        quantity: item.cart_quantity || 1
      }, function () {
        // Optionally show a success message for each item
      }, function () {
        toastr.error(`Failed to add ${item.name} to cart.`);
      });
    });
    toastr.success("All wishlist items added to cart!");
  },
};
