<?php

namespace App\Core;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $config = include CONFIG_PATH . '/database.php';
        $dbConfig = $config['connections'][$config['default']];

        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
        
        try {
            $this->connection = new \PDO(
                $dsn,
                $dbConfig['username'],
                $dbConfig['password'],
                $dbConfig['options']
            );
        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance->connection;
    }

    private function __clone() {}
    
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
