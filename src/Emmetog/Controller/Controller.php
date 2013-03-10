<?php

namespace Emmetog\Controller;

use Emmetog\Config\Config;

abstract class Controller
{

    /**
     *
     * @var \Apl\Config\Config
     */
    public $config;

    public function __construct(Config $config) {
	$this->config = $config;
    }

    abstract protected function build();
}

?>
