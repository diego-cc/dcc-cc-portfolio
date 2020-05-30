<?php

/**********************************************************
 * Project:     api-practice
 * File:        api/classes/Product.php
 * Author:      Adrian <Adrian@tafe.wa.edu.au>
 * Date:        2020-05-06
 * Version:     1.0.0
 * Description: Product Class
 **********************************************************/

namespace DccCcPortfolio;

use PDO;
use PDOStatement;

class Product
{
    /**
     * Database connection and table name
     */
    /** @var */
    private $conn;
    /** @var string */
    private $tableName = "products";

    /**
     * Product object properties
     */
    /** @var */
    public $id;
    /** @var */
    public $name;
    /** @var */
    public $description;
    /** @var */
    public $price;
    /** @var */
    public $categoryID;
    /** @var */
    public $categoryName;
    /** @var */
    public $image;
    /** @var */
    public $createdAt;
    /** @var */
    public $updatedAt;

    /**
     * Product constructor.
     * Takes a database connection as an argument
     * Use: `$prods = new Product($connection);`
     *
     * @param $db PDO
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * @param  string  $text
     * @return string
     */
    private function sanitize(string $text): string
    {
        return htmlspecialchars(strip_tags($text));
    }

    /**
     * Read and return product data
     * Use: `$productList = $prods->read();`
     *
     * @return array('error' => string, 'message' => string, 'stmt' => \PDOStatement)
     */
    public function read()
    {
        // select all query
        $query = "SELECT 
                    c.name as category_name, p.id, p.name, p.description, 
                    p.price,  p.category_id, p.created_at, p.updated_at
                 FROM {$this->tableName} AS p
                    LEFT JOIN categories AS c
                        ON p.category_id = c.id
                 ORDER BY p.created_at DESC;";

        // prepare, bind named parameter, and execute query
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $results = [];
        try {
            $stmt = $this->conn->prepare($query);
            if ($stmt->execute()) {
                $results = ['error' => false, 'stmt' => $stmt];
            } else {
                $results = ['error' => true, 'message' => 'Could not retrieve products: invalid query'];
            }
        } catch (\PDOException $e) {
            $results = ['error' => true, 'message' => 'Could not retrieve products: connection failed'];
        } finally {
            return $results;
        }
    }

    /**
     * Read and return product data for a given ID
     * Use: `$productList = $prods->readOne($id);`
     * @param $id
     * @return bool|PDOStatement
     */
    public function readOne($id)
    {
        // select ONE query
        $query = "
            SELECT 
                c.name as category_name, p.id, p.name, p.description, 
                p.price,  p.category_id, p.created_at, p.updated_at
            FROM {$this->tableName} AS p
                LEFT JOIN categories AS c
                    ON p.category_id = c.id
            WHERE p.id = :productID
            ORDER BY p.created_at DESC;";

        // prepare, bind named parameter, and execute query
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productID', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }


    /**
     * Create Method inserts a new record into the table
     * Use: `$product->create()`
     *
     * @return bool
     */
    public function create()
    {
        // query to insert record
        $query = "
            INSERT INTO {$this->tableName}(`id`, `name`, `description`, `price`, 
                        `category_id`, `created_at`, `updated_at`)
            VALUES (null, :prodName, :prodDescription, :prodPrice, :categoryID, now(), NULL);";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = $this->sanitize($this->name);
        $this->price = $this->sanitize($this->price);
        $this->description = $this->sanitize($this->description);
        $this->categoryID = $this->sanitize($this->categoryID);

        // bind values
        $stmt->bindParam(":prodName", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":prodPrice", $this->price, PDO::PARAM_STR);
        $stmt->bindParam(":prodDescription", $this->description, PDO::PARAM_STR);
        $stmt->bindParam(":categoryID", $this->categoryID, PDO::PARAM_INT);

        // execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Update Product
     * The Product data is retrieved from the private variables
     *
     * @return bool
     */
    public function update()
    {
        // query to insert record
        $query = "
            UPDATE {$this->tableName}
            SET 
                name = :prodName, 
                description = :prodDescription,
                price = :prodPrice,
                category_id = :categoryID,
                updated_at = now()
             WHERE id=:prodID;";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = Utils::sanitize(($this->id));
        $this->name = Utils::sanitize(($this->name));
        $this->price = Utils::sanitize(($this->price));
        $this->description = Utils::sanitize(($this->description));
        $this->categoryID = Utils::sanitize(($this->categoryID));

        // bind values
        $stmt->bindParam(":prodID", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":prodName", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":prodPrice", $this->price, PDO::PARAM_STR);
        $stmt->bindParam(":prodDescription", $this->description, PDO::PARAM_STR);
        $stmt->bindParam(":categoryID", $this->categoryID, PDO::PARAM_INT);

        // execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    /**
     * Delete a product with given ID
     * Use: `$productList = $prods->delete($id);`
     *
     * @param $id
     */
    public function delete($id)
    {
        // select ONE query
        $query = "
            DELETE FROM {$this->tableName}
            WHERE id = :productID
            ";

        // prepare, bind named parameter, and execute query
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productID', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt;
    }


    /**
     * Read and return the products with the provided search
     * text contained in the product name or description. If
     * empty search text given, then all products are returned.
     *
     * Use: `$productList = $prods->read($findThis);`
     *
     * @param  string  $searchText
     * @return bool|PDOStatement
     */
    public function search(string $searchText)
    {
        $searchDescription = $searchName = '%'.$this->sanitize($searchText).'%';

        $query = "
            SELECT 
                c.name as category_name, p.id, p.name, p.description, 
                p.price,  p.category_id, p.created_at, p.updated_at
            FROM {$this->tableName} AS p
                LEFT JOIN categories AS c
                    ON p.category_id = c.id
            WHERE p.name LIKE :prodName
            OR p.description LIKE :prodDescription
            ORDER BY p.created_at DESC;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':prodName', $searchName, PDO::PARAM_STR);
        $stmt->bindParam(':prodDescription', $searchDescription, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt;
    }

}
