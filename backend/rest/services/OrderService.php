<?php
require_once __DIR__ . "/../dao/OrderDao.php";

class OrderService {
    private $orderDao;

    public function __construct()
    {
        $this->orderDao = new OrderDao();
    }

    public function add_order($user_id, $order)
    {
        return $this->orderDao->add_order($order, $user_id);
    }

    public function get_orders_by_user($user_id)
    {
        return $this->orderDao->get_orders_by_user($user_id);
    }

    public function count_pending_orders($user_id)
    {
        return $this->orderDao->count_pending_orders($user_id);
    }

    public function count_delivered_orders($user_id)
    {
        return $this->orderDao->count_delivered_orders($user_id);
    }

    public function count_total_orders($user_id)
    {
        return $this->orderDao->count_total_orders($user_id);
    }

    public function update_order_status($order_id, $new_status_id)
    {
        return $this->orderDao->update_order_status($order_id, $new_status_id);
    }

    public function delete_order($order_id)
    {
        return $this->orderDao->delete_order($order_id);
    }
}