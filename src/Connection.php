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
        if ($hash === false) {
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
        if (password_verify($password, $hash) === true) {
            return true;
        } else {
            return false;
        }
    }

    public function getBalans($login)
    {
        $userInfo = $this->getUser($login);
        if (empty($userInfo)) {
            throw new Exception('Пользователь не найден!');
        }

        $st = $this->conn->prepare('select value from billing where user_id=:userId');
        $st->bindParam('userId', $userInfo['id']);
        $st->execute();
        $res = $st->fetch(PDO::FETCH_ASSOC);
        if ($res !== false) {
            return $res['value'];
        } else {
            return null;
        }
    }

    private function getUser($login)
    {
        if (empty($login)) {
            throw new Exception('Значение не должно быть пустым');
        }
        $st = $this->conn->prepare('select * from users where login=:login');
        $st->bindParam('login', $login);
        $st->execute();
        $res = $st->fetch(PDO::FETCH_ASSOC);
        if ($res !== false) {
            return $res;
        } else {
            return null;
        }
    }

    public function pullOff(int $value, $login)
    {
        $userInfo = $this->getUser($login);
        if (empty($userInfo)) {
            throw new Exception('Пользователь не найден!');
        }
        try {
            $this->conn->beginTransaction();
            $this->conn->exec('lock tables billing');
            //get current balans
            $balans = $this->getBalans($login);
            if ((int) $balans === 0) {
                throw new Exception('У Вас отсутствуют средства на балансе!');
            }
            if ((int) $balans < (int) $value) {
                throw new Exception('Сумма для вывода превышает текущий баланс!');
            }
            $newBalans = (int) $balans - (int) $value;
            $st = $this->conn->prepare('update billing set value=:value where user_id=:userId');
            $st->bindParam('value', $newBalans);
            $st->bindParam('userId', $userInfo['id']);
            $st->execute();
            $this->conn->commit();
            $this->conn->exec('unlock tables');
        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->conn->exec('unlock tables');
            throw new Exception($e->getMessage());
        }
    }
}
