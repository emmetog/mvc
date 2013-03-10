<?php

namespace Emmetog\Exception;

class Exception403 extends \Exception{
    
    public function __construct($message='')
    {
        // TODO: Move this logic to the Dispatcher
        header("HTTP/1.1 403 Forbidden");
        
        // TODO: If in debug mode we show the message, otherwise don't
        echo $message;
        exit();
    }
    
}
?>
