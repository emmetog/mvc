<?php

namespace Emmetog\Dispatcher;

use Emmetog\Config\Config;

/**
 * The Dispatcher collects the input parameters and uses the Router to
 * determine the correct Controller to call.
 */
class Dispatcher
{

    /**
     * The config object
     * 
     * @var Config
     */
    public $config;

    /**
     * Sets up the dispatcher.
     * 
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Dispatches a controller.
     * 
     * @param string $controllerName The name of the controller to execute.
     */
    public function dispatch($controllerName)
    {
//        $router = new Router();
//
//        $routes = $this->config->getConfiguration('routes', 'routes');
//
//        foreach ($routes as $target => $pattern)
//        {
//            $router->map($target, $pattern);
//        }
//        
//        $route = \Apl\InputFilter\FilterGet::getFilteredInput('route', 'filterString');
//        $route = ( $route ) ? $route : '';
//        
//        $route = $router->match(strtolower($route));
//        
//        if (!$route)
//        {
//            die('Invalid route');
//        }
//
//        $controllerClass = '\\' . $this->config->getConfiguration('application', 'app_namespace')
//                . '\\Controller\\' . $route->getController() . '\\' . $route->getAction();
        $controller = $this->config->getClass($controllerClass);

        try
        {
            $controller->execute($route);
        }
        catch (\Exception $e)
        {
            // TODO: load the '500 internal error' template (dont exit in the exception)
            echo 'Internal Server Error: ' . $e->getMessage();
        }
    }

}

?>
