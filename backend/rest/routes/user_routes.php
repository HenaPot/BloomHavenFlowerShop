<?php

require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../../utils/ResponseHelper.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::set('user_service', new UserService());

Flight::group('/users', function() {
    
    /**
     * @OA\Get(
     *     path="/users/current",
     *     summary="Get the currently authenticated user's details.",
     *     description="Fetches the details of the currently authenticated user based on the user ID retrieved from the session or token.",
     *     tags={"User"},
     *     security={{"ApiKey": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully fetched user details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", example="3", description="User ID"),
     *             @OA\Property(property="name", type="string", example="Hena Potogija", description="User's full name"),
     *             @OA\Property(property="email", type="string", example="hena.potogija@stu.ibu.edu.ba", description="User's email address"),
     *             @OA\Property(property="date_of_birth", type="string", example="2025-04-16", description="User's date of birth"),
     *             @OA\Property(property="username", type="string", example="hena", description="User's username"),
     *             @OA\Property(property="image", type="string", nullable=true, example=null, description="URL of the user's profile image"),
     *             @OA\Property(property="role_id", type="string", example="1", description="Role ID of the user"),
     *             @OA\Property(property="address", type="string", example="Testna adresa", description="User's address")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    Flight::route('GET /current', function() {
        $current_user_id = Flight::get('user');
        error_log("Current User ID: " . $current_user_id);     
        $user = Flight::get('user_service')->get_user_by_id($current_user_id);
        unset($user['password']);
        ResponseHelper::handleServiceResponse($user);
    });

    /**
     * @OA\Put(
     *     path="/users/update",
     *     summary="Update the currently authenticated user's details.",
     *     description="Updates the details of the currently authenticated user based on the provided data.",
     *     tags={"User"},
     *     security={{"ApiKey": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Hena Miju", description="Updated name of the user"),
     *             @OA\Property(property="email", type="string", example="hena.potogija@stu.ibu.edu.ba", description="Updated email of the user"),
     *             @OA\Property(property="date_of_birth", type="string", example="2025-04-16", description="Updated date of birth of the user"),
     *             @OA\Property(property="username", type="string", example="testiram radi li update", description="Updated username of the user"),
     *             @OA\Property(property="image", type="string", nullable=true, example=null, description="Updated profile image URL of the user"),
     *             @OA\Property(property="address", type="string", example="Testna adresa", description="Updated address of the user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated user details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=3, description="User ID"),
     *             @OA\Property(property="name", type="string", example="Hena Miju", description="Updated name of the user"),
     *             @OA\Property(property="email", type="string", example="hena.potogija@stu.ibu.edu.ba", description="Updated email of the user"),
     *             @OA\Property(property="date_of_birth", type="string", example="2025-04-16", description="Updated date of birth of the user"),
     *             @OA\Property(property="username", type="string", example="testiram radi li update", description="Updated username of the user"),
     *             @OA\Property(property="image", type="string", nullable=true, example=null, description="Updated profile image URL of the user"),
     *             @OA\Property(property="role_id", type="string", example="1", description="Role ID of the user"),
     *             @OA\Property(property="address", type="string", example="Testna adresa", description="Updated address of the user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid input")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    Flight::route('PUT /update', function() {
        $current_user_id = Flight::get('user');
        $data = Flight::request()->data->getData();
        
        $user = Flight::get('user_service')->update_user($current_user_id, $data);
        ResponseHelper::handleServiceResponse($user);
    });

    /**
     * @OA\Delete(
     *     path="/users/delete/{user_id}",
     *     summary="Delete a user by ID.",
     *     description="Deletes a user from the system based on the provided user ID.",
     *     tags={"User"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to delete",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully deleted the user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="You have successfully deleted the user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    Flight::route('DELETE /delete/@user_id', function ($user_id) {
        $user_service = new UserService();
        $result = $user_service->delete_user($user_id);
        ResponseHelper::handleServiceResponse($result, "You have successfully deleted the user");
    });

});