<?php

spl_autoload_register(function ($class) {
    $path = null;
    $aClass = explode('\\', $class);
    if($aClass[0] !== 'App'){
        throw new Exception('Неизвестное пространство имен');
    }
    array_shift($aClass);
    $path = implode(DIRECTORY_SEPARATOR, $aClass);
    $file = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $path.'.php';
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    throw new Exception('Класс ' . $class . ' не найден');
});