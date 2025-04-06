<?php
 header("Access-Control-Allow-Origin: *");
 header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
 header("Access-Control-Allow-Headers: Content-Type, Authorization");
 header("Access-Control-Allow-Credentials", "true");
 require_once __DIR__ . '/../services/ProductService.php';
 
 use Firebase\JWT\JWT;
 use Firebase\JWT\Key;
 
 Flight::set('product_service', new ProductService());
 
 Flight::group('/products', function() {
 
     Flight::route('POST /add', function () {
         $data = Flight::request()->data->getData();
         $product = [
             'name' => $data['name'],
             'category_id' => $data['category_id'],
             'quantity' => $data['quantity'],
             'price_each' => $data['price_each'],
             'description' => $data['description']
         ];
     
         $inserted_product = Flight::get('product_service')->add_product($product);
         
         Flight::json(['message' => 'Product added successfully', 'product' => $inserted_product]);
     });
 
     Flight::route('GET /product/@id', function ($id) {
         $product = Flight::get('product_service')->get_product_by_id($id);
     
         if ($product) {
             Flight::json($product);
         } else {
             Flight::json(['message' => 'Product not found'], 404);
         }
     });
 
     Flight::route('GET /products', function () {
         $search = Flight::request()->query['search'] ?? null;
         $sort = Flight::request()->query['sort'] ?? null;
         $min_price = Flight::request()->query['min_price'] ?? null;
         $max_price = Flight::request()->query['max_price'] ?? null;
         $category_id = Flight::request()->query['category_id'] ?? null;
     
         $products = Flight::get('product_service')->get_all_products($search, $sort, $min_price, $max_price, $category_id);
     
         Flight::json($products);
     });
 
     Flight::route('DELETE /delete/@product_id', function ($product_id) {
         if($product_id == NULL || $product_id == '') {
             Flight::halt(500, "Required parameters are missing!");
         }
 
         $product_service = new productService();
         $product_service->delete_product($product_id);
         
         Flight::json(['data' => NULL, 'message' => "You have successfully deleted the product"]);
     });
     
     
     Flight::route('PUT /update/@id', function($id) {
         $data = Flight::request()->data->getData();
         
         $product = Flight::get('product_service')->update_product($id, $data);
         Flight::json(["message" => "Product updated successfully", "product" => $product], 200);
 
     });
     
 
 });