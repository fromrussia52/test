<?php

namespace App\DB;

class Connection
{
    private $dbHost = null;
    private $dbPort = null;
    private $dbName = null;
    private $dbUser = null;
    private $dbPassword = null;
    private $conn = null;

    public function __construct()
    {
        $this->dbHost = '127.0.0.1';
        $this->dbPort = 3306;
        $this->dbName = 'test';
        $this->dbUser = 'test';
        $this->dbPassword = 'test';
    }

    public function connect()
    {
        $dsn = 'mysql:dbname=' . $this->dbName . ';host=' . $this->dbHost;
        try {
            if(!$this->conn){
                return $this->conn = new \PDO($dsn, $this->dbUser, $this->dbPassword);
            } else {
                return $this->conn;
            }
        } catch (PDOException $e) {
            throw new \Exception('Ошибка при подключении к БД: ' . $e->getMessage());
        }
    }
}
