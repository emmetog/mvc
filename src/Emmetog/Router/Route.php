<?php

namespace Emmetog\Router;

class Route
{

    protected $controller = '';
    protected $originalUrl = '';
    protected $routeName = '';
    protected $parsedParams = array();
    
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }
    
    public function setOriginalUrl($originalUrl)
    {
        $this->originalUrl = $originalUrl;
        return $this;
    }
    
    public function setParams(array $params)
    {
        $this->parsedParams = $params;
        return $this;
    }

    public function getController()
    {
        return $this->controller;
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
