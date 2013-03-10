<?php

namespace Emmetog\View;

use Emmetog\View\ViewInterface;
use Emmetog\Config\Config;

abstract class View implements ViewInterface
{

    /**
     * @var Config
     */
    protected $config;

    public final function __construct(Config $config)
    {
        $this->config = $config;
    }

}

?>
