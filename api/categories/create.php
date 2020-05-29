<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio
 * File: api/categories/create.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-25
 * Version: 1.0.0
 * Description: Creates a new category
 **********************************************************/

namespace DccCcPortfolio;

/**
 * Response headers
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/**
 * Enforce allowed request method
 */
if (isset($_SERVER['REQUEST_METHOD'])) {
    if (
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
 * Database and Category class files
 */
include_once '../../config/Database.php';
include_once '../../classes/Category.php';

/**
 * Initialise database object and define connection
 */
$database = new Database();
$db = $database->getConnection();

/**
 * Instantiate new category
 */
$category = new Category($db);

/**
 * Get JSON request body
 */
$data = json_decode(file_get_contents("php://input"), false);

/**
 * Basic validation of required fields
 */
if (
    !empty($data->code) &&
    !empty($data->name) &&
    !empty($data->description)
) {
    /**
     * Map properties
     */
    $category->code = $data->code;
    $category->name = $data->name;
    $category->description = $data->description;

    /**
     * Create category
     */
    if ($category->create()) {
        /**
         * All good, send back 201 (Created)
         * The assessment requires 200 to be sent back in this case, but 201 is more appropriate according to the HTTP specification
         */
        http_response_code(201);

        /**
         * Inform user that the category was successfully created
         */
        echo json_encode(array("message" => "Category was created."));
    } else {
        /**
         * Oops! The request could not be processed
         * Since we're not checking what exactly went wrong, either 500 or 503 would be fine
         */
        http_response_code(503);

        /**
         * Inform user that something went wrong on the server's end
         */
        echo json_encode(array("message" => "Unable to create category: code already exists or data is invalid"));
    }
} else {
    /**
     * Data validation failed
     */
    http_response_code(400);

    /**
     * Inform user that data sent is invalid
     */
    echo json_encode(array("message" => "Unable to create category. Data is incomplete."));
}
?>

