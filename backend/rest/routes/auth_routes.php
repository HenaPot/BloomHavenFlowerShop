<?php
    require_once __DIR__ . '/../../config.php';
    require_once __DIR__ . '/../services/AuthService.php';
    require_once __DIR__ . '/../../utils/ResponseHelper.php';

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    Flight::set('auth_service', new AuthService());

    Flight::group('/auth', function() {

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Authenticate a user and return a JWT token.",
     *     description="Logs in a user by validating their email and password, and returns a JWT token if successful.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="hena.potogija@stu.ibu.edu.ba", description="User's email address"),
     *             @OA\Property(property="password", type="string", example="password123", description="User's password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully authenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=3, description="User ID"),
     *             @OA\Property(property="name", type="string", example="Hena Potogija", description="User's name"),
     *             @OA\Property(property="email", type="string", example="hena.potogija@stu.ibu.edu.ba", description="User's email address"),
     *             @OA\Property(property="username", type="string", example="hena", description="User's username"),
     *             @OA\Property(property="role_id", type="integer", example=1, description="User's role ID"),
     *             @OA\Property(property="address", type="string", example="Testna adresa", description="User's address"),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...", description="JWT token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Invalid username or password",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid username or password")
     *         )
     *     )
     * )
     */
    Flight::route('POST /login', function() {
        $payload = Flight::request()->data->getData();

        $user = Flight::get('auth_service')->get_user_by_email($payload['email']);

        if (!$user || !password_verify($payload['password'], $user['password']))
            Flight::halt(500, "Invalid username or password");

        unset($user['password']);

        $jwt_payload = [
            'user' => $user,
            'iat' => time(),
            'exp' => time() + (60 * 60) // valid for an hour
        ];

        $token = JWT::encode(
            $jwt_payload,
            Config::JWT_SECRET(),
            'HS256'
        );

        Flight::json(
            array_merge($user, ['token' => $token])
        );
    });

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register a new user and return a JWT token.",
     *     description="Registers a new user by validating the input data, hashing the password, and returning a JWT token if successful.",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "name", "email", "password", "repeat_password_signup", "address", "username", "date_of_birth"},
     *             @OA\Property(property="username", type="string", example="miju_h", description="User's username"),
     *             @OA\Property(property="name", type="string", example="Hena Miju", description="User's full name"),
     *             @OA\Property(property="email", type="string", example="hena.potogija@stu.ibu.edu.ba", description="User's email address"),
     *             @OA\Property(property="password", type="string", example="password123", description="User's password"),
     *             @OA\Property(property="repeat_password_signup", type="string", example="password123", description="Repeat password for confirmation"),
     *             @OA\Property(property="address", type="string", example="Testna adresa", description="User's address"),
     *             @OA\Property(property="date_of_birth", type="string", example="2025-04-08", description="User's date of birth")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully registered",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=3, description="User ID"),
     *             @OA\Property(property="name", type="string", example="Hena Potogija", description="User's name"),
     *             @OA\Property(property="email", type="string", example="hena.potogija@stu.ibu.edu.ba", description="User's email address"),
     *             @OA\Property(property="username", type="string", example="hena", description="User's username"),
     *             @OA\Property(property="role_id", type="integer", example=1, description="User's role ID"),
     *             @OA\Property(property="address", type="string", example="Testna adresa", description="User's address"),
     *             @OA\Property(property="date_of_birth", type="string", example="2025-04-08", description="User's date of birth"),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...", description="JWT token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input or mismatched passwords",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Email, password, and repeat password are required.")
     *         )
     *     ),
          * )
     */
    Flight::route('POST /register', function() {
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
        $data['role_id'] = isset($data['role_id']) ? intval($data['role_id']) : 2;

        $user = Flight::get('user_service')->add_user($data);

        $jwt_payload = [
            'user' => $user,
            'iat' => time(),
            'exp' => time() + (60 * 60) // valid for an hour
        ];

        $token = JWT::encode(
            $jwt_payload,
            Config::JWT_SECRET(),
            'HS256'
        );

        Flight::json(
            array_merge($user, ['token' => $token])
        );
    });

});