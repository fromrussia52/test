<?php

namespace App\Template;

class Renderer
{
    private $tmplDir = null;

    public function __construct($dir = null)
    {
        if(empty($dir)){
            $this->tmplDir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'assets';    
        }else {
            $this->tmplDir = $dir;
        }
    }

    public function render($name, $vars = [])
    {
        $file = rtrim($this->tmplDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'template.html';
        if(file_exists($file)){
            $content = file_get_contents($file);
            if(count($vars) > 0){
                $patterns = [];
                $replacements = [];
                foreach($vars as $var=>$value){
                    $patterns[] = '/{{' . $var . '}}/';
                    $replacements[] = $value;
                }
                $content = preg_replace($patterns, $replacements, $content);
            }
            echo $content;
        } else {
            echo 'Шаблон не найден!';
        }
        exit;
    }
}