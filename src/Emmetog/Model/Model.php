<?php

namespace Emmetog\Model;

use Emmetog\Config\Config;

abstract class Model
{

    /**
     * @var Emmetog\Database\Connection
     */
    protected $db;

    /**
     * @var Emmetog\Config\Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        
        $this->init();
    }
    
    abstract protected function init();

}

?>
