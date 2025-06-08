var ProductService = {
  init: function () {
    ProductService.initProductFormsValidation();
    ProductService.loadCategories();
    ProductService.handleNavbarSearch();
    
    $('#addItemModal').on('show.bs.modal', function () {
      const form = document.getElementById('addItemForm');
      if (form) {
        form.reset();

        const fileInput = form.querySelector('input[type="file"]');
        if (fileInput) {
          fileInput.value = '';
        }

        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
          select.selectedIndex = 0;
        });
      }
    });
    ProductService.initProductFormsValidation();
  },

  initProductFormsValidation: function () {
    FormValidation.validate(
      "#addItemForm",
      {
        name: "required",
        category_id: "required",
        quantity: {
          required: true,
          digits: true,
          min: 1
        },
        price_each: {
          required: true,
          number: true,
          min: 0.01
        }
      },
      {
        name: "Please enter the product name.",
        category_id: "Please enter the product category.",
        quantity: {
          required: "Please enter the quantity.",
          digits: "Quantity must be a whole number.",
          min: "Quantity must be at least 1."
        },
        price_each: {
          required: "Please enter the price.",
          number: "Price must be a valid number.",
          min: "Price must be at least 0.01."
        }
      },
      ProductService.addProduct
    );

    // Edit Product Form
    FormValidation.validate(
      "#editItemForm",
      {
        name: "required",
        category_id: "required",
        quantity: {
          required: true,
          digits: true,
          min: 1
        },
        price_each: {
          required: true,
          number: true,
          min: 0.01
        }
      },
      {
        name: "Please enter the product name.",
        category_id: "Please enter the product category.",
        quantity: {
          required: "Please enter the quantity.",
          digits: "Quantity must be a whole number.",
          min: "Quantity must be at least 1."
        },
        price_each: {
          required: "Please enter the price.",
          number: "Price must be a valid number.",
          min: "Price must be at least 0.01."
        }
      },
      ProductService.updateProduct
    );
  },

  addProduct: function (data) {
    ProductService.loadCategories()
    Utils.block_ui("#addItemForm");

    RestClient.post(
      "products/add",
      data,
      function (response) {
        const productId = response.id;

        const filesInput = document.getElementById("formFileMultiple");
        if (filesInput.files.length > 0) {
          let uploaded = 0;
          for (let i = 0; i < filesInput.files.length; i++) {
            const singleForm = new FormData();
            singleForm.append("product_image", filesInput.files[i]);

            RestClient.uploadFile(
              `products/upload_image/${productId}`,
              singleForm,
              function () {
                uploaded++;
                if (uploaded === filesInput.files.length) {
                  toastr.success("Product and all images uploaded.");
                  $("#addItemModal").modal("hide");
                  ProductService.getAllProducts();
                  Utils.unblock_ui("#addItemForm");
                }
              },
              function () {
                toastr.error("One or more images failed to upload.");
                Utils.unblock_ui("#addItemForm");
              }
            );
          }
        } else {
          toastr.success("Product added without images.");
          $("#addItemModal").modal("hide");
          ProductService.getAllProducts();
          Utils.unblock_ui("#addItemForm");
        }
      },
      function (error) {
        toastr.error("Failed to add product.");
        Utils.unblock_ui("#addItemForm");
      }
    );
  },
  getAllProducts : function(){
    RestClient.get("products", function(data){
        Utils.datatable('itemsTable', [
            { data: 'name', title: 'Name' },
            { data: 'category_name', title: 'Category' },
            { data: 'quantity', title: 'Quantity' },
            { data: 'price_each', title: 'Price' },
            { data: 'description', title: 'Description' },
            {
            title: 'Actions',
                render: function (data, type, row, meta) {
                    const rowStr = encodeURIComponent(JSON.stringify(row));
                    return `<div class="d-flex justify-content-center gap-2 mt-1">
                        <button class="btn btn-sm btn-success save-order" data-bs-target="#editItemModal" onclick="ProductService.openEditModal('${row.id}')">Edit</button>
                        <button class="btn btn-danger" onclick="ProductService.openDeleteConfirmationDialog(decodeURIComponent('${rowStr}'))">Delete</button>
                    </div>
                    `;
                }
            }
        ], data, 10);
    }, function (xhr, status, error) {
        console.error('Error fetching data from file:', error);
    });
  },

  getProductById: function(id, callback) {
    RestClient.get('products/' + id, function(data) {
      localStorage.setItem('selected_product', JSON.stringify(data));

      $('input[name="name"]').val(data.name);
      $('input[name="quantity"]').val(data.quantity);
      $('input[name="price_each"]').val(data.price_each);
      $('input[name="description"]').val(data.description);
      const imageContainer = document.getElementById("existingImages");
      imageContainer.innerHTML = ""; 

      if (data.images && data.images.length > 0) {
        data.images.forEach(img => {
          const imageWrapper = document.createElement("div");
          imageWrapper.classList.add("position-relative");

          const imageElement = document.createElement("img");
          imageElement.src = 'backend/' + img.image;
          imageElement.classList.add("img-thumbnail");
          imageElement.style.height = "100px";

          const deleteBtn = document.createElement("button");
          deleteBtn.innerHTML = "&times;";
          deleteBtn.classList.add("btn", "btn-sm", "btn-danger", "position-absolute", "top-0", "end-0");
          deleteBtn.onclick = function () {
            imageWrapper.remove();
          };

          imageWrapper.dataset.imageId = img.id;
          imageWrapper.appendChild(imageElement);
          imageWrapper.appendChild(deleteBtn);
          imageContainer.appendChild(imageWrapper);
        });
      }

      RestClient.get('categories/category?name=' + encodeURIComponent(data.category), function (categoryData) {
        if (categoryData && categoryData.id) {
          $('select[name="category_id"]').val(categoryData.id).trigger('change');
        } else {
          console.error('Category ID not found for category:', data.category);
        }

        if (callback) callback(); // ✅ pozovi modal tek kad sve završi
      });

    }, function(xhr, status, error) {
      console.error('Error fetching product data:', error);
    });
  },

  openEditModal: function (id) {
  Utils.block_ui("#editItemModal");

  ProductService.loadCategories().then(function () {
    ProductService.getProductById(id, function () {
      $('#editItemModal').modal('show');
      Utils.unblock_ui("#editItemModal");
    });
  });
},

loadCategories: function () {
  return new Promise(function (resolve, reject) {
    RestClient.get('categories', function (categories) {
      const categorySelect = $('select[name="category_id"]');
      categorySelect.empty(); // Clear existing options

      categories.forEach(function (category) {
        categorySelect.append(
          $('<option>', {
            value: category.id,
            text: category.name,
          })
        );
      });

      resolve(); // Sve prošlo dobro
    }, function (xhr, status, error) {
      console.error('Failed to load categories:', error);
      reject(error);
    });
  });
},

updateProduct: function () {
  const product = JSON.parse(localStorage.getItem("selected_product"));
  const productId = product.id;

  const updatedData = {
    name: $('#editItemForm input[name="name"]').val(),
    quantity: parseInt($('#editItemForm input[name="quantity"]').val()),
    price_each: parseFloat($('#editItemForm input[name="price_each"]').val()),
    description: $('#editItemForm input[name="description"]').val(),
    category_id: parseInt($('#editItemForm select[name="category_id"]').val())
  };

  Utils.block_ui("#editItemModal");

  RestClient.put(
    "products/update/" + productId,
    updatedData,
    function () {
      const existingImageIds = Array.from(document.querySelectorAll("#existingImages div"))
        .map(div => parseInt(div.dataset.imageId));

      const newImagesInput = document.getElementById("formFileMultiple1");
      const formData = new FormData();
      formData.append("existingImageIds", JSON.stringify(existingImageIds));

      if (newImagesInput.files.length > 0) {
        for (let i = 0; i < newImagesInput.files.length; i++) {
          formData.append("new_images[]", newImagesInput.files[i]);
        }
      }

      RestClient.uploadFile(
        `products/product_images/${productId}`,
        formData,
        function () {
          toastr.success("Product and images updated.");
          document.getElementById("formFileMultiple1").value = "";
          $("#editItemModal").modal("hide");
          ProductService.getAllProducts();
          Utils.unblock_ui("#editItemModal");
        },
        function () {
          toastr.error("Failed to update images.");
          Utils.unblock_ui("#editItemModal");
        }
      );
    },
    function () {
      toastr.error("Failed to update product.");
      Utils.unblock_ui("#editItemModal");
    }
  );
},

openDeleteConfirmationDialog: function (productStr) {
  try {
    const product = JSON.parse(productStr);
    ProductService.deleteProduct(product.id);
  } catch (e) {
    console.error("Invalid product data for deletion:", e);
    toastr.error("Failed to parse product data.");
  }},

  deleteProduct: function (productId) {
  if (!productId) {
    toastr.error("Product ID not provided.");
    return;
  }

  if (!confirm("Are you sure you want to delete this product? This action cannot be undone.")) {
    return;
  }

  Utils.block_ui("body"); // You can change this selector to match your UI

  RestClient.delete(
    `products/delete/${productId}`,
    {},
    function (response) {
      toastr.success("Product has been deleted successfully.");
      ProductService.getAllProducts();
    },
    function (error) {
      toastr.error("Error deleting the product.");
    }
  );
    Utils.unblock_ui("body");
  },

  loadProducts: function (filters = {}) {
    const params = new URLSearchParams(filters).toString();
    const url = params ? `products?${params}` : "products";

    RestClient.get(
      url,
      function (products) {
        const container = document.getElementById("products-list");
        container.innerHTML = "";

        if (!products.length) {
          container.innerHTML = "<div class='col-12 text-center'>No products found.</div>";
          return;
        }

        products.forEach(product => {
          const imageUrl = (product.images && product.images.length > 0)
            ? 'backend/' + product.images[0].image
            : 'frontend/assets/images/kvalitetno_cvijece.webp';

          // Render card with a data attribute for the product ID
          container.innerHTML += `
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="card h-100 product-card" data-product-id="${product.id}" style="cursor:pointer;">
                <img src="${imageUrl}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Product Image">
                <div class="card-body">
                  <h5 class="card-title mb-3">${product.name}</h5>
                  <p class="mb-1"><strong>Category:</strong> ${product.category_name}</p>
                  <p class="mb-1"><strong>Price:</strong> $${product.price_each}</p>
                  <p class="mb-1"><strong>Quantity:</strong> ${product.quantity}</p>
                  <p class="mb-1">${product.description || ""}</p>
                </div>
              </div>
            </div>
          `;
        });

        // Add click listeners to all product cards
        document.querySelectorAll('.product-card').forEach(card => {
          card.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            localStorage.setItem('selected_product_id', productId);
            window.location.hash = "#flower";
          });
        });
      },
      function () {
        document.getElementById("products-list").innerHTML =
          "<div class='col-12 text-center'>Failed to load products.</div>";
      }
    );
  },
  renderCategoryCheckboxes: function () {
    RestClient.get("categories", function (categories) {
      const container = document.getElementById("category-checkboxes");
      container.innerHTML = "";
      categories.forEach(cat => {
        container.innerHTML += `
          <div class="form-check category-item">
            <input class="form-check-input category-checkbox" type="checkbox" id="cat${cat.id}" value="${cat.id}">
            <label class="form-check-label p-2" for="cat${cat.id}">${cat.name}</label>
          </div>
        `;
      });
    });
  },

  renderProductDetails: function() {
    const productId = localStorage.getItem('selected_product_id');
    if (!productId) return;

    // Log the view
    ProductService.logProductView(productId);

    RestClient.get('products/' + productId, function(product) {
      document.getElementById('flower-name').textContent = product.name;
      document.getElementById('flower-category').textContent = product.category;
      document.getElementById('flower-price').textContent = "$" + product.price_each;
      document.getElementById('flower-description').textContent = product.description;
      document.getElementById('flower-quantity-input').value = 1;

      // Images
      const mainImage = document.getElementById('flower-main-image');
      const thumbnails = document.getElementById('flower-thumbnails');
      thumbnails.innerHTML = "";

      if (product.images && product.images.length > 0) {
        mainImage.src = 'backend/' + product.images[0].image;
        product.images.forEach((img, idx) => {
          const thumb = document.createElement('img');
          thumb.src = 'backend/' + img.image;
          thumb.className = "img-thumbnail";
          thumb.style.height = "70px";
          thumb.style.width = "70px";
          thumb.style.objectFit = "cover";
          thumb.style.cursor = "pointer";
          thumb.onclick = function() {
            mainImage.src = thumb.src;
          };
          thumbnails.appendChild(thumb);
        });
      } else {
        mainImage.src = 'frontend/assets/images/kvalitetno_cvijece.webp';
      }

      // Attach event listeners for Add to Wishlist and Add to Cart
      const wishlistBtn = document.getElementById("addToWishlistBtn");
      if (wishlistBtn) {
        wishlistBtn.onclick = function () {
          const quantity = parseInt(document.getElementById("flower-quantity-input").value) || 1;
          WishlistService.addToWishlist(productId, quantity);
        };
      }

      const cartBtn = document.getElementById("addToCartBtn");
      if (cartBtn) {
        cartBtn.onclick = function () {
          const quantity = parseInt(document.getElementById("flower-quantity-input").value) || 1;
          const productId = localStorage.getItem('selected_product_id');
          RestClient.post("cart/add", {
            product_id: productId,
            quantity: quantity
          }, function () {
            toastr.success("Added to cart!");
          }, function () {
            toastr.error("Failed to add to cart.");
          });
        }
      }

      // Attach quantity input validation
      const quantityInput = document.getElementById('flower-quantity-input');
      if (quantityInput) {
        quantityInput.addEventListener('change', function () {
          let val = parseInt(this.value);
          if (isNaN(val) || val < 1) {
            this.value = 1;
            toastr.warning("Quantity must be a positive number.");
          }
          this.value = Math.floor(this.value);
        });
        quantityInput.addEventListener('input', function () {
          this.value = this.value.replace(/[^0-9]/g, '');
        });
      }
    });
  },
  handleNavbarSearch: function () {
    const searchInput = document.getElementById("navbar-search-input");
    const searchBtn = document.getElementById("navbar-search-btn");
    if (!searchInput || !searchBtn) return;

    // Remove previous listeners to avoid duplicates
    searchBtn.onclick = null;
    searchInput.onkeydown = null;

    function doSearch() {
      const searchTerm = searchInput.value.trim();

      // Validation: require at least 2 characters
      if (searchTerm.length < 2) {
        toastr.error("Search term must be at least 2 characters.");
        searchInput.focus();
        return;
      }

      if (window.location.hash === "#products") {
        ProductService.renderCategoryCheckboxes();
        ProductService.loadProducts({ search: searchTerm });
      } else {
        localStorage.setItem("products_search_term", searchTerm);
        window.location.hash = "#products";
      }
    }

    searchBtn.onclick = doSearch;
    searchInput.onkeydown = function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        doSearch();
      }
    };
  },

  applyStoredSearch: function () {
    const searchTerm = localStorage.getItem("products_search_term") || "";
    if (searchTerm) {
      localStorage.removeItem("products_search_term");
      ProductService.loadProducts({ search: searchTerm });
      // Optionally, set the search box value if on products page
      const searchInput = document.getElementById("navbar-search-input");
      if (searchInput) searchInput.value = searchTerm;
    } else {
      ProductService.loadProducts();
      // Optionally clear the search box if not searching
      const searchInput = document.getElementById("navbar-search-input");
      if (searchInput) searchInput.value = "";
    }
  },

  reloadProductsView: function(searchTerm) {
    ProductService.renderCategoryCheckboxes();
    ProductService.loadProducts(searchTerm ? { search: searchTerm } : {});
    const searchInput = document.getElementById("navbar-search-input");
    if (searchInput) searchInput.value = searchTerm || "";
  },

  logProductView: function(productId) {
    const user = JSON.parse(localStorage.getItem("user"));
    if (user && user.id) {
      const payload = {
        customer_id: user.id,
        product_id: parseInt(productId)
      };
      RestClient.post("product_views/add", payload, function () {
      }, function () {
        console.warn("⚠ Failed to log product view.");
      });
    }
  },

  loadUserProductViews: function () {
  RestClient.get("product_views", function (data) {
    Utils.datatable(
      "productViewsTable",
      [
        { data: 'product_name', title: 'Product' },
        { data: 'time', title: 'Viewed At' },
        {
          title: 'Actions',
          render: function (data, type, row) {
            return `
              <div class="text-center">
                <a href="#flower" class="btn btn-sm btn-outline-dark"
                   onclick="localStorage.setItem('selected_product_id', ${row.product_id})">
                  View Product
                </a>
              </div>`;
          }
        }
      ],
      data,
      5
    );
    }, function (xhr, status, error) {
      console.error("Error loading product views:", error);
      toastr.error("Failed to load product views.");
    });
  },

  loadDashboardSummary: function () {
  const user = JSON.parse(localStorage.getItem("user"));
  if (!user || !user.id) return;

  // Total Orders
  RestClient.get("order/count_all", function (res) {
    document.getElementById("total-orders-count").textContent = res || 0;
  }, function () {
    console.warn("Failed to load total orders");
  });

  // Wishlist Items
  RestClient.get("wishlist/summary", function (res) {
    document.getElementById("wishlist-count").textContent = res.total_count || 0;
  }, function () {
    console.warn("Failed to load wishlist summary");
  });

  // Pending Orders
  RestClient.get("order/count_pending", function (res) {
    document.getElementById("pending-count").textContent = res || 0;
  }, function () {
    console.warn("Failed to load pending orders");
  });

  // Delivered Orders
  RestClient.get("order/count_delivered", function (res) {
      document.getElementById("delivered-count").textContent = res || 0;
    }, function () {
      console.warn("Failed to load delivered orders");
    });
  },

  // Call this to force re-rendering flower details
  forceRenderFlower: function() {
    if (window.location.hash === "#flower") {
      ProductService.renderProductDetails();
    }
  }
};
