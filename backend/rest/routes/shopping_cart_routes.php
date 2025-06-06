<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials", "true");

require_once __DIR__ . '/../services/ShoppingCartService.php';
require_once __DIR__ . '/../../utils/ResponseHelper.php';

Flight::set('cart_service', new ShoppingCartService());

Flight::group('/cart', function () {
    /**
     * @OA\Get(
     *     path="/cart",
     *     summary="Get filtered shopping cart items based on search and sort criteria",
     *     description="Fetches a list of cart items that match the search criteria and are sorted by the given column and order.",
     *     tags={"Shopping Cart"},
     *     security={{"ApiKey": {}}},
     *     parameters={
     *         @OA\Parameter(
     *             name="search",
     *             in="query",
     *             description="Search term to filter cart items",
     *             required=false,
     *             @OA\Schema(type="string")
     *         ),
     *         @OA\Parameter(
     *             name="sort_by",
     *             in="query",
     *             description="Column to sort by (allowed values: 'name', 'price_each')",
     *             required=false,
     *             @OA\Schema(
     *                 type="string",
     *                 enum={"name", "price_each"},
     *                 default="name"
     *             )
     *         ),
     *         @OA\Parameter(
     *             name="sort_order",
     *             in="query",
     *             description="Order to sort by (allowed values: 'asc', 'desc')",
     *             required=false,
     *             @OA\Schema(
     *                 type="string",
     *                 enum={"asc", "desc"},
     *                 default="asc"
     *             )
     *         )
     *     },
     *     responses={
     *         @OA\Response(
     *             response=200,
     *             description="Successfully fetched filtered cart items",
     *             @OA\JsonContent(
     *                 type="array",
     *                 items=@OA\Items(
     *                     type="object",
     *                     properties={
     *                         @OA\Property(property="product_id", type="string"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="category_id", type="string"),
     *                         @OA\Property(property="price", type="string"),
     *                         @OA\Property(property="description", type="string"),
     *                         @OA\Property(property="cart_quantity", type="string")
     *                     }
     *                 )
     *             )
     *         ),
     *         @OA\Response(
     *             response=400,
     *             description="Invalid input data",
     *             @OA\JsonContent(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(property="error", type="string", example="Invalid input data.")
     *                 }
     *             )
     *         ),
     *         @OA\Response(
     *             response=500,
     *             description="Internal server error",
     *             @OA\JsonContent(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(property="error", type="string", example="Internal server error.")
     *                 }
     *             )
     *         )
     *     }
     * )
     */
    Flight::route('GET /', function () {
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
        $user_id = Flight::get('user')->id; 
        $queryParams = Flight::request()->query;

        $search = isset($queryParams['search']) ? trim($queryParams['search']) : "";
        $sort_by = isset($queryParams['sort_by']) ? strtolower($queryParams['sort_by']) : "name";
        $sort_order = isset($queryParams['sort_order']) ? strtolower($queryParams['sort_order']) : "asc";

        $cart = Flight::get('cart_service')->get_filtered_cart($user_id, $search, $sort_by, $sort_order);
        ResponseHelper::handleServiceResponse($cart);
    });

    /**
     * @OA\Get(
     *     path="/cart/summary",
     *     summary="Get shopping cart summary",
     *     description="Fetches the cart summary for a specific user, including total value and total count.",
     *     tags={"Shopping Cart"},
     *     security={{"ApiKey": {}}},
     *     responses={
     *         @OA\Response(
     *             response=200,
     *             description="Successfully fetched cart summary",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(
     *                     property="total_value",
     *                     type="string",
     *                     description="Total value of the items in the cart"
     *                 ),
     *                 @OA\Property(
     *                     property="total_count",
     *                     type="string",
     *                     description="Total count of the items in the cart"
     *                 )
     *             )
     *         ),
     *         @OA\Response(
     *             response=500,
     *             description="Internal server error"
     *         )
     *     }
     * )
     */
    Flight::route('GET /summary', function () {
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
        $user_id = Flight::get('user')->id; 
        $summary = Flight::get('cart_service')->get_cart_summary_by_user($user_id);
        ResponseHelper::handleServiceResponse($summary);
    });
    

    /**
     * @OA\Post(
     *     path="/cart/add",
     *     summary="Add an item to the cart",
     *     tags={"Shopping Cart"},
     *     security={{"ApiKey": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id"},
     *             @OA\Property(
     *                 property="product_id",
     *                 type="integer",
     *                 example=1
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item added to cart",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Item added to cart"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid input data."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error."
     *             )
     *         )
     *     )
     * )
     */
    Flight::route('POST /add', function () {
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
        $user_id = Flight::get('user')->id;
        $data = Flight::request()->data->getData();

        $product_id = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        $result = Flight::get('cart_service')->add_to_cart($user_id, $product_id, $quantity);
        ResponseHelper::handleServiceResponse($result, 'Item added to cart');
    });

    /**
     * @OA\Delete(
     *     path="/cart/remove/{product_id}",
     *     tags={"Shopping Cart"},
     *     summary="Remove an item from the shopping cart",
     *     description="Removes a product from the user's cart by product ID.",
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to remove",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item successfully removed from cart",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Item removed from cart")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid input")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    Flight::route('DELETE /remove/@product_id', function ($product_id) {
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
        $user_id = Flight::get('user')->id;
        $result = Flight::get('cart_service')->remove_from_cart($user_id, $product_id);
        ResponseHelper::handleServiceResponse($result, 'Item removed from cart');
    });

    /**
     * @OA\Put(
     *     path="/cart/update",
     *     tags={"Shopping Cart"},
     *     summary="Update product quantity in the shopping cart",
     *     description="Updates the quantity of a product in the user's cart. User ID is retrieved from token/session.",
     *     security={{"ApiKey": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "quantity"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=76)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cart updated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid input data.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error.")
     *         )
     *     )
     * )
     */
    Flight::route('PUT /update', function () {
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
        $user_id = Flight::get('user')->id;
        $data = Flight::request()->data->getData();

        $result = Flight::get('cart_service')->update_quantity(
            $user_id,
            $data['product_id'] ?? null,
            $data['quantity'] ?? null
        );

        ResponseHelper::handleServiceResponse($result, 'Cart updated');
    });

    /**
     * @OA\Delete(
     *     path="/cart/clear",
     *     tags={"Shopping Cart"},
     *     summary="Remove all items from the shopping cart",
     *     description="Deletes all products in the user's cart. User ID is retrieved from token/session.",
     *     security={{"ApiKey": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Item successfully removed from cart",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Item removed from cart")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid input")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    Flight::route('DELETE /clear', function () {
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
        $user_id = Flight::get('user')->id;
        $result = Flight::get('cart_service')->clear_cart($user_id);
        ResponseHelper::handleServiceResponse($result, 'Cart cleared');
    });

});