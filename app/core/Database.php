<?php
/**
 * Database Wrapper using PDO
 * Project: Teknisi Rumah Tangga
 */

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $port = DB_PORT;
    private $charset = DB_CHARSET;

    private $dbh;
    private $stmt;
    private $error;
    private $isConnected = false;

    private static $sharedDbh = null;
    private static $sharedConnected = false;
    private static $sharedError = null;
    private static $hasAttempted = false;

    public function __construct() {
        if (self::$hasAttempted) {
            $this->dbh = self::$sharedDbh;
            $this->isConnected = self::$sharedConnected;
            $this->error = self::$sharedError;
            return;
        }

        self::$hasAttempted = true;

        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
        $options = [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 2,
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            $this->isConnected = true;
            self::$sharedDbh = $this->dbh;
            self::$sharedConnected = true;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            $this->isConnected = false;
            self::$sharedConnected = false;
            self::$sharedError = $this->error;
        }
    }

    /**
     * Check if database connection is active
     *
     * @return bool
     */
    public function isConnected() {
        return $this->isConnected;
    }

    /**
     * Get connection error message if any
     *
     * @return string|null
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Prepare a SQL statement
     *
     * @param string $sql
     * @return $this|null
     */
    public function query($sql) {
        if (!$this->isConnected) {
            return null;
        }
        $this->stmt = $this->dbh->prepare($sql);
        return $this;
    }

    /**
     * Bind values with automatic type detection
     *
     * @param string $param
     * @param mixed $value
     * @param int|null $type
     * @return void
     */
    public function bind($param, $value, $type = null) {
        if (!$this->stmt) {
            return;
        }
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Execute prepared statement
     *
     * @return bool
     */
    public function execute() {
        if (!$this->stmt) {
            return false;
        }
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * Get result set as array of objects
     *
     * @return array
     */
    public function resultSet() {
        if ($this->execute()) {
            return $this->stmt->fetchAll();
        }
        return [];
    }

    /**
     * Get single record object
     *
     * @return object|false
     */
    public function single() {
        if ($this->execute()) {
            return $this->stmt->fetch();
        }
        return false;
    }

    /**
     * Get affected row count
     *
     * @return int
     */
    public function rowCount() {
        return $this->stmt ? $this->stmt->rowCount() : 0;
    }

    /**
     * Get last inserted ID
     *
     * @return string|false
     */
    public function lastInsertId() {
        return $this->dbh ? $this->dbh->lastInsertId() : false;
    }
}
