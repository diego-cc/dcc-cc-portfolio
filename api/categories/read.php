<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio
 * File: api/categories/read.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-25
 * Version: 1.0.0
 * Description: Read all categories from the database
 **********************************************************/

namespace DccCcPortfolio;

use PDO;

/**
 * Response headers
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");

/**
 * Enforce allowed request method
 */
if (isset($_SERVER['REQUEST_METHOD'])) {
    if (
        $_SERVER['REQUEST_METHOD'] !== 'GET'
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
 * Read categories from db
 */
$stmt = $category->read();

/**
 * Get row count
 */
$num = $stmt->rowCount();

/**
 * Check record count
 */
if ($num > 0) {
    /**
     * Store records in an array
     */
    $categories["records"] = [];

    /**
     * Get records from categories table
     */
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        $category_record = array(
            "id" => $row->id,
            "code" => $row->code,
            "name" => $row->created_at,
            "description" => $row->description,
            "created_at" => $row->created_at,
            "updated_at" => $row->updated_at,
            "deleted_at" => $row->deleted_at
        );

        /**
         * Push category_record into the categories array
         */
        $categories["records"][] = $category_record;
    }

    /**
     * All good, send 200 OK
     */
    http_response_code(200);

    /**
     * Send categories as JSON in the response body
     */
    echo json_encode($categories);
} else {
    /**
     * No records were found, send 404
     */
    http_response_code(404);

    /**
     * Send an error message as JSON as well
     */
    echo json_encode(
        array(
            "message" => "No category records were found"
        )
    );
}
?>