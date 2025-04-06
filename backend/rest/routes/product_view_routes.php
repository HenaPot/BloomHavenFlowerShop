<?php
 header("Access-Control-Allow-Origin: *");
 header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
 header("Access-Control-Allow-Headers: Content-Type, Authorization");
 header("Access-Control-Allow-Credentials", "true");
 require_once __DIR__ . '/../services/ProductViewService.php';
 
 use Firebase\JWT\JWT;
 use Firebase\JWT\Key;
 
 Flight::set('product_view_service', new ProductViewService());
 
 Flight::group('/product_views', function() {
 
     Flight::route('POST /add', function() {
         $data = Flight::request()->data->getData();
         $customer_id = $data['customer_id'];
         $product_id = $data['product_id'];
         $time = date("Y-m-d H:i:s");
     
         // Validate required fields
         if (empty($customer_id) || empty($product_id) || empty($time)) {
             Flight::json(['error' => 'Missing required fields'], 400);
             return;
         }
     
         // Call the service layer to add or update the product view
         $productViewService = Flight::get('product_view_service');
         $result = $productViewService->addOrUpdateProductView($customer_id, $product_id, $time);
     
         // Return response
         Flight::json($result);
     });
 
     Flight::route('GET /', function() {
         $user_id = Flight::get('user'); // Get current user ID
         if (!$user_id) {
             Flight::json(['error' => 'User not authenticated'], 401);
             return;
         }
     
         $productViewService = Flight::get('product_view_service');
         $result = $productViewService->getUserProductViews($user_id);
         Flight::json($result);
     });
 
 });