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

    Flight::route('POST /update', function() {
        $current_user_id = Flight::get('user');
        $data = Flight::request()->data->getData();
        
        $user = Flight::get('user_service')->update_user($current_user_id, $data);
        Flight::json(
            $user
        );
    });

    Flight::route('DELETE /delete/@user_id', function ($user_id) {
        if($user_id == NULL || $user_id == '') {
            Flight::halt(500, "Required parameters are missing!");
        }

        $user_service = new UserService();
        $user_service->delete_user($user_id);
        
        Flight::json(['data' => NULL, 'message' => "You have successfully deleted the user"]);
    });

});