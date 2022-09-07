<?php
namespace Kernel\Route;

class Route
{
    private static $routing = array();
    private static $count = 0;

    private function __construct(){
    }

    public static function add(string $method, string $path, string $controller, string $function='index', $role='') : void {
        self::$routing[self::$count]['controller'] = $controller;
        self::$routing[self::$count]['function'] = $function;
        self::$routing[self::$count]['method'] = $method;
        self::$routing[self::$count]['path'] = $path;
        self::$routing[self::$count]['role'] = $role;
        self::$count++;
    }

    public static function get(){
        return self::$routing;
    }
}