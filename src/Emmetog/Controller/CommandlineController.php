<?php

namespace Emmetog\Controller;

use Emmetog\Controller\Controller;
use Emmetog\InputFilter\FilterCommandLineArg;

abstract class CommandlineController extends Controller
{

    protected $controllerName = '';
    protected $actionName = '';
    
    private $colors = array(
        'red' => "\033[0;31m",
        'green' => "\033[0;32m",
        'yellow' => "\033[1;33m",
    );

    final public function execute()
    {
        $this->build();
    }

    public function assign($variable, $value)
    {
        $this->view->assign($variable, $value);
    }

    protected function output($message, $color = '')
    {
        $colorString = "";
        
        if (FilterCommandLineArg::getFilteredInput('colors', 'filterString'))
        {
            if(array_key_exists($color, $this->colors))
            {
                $colorString = $this->colors[$color];
            }
        }

        echo $colorString . date('Y-m-d H:i:s') . " [" . getmypid() . '] ' . trim($message) . "\033[0m".PHP_EOL;
    }
}

?>
