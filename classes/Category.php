<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio
 * File: classes/Category.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-25
 * Version: 1.0.0
 * Description: Model class for a category
 **********************************************************/

namespace DccCcPortfolio;

include_once 'Utils.php';

use PDO;
use PDOStatement;

/**
 * Category model class
 * @package DccCcPortfolio
 */
class Category
{
    /**
     * Database connection
     * @var PDO
     */
    private $conn;

    /**
     * Table name in the database
     * @var string
     */
    private $tableName = 'categories';

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * File name with extension
     * @var string
     */
    public $icon;

    /**
     * @var \DateTime | string
     */
    public $createdAt;

    /**
     * @var \DateTime | string
     */
    public $updatedAt;

    /**
     * @var \DateTime | string
     */
    public $deletedAt;

    /**
     * Category constructor.
     * Use: `$category = new Category($db)`
     * @param  PDO  $db  PDO connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Read and return all categories as a PDO statement
     * Use: `$categoriesList = $category->read();`
     *
     * @return bool|PDOStatement
     */
    public function read()
    {
        /**
         * SELECT all categories
         */
        $query = "SELECT 
                    c.id, c.code, c.name, c.description, 
                    c.created_at,  c.updated_at, c.deleted_at
                 FROM {$this->tableName} AS c
                 ORDER BY c.created_at DESC;";

        /**
         * Prepare SELECT statement
         */
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Create a new category
     * Use: `$category->create()`
     *
     * @return bool Whether the operation was successful
     */
    public function create()
    {
        /**
         * INSERT a new category
         */
        $query = "
            INSERT INTO {$this->tableName}(`id`, `code`, `name`, `description`,
                        `created_at`, `updated_at`, `deleted_at`)
            VALUES (null, :code, :name, :description, now(), NULL, NULL);";

        /**
         * Prepare INSERT statement
         */
        $stmt = $this->conn->prepare($query);

        /**
         * Sanitise values
         */
        $this->code = Utils::sanitize($this->code);
        $this->name = Utils::sanitize($this->name);
        $this->description = Utils::sanitize($this->description);

        /**
         * Bind sanitised values
         */
        $stmt->bindParam(":code", $this->code, PDO::PARAM_STR);
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":description", $this->description, PDO::PARAM_STR);

        /**
         * Execute query
         */
        try {
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (\PDOException $ex) {
            return false;
        }
    }

    /**
     * Read and return category data for a given ID
     * Use: `$categoriesList = $cats->readOne($id);`
     * @param $id
     * @return bool|PDOStatement
     */
    public function readOne($id)
    {
        /**
         * SELECT a category
         */
        $query = "SELECT 
                p.id, p.code, p.name, p.description, 
                p.created_at,  p.updated_at, p.deleted_at
            FROM {$this->tableName} AS p
            WHERE p.id = :id;";

        /**
         * Prepare statement
         */
        $stmt = $this->conn->prepare($query);

        /**
         * Bind ID param
         */
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        /**
         * Execute query
         */
        $stmt->execute();

        return $stmt;
    }

    /**
     * Update category
     *
     * @return bool
     */
    public function update()
    {
        /**
         * UPDATE statement
         */
        $query = "
            UPDATE {$this->tableName}
            SET 
                code = :code, 
                name = :name,
                description = :description,
                updated_at = now()
             WHERE id=:id;";

        /**
         * Prepare statement
         */
        $stmt = $this->conn->prepare($query);

        /**
         * Basic sanitation
         */
        $this->id = Utils::sanitize($this->id);
        $this->code = Utils::sanitize($this->code);
        $this->name = Utils::sanitize($this->name);
        $this->description = Utils::sanitize($this->description);

        /**
         * Bind params
         */
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":code", $this->code, PDO::PARAM_STR);
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":description", $this->description, PDO::PARAM_STR);

        /**
         * Execute UPDATE statement
         * Ensure that a category was indeed updated
         */
        try {
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return true;
            }
            return false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a category
     * @param  string  $id  The ID of the category to be deleted
     * @return bool | PDOStatement The result of the query
     */
    public function delete($id)
    {
        /**
         * DELETE statement
         */
        $query = "
            DELETE FROM {$this->tableName}
            WHERE id = :id
            ";

        /**
         * Prepare statement, bind ID and execute query
         */
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt;
        } catch (\PDOException $e) {
            return $stmt;
        }
    }

    /**
     * Search for a category based on its code or description
     * If $searchText is empty or whitespace, all categories match
     * This behaviour can be easily modified like this:
     *
     * `if (!strlen(trim($searchText))) { // handle this case }`
     *
     * Use: `$categoriesList = $cats->search($findThis);`
     *
     * @param  string  $searchText  Raw user search query
     * @return bool|PDOStatement Query results
     */
    public function search(string $searchText)
    {
        $searchDescription = $searchCode = '%'.Utils::sanitize($searchText).'%';

        $query = "
            SELECT 
                c.id, c.code, c.name, c.description, 
                c.created_at,  c.updated_at, c.deleted_at
            FROM {$this->tableName} AS c
            WHERE c.description LIKE :searchDescription
            OR c.code LIKE :searchCode
            ORDER BY c.created_at DESC;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':searchDescription', $searchDescription, PDO::PARAM_STR);
        $stmt->bindParam(':searchCode', $searchCode, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt;
    }
}

?>