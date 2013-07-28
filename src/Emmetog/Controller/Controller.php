<?php

namespace Emmetog\Controller;

use Emmetog\Config\HasConfig;

abstract class Controller
{

    /**
     * The params that were passed to the controller from the Router.
     *
     * @var array
     */
    private $params;

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
