<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials", "true");

require_once __DIR__ . '/../services/ItemInOrderService.php';

Flight::set('item_in_order_service', new ItemInOrderService());

Flight::group('/item_in_order', function () {

    Flight::route('POST /', function () {
        $user_id = Flight::get('user'); 
        $data = Flight::request()->data->getData();
    
        $item_in_order = Flight::get('item_in_order_service')->add_item_in_order($data["order_id"], $data["product_id"], $data["quantity"]);
        
        Flight::json($item_in_order);
    });

});