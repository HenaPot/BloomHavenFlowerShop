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
        console.log(item);
      const imageUrl = (item.images && item.images.length > 0)
        ? 'backend/' + item.images[0].image
        : 'frontend/assets/images/kvalitetno_cvijece.webp';

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
                <button class="btn btn-outline-danger w-100 mb-2 remove-from-wishlist-btn" data-product-id="${item.product_id}">
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
    WishlistService.attachActionEvents();

    const clearBtn = document.getElementById("clearWishlistBtn");
    if (clearBtn) {
      clearBtn.onclick = function () {
        WishlistService.clearWishlist();
      };
    }
  },

  attachQuantityEvents: function () {
    document.querySelectorAll('.quantity-input').forEach(input => {
      input.addEventListener('change', function () {
        if (parseInt(this.value) < 1) this.value = 1;
      });
    });
  },

  attachActionEvents: function () {
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
      button.addEventListener('click', function () {
        const productId = this.getAttribute('data-product-id');
        // Implement add to cart logic here
        toastr.success("Added to cart!");
      });
    });

    document.querySelectorAll('.remove-from-wishlist-btn').forEach(button => {
      button.addEventListener('click', function () {
        const productId = this.getAttribute('data-product-id');
        // Implement remove from wishlist logic here
        toastr.info("Removed from wishlist!");
      });
    });
  },
  
  clearWishlist: function () {
    if (!confirm("Are you sure you want to clear your wishlist?")) return;

    RestClient.delete("wishlist/clear", {}, function () {
      toastr.success("Wishlist cleared successfully.");
      WishlistService.getWishlist(); // refresh after clear
    }, function () {
      toastr.error("Failed to clear wishlist.");
    });
  }
};