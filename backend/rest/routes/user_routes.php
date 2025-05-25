<?php

require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../../utils/ResponseHelper.php';
require_once __DIR__ . '/../../data/Roles.php';

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
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
        $current_user_id = Flight::get('user')->id;
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
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
        $current_user_id = Flight::get('user')->id;
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
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
        $user_service = new UserService();
        $result = $user_service->delete_user($user_id);
        ResponseHelper::handleServiceResponse($result, "You have successfully deleted the user");
    });

    Flight::route('POST /upload_image', function () {
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
        $user_id = Flight::get('user')->id;

        if (!isset($_FILES['profile_picture'])) {
            Flight::halt(400, 'No file uploaded.');
        }

        $file = $_FILES['profile_picture'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed)) {
            Flight::halt(400, 'Only JPG, PNG, or WEBP images are allowed.');
        }

        $uploads_dir = __DIR__ . '/../../uploads/';
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_name = uniqid("profile_", true) . '.' . $ext;
        $target_path = $uploads_dir . $new_name;

        if (!move_uploaded_file($file['tmp_name'], $target_path)) {
            Flight::halt(500, 'Failed to move uploaded file.');
        }

        // Save relative image path to DB
        $relative_url = '/uploads/' . $new_name;
        Flight::get('user_service')->update_user($user_id, ['image' => $relative_url]);

        echo json_encode(['status' => 'success', 'image_url' => $relative_url]);
        
    });

});