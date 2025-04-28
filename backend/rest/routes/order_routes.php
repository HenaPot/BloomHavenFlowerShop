<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials", "true");

require_once __DIR__ . '/../services/OrderService.php';
require_once __DIR__ . '/../../utils/ResponseHelper.php';


Flight::set('order_service', new OrderService());

Flight::group('/order', function () {

    /**
     * @OA\Get(
     *     path="/order/all",
     *     summary="Get all orders for the authenticated user.",
     *     description="Fetches a list of all orders placed by the authenticated user, including order details.",
     *     tags={"Order"},
     *     security={{"ApiKey": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully fetched all orders",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="order_id", type="string", example="4", description="ID of the order"),
     *                 @OA\Property(property="order_date", type="string", example="2025-04-04 16:22:46", description="Date and time when the order was placed"),
     *                 @OA\Property(property="product_names", type="string", example="Red Rose Bouquet, Tulip Bouquet", description="Names of the products in the order"),
     *                 @OA\Property(property="quantities", type="string", example="3,5", description="Quantities of the products in the order"),
     *                 @OA\Property(property="total_price", type="string", example="175", description="Total price of the order"),
     *                 @OA\Property(property="status_name", type="string", example="Pending", description="Status of the order")
     *             )
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
    Flight::route('GET /all', function () {
        $user_id = Flight::get('user'); 
    
        $order_details = Flight::get('order_service')->get_orders_by_user($user_id);
        
        ResponseHelper::handleServiceResponse($order_details);
    });

    /**
     * @OA\Get(
     *     path="/order/count_pending",
     *     summary="Count all pending orders for the authenticated user.",
     *     description="Returns the total number of pending orders for the authenticated user.",
     *     tags={"Order"},
     *     security={{"ApiKey": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully counted pending orders",
     *         @OA\JsonContent(
     *             type="string",
     *             example="3",
     *             description="The total number of pending orders"
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
    Flight::route('GET /count_pending', function () {
        $user_id = Flight::get('user'); 
        $summary = Flight::get('order_service')->count_pending_orders($user_id);
        ResponseHelper::handleServiceResponse($summary);
    });

    /**
     * @OA\Get(
     *     path="/order/count_delivered",
     *     summary="Count all delivered orders for the authenticated user.",
     *     description="Returns the total number of delivered orders for the authenticated user.",
     *     tags={"Order"},
     *     security={{"ApiKey": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully counted delivered orders",
     *         @OA\JsonContent(
     *             type="string",
     *             example="5",
     *             description="The total number of delivered orders"
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
    Flight::route('GET /count_delivered', function () {
        $user_id = Flight::get('user'); 
        $summary = Flight::get('order_service')->count_delivered_orders($user_id);
        ResponseHelper::handleServiceResponse($summary);
    });

    /**
     * @OA\Get(
     *     path="/order/count_all",
     *     summary="Count all orders for the authenticated user.",
     *     description="Returns the total number of all orders for the authenticated user.",
     *     tags={"Order"},
     *     security={{"ApiKey": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully counted all orders",
     *         @OA\JsonContent(
     *             type="string",
     *             example="10",
     *             description="The total number of all orders"
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
    Flight::route('GET /count_all', function () {
        $user_id = Flight::get('user'); 
        $summary = Flight::get('order_service')->count_total_orders($user_id);
        ResponseHelper::handleServiceResponse($summary);
    });
    
    /**
     * @OA\Post(
     *     path="/order/add",
     *     summary="Add a new order.",
     *     description="Creates a new order for the authenticated user.",
     *     tags={"Order"},
     *     security={{"ApiKey": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "surname", "address", "city", "country", "phone_number"},
     *             @OA\Property(property="name", type="string", example="Hena", description="Customer's first name"),
     *             @OA\Property(property="surname", type="string", example="Potogija", description="Customer's last name"),
     *             @OA\Property(property="address", type="string", example="Francuske revolucije bb", description="Customer's address"),
     *             @OA\Property(property="city", type="string", example="Sarajevo", description="Customer's city"),
     *             @OA\Property(property="country", type="string", example="Bosnia and Herzegovina", description="Customer's country"),
     *             @OA\Property(property="phone_number", type="string", example="+387 60 666 7398", description="Customer's phone number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order successfully created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Purchase made successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid input data")
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
    Flight::route('POST /add', function () {
        $user_id = Flight::get('user');
        $data = Flight::request()->data->getData();
        $result = Flight::get('order_service')->add_order($user_id, $data);
        ResponseHelper::handleServiceResponse($result, 'Purchase made successfully!');
    });

    /**
     * @OA\Delete(
     *     path="/order/remove/{order_id}",
     *     summary="Delete an order by ID.",
     *     description="Deletes an order for the authenticated user based on the provided order ID.",
     *     tags={"Order"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="order_id",
     *         in="path",
     *         required=true,
     *         description="ID of the order to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order successfully deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Order removed.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid order ID")
     *         )
     *     ),
     * )
     */
    Flight::route('DELETE /remove/@order_id', function ($order_id) {
        $user_id = Flight::get('user');
        $result = Flight::get('order_service')->delete_order($order_id);
        ResponseHelper::handleServiceResponse($result, 'Order removed.');
    });

    /**
     * @OA\Put(
     *     path="/order/update",
     *     summary="Update the status of an order.",
     *     description="Updates the status of an existing order for the authenticated user.",
     *     tags={"Order"},
     *     security={{"ApiKey": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id", "new_status_id"},
     *             @OA\Property(property="order_id", type="integer", example=1, description="ID of the order to update"),
     *             @OA\Property(property="new_status_id", type="integer", example=2, description="New status ID for the order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order successfully updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Order updated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid input data")
     *         )
     *     ),
     * )
     */
    Flight::route('PUT /update', function () {
        $user_id = Flight::get('user');
        $data = Flight::request()->data->getData();
        $result = Flight::get('order_service')->update_order_status($data["order_id"], $data["new_status_id"]);
        ResponseHelper::handleServiceResponse($result, 'Order updated');
    });

});