<?php

namespace Apl\Dispatcher;

/**
 * The Dispatcher collects the input parameters and uses the Router to
 * determine the correct Controller to call.
 */
class Dispatcher
{

    /**
     * The config object
     * 
     * @var \Apl\Config\Config
     */
    public $config;
    
    /**
     * Sets up the dispatcher.
     * 
     * @param \Apl\Config\Config $config
     * @param \Apl\Cache\CacheInterface $cache
     */
    public function __construct(\Apl\Config\Config $config)
    {
        $this->config = $config;
        $this->cache = $cache;
    }

    public function dispatch()
    {
        $router = new \Apl\Router\Router();

        $routes = $this->config->getConfiguration('routes', 'routes');

        foreach ($routes as $target => $pattern)
        {
            $router->map($target, $pattern);
        }
        
        $route = \Apl\InputFilter\FilterGet::getFilteredInput('route', 'filterString');
        $route = ( $route ) ? $route : '';
        
        $route = $router->match(strtolower($route));
        
        if (!$route)
        {
            die('Invalid route');
        }

        $controllerClass = '\\' . $this->config->getConfiguration('application', 'app_namespace')
                . '\\Controller\\' . $route->getController() . '\\' . $route->getAction();
        $controller = $this->config->getClass($controllerClass);

        try
        {
            $controller->execute($route);
        }
        catch (\Exception $e)
        {
            // TODO: load the '500 internal error' template (dont exit in the exception)
            echo 'Internal Server Error: '.$e->getMessage();
        }
    }

}

?>
