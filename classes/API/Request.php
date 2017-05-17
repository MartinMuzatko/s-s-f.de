<?php namespace API;

use \ProcessWire\Wire;

/**
 *
 */
class Request extends Wire
{

    public static function getPayload()
    {
        return json_decode(file_get_contents('php://input'));
    }

    public static function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}
