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

    /**
     * Creates a new instance of the Router.
     * 
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Sets the map which relates controllers to url patterns.
     * 
     * @param array $map
     */
    public function setMap(array $map)
    {
        $this->map = $map;
    }

    /**
     * Sets the placeholders which will be replaced in the patterns.
     * 
     * @param array $placeholders
     */
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
        if (empty($this->map))
        {
            $this->setMap($config->getConfiguration('url_map'));
        }
        if (empty($this->placeholders))
        {
            $this->setPlaceholders($config->getConfiguration('url_placeholders'));
        }

//        var_dump($this->map); die;

        if (!isset($this->map[$routeName]))
        {
            throw new RouterUrlNotMatchedException();
        }

        return $this->replacePlaceholders($this->map[$routeName], $params);
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
            $preg_match_pattern = '@' . $this->replacePlaceholders($urlPattern, $this->placeholders) . '@is';

            if (preg_match($preg_match_pattern, $url, $params))
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

    protected function replacePlaceholders($pattern, array $placeholders)
    {
        foreach ($this->placeholders as $placeholder => $replacement)
        {
            $pattern = str_replace('<' . $placeholder . '>', $replacement, $pattern);
        }
        return $pattern;
    }

}

class RouterException extends \Exception
{
    
}

class RouterUrlNotMatchedException extends RouterException
{
    
}

?>
