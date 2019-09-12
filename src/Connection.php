<?php

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

        $dsn = 'mysql:dbname=' . $this->dbName . ';host=' . $this->dbHost;
        try {
            $this->conn = new PDO($dsn, $this->dbUser, $this->dbPassword);
        } catch (PDOException $e) {
            throw new Exception('Ошибка при подключении к БД: ' . $e->getMessage());
        }
    }

    public function registrateUser($login, $password)
    {
        if ($this->isUserExists($login) === true) {
            throw new Exception('Данный пользователь уже зарегистрирован!');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if($hash === false){
            throw new Exception('Ошибка получения хэша пароля');
        }
        $st = $this->conn->prepare('insert into users(login, password) values(:login, :password)');
        $st->bindParam('login', $login);
        $st->bindParam('password', $hash);
        $st->execute();
        if ($st->errorCode() != '0000') {
            throw new Exception($st->errorInfo()[2]);
        }
    }

    private function isUserExists($login)
    {
        $st = $this->conn->prepare('select count(*) from users where login=:login');
        $st->bindParam('login', $login);
        $st->execute();
        $res = $st->fetch(PDO::FETCH_ASSOC);
        if ($res['count(*)'] == '0') {
            return false;
        } else {
            return true;
        }
    }

    public function login($login, $password)
    {
        if ($this->isUserExists($login) === false) {
            throw new Exception('Данный пользователь не существует!');
        }

        $st = $this->conn->prepare('select password from users where login=:login');
        $st->bindParam('login', $login);
        $st->execute();
        $res = $st->fetch(PDO::FETCH_ASSOC);
        $hash = $res['password'];
        if(password_verify($password, $hash) === true) {
            return true;
        } else {
            return false;
        }
    }
}
