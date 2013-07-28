<?php

namespace Emmetog\Model;

use Emmetog\Config\HasConfig;

abstract class Model
{

    use HasConfig;

    public function __construct()
    {
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
