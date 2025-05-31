var OrderService = {
  statusList: [],

  getAllOrders: function () {
    RestClient.get("order/statuses", function (statuses) {
      OrderService.statusList = statuses; // cache

      RestClient.get("order/all_orders", function (data) {
        Utils.datatable(
          "ordersTable",
          [
            { data: 'order_id', title: 'Order ID' },
            { data: 'order_date', title: 'Date and Time' },
            { data: 'product_names', title: 'Products' },
            { data: 'quantities', title: 'Product Quantities' },
            { data: 'total_price', title: 'Total Price' },
            {
              data: null,
              title: 'Status',
              render: function (data, type, row) {
                return OrderService.renderStatusDropdown(row);
              }
            },
            {
            title: 'Actions',
                render: function (data, type, row, meta) {
                    const rowStr = encodeURIComponent(JSON.stringify(row));
                    return `<div class="d-flex justify-content-center gap-2 mt-1">
                    <button class="btn btn-danger delete-order-btn" data-order-id="${row.order_id}">Delete</button>
                    </div>
                    `;
                }
            }
          ],
          data,
          10
        );
      }, function (xhr, status, error) {
        console.error('Error fetching orders:', error);
      });

    }, function (xhr, status, error) {
      console.error('Error fetching statuses:', error);
    });
  },

  renderStatusDropdown: function (order) {
    const options = OrderService.statusList.map(status => {
      const selected = status.name === order.status_name ? "selected" : "";
      return `<option value="${status.id}" ${selected}>${status.name}</option>`;
    }).join("");

    return `
      <select class="form-select order-status-dropdown" data-order-id="${order.order_id}">
        ${options}
      </select>`;
  },

  updateOrderStatus: function (orderId, newStatusId) {
    RestClient.put(
      `order/update`,
      { order_id: orderId, new_status_id: newStatusId },
      function () {
        toastr.success("Order status updated.");
      },
      function () {
        toastr.error("Failed to update order status.");
      }
    );
  },

    openDeleteConfirmationDialog: function (orderId) {
    if (!orderId) {
        toastr.error("Order ID not provided.");
        return;
    }
    OrderService.deleteOrder(orderId);
    },

  deleteOrder: function (orderId) {
  if (!orderId) {
    toastr.error("Order ID not provided.");
    return;
  }

  if (!confirm("Are you sure you want to delete this order? This action cannot be undone.")) {
    return;
  }

  Utils.block_ui("body"); // You can change this selector to match your UI

  RestClient.delete(
    `order/remove/${orderId}`,
    {},
    function (response) {
      toastr.success("Order has been deleted successfully.");
      OrderService.getAllOrders();
    },
    function (error) {
      toastr.error("Error deleting the order.");
    }
  );
    Utils.unblock_ui("body");
  }
};
