<?php

require_once __DIR__ . '/../services/UserService.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::set('user_service', new UserService());

Flight::group('/users', function() {
    
    Flight::route('GET /current', function() {
        $current_user_id = Flight::get('user');
        error_log("Current User ID: " . $current_user_id);     
        $user = Flight::get('user_service')->get_user_by_id($current_user_id);
        unset($user['password']);
        Flight::json($user);
    });

});