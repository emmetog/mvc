<?php

namespace Emmetog\Router;

use Emmetog\Router\Route;
use Emmetog\Config\Config;

class Router
{

    /**
     * The map of patterns to controllers.
     *
     * @var array
     */
    private $map = array();

    /**
     * An array of placeholders that will be swapped out of the patterns.
     *
     * @var array
     */
    private $placeholders = array();

    /**
     * The config object.
     * 
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function setMap(array $map)
    {
        $this->map = $map;
    }

    public function setPlaceholders(array $placeholders)
    {
        $this->placeholders = $placeholders;
    }

    /**
     * Generate the URL for a named route, filling in the parameters.
     *
     * @todo Fill out this method.
     * 
     * @param string $routeName The name of the route.
     * @param array $params Associative array of parameters to replace placeholders with.
     * 
     * @return string The URL of the route with named parameters in place.
     */
    public function generate($routeName, array $params = array())
    {
        throw new \BadMethodCallException('This method is not yet implemented!');
    }

    /**
     * Match a given url against the map.
     * 
     * @param string $url The url to match.
     * 
     * @throws RouterUrlNotMatchedException If the url can not be matched.
     * 
     * @return \Emmetog\Router\Route A Route object on success, false on failure (no match).
     */
    public function match($url)
    {
        foreach ($this->map as $controller => $urlPattern)
        {
            if (preg_match($this->compilePattern($urlPattern), $url, $params))
            {
                $route = $this->config->getClass('Emmetog\Router\Route');
                $route->setController($controller)
                        ->setOriginalUrl($url)
                        ->setParams($params);
                return $route;
            }
        }

        throw new RouterUrlNotMatchedException();
    }

    protected function compilePattern($pattern)
    {
        foreach ($this->placeholders as $placeholder => $replacement)
        {
            $pattern = str_replace('<' . $placeholder . '>', $replacement, $pattern);
        }

        $compiledPattern = '@' . $pattern . '@is';

        return $compiledPattern;
    }
}

class RouterException extends \Exception
{
    
}

class RouterUrlNotMatchedException extends RouterException
{
    
}

?>
