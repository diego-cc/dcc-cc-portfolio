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
     * @return array
     * A result that indicates whether an error has occurred and its message.
     * Examples:
     *
     * ["error" => false]
     *
     * ["error" => true, "message" => "Could not connect to database"]
     *
     */
    public function create()
    {
        /**
         * Sanitise values
         */
        $this->code = Utils::sanitize($this->code);
        $this->name = Utils::sanitize($this->name);
        $this->icon = Utils::sanitize($this->icon);
        $this->description = Utils::sanitize($this->description);

        /**
         * INSERT a new category
         */
        $query = '';

        if (!$this->icon) {
            $query = "
            INSERT INTO {$this->tableName}(`id`, `code`, `name`, `description`,
                        `created_at`, `updated_at`, `deleted_at`)
            VALUES (null, :code, :name, :description, now(), NULL, NULL);";
        } else {
            $query = "
            INSERT INTO {$this->tableName}(`id`, `code`, `name`, `icon`, `description`,
                        `created_at`, `updated_at`, `deleted_at`)
            VALUES (null, :code, :name, :icon, :description, now(), NULL, NULL);";
        }

        /**
         * Prepare INSERT statement
         */
        $stmt = $this->conn->prepare($query);

        /**
         * Bind sanitised values
         */
        $stmt->bindParam(":code", $this->code, PDO::PARAM_STR);
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":description", $this->description, PDO::PARAM_STR);

        if ($this->icon) {
            $stmt->bindParam(":icon", $this->icon, PDO::PARAM_STR);
        }

        /**
         * Execute query
         */
        try {
            if ($stmt->execute()) {
                return ['error' => false];
            }
            return ['error' => true, 'message' => 'Could not add category. Please try again later.'];
        } catch (\PDOException $ex) {
            return ['error' => true, 'message' => $ex->getMessage()];
        } catch (\Exception $ex) {
            return ['error' => true, 'message' => $ex->getMessage()];
        }
    }

    /**
     * Read and return category data for a given ID
     * Use: `$categoriesList = $cats->readOne($id);`
     * @param $id
     * @return array
     */
    public function readOne($id)
    {
        /**
         * SELECT a category
         */
        $query = "SELECT 
                c.id, c.code, c.name, c.description, c.icon, 
                c.created_at,  c.updated_at, c.deleted_at
            FROM {$this->tableName} AS c
            WHERE c.id = :id
            LIMIT 1;";

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
        if ($stmt->execute()) {
            $cat = $stmt->fetch(PDO::FETCH_OBJ);

            if ($cat) {
                // category found, return it
                return ['error' => false, 'category' => $cat];
            }
            // category not found, return error
            return ['error' => true, 'message' => ['Warning' => 'Category not found']];
        }
        // statement failed to execute, return error
        return [
            'error' => true,
            'message' => [['Warning' => 'Could not retrieve category from database. Please try again later.']]
        ];
    }

    /**
     * Get all products that belong to this category
     * @param  int  $limit  Optionally limit the number of results (e.g. for pagination)
     * @param  int  $rowsToSkip  Optionally skip rows
     * @return array Query results in the format ('error' => bool, 'message' => string, 'stmt' => PDOStatement)
     */
    public function readAssociatedProducts(int $limit = -1, int $rowsToSkip = 0)
    {
        $q = "";
        $stmt = "";
        $this->id = Utils::sanitize($this->id);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($limit) && $limit > 0 && $rowsToSkip >= 0) {
            $limit = Utils::sanitize($limit);
            $q = "SELECT * FROM products AS p WHERE p.category_id = :id LIMIT :rowsToSkip, :limit";
            $stmt = $this->conn->prepare($q);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':rowsToSkip', $rowsToSkip, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        } else {
            $q = "SELECT * FROM products AS p WHERE p.category_id = :id";
            $stmt = $this->conn->prepare($q);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        }

        try {
            if ($stmt->execute()) {
                return ['error' => false, 'stmt' => $stmt];
            }
            return ['error' => true, 'message' => 'Could not retrieve products: failed to execute query'];
        } catch (\PDOException $ex) {
            return ['error' => true, 'message' => 'Could not connect to database'];
        }
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
                icon = :icon,
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
        $this->icon = Utils::sanitize($this->icon);

        /**
         * Bind params
         */
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":code", $this->code, PDO::PARAM_STR);
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":description", $this->description, PDO::PARAM_STR);
        $stmt->bindParam(":icon", $this->icon, PDO::PARAM_STR);

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
        if (!isset($id)) $id = $this->id;

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

    /**
     * @return array|bool[]
     */
    public function getIconImage()
    {
        // Get icon extension
        $ext = pathinfo($this->icon, PATHINFO_EXTENSION);

        if ($ext === 'png') {
            // icon extension is valid, check if it was uploaded
            try {
                date_default_timezone_set('Australia/Perth');

                // Instead of using a local path, the format below can be useful if images are hosted elsewhere
                // It can also avoid potential headaches with nested paths
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';

                $filePath = $protocol.$_SERVER['SERVER_NAME'].'/app/categories/uploads/'.date_format(
                        new \DateTime($this->createdAt),
                        'd-m-Y'
                    ).'/'.sha1($this->icon.'_'.$this->code).'.png';

                // If the image size can be retrieved, that means it exists!
                // Otherwise, don't throw an error (@), return false instead
                // See: https://stackoverflow.com/a/15132482
                if (@getimagesize($filePath)) {
                    // return icon path
                    return ['found' => true, 'path' => $filePath];
                }
            } catch (\Exception $e) {
                return ['found' => false];
            }
        }
        // icon was not found, show its name anyway
        // a placeholder icon could be displayed here as well
        return ['found' => false];
    }

    /**
     * Saves uploaded images
     * @param  array  $img  Array containing image data, from $_FILES
     * @param  string  $uploadDir  Upload directory
     * @return  array   Results: ['error' => bool, 'messages' => array]
     */
    public function saveIconImage($img, $uploadDir)
    {
        // if upload directory doesn't exist, create it recursively
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // hash icon with corresponding category code
        // not perfect, but since $code is unique should be good enough for now
        $uploadedFile = $uploadDir.sha1($this->icon.'_'.$this->code).'.png';

        // save icon / show warning message if it could not be saved
        if (isset($img['tmp_name'])) {
            if (!move_uploaded_file($img['tmp_name'], $uploadedFile)) {
                return [
                    'error' => true,
                    'messages' => [
                        [
                            'Warning' =>
                                'Could not save image. Please try again or contact your server administrator.'
                        ]
                    ]
                ];
            }
            return [
                'error' => false,
                'messages' => [
                    [
                        'Success' => 'Image successfully saved'
                    ]
                ]
            ];
        }
        return [
            'error' => true,
            'messages' => [
                [
                    'Warning' => 'Invalid image path'
                ]
            ]
        ];
    }

    /**
     * @param  string  $action  Action type, e.g. "CREATE" or "UPDATE"
     * @param  string  $code
     * @param  string  $name
     * @param  string  $icon
     * @param  string  $description
     * @return array   Save request results in the format: ['error' => bool, 'messages' => array, 'fields' => array]
     * @throws \Exception If $action is invalid, an exception is thrown
     */
    public function handleSaveRequest(string $action, string $code, string $name, string $icon, string $description)
    {
        date_default_timezone_set('Australia/Perth');
        $uploadDir = 'uploads/'.date_format(new \DateTime($this->createdAt), 'd-m-Y').'/';
        $uploadedFile = '';
        $messages = [];

        // Form validation
        if ($_POST) {
            // Icon validation
            if ($_FILES['icon']['error'] === UPLOAD_ERR_OK) {
                $img = $_FILES['icon'];

                $imageType = $img['type'];
                $imageSizeMB = $img['size'] / (1024 * 1024);

                $imgSize = getimagesize($img['tmp_name']);
                $imageX = $imgSize[0];
                $imageY = $imgSize[1];

                $icon = Utils::sanitize($img['name']);

                if ($imageType !== 'image/png') {
                    $messages[] = ['Danger' => 'Invalid image format. Only PNG is allowed'];
                }

                if ($imageX > 256 || $imageY > 256) {
                    $messages[] = ['Danger' => 'Only icons up to 256x256 are allowed'];
                }

                if ($imageSizeMB > 2) {
                    $messages[] = ['Danger' => 'Only icons up to 2 MB are allowed'];
                }

                if (strlen($icon) > 255 || strlen(trim($icon)) === '.png') {
                    $messages[] = ['Warning' => 'Invalid icon filename. Up to 255 characters are allowed'];
                }
            }

            // Code validation
            if (isset($_POST['code'])) {
                $code = Utils::sanitize($_POST['code']);

                if (strlen(trim($code)) <= 0) {
                    $messages[] = ['Danger' => 'Please provide a category code'];
                }

                if (strlen(trim($code)) !== 4) {
                    $messages[] = ['Danger' => 'Exactly 4 characters are required for the category code'];
                }
            }

            // Name validation
            if (isset($_POST['name'])) {
                $name = Utils::sanitize($_POST['name']);

                if (strlen(trim($name)) <= 0) {
                    $messages[] = ['Danger' => 'Please provide a category name'];
                }

                if (strlen($name) > 32) {
                    $messages[] = ['Danger' => 'Only category names up to 32 characters are allowed'];
                }
            }

            // Description validation
            if (isset($_POST['description'])) {
                $description = Utils::sanitize($_POST['description']);

                if (strlen(trim($description)) <= 0) {
                    $messages[] = ['Danger' => 'Please provide a description'];
                }

                if (strlen($description) > 255) {
                    $messages[] = ['Danger' => 'Only descriptions up to 255 characters are allowed'];
                }
            }

            // No errors, save data
            if (empty($messages)) {
                if (strtoupper(trim($action)) === 'CREATE') {
                    $this->code = $code;
                    $this->name = $name;
                    $this->icon = !empty(trim($icon)) ? $icon : $this->icon;
                    $this->description = $description;

                    $result = $this->create();

                    if (!$result['error']) {
                        // category was added to database, save icon
                        $saveImageResults = $this->saveIconImage($img, $uploadDir);

                        if ($saveImageResults['error']) {
                            $messages[] = $saveImageResults['messages'][0];
                        }
                        $messages[] = ['Success' => 'Category successfully added'];
                        return [
                            'error' => false,
                            'messages' => $messages,
                            'fields' => ['code' => $code, 'name' => $name, 'description' => $description]
                        ];
                    } else {
                        // category was not added, show error
                        $messages[] = ['Warning' => 'Could not save changes: '.$result['message']];
                    }
                } else {
                    if (strtoupper(trim($action)) === 'UPDATE') {
                        // check properties to avoid over-posting requests
                        if (
                            empty(trim($icon)) &&
                            $this->code === $code &&
                            $this->description === $description &&
                            $this->name === $name
                        ) {
                            $messages[] = [
                                'Warning' => 'Nothing to update'
                            ];
                            return [
                                'error' => true,
                                'messages' => $messages,
                                'fields' => [
                                    'code' => $code,
                                    'name' => $name,
                                    'description' => $description
                                ]
                            ];
                        }
                        $this->name = $name;
                        $this->description = $description;
                        $oldIcon = $this->icon;
                        $this->icon = !empty(trim($icon)) && $this->icon !== $icon ? $icon : $this->icon;

                        $oldCode = $this->code;

                        if ($oldCode !== $code) {
                            if ($oldIcon !== $this->icon) {
                                // both code and icon have changed, save image
                                $this->code = $code;
                                $imgSaveResult = $this->saveIconImage($img, $uploadDir);

                                if ($imgSaveResult['error']) {
                                    $messages[] = $imgSaveResult['messages'][0];
                                }
                            } else {
                                // code has changed but icon hasn't, simply update the hash of the existing image
                                $iconResult = $this->getIconImage();

                                if ($iconResult['found']) {
                                    // image found, update hash
                                    $imgFilename = 'uploads/'.
                                        date_format(new \DateTime($this->createdAt), 'd-m-Y').
                                        '/'.
                                        basename($iconResult['path']);
                                    $newImgFilename = 'uploads/'.
                                        date_format(new \DateTime($this->createdAt), 'd-m-Y').
                                        '/'.
                                        sha1($this->icon.'_'.$code).
                                        '.png';

                                    rename($imgFilename, $newImgFilename);
                                }
                            }
                        } else {
                            if ($oldIcon !== $this->icon) {
                                // code hasn't changed, but icon has. save it.
                                $this->code = $code;
                                $this->icon = $icon;

                                $imgSaveResult = $this->saveIconImage($img, $uploadDir);

                                if ($imgSaveResult['error']) {
                                    $messages[] = $imgSaveResult['messages'][0];
                                }
                            }
                        }

                        $this->code = $code;
                        if ($this->update()) {
                            return [
                                'error' => false,
                                'messages' => [['Success' => 'Category successfully updated']],
                                'fields' => ['code' => $code, 'name' => $name, 'description' => $description]
                            ];
                        } else {
                            return [
                                'error' => true,
                                'messages' => [['Warning' => 'Failed to update category. Verify that the information entered is correct and try again.']],
                                'fields' => ['code' => $code, 'name' => $name, 'description' => $description]
                            ];
                        }
                    } else {
                        throw new \Exception('Invalid action');
                    }
                }
            }
            return [
                'error' => true,
                'messages' => $messages,
                'fields' => ['code' => $code, 'name' => $name, 'description' => $description]
            ];
        }
        return [
            'error' => true,
            'messages' => [['Danger' => 'No data sent']],
            'fields' => ['code' => $code, 'name' => $name, 'description' => $description]
        ];
    }
}

?>