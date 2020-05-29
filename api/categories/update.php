<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio
 * File: api/categories/update
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-26
 * Version: 1.0.0
 * Description: Update a category record
 **********************************************************/

namespace DccCcPortfolio;

/**
 * Response headers
 * POST method is also allowed because forms in traditional web pages (i.e. no fancy JS frameworks/libraries) only have GET and POST available
 * Of course, it can be removed if this is not a concern
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/**
 * Enforce allowed request methods
 */
if (isset($_SERVER['REQUEST_METHOD'])) {
    if (
        $_SERVER['REQUEST_METHOD'] !== 'PUT' &&
        $_SERVER['REQUEST_METHOD'] !== 'POST'
    ) {
        /**
         * Invalid request method
         */
        http_response_code(405);
        echo json_encode(
            array(
                "message" => "Error: request method not allowed"
            )
        );
        exit;
    }
}

/**
 * Include database and category
 */
include_once '../../config/Database.php';
include_once '../../classes/Category.php';

/**
 * Instantiate DB, get connection and pass it to a new instance of category
 */
$database = new Database();
$db = $database->getConnection();
$category = new Category($db);

/**
 * Extract JSON data from request body
 */
$data = json_decode(file_get_contents("php://input"), false);

/**
 * Basic validation of required fields
 */
if (
    !empty($data->id) &&
    !empty($data->code) &&
    !empty($data->name) &&
    !empty($data->description)
) {
    /**
     * Map each property
     */
    $category->id = $data->id;
    $category->code = $data->code;
    $category->name = $data->name;
    $category->description = $data->description;

    /**
     * Verify that category was updated
     */
    if ($category->update()) {
        /**
         * All good, send back 200 A-OK
         */
        http_response_code(200);

        /**
         * Inform user that the category was updated
         */
        echo json_encode(array("message" => "Category was updated."));
    } else {
        /**
         * Something went wrong, return 500 (Internal Server Error)
         */
        http_response_code(500);

        /**
         * Inform user that the category was not updated
         */
        echo json_encode(array("message" => "Unable to update the category: category not found or invalid data"));
    }
} else {
    /**
     * Validation failed, send back 400 (Bad Request)
     */
    http_response_code(400);

    /**
     * Inform user that some fields are missing
     */
    echo json_encode(array("message" => "Unable to update category. Data is incomplete."));
}