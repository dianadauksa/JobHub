<?php

namespace Framework;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

class Database {
    public $connection;

    public function __construct($config) {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];
        
        try {
            $this->connection = new PDO($dsn, $config['username'], $config['password'], $options);
        }
        catch (PDOException $e) {
            throw new Exception("Database connection failed: {$e->getMessage()}");
        }
    }

    /**
     * Query the database
     * 
     * @param string $query
     * @param array $params
     * @return PDOStatement
     * @throws PDOException
     */
    public function query($query, $params = []): PDOStatement {
        try {
            $statement = $this->connection->prepare($query);
            foreach ($params as $key => $value) {
                $statement->bindValue(":$key", $value);
            }
            $statement->execute();
            return $statement;
        }
        catch (PDOException $e) {
            throw new Exception("Database query failed: {$e->getMessage()}");
        }
    }
}