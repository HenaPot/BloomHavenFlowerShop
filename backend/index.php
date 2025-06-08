<?php

require 'vendor/autoload.php';
require 'middleware/AuthMiddleware.php';

Flight::register('auth_middleware', "AuthMiddleware");

// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Authentication");
header("Access-Control-Allow-Credentials: true");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

Flight::route('/*', function() {
    // Public routes
    if (
        strpos(Flight::request()->url, '/auth/login') === 0 ||
        strpos(Flight::request()->url, '/auth/register') === 0
    ) {
        return TRUE;
    }

    // Authenticated routes
    try {
        $token = Flight::request()->getHeader("Authentication");
        if (Flight::auth_middleware()->verifyToken($token)) {
            return TRUE;
        }
    } catch (\Exception $e) {
        Flight::halt(401, $e->getMessage());
    }
});

require 'rest/routes/user_routes.php';
require 'rest/routes/shopping_cart_routes.php';
require 'rest/routes/auth_routes.php';
require 'rest/routes/wishlist_routes.php';
require 'rest/routes/order_routes.php';
require 'rest/routes/item_in_order_routes.php';
require 'rest/routes/product_routes.php';
require 'rest/routes/product_view_routes.php';
require 'rest/routes/category_routes.php';

Flight::start();
