<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials", "true");

require_once __DIR__ . '/../services/WishlistService.php';

Flight::set('wishlist_service', new WishlistService());

Flight::group('/wishlist', function () {

    Flight::route('GET /', function () {
        $user_id = Flight::get('user');
        $wishlist = Flight::get('wishlist_service')->get_wishlist_by_user($user_id);
        Flight::json($wishlist);
    });

    Flight::route('POST /add', function () {
        $user_id = Flight::get('user');
        $data = Flight::request()->data->getData();
        Flight::get('wishlist_service')->add_to_wishlist($user_id, $data['product_id']);
        Flight::json(['message' => 'Item added to wishlist']);
    });

    Flight::route('DELETE /remove/@product_id', function ($product_id) {
        $user_id = Flight::get('user');
        Flight::get('wishlist_service')->remove_from_wishlist($user_id, $product_id);
        Flight::json(['message' => 'Item removed from wishlist']);
    });

    Flight::route('POST /update', function () {
        $user_id = Flight::get('user');
        $data = Flight::request()->data->getData();
        Flight::get('wishlist_service')->update_quantity($user_id, $data['product_id'], $data['quantity']);
        Flight::json(['message' => 'Wishlist updated']);
    });

    Flight::route('DELETE /clear', function () {
        $user_id = Flight::get('user');
        Flight::get('wishlist_service')->clear_wishlist($user_id);
        Flight::json(['message' => 'wishlist cleared']);
    });

});