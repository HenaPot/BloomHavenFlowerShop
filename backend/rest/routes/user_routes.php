<?php

require_once __DIR__ . '/../services/UserService.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::set('user_service', new UserService());

Flight::group('/users', function() {
    
    Flight::route('POST /add', function() {
        $data = Flight::request()->data->getData();

        if (!isset($data['email']) || !isset($data['password']) || !isset($data['repeat_password_signup'])) {
            Flight::halt(400, 'Email, password and repeat password are required.');
        }

        if (trim($data['email']) == "" || trim($data['password']) == "" || trim($data['repeat_password_signup']) == "" || trim($data['address']) == "" ) {
            Flight::halt(400, 'Email, password, repeat password, and address cannot be empty.');
        }

        if ($data['password'] !== $data['repeat_password_signup']) {
            Flight::halt(400, 'Password and repeat password do not match');
        }
        
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['repeat_password_signup']);

        $data['role_id'] = 1;

        $user = Flight::get('user_service')->add_user($data);

        Flight::json(
            $user
        );
    });

    Flight::route('GET /current', function() {
        $current_user_id = Flight::get('user');
    
        // Debugging: Print Retrieved User ID
        error_log("Current User ID: " . $current_user_id);
    
        if (!$current_user_id) {
            Flight::json(["error" => "User ID not found"], 400);
            return;
        }
    
        $user = Flight::get('user_service')->get_user_by_id($current_user_id);
    
        // Debugging: Check if get_user_by_id() returns valid data
        error_log("User Data: " . print_r($user, true));
    
        Flight::json($user);
    });
    

});