<?php
 header("Access-Control-Allow-Origin: *");
 header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS");
 header("Access-Control-Allow-Headers: Content-Type, Authorization");
 header("Access-Control-Allow-Credentials", "true");
 require_once __DIR__ . '/../services/ProductService.php';
 require_once __DIR__ . '/../../utils/ResponseHelper.php';
 
 use Firebase\JWT\JWT;
 use Firebase\JWT\Key;
 
 Flight::set('product_service', new ProductService());
 
 Flight::group('/products', function() {
 
     /**
     * @OA\Post(
     *     path="/products/add",
     *     summary="Add a new product.",
     *     description="Creates a new product and returns the created product in the response.",
     *     tags={"Products"},
     *     security={{"ApiKey": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category_id", "quantity", "price_each", "description"},
     *             @OA\Property(property="name", type="string", example="Red Rose Bouquet", description="Name of the product"),
     *             @OA\Property(property="category_id", type="integer", example=1, description="ID of the category the product belongs to"),
     *             @OA\Property(property="quantity", type="integer", example=50, description="Available quantity of the product"),
     *             @OA\Property(property="price_each", type="number", format="float", example=25.99, description="Price of each unit of the product"),
     *             @OA\Property(property="description", type="string", example="A beautiful bouquet of red roses.", description="Description of the product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product successfully created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1, description="ID of the created product"),
     *             @OA\Property(property="name", type="string", example="Red Rose Bouquet", description="Name of the product"),
     *             @OA\Property(property="category_id", type="integer", example=1, description="ID of the category the product belongs to"),
     *             @OA\Property(property="quantity", type="integer", example=50, description="Available quantity of the product"),
     *             @OA\Property(property="price_each", type="number", format="float", example=25.99, description="Price of each unit of the product"),
     *             @OA\Property(property="description", type="string", example="A beautiful bouquet of red roses.", description="Description of the product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid input")
     *         )
     *     ),
     * )
     */
     Flight::route('POST /add', function () {
         Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
         $data = Flight::request()->data->getData();
         $product = [
             'name' => $data['name'],
             'category_id' => $data['category_id'],
             'quantity' => $data['quantity'],
             'price_each' => $data['price_each'],
             'description' => $data['description']
         ];
     
         $inserted_product = Flight::get('product_service')->add_product($product);
         ResponseHelper::handleServiceResponse($inserted_product);
     });
 
     /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Get product details by ID.",
     *     description="Fetches the details of a specific product by its ID.",
     *     tags={"Products"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to fetch",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully fetched product details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1, description="ID of the product"),
     *             @OA\Property(property="name", type="string", example="Red Rose Bouquet", description="Name of the product"),
     *             @OA\Property(property="category", type="string", example="Bouquet", description="ID of the category the product belongs to"),
     *             @OA\Property(property="quantity", type="string", example="50", description="Available quantity of the product"),
     *             @OA\Property(property="price_each", type="string", example="25.99", description="Price of each unit of the product"),
     *             @OA\Property(property="description", type="string", example="A beautiful bouquet of red roses.", description="Description of the product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid input")
     *         )
     *     ),
     * )
     */
     Flight::route('GET /@id', function ($id) {
        Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
        $product = Flight::get('product_service')->get_product_by_id($id);
        ResponseHelper::handleServiceResponse($product);
     });
 
     /**
     * @OA\Get(
     *     path="/products",
     *     summary="Get all products.",
     *     description="Fetches a list of all products, including their details.",
     *     tags={"Products"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search term to filter products by name or description",
     *         @OA\Schema(type="string", example="Bouquet")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         required=false,
     *         description="Sort order for the products (e.g., 'price_asc', 'price_desc')",
     *         @OA\Schema(type="string", example="price_asc")
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         required=false,
     *         description="Minimum price to filter products",
     *         @OA\Schema(type="number", format="float", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         required=false,
     *         description="Maximum price to filter products",
     *         @OA\Schema(type="number", format="float", example=100)
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=false,
     *         description="Category ID to filter products",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully fetched all products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="string", example="1", description="ID of the product"),
     *                 @OA\Property(property="name", type="string", example="Red Rose Bouquet", description="Name of the product"),
     *                 @OA\Property(property="category_name", type="string", example="Bouquets", description="Name of the category the product belongs to"),
     *                 @OA\Property(property="quantity", type="string", example="50", description="Available quantity of the product"),
     *                 @OA\Property(property="price_each", type="string", example="25.99", description="Price of each unit of the product"),
     *                 @OA\Property(property="description", type="string", example="A beautiful bouquet of red roses.", description="Description of the product")
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
     Flight::route('GET /', function () {
         Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
         $search = Flight::request()->query['search'] ?? null;
         $sort = Flight::request()->query['sort'] ?? null;
         $min_price = Flight::request()->query['min_price'] ?? null;
         $max_price = Flight::request()->query['max_price'] ?? null;
         $category_id = Flight::request()->query['category_id'] ?? null;
     
         $products = Flight::get('product_service')->get_all_products($search, $sort, $min_price, $max_price, $category_id);
     
         ResponseHelper::handleServiceResponse($products);
     });
 
     /**
     * @OA\Delete(
     *     path="/products/delete/{product_id}",
     *     summary="Delete a product by ID.",
     *     description="Deletes a product based on the provided product ID.",
     *     tags={"Products"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product successfully deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="You have successfully deleted the product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid product ID")
     *         )
     *     ),
     * )
     */
     Flight::route('DELETE /delete/@product_id', function ($product_id) {
         Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
         $product_service = new productService();
         $result = $product_service->delete_product($product_id);
         ResponseHelper::handleServiceResponse($result, "You have successfully deleted the product");
     });
     
     
     /**
     * @OA\Put(
     *     path="/products/update/{id}",
     *     summary="Update a product by ID.",
     *     description="Updates the details of an existing product based on the provided product ID.",
     *     tags={"Products"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category_id", "quantity", "price_each", "description"},
     *             @OA\Property(property="name", type="string", example="Red Rose Bouquet", description="Updated name of the product"),
     *             @OA\Property(property="category_id", type="integer", example=1, description="Updated category ID of the product"),
     *             @OA\Property(property="quantity", type="integer", example=50, description="Updated available quantity of the product"),
     *             @OA\Property(property="price_each", type="number", format="float", example=25.99, description="Updated price of each unit of the product"),
     *             @OA\Property(property="description", type="string", example="A beautiful bouquet of red roses.", description="Updated description of the product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product successfully updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1, description="ID of the updated product"),
     *             @OA\Property(property="name", type="string", example="Red Rose Bouquet", description="Updated name of the product"),
     *             @OA\Property(property="category_id", type="integer", example=1, description="Updated category ID of the product"),
     *             @OA\Property(property="quantity", type="integer", example=50, description="Updated available quantity of the product"),
     *             @OA\Property(property="price_each", type="number", format="float", example=25.99, description="Updated price of each unit of the product"),
     *             @OA\Property(property="description", type="string", example="A beautiful bouquet of red roses.", description="Updated description of the product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid input")
     *         )
     *     ),
     * )
     */
     Flight::route('PUT /update/@id', function($id) {
         Flight::auth_middleware()->authorizeRoles(Roles::ADMIN);
         $data = Flight::request()->data->getData();
         $product = Flight::get('product_service')->update_product($id, $data);
         ResponseHelper::handleServiceResponse($product);
     });
     
    /**
     * @OA\Post(
     *     path="/products/upload_image/{product_id}",
     *     summary="Upload an image for a product.",
     *     description="Uploads an image file for the specified product. Only JPG, PNG, or WEBP images are allowed.",
     *     tags={"Products"},
     *     security={{"ApiKey": {}}},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to upload the image for",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Image file to upload",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"product_image"},
     *                 @OA\Property(
     *                     property="product_image",
     *                     type="string",
     *                     format="binary",
     *                     description="The image file to upload (JPG, PNG, WEBP)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product image uploaded successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product image uploaded successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input or file type",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="No file uploaded.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Failed to move uploaded file.")
     *         )
     *     )
     * )
     */
    Flight::route('POST /upload_image/@product_id', function($product_id) {
        Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
        if (!isset($_FILES['product_image'])) {
            Flight::halt(400, 'No file uploaded.');
        }

        $file = $_FILES['product_image'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($file['type'], $allowed)) {
            Flight::halt(400, 'Only JPG, PNG, or WEBP images are allowed.');
        }

        $uploads_dir = __DIR__ . '/../../uploads/';
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_name = uniqid("product_", true) . '.' . $ext;
        $target_path = $uploads_dir . $new_name;

        if (!move_uploaded_file($file['tmp_name'], $target_path)) {
            Flight::halt(500, 'Failed to move uploaded file.');
        }

        // Save image path to product_image table
        $relative_url = '/uploads/' . $new_name;
        $product_service = Flight::get('product_service');
        $result = $product_service->add_product_image([
            'product_id' => $product_id,
            'image' => $relative_url
        ]);

        ResponseHelper::handleServiceResponse($result, 'Product image uploaded successfully.');
    });
 
 });