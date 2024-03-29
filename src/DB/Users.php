<?php

namespace App\DB;

class Users
{
    private $conn = null;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function registrateUser($login, $password)
    {
        if ($this->isUserExists($login) === true) {
            throw new \Exception('Данный пользователь уже зарегистрирован!');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($hash === false) {
            throw new \Exception('Ошибка получения хэша пароля');
        }
        $st = $this->conn->prepare('insert into users(login, password) values(:login, :password)');
        $st->bindParam('login', $login);
        $st->bindParam('password', $hash);
        $st->execute();
        if ($st->errorCode() != '0000') {
            throw new \Exception($st->errorInfo()[2]);
        }
    }

    private function isUserExists($login)
    {
        $st = $this->conn->prepare('select count(*) from users where login=:login');
        $st->bindParam('login', $login);
        $st->execute();
        $res = $st->fetch(\PDO::FETCH_ASSOC);
        if ($res['count(*)'] == '0') {
            return false;
        } else {
            return true;
        }
    }

    public function login($login, $password)
    {
        if ($this->isUserExists($login) === false) {
            throw new \Exception('Данный пользователь не существует!');
        }

        $st = $this->conn->prepare('select password from users where login=:login');
        $st->bindParam('login', $login);
        $st->execute();
        $res = $st->fetch(\PDO::FETCH_ASSOC);
        $hash = $res['password'];
        if (password_verify($password, $hash) === true) {
            return true;
        } else {
            return false;
        }
    }

    public function getUser($login)
    {
        if (empty($login)) {
            throw new \Exception('Значение не должно быть пустым');
        }
        $st = $this->conn->prepare('select * from users where login=:login');
        $st->bindParam('login', $login);
        $st->execute();
        $res = $st->fetch(\PDO::FETCH_ASSOC);
        if ($res !== false) {
            return $res;
        } else {
            return null;
        }
    }
}
