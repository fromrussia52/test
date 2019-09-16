<?php

namespace App\Controller;

use App\DB\Connection;
use App\DB\Users;

class UserController
{
    private $users = null;

    public function __construct()
    {
        $conn = (new Connection())->connect();
        $this->users = new Users($conn);
    }

    public function login()
    {
        $login = $_POST['login'];
        $password = $_POST['password'];
        if (preg_match('/[a-z][0-9a-z]*/i', $login) !== 1) {
            throw new \Exception('Ошибка валидации логина');
        }
        if (mb_strlen($password) < 6) {
            throw new \Exception('Длина пароля не должна быть меньше 6 символов');
        }
        if ($this->users->login($login, $password) === false) {
            throw new \Exception('Ошибка аутентификации', 401);
        }
        $_SESSION['login'] = $login;
    }

    public function registrate()
    {
        $login = 'login1';
        $password = 'password1';
        $this->users->registrateUser($login, $password);
    }

    public function isauth()
    {
        if (isset($_SESSION['login'])) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function logout()
    {
        unset($_SESSION['login']);
        session_destroy();
        echo 'true';
    }
}
