<?php

namespace Emmetog\Model;

use Emmetog\Config\Config;

abstract class Model
{

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->init();
    }

    /**
     * Performs any initiation, this method can be overridden in children.
     */
    protected function init()
    {
        
    }

}

?>
