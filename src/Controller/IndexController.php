<?php

namespace App\Controller;

use App\Template\Renderer;

class IndexController
{
    private $tmpl = null;

    public function __construct()
    {
        $this->tmpl = new Renderer();
    }

    public function index()
    {
        $this->tmpl->render('index', ['title' => 'Тестовое приложение']);
    }
}