<?php

require_once './autoloader.php';

session_start();

$kernel = new App\Kernel();
$kernel->handle();