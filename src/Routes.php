<?php

namespace App;

class Routes
{
    private $path = null;
    private $routesMap = [];
    private $scheme = null;
    private $host = null;
    private $method = null;

    public function __construct()
    {
        $this->scheme = $_SERVER["SERVER_PORT"] === '80' ? 'http' : 'https';
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->host = $_SERVER["REMOTE_ADDR"];
        $this->path = $_SERVER['PATH_INFO'] ?? '/';
        $this->routesMap = [
            [
                'route' => '/api/login',
                'name' => 'login',
                'scheme' => '.+',
                'host' => '.+',
                'method' => 'POST',
                'controller' => 'App\\Controller\\UserController',
                'action' => 'login'
            ], [
                'route' => '/api/registrate',
                'name' => 'registrate',
                'scheme' => '.+',
                'host' => '.+',
                'method' => 'POST',
                'controller' => 'App\\Controller\\UserController',
                'action' => 'registrate'
            ], [
                'route' => '/api/isauth',
                'name' => 'isauth',
                'scheme' => '.+',
                'host' => '.+',
                'method' => 'GET',
                'controller' => 'App\\Controller\\UserController',
                'action' => 'isauth'
            ], [
                'route' => '/api/logout',
                'name' => 'logout',
                'scheme' => '.+',
                'host' => '.+',
                'method' => 'GET',
                'controller' => 'App\\Controller\\UserController',
                'action' => 'logout'
            ], [
                'route' => '/api/balans',
                'name' => 'balans',
                'scheme' => '.+',
                'host' => '.+',
                'method' => 'GET',
                'controller' => 'App\\Controller\\BillingController',
                'action' => 'balans'
            ], [
                'route' => '/api/pulloff',
                'name' => 'pulloff',
                'scheme' => '.+',
                'host' => '.+',
                'method' => 'GET',
                'controller' => 'App\\Controller\\BillingController',
                'action' => 'pulloff'
            ], [
                'route' => '/',
                'name' => 'index',
                'scheme' => '.+',
                'host' => '.+',
                'method' => 'GET',
                'controller' => 'App\\Controller\\IndexController',
                'action' => 'index'
            ]
        ];
    }

    public function execute()
    {
        $error = null;
        $markMatch = false;
        $controller = null;
        $action = null;

        foreach ($this->routesMap as $routeInfo) {
            if (strcasecmp($routeInfo['route'], $this->path) !== 0) {
                continue;
            }
            if (preg_match('#' . $routeInfo['method'] . '#i', $this->method) !== 1) {
                $error = 'Разрешен метод ' . $routeInfo['method'];
                break;
            }
            if (preg_match('#' . $routeInfo['scheme'] . '#i', $this->scheme) !== 1) {
                $error = 'Разрешена схема ' . $routeInfo['scheme'];
                break;
            }
            if (preg_match('#' . $routeInfo['host'] . '#i', $this->host) !== 1) {
                $error = 'Разрешен хост ' . $routeInfo['host'];
                break;
            }
            $markMatch = true;
            $controller = $routeInfo['controller'];
            $action = $routeInfo['action'];
            break;
        }

        if ($markMatch === false) {
            throw new \Exception('Роут ' . $this->method . ' ' . $this->path . ' не найден!' . (!empty($error) ? ' ' . $error : ''), 404);
        }

        (new $controller)->{$action}();
    }
}
