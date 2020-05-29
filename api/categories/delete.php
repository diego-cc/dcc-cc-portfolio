<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio
 * File: api/categories/delete.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-26
 * Version: 1.0.0
 * Description: Delete a category with a given ID
 **********************************************************/

namespace DccCcPortfolio;

/**
 * Response headers
 * POST method is also allowed because forms in traditional web pages (i.e. no fancy JS frameworks/libraries) only have GET and POST available
 * Of course, it can be removed if this is not a concern
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE,POST");
header("Content-Type: application/json; charset=UTF-8");

/**
 * Enforce allowed request methods
 */
if (isset($_SERVER['REQUEST_METHOD'])) {
    if (
        $_SERVER['REQUEST_METHOD'] !== 'DELETE' &&
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
 * Include DB and category files
 */
include_once '../../config/Database.php';
include_once '../../classes/Category.php';

/**
 * Read request body
 */
$data = json_decode(file_get_contents("php://input"), false);

/**
 * Get ID from JSON request body
 */
if (isset($data->id)) {
    if (!is_numeric($data->id)) {
        /**
         * ID is non-numerical, return 404
         * 400 would probably be more suitable here
         */
        http_response_code(404);
        echo json_encode(
            array(
                "message" => "id must be a number"
            )
        );
        return;
    }
    $id = $data->id;

    /**
     * Instantiate DB and get its connection
     */
    $database = new Database();
    $db = $database->getConnection();

    /**
     * Instantiate a new category with $db
     */
    $category = new Category($db);

    /**
     * Delete category
     */
    $stmt = $category->delete($id);
    $num = $stmt->rowCount();

    /**
     * Check rows affected
     */
    if ($num > 0) {
        /**
         * All good, row was deleted
         */
        http_response_code(200);

        /**
         * Inform user that a category was deleted
         */
        echo json_encode(array("message" => "Category deleted."));
    } else {
        /**
         * 0 rows affected, return 404 category not found
         */
        http_response_code(404);

        /**
         * Inform user that no category was deleted
         */
        echo json_encode(
            array("message" => "Category not deleted: invalid ID")
        );
    }
} else {
    /**
     * ID not present in the request body
     */
    http_response_code(404);

    echo json_encode(
        array("message" => "No id provided.")
    );
}