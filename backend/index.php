<?php

require 'vendor/autoload.php';
require 'rest/routes/user_routes.php';

// Test route to verify FlightPHP is working
Flight::route('GET /', function () {
    echo "FlightPHP is working!";
});

Flight::start();
