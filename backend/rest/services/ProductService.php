<?php

require_once __DIR__ . "/../dao/ProductDao.php";

class ProductService {
    private $productDao;

    public function __construct() {
        $this->productDao = new productDao();
    }

    public function add_product($product) {
        if (empty($product)) {
            return "Invalid input";
        }
        return $this->productDao->add_product($product);
    }

    public function get_product_by_id($id) {
        if (empty($id)) {
            return "Invalid input";
        }
        return $this->productDao->get_product_by_id($id);
    }

    public function get_all_products($search = null, $sort = null, $min_price = null, $max_price = null, $category_id = null) {
        return $this->productDao->get_all_products($search, $sort, $min_price, $max_price, $category_id);
    }

    public function update_product($product_id, $product) {
        if (empty($product_id) || empty($product)) {
            return "Invalid input";
        }
        return $this->productDao->update_product($product_id, $product);
    }

    public function delete_product($product_id) {
        if (empty($product_id)) {
            return "Invalid input";
        }
        $this->productDao->delete_product($product_id);
    }

    public function add_product_image($data) {
        return $this->productDao->insert('product_image', $data);
    }
}