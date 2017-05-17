<?php namespace API;

use \ProcessWire;

/**
 *
 */
class Route
{

    function __construct($method, $route, $callback)
    {
        $this->method = $method;
        $this->route = $route;
        $this->callback = $callback;
    }
}
