<?php

namespace Emmetog\Exception;

class Exception400 extends \Exception{
    
    public function __construct($message='')
    {
        header("HTTP/1.1 400 Bad Request");
        
        // TODO: If in debug mode we show the message, otherwise don't
        echo $message;
        exit();
    }
    
}
?>
