<?php

namespace App;

use App\Controller\Base;
use App\Template\Renderer;

class Routes
{ 
    private $path = null;
    private $tmpl = null;
    private $controller = null;
    private $routes = [];

    public function __construct(Base $controller)
    {
        $this->path = $_SERVER['PATH_INFO'] ?? '/';
        $this->tmpl = new Renderer();
        $this->controller = $controller;
        $this->routes = ['login', 'registrate', 'isauth', 'logout', 'balans', 'pulloff'];
    }

    public function start()
    {
        if ($this->path === '/' || $this->path === '/index.php') {
            $this->tmpl->render('index', ['title' => 'Тестовое приложение']);
        } else {
            $aPath = explode('/', $this->path);
            array_shift($aPath);
            if ($aPath[0] !== 'api' || count($aPath) > 2) {
                throw new \Exception('Роут ' . $this->path . ' не найден!', 404);
            }

            if(!in_array($aPath[1], $this->routes)){
                throw new \Exception('Роут ' . $this->path . ' не найден!', 404);
            }
            
            $method = 'action' . ucfirst($aPath[1]);
            $this->controller->{$method}();
        }
    }
}
