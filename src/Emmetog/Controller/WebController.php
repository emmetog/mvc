<?php

namespace Emmetog\Controller;

use Emmetog\Controller\Controller;
use Emmetog\Router\Route;
use Emmetog\View\ViewInterface;
use Emmetog\View\TwigView;
use Emmetog\Cache\CacheInterface;

abstract class WebController extends Controller
{

    /**
     * The view object.
     * 
     * @var ViewInterface
     */
    protected $view;

    /**
     * The cache object.
     * 
     * @var CacheInterface
     */
    protected $cache;
    
    /**
     * The variables to pass to the view.
     *
     * @var array
     */
    protected $viewVariables = array();
    
    /**
     * The layout template to use in the view.
     *
     * @var string
     */
    protected $layout = '';
    
    /**
     * The javascript files to include.
     *
     * @var array
     */
    protected $jsFiles = array();
    
    /**
     * The css files to include.
     *
     * @var array
     */
    protected $cssFiles = array();

    /**
     * Creates a new instance of a controller.
     * 
     * @param Config $config The config object.
     */
    public function __construct($config)
    {
        parent::__construct($config);
    }

    /**
     * Executes the controller.
     */
    final public function execute()
    {
        $this->cache = $this->config->getCache();

        $cacheDefinition = $this->cacheDefinition();
        /**
         * @todo Process and implement the cacheDefinition
         */
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

        $this->render($this->viewVariables, $this->layout);
    }

    /**
     * Assigns a variable to the view.
     * 
     * @param string $variable The name of the variable (its name in the view).
     * @param mixed $value The value of the variable.
     */
    public function assign($variable, $value)
    {
        $this->viewVariables[$variable] = $value;
    }
    
    /**
     * Adds a JS file to be included in the rendered page.
     * 
     * @param string $filename
     */
    public function includeJsFile($filename)
    {
        $this->jsFiles[] = $filename;
    }
    
    /**
     * Adds a CSS file to be included in the rendered page.
     * 
     * @param string $filename
     */
    public function includeCssFile($filename)
    {
        $this->cssFiles[] = $filename;
    }

    /**
     * Gets the definition of the cache.
     */
    abstract protected function cacheDefinition();

    /**
     * Renders the view.
     * 
     * @param array $variables The variables to pass to the view.
     * @param string $template The template to render.
     */
    protected function render($variables, $template)
    {
        $this->view = new TwigView($this->config);

        foreach ($variables as $variableName => $variableValue)
        {
            $this->view->assign($variableName, $variableValue);
        }
        
        $this->view->assign('js_files', $this->jsFiles);
        $this->view->assign('css_files', $this->cssFiles);

        $this->view->setTemplate($template);

        $this->view->render();
    }

    /**
     * Executes before the build, can be overwritten by children.
     */
    public function preBuild()
    {
        
    }

    /**
     * Sets the layout template.
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

}

class WebControllerException extends \Exception {
    
}

?>
