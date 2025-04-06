<?php
 
 require_once __DIR__ . "/../dao/ProductViewDao.php";
 
 class ProductViewService{
     private $productViewDao;
 
     public function __construct()
     {
         $this->productViewDao = new ProductViewDao();
     }
 
     public function addOrUpdateProductView($customer_id, $product_id, $time) {
         return $this->productViewDao->insertOrUpdateProductView($customer_id, $product_id, $time);
     }
 
     public function getUserProductViews($user_id) {
         return $this->productViewDao->getUserProductViews($user_id);
     }
 }