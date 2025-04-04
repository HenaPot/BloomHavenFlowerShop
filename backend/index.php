<?php

require 'vendor/autoload.php';
require 'rest/routes/middleware_routes.php';
require 'rest/routes/user_routes.php';
require 'rest/routes/shopping_cart_routes.php';
require 'rest/routes/auth_routes.php';
require 'rest/routes/wishlist_routes.php';
require 'rest/routes/order_routes.php';
require 'rest/routes/item_in_order_routes.php';

Flight::start();
