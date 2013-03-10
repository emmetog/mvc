<?php

namespace Emmetog\Controller;

use Emmetog\Controller\Controller;

abstract class CommandlineController extends Controller
{

    protected $controllerName = '';
    protected $actionName = '';

    public function __construct($config)
    {
        parent::__construct($config);
    }

    final public function execute()
    {
        $this->build();
    }

    public function assign($variable, $value)
    {
        $this->view->assign($variable, $value);
    }

    protected function output($message)
    {
        echo date('Y-m-d H:i:s') . " [" . getmypid() . '] ' . trim($message) . PHP_EOL;
    }

}

?>
