<?php
abstract class Config
{
    public static $items = [];

    public static function get($key = null)
    {
        return self::$items[$key] ?? null;
    }

    public static function set($key, $val)
    {
        self::$items[$key] = $val;
    }
}
