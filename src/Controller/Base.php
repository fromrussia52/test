<?php

namespace App\Controller;

use App\DB\Connection;
use App\DB\Billing;
use App\DB\Users;

class Base
{
    private $users = null;
    private $billing = null;

    public function __construct()
    {
        $conn = (new Connection())->connect();
        $this->users = new Users($conn);
        $this->billing = new Billing($conn);
    }

    public function actionLogin()
    {
        $login = $_POST['login'];
        $password = $_POST['password'];
        if (preg_match('/[a-z][0-9a-z]*/i', $login) !== 1) {
            throw new \Exception('Ошибка валидации логина');
        }
        if (preg_match('/.{6,}/i', $password) !== 1) {
            throw new \Exception('Длина пароля должна быть больше 6 символов');
        }
        if ($this->users->login($login, $password) === false) {
            throw new \Exception('Ошибка аутентификации', 401);
        }
        $_SESSION['login'] = $login;
    }

    public function actionRegistrate()
    {
        $login = 'login1';
        $password = 'password1';
        $this->users->registrateUser($login, $password);
    }

    public function actionIsauth()
    {
        if (isset($_SESSION['login'])) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function actionLogout()
    {
        unset($_SESSION['login']);
        session_destroy();
        echo 'true';
    }

    public function actionBalans()
    {
        $login = $_SESSION['login'];
        $userInfo = $this->users->getUser($login);
        if (empty($userInfo)) {
            throw new \Exception('Пользователь не найден!');
        }
        echo $this->billing->getBalans($userInfo['id']);
    }

    public function actionPulloff()
    {
        $value = $_GET['value'];
        if (empty($value)) {
            throw new \Exception('Значение не может быть пустым');
        }
        if (preg_match('/[0-9\.]+/i', $value) !== 1) {
            throw new \Exception('Ошибка валидации значения');
        }
        $login = $_SESSION['login'];
        session_write_close();
        $userInfo = $this->users->getUser($login);
        if (empty($userInfo)) {
            throw new \Exception('Пользователь не найден!');
        }
        echo $this->billing->pullOff($value, $userInfo['id']);
    }
}
