<?php

require_once './autoloader.php';

use App\Controller\Base;
use App\Routes;

session_start();

try {
    $controller = new Base();
    $routes = new Routes($controller);
    $routes->start();
} catch (Exception $e) {
    $code = $e->getCode();
    switch ($code) {
        case 401:
            header('HTTP/1.1 ' . $code . ' Unauthorized');
            echo $e->getMessage();
            break;

        case 404:
            header('HTTP/1.1 ' . $code . ' Not Found');
            echo $e->getMessage();
            break;

        default:
            header('HTTP/1.1 500 Internal Server Error');
            echo $e->getMessage();
            break;
    }
}
