<?php

session_start();

require_once './src/Controller.php';
require_once './src/Connection.php';
require_once './src/Template.php';

try {
    $controller = new Controller();
    $controller->start();
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
