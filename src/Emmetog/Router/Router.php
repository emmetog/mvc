<?php

namespace Emmetog\Router;

class Router
{

    private $routes = array();
    private $namedRoutes = array();
    private $basePath = '';

    /**
     * Set the base path.
     * Useful if you are running your application from a subdirectory.
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Map a route to a target
     *
     * @param string $method One of 4 HTTP Methods, or a pipe-separated list of multiple HTTP Methods (GET|POST|PUT|DELETE)
     * @param string $route The route regex, custom regex must start with an @. You can use multiple pre-set regex filters, like [i:id]
     * @param mixed $target The target where this route should point to. Can be anything.
     *
     */
    public function map($target, $pattern)
    {
        $pattern = $this->basePath . $pattern;

        $this->routes[] = array(
            'target' => $target,
            'pattern' => $pattern
        );
    }

    /**
     * Reversed routing
     *
     * Generate the URL for a named route. Replace regexes with supplied parameters
     *
     * @param string $routeName The name of the route.
     * @param array @params Associative array of parameters to replace placeholders with.
     * @return string The URL of the route with named parameters in place.
     */
    public function generate($routeName, array $params = array())
    {

        // Check if named route exists
        if (!isset($this->namedRoutes[$routeName]))
        {
            throw new RouterException("Route '{$routeName}' does not exist.");
        }

        // Replace named parameters
        $route = $this->namedRoutes[$routeName];
        $url = $route;

        if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER))
        {

            foreach ($matches as $match)
            {
                list($block, $pre, $type, $param, $optional) = $match;

                if ($pre)
                {
                    $block = substr($block, 1);
                }

                if (isset($params[$param]))
                {
                    $url = str_replace($block, $params[$param], $url);
                }
                elseif ($optional)
                {
                    $url = str_replace($block, '', $url);
                }
            }
        }

        return $url;
    }

    /**
     * Match a given Request Url against stored routes
     * @param string $requestUrl
     * @return \Apl\Router\Route A Route object on success, false on failure (no match).
     */
    public function match($requestUrl)
    {
        $params = array();
        $match = false;

        // Strip query string (?a=b) from Request Url
        if (false !== strpos($requestUrl, '?'))
        {
            $requestUrl = strstr($requestUrl, '?', true);
        }
        
        $requestMethod = null;

        // set Request Method if it isn't passed as a parameter
        if ($requestMethod === null)
        {
            $requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        }

        // Force request_order to be GP
        // http://www.mail-archive.com/internals@lists.php.net/msg33119.html
//	$_REQUEST = array_merge($_GET, $_POST);

        foreach ($this->routes as $handler)
        {
            // Check for a wildcard (matches all)
            if ($handler['pattern'] === '*')
            {
                $match = true;
            }
            elseif (isset($handler['pattern'][0]) && $handler['pattern'][0] === '@')
            {
                $match = preg_match('`' . substr($handler['pattern'], 1) . '`', $requestUrl, $params);
            }
            else
            {
                $route = null;
                $regex = false;
                $j = 0;
                $n = isset($handler['pattern'][0]) ? $handler['pattern'][0] : null;
                $i = 0;

                // Find the longest non-regex substring and match it against the URI
                while (true)
                {
                    if (!isset($handler['pattern'][$i]))
                    {
                        break;
                    }
                    elseif (false === $regex)
                    {
                        $c = $n;
                        $regex = $c === '[' || $c === '(' || $c === '.';
                        if (false === $regex && false !== isset($handler['pattern'][$i + 1]))
                        {
                            $n = $handler['pattern'][$i + 1];
                            $regex = $n === '?' || $n === '+' || $n === '*' || $n === '{';
                        }
                        if (false === $regex && $c !== '/' && (!isset($requestUrl[$j]) || $c !== $requestUrl[$j]))
                        {
                            continue 2;
                        }
                        $j++;
                    }
                    $route .= $handler['pattern'][$i++];
                }

                $regex = $this->compileRoute($route);
                $match = preg_match($regex, $requestUrl, $params);
            }

            if (($match == true || $match > 0))
            {

                if ($params)
                {
                    foreach ($params as $key => $value)
                    {
                        if (is_numeric($key)) unset($params[$key]);
                    }
                }
                
                $target = explode('\\', $handler['target']);
                
                if(!count($target) == 2) {
                    throw new \RuntimeException('Invalid target "'.$handler['target'].'" for url pattern '.$handler['pattern']. '. Target must be in the form {controller]\\{action}');
                }

                $route = new Route(
                                $target[0],
                                $target[1],
                                $params,
                                $requestUrl
                );
                
                return $route;
            }
        }
        return false;
    }

    /**
     * Compile the regex for a given route (EXPENSIVE)
     */
    private function compileRoute($route)
    {
        if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER))
        {

            $match_types = array(
                'i' => '[0-9]++',
                'a' => '[0-9A-Za-z]++',
                'h' => '[0-9A-Fa-f]++',
                '*' => '.+?',
                '**' => '.++',
                '' => '[^/]++'
            );

            foreach ($matches as $match)
            {
                list($block, $pre, $type, $param, $optional) = $match;

                if (isset($match_types[$type]))
                {
                    $type = $match_types[$type];
                }
                if ($pre === '.')
                {
                    $pre = '\.';
                }

                //Older versions of PCRE require the 'P' in (?P<named>)
                $pattern = '(?:'
                        . ($pre !== '' ? $pre : null)
                        . '('
                        . ($param !== '' ? "?P<$param>" : null)
                        . $type
                        . '))'
                        . ($optional !== '' ? '?' : null);

                $route = str_replace($block, $pattern, $route);
            }
        }
        return "`^$route$`";
    }

}

class RouterException extends \Exception {
    
}

?>
