<?php

namespace Emmetog\Router;

use Emmetog\Config\HasConfig;

class Router
{

    use HasConfig;

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
            $this->setMap($this->config->getConfiguration('url_map'));
        }
        if (empty($this->placeholders))
        {
            $this->setPlaceholders($this->config->getConfiguration('url_placeholders'));
        }

        if (!isset($this->map[$routeName]))
        {
            throw new RouterUrlNotMatchedException();
        }

        $url = 'http://' . $this->replacePlaceholders($this->map[$routeName]);
        $url = $this->insertParamPatterns($url, $params);

        return $url;
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
            $preg_match_pattern = '@^' . $this->replacePlaceholders($urlPattern) . '$@is';
            $preg_match_pattern = $this->constructParamPatterns($preg_match_pattern);

            if (preg_match($preg_match_pattern, $url, $params))
            {
                // We're only interested in the params which have associative keys.
                foreach ($params as $key => $value)
                {
                    if (is_integer($key))
                    {
                        unset($params[$key]);
                    }
                }

                $route = $this->config->getClass('Emmetog\Router\Route');
                $route->setController($controller)
                        ->setOriginalUrl($url)
                        ->setParams($params);
                return $route;
            }
        }

        throw new RouterUrlNotMatchedException();
    }

    protected function replacePlaceholders($pattern)
    {
        // Replate the preset placeholders with their real values.
        foreach ($this->placeholders as $placeholder => $replacement)
        {
            $pattern = str_replace('<' . $placeholder . '>', $replacement, $pattern);
        }
        return $pattern;
    }

    protected function constructParamPatterns($pattern)
    {
        // Make the correct pattern out of any patterns in the url.
        if (preg_match_all('@\<([a-zA-Z_\-]+):([^\>]+)\>@', $pattern, $patternMatches, PREG_SET_ORDER))
        {
            foreach ($patternMatches as $patternMatch)
            {
                $pattern = str_replace('<' . $patternMatch[1] . ':' . $patternMatch[2] . '>', '(?<' . $patternMatch[1] . '>' . $patternMatch[2] . ')', $pattern);
            }
        }
        return $pattern;
    }

    protected function insertParamPatterns($pattern, $params)
    {
        foreach ($params as $key => $param)
        {
            $pattern = preg_replace('@\<' . $key . '[^\>]*\>@', $param, $pattern);
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
