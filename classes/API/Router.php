<?php namespace API;

use \ProcessWire;

/**
 *
 */
class Router extends Resource
{

    public $routes;

    function __construct(Array $routes = [])
    {
        $this->routes = $routes;
    }

    public function add(Route $route)
    {
        array_push($this->routes, $route);
        return this;
    }

    public function get(Route $route)
    {
        return $this->add($route);
    }

    public function post(Route $route)
    {
        return $this->add($route);
    }

    public function put(Route $route)
    {
        return $this->add($route);
    }

    public function delete(Route $route)
    {
        return $this->add($route);
    }

    public function execute($segments)
    {
        foreach ($this->routes as $route) {
            if ($_SERVER['REQUEST_METHOD'] == $route->method) {
                $routeAddress = str_replace('/', '\/', $route->route);
                preg_match('/^'.$routeAddress.'$/', $segments, $matches);
                if (count($matches)) {
                    $data = call_user_func_array($route->callback, $matches);
                    return $data;
                }
            }
        }
    }

}
