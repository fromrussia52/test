<?php

namespace App;

use App\Routes;

class Kernel 
{
    public function handle()
    {
        try {
            (new Routes())->execute();
        } catch (\Exception $e) {
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
    }
}