<?php namespace API;

use \ProcessWire\Wire;

/**
 *
 */
class Response extends Wire
{
    public static $isJson = false;

    public static function setContentTypeJSON()
    {
        header("Content-Type: application/json");
        self::$isJson = true;
    }
}
