<?php
/**********************************************************
 * Package: dcc_cc_portfolio
 * Project: dcc-cc-portfolio
 * File: config/Database.php
 * Author: Diego <20026893@tafe.wa.edu.au>
 * Date: 2020-05-25
 * Version: 1.0.0
 * Description: Manages database connection
 **********************************************************/

namespace DccCcPortfolio;

use PDO;

/**
 * Class Database
 * @package DccCcPortfolio
 */
class Database
{
    /** @var string */
    private $dbType = 'mysql';
    /** @var string */
    private $dbName = 'cc_store';
    /** @var string */
    private $dbHost = 'localhost';
    /** @var string */
    private $dbPort = '3306';
    /** @var string */
    private $dbCharSet = 'utf8';
    /** @var string */
    private $dbUser = 'cc_store_user';
    /** @var string */
    private $dbPassword = 'Secret1';
    /** @var string */
    private $dsn = '';
    /** @var */
    public $connection;

    /**
     * Database constructor.
     * Use: `$db = new Database();`
     */
    public function __construct()
    {
        $this->dsn = "{$this->dbType}:dbname={$this->dbName};".
            "host={$this->dbHost};port={$this->dbPort};charset={$this->dbCharSet}";
    }

    /**
     * Create DB connection and return to caller
     * Use: `$connection = $db->getConnection();`
     * @return PDO
     */
    public function getConnection()
    {
        $this->connection = new PDO($this->dsn, $this->dbUser, $this->dbPassword);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        return $this->connection;
    }
}

?>