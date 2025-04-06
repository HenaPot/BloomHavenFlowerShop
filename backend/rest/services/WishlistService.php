<?php
require_once __DIR__ . "/../dao/WishlistDao.php";

class WishlistService {
    private $wishlistDao;

    public function __construct()
    {
        $this->wishlistDao = new WishlistDao();
    }

    public function add_to_wishlist($user_id, $product_id)
    {
        return $this->wishlistDao->add_to_wishlist($user_id, $product_id);
    }

    public function remove_from_wishlist($user_id, $product_id)
    {
        return $this->wishlistDao->remove_from_wishlist($user_id, $product_id);
    }

    public function update_quantity($user_id, $product_id, $quantity)
    {
        return $this->wishlistDao->update_quantity($user_id, $product_id, $quantity);
    }

    public function get_filtered_wishlist($user_id, $search = "", $sort_by = "name", $sort_order = "asc")
    {
        return $this->wishlistDao->get_wishlist_by_user($user_id, $search, $sort_by, $sort_order);
    }

    public function clear_wishlist($user_id)
    {
        return $this->wishlistDao->clear_wishlist($user_id);
    }

    public function get_wishlist_summary_by_user($user_id)
    {
        return $this->wishlistDao->get_wishlist_summary_by_user($user_id);
    }
}