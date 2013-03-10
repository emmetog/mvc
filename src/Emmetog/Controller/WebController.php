<?php

namespace Emmetog\Controller;

use Emmetog\Controller\Controller;
use Emmetog\Router\Route;

abstract class WebController extends Controller
{

    /**
     * @var \Apl\View\ViewInterface
     */
    protected $view;

    /**
     * @var \Apl\Cache\CacheInterface
     */
    protected $cache;
    protected $templateRoot = '';
    protected $viewVariables = array();
    protected $layout = '';

    public function __construct($config)
    {
        parent::__construct($config);
    }

    final public function execute()
    {
        $this->cache = $this->config->getCache();

        $cacheDefinition = $this->cacheDefinition();
        $assignedVars = array();

//        if ($cacheDefinition)
//        {
//            // See if the cached output already exists.
//            $assignedVars = $this->cache->exists(__CLASS__);
//
//            if ($assignedVars)
//            {
//                echo "Cached output found, not running the build() again.";
//            }
//            else
//            {
//                echo "No cached output found, running the build() again.";
//            }
//        }

        if ($assignedVars)
        {
            $this->viewVariables = $assignedVars;
        }
        else
        {
            $this->preBuild();

            $this->build();

            if ($cacheDefinition)
            {
                // Save the assigned variables into the cache.
                $this->cache->set(__CLASS__, serialize($this->viewVariables));
            }
        }
        
        if (!$this->layout)
        {
            $this->layout = $route->getController() . DIRECTORY_SEPARATOR
                    . $route->getAction() . '.tpl';
        }
        
        $this->render($this->viewVariables, $this->layout);
    }

    public function assign($variable, $value)
    {
        $this->viewVariables[$variable] = $value;
    }

    abstract protected function cacheDefinition();
    
    abstract protected function render($viewVars, $template);

    public function preBuild()
    {
        
    }
    
    public function setLayout($layout) {
        $this->layout = $layout;
    }

}

?>
