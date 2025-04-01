<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials", "true");

require_once __DIR__ . '/../services/ShoppingCartService.php';

Flight::set('cart_service', new ShoppingCartService());

Flight::group('/cart', function () {

    Flight::route('GET /', function () {
        $user_id = Flight::get('user');
        $cart = Flight::get('cart_service')->get_cart_by_user($user_id);
        Flight::json($cart);
    });

    Flight::route('POST /add', function () {
        $user_id = Flight::get('user');
        $data = Flight::request()->data->getData();
        Flight::get('cart_service')->add_to_cart($user_id, $data['product_id']);
        Flight::json(['message' => 'Item added to cart']);
    });

    Flight::route('DELETE /remove/@product_id', function ($product_id) {
        $user_id = Flight::get('user');
        Flight::get('cart_service')->remove_from_cart($user_id, $product_id);
        Flight::json(['message' => 'Item removed from cart']);
    });

    Flight::route('POST /update', function () {
        $user_id = Flight::get('user');
        $data = Flight::request()->data->getData();
        Flight::get('cart_service')->update_quantity($user_id, $data['product_id'], $data['quantity']);
        Flight::json(['message' => 'Cart updated']);
    });

    Flight::route('DELETE /clear', function () {
        $user_id = Flight::get('user');
        Flight::get('cart_service')->clear_cart($user_id);
        Flight::json(['message' => 'Cart cleared']);
    });

});