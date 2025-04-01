<?php
require_once __DIR__ . "/BaseDao.php";

class WishlistDao extends BaseDao {
    public function __construct()
    {
        parent::__construct('wishlist');
    }

    public function add_to_wishlist($user_id, $product_id)
    {
        $wishlist_item = $this->query_unique(
            "SELECT * FROM wishlist WHERE user_id = :user_id AND product_id = :product_id",
            ["user_id" => $user_id, "product_id" => $product_id]
        );

        if ($wishlist_item) {
            $new_quantity = $wishlist_item['quantity'] + 1;
            $this->update_quantity($user_id, $product_id, $new_quantity);

        } else {
            $this->insert("wishlist", [
                "user_id" => $user_id,
                "product_id" => $product_id,
                "quantity" => 1
            ]);
        }
    }

    public function remove_from_wishlist($user_id, $product_id)
    {
        $query = "DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
        $this->query($query, [
            "user_id" => $user_id,
            "product_id" => $product_id
        ]);
    }

    public function update_quantity($user_id, $product_id, $quantity)
    {
        $query = "UPDATE wishlist SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
        $this->query($query, [
            "quantity" => $quantity,
            "user_id" => $user_id,
            "product_id" => $product_id
        ]);
    }

    public function get_wishlist_by_user($user_id)
    {
        return $this->query(
            "SELECT 
                p.id AS product_id,
                p.name,
                p.category_id,
                p.quantity AS product_stock,
                p.description,
                w.quantity AS wishlist_quantity
            FROM wishlist w
            JOIN product p ON w.product_id = p.id
            WHERE w.user_id = :user_id",
            ["user_id" => $user_id]
        );
    }

    public function clear_wishlist($user_id)
    {
        $this->query("DELETE FROM wishlist WHERE user_id = :user_id", ["user_id" => $user_id]);
    }
}