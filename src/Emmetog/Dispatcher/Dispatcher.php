<?php

namespace Emmetog\Dispatcher;

use Emmetog\Config\Config;
use Emmetog\Config\ConfigClassNotFoundException;
use Emmetog\Router\Route;

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
     * @param string $controllerClass The fully qualified name (including namespaces) of the controller class to execute.
     */
    public function dispatch(Route $route)
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

        try
        {
            $controller = $this->config->getClass($route->getController());
        }
        catch (ConfigClassNotFoundException $e)
        {
            echo 'Requested controller does not exist: ' . $e->getMessage();
        }

        try
        {
            $controller->setParams($route->getParams());
            $controller->execute();
        }
        catch (\Exception $e)
        {
            // TODO: load the '500 internal error' template (dont exit in the exception)
            echo '<h1>Internal Server Error</h1><p>' . get_class($e) . ' ' . $e->getMessage() . '</p>' . PHP_EOL;
            die;
        }
    }

}

?>
