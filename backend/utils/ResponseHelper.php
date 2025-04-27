<?php

class ResponseHelper {
    public static function handleServiceResponse($result, $successMessage) {
        if ($result === "Server error") {
            Flight::halt(500, json_encode(['error' => 'Internal server error.']));
        } elseif ($result === "Invalid input") {
            Flight::halt(400, json_encode(['error' => 'Invalid input data.']));
        } else {
            Flight::json(['message' => $successMessage]);
        }
    }
}
    
    