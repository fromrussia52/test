<?php

namespace App\Controller;

use App\DB\Connection;
use App\DB\Billing;
use App\DB\Users;

class BillingController
{
    private $billing = null;
    private $users = null;

    public function __construct()
    {
        $conn = (new Connection())->connect();
        $this->billing = new Billing($conn);
        $this->users = new Users($conn);
    }

    public function balans()
    {
        $login = $_SESSION['login'];
        $userInfo = $this->users->getUser($login);
        if (empty($userInfo)) {
            throw new \Exception('Пользователь не найден!');
        }
        echo $this->billing->getBalans($userInfo['id']);
    }

    public function pulloff()
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
