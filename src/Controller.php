<?php

class Controller
{
    private $path = null;
    private $db = null;
    private $tmpl = null;

    public function __construct()
    {
        $this->path = $_SERVER['PATH_INFO'] ?? '/';
        $this->db = new Connection();
        $this->tmpl = new Template();
    }

    public function start()
    {
        //routes
        if ($this->path === '/' || $this->path === '/index.php') {
            $this->tmpl->render();
        } else {
            $aPath = explode('/', $this->path);
            array_shift($aPath);
            if ($aPath[0] !== 'api') {
                throw new Exception('Роут ' . $this->path . ' не найден!', 404);
            }

            switch ($aPath[1]) {
                case 'login':
                    $login = $_POST['login'];
                    $password = $_POST['password'];
                    if (preg_match('/[a-z][0-9a-z]*/i', $login) !== 1) {
                        throw new Exception('Ошибка валидации логина');
                    }
                    if (preg_match('/[a-z][0-9a-z_\-\.]*/i', $password) !== 1) {
                        throw new Exception('Ошибка валидации пароля');
                    }
                    if ($this->db->login($login, $password) === false) {
                        throw new Exception('Ошибка аутентификации', 401);
                    }
                    $_SESSION['login'] = $login;
                    break;

                case 'registrate':
                    $login = 'login1';
                    $password = 'password1';
                    $this->db->registrateUser($login, $password);
                    break;

                case 'isauth':
                    if(isset($_SESSION['login'])){
                        echo 'true';
                    }else {
                        echo 'false';
                    }
                    break;
            }
        }
    }
}
