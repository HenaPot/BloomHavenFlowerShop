<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials", "true");

require_once __DIR__ . '/../services/OrderService.php';

Flight::set('order_service', new OrderService());

Flight::group('/order', function () {

    Flight::route('GET /all', function () {
        $user_id = Flight::get('user'); 
    
        $order_details = Flight::get('order_service')->get_orders_by_user($user_id);
        
        Flight::json($order_details);
    });

    Flight::route('GET /count_pending', function () {
        $user_id = Flight::get('user'); 
    
        $summary = Flight::get('order_service')->count_pending_orders($user_id);
    
        Flight::json($summary);
    });

    Flight::route('GET /count_delivered', function () {
        $user_id = Flight::get('user'); 
    
        $summary = Flight::get('order_service')->count_delivered_orders($user_id);
    
        Flight::json($summary);
    });

    Flight::route('GET /count_all', function () {
        $user_id = Flight::get('user'); 
    
        $summary = Flight::get('order_service')->count_total_orders($user_id);
    
        Flight::json($summary);
    });
    

    Flight::route('POST /add', function () {
        $user_id = Flight::get('user');
        $data = Flight::request()->data->getData();
        Flight::get('order_service')->add_order($user_id, $data);
        Flight::json(['message' => 'Purchase made successfully!']);
    });

    Flight::route('DELETE /remove/@order_id', function ($order_id) {
        $user_id = Flight::get('user');
        Flight::get('order_service')->delete_order($order_id);
        Flight::json(['message' => 'Order removed.']);
    });

    Flight::route('POST /update', function () {
        $user_id = Flight::get('user');
        $data = Flight::request()->data->getData();
        Flight::get('order_service')->update_order_status($data["order_id"], $data["new_status_id"]);
        Flight::json(['message' => 'Order updated']);
    });

});