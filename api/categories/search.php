<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio
 * File: api/categories/search.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-26
 * Version: 1.0.0
 * Description: Search for a category based on code or description
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
 * Include relevant files
 */
include_once '../../config/Database.php';
include_once '../../classes/Category.php';

/**
 * Verify whether a search query string is present
 */
if (isset($_GET['search'])) {
    $searchText = $_GET['search'];

    $database = new Database();
    $db = $database->getConnection();
    $category = new Category($db);

    // code for search categories starts here
    /**
     * Search for a category with sanitised user input
     */
    $stmt = $category->search($searchText);

    $numRecords = $stmt->rowCount();

    if ($numRecords > 0) {
        // products array
        $categoriesList["records"] = [];

        /**
         * Map each property
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
         * Send back results
         */
        echo json_encode($categoriesList);
    } else {
        /**
         * No results, send back 404 not found
         */
        http_response_code(404);

        /**
         * Inform user that no categories were found
         */
        echo json_encode(
            array("message" => "No categories found.")
        );
    }
}