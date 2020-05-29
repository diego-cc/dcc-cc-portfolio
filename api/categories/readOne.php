<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio
 * File: api/categories/readOne.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-25
 * Version: 1.0.0
 * Description: Read a category record from the database
 **********************************************************/

namespace DccCcPortfolio;

use PDO;

/**
 * Set up response headers
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
 * Read JSON request body
 */
$data = json_decode(file_get_contents("php://input"), false);

/**
 * Verify whether id was sent
 */
if (isset($data->id)) {
    $id = $data->id;

    /**
     * Instantiate DB and get a new connection
     */
    $database = new Database();
    $db = $database->getConnection();

    /**
     * Initialise category with DB connection
     */
    $category = new Category($db);

    /**
     * Read product with given id from the request
     */
    $stmt = $category->readOne($id);

    /**
     * Get number of records
     */
    $num = $stmt->rowCount();

    /**
     * Check whether any records were found
     */
    if ($num > 0) {
        /**
         * Store records here
         */
        $categoriesList["records"] = [];

        /**
         * Map records found
         */
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $record = array(
                "id" => $row->id,
                "code" => $row->code,
                "name" => $row->name,
                "description" => html_entity_decode($row->description),
                "created_at" => $row->created_at,
                "updated_at" => $row->updated_at,
                "deleted_at" => $row->deleted_at
            );

            /**
             * Push record into categoriesList
             */
            $categoriesList['records'][] = $record;
        }

        /**
         * All good, send back 200 OK
         */
        http_response_code(200);

        /**
         * Send back JSON response with category records (in practice, should be only 1 record)
         */
        echo json_encode($categoriesList);
    } else {
        /**
         * No records were found, send back 404
         */
        http_response_code(404);

        /**
         * Inform user that no records were found
         */
        echo json_encode(
            array("message" => "No category found.")
        );
    }
} else {
    /**
     * ID not provided, send back 404
     */
    http_response_code(404);

    /**
     * Inform user that ID is missing
     */
    echo json_encode(
        array("message" => "No id provided.")
    );
}
?>