<?php
require_once __DIR__ . "/../dao/ShoppingCartDao.php";

class ShoppingCartService {
    private $shoppingCartDao;

    public function __construct()
    {
        $this->shoppingCartDao = new ShoppingCartDao();
    }

    public function add_to_cart($user_id, $product_id)
    {
        return $this->shoppingCartDao->add_to_cart($user_id, $product_id);
    }

    public function remove_from_cart($user_id, $product_id)
    {
        return $this->shoppingCartDao->remove_from_cart($user_id, $product_id);
    }

    public function update_quantity($user_id, $product_id, $quantity)
    {
        return $this->shoppingCartDao->update_quantity($user_id, $product_id, $quantity);
    }

    public function get_filtered_cart($user_id, $search = "", $sort_by = "name", $sort_order = "asc")
    {
        return $this->shoppingCartDao->get_cart_by_user($user_id, $search, $sort_by, $sort_order);
    }

    public function get_cart_summary_by_user($user_id)
    {
        return $this->shoppingCartDao->get_cart_summary_by_user($user_id);
    }

    public function clear_cart($user_id)
    {
        return $this->shoppingCartDao->clear_cart($user_id);
    }
}