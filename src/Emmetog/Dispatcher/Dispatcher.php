<?php

namespace Emmetog\Dispatcher;

use Emmetog\Config\HasConfig;
use Emmetog\Config\ConfigClassNotFoundException;
use Emmetog\Router\Route;

/**
 * The Dispatcher collects the input parameters and uses the Router to
 * determine the correct Controller to call.
 */
class Dispatcher
{

    use HasConfig;

    /**
     * Dispatches a controller.
     * 
     * @param string $controllerClass The fully qualified name (including namespaces) of the controller class to execute.
     */
    public function dispatch(Route $route)
    {

        try
        {
            $controller = $this->config->getClass($route->getController());
        }
        catch (ConfigClassNotFoundException $e)
        {
            echo 'Requested controller does not exist: ' . $e->getMessage();
            die;
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
