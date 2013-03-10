<?php

namespace Emmetog\Router;

class Route
{

    protected $controller;
    protected $action;
    protected $originalUrl;
    protected $routeName;
    protected $parsedParams;
    protected $requestMethod;

    public function __construct($controller, $action, $parsedParams, $originalUrl)
    {
        $this->controller = $controller;
        $this->action = $action;
        $this->originalUrl = $originalUrl;
        $this->parsedParams = $parsedParams;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    public function getParams()
    {
        return $this->parsedParams;
    }
}

?>
