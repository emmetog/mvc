<?php

namespace Emmetog\Controller;

use Emmetog\Config\Config;

abstract class Controller
{

    /**
     * The config object.
     * 
     * @var Config
     */
    public $config;
    
    /**
     * The params that were passed to the controller from the Router.
     *
     * @var array
     */
    private $params;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }
    
    public function setParams(array $params)
    {
        $this->params = $params;
    }
    
    public function getParams()
    {
        return $this->params;
    }

    abstract protected function build();
}

?>
