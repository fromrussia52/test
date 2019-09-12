<?php

class Template
{
    private $path = null;

    public function __construct()
    {
        $this->path = __DIR__ . '/../assets/template.html';    
    }

    public function render()
    {
        if(file_exists($this->path)){
            $content = file_get_contents($this->path);
            echo $content;
        } else {
            echo 'Шаблон не найден!';
        }
        exit;
    }
}