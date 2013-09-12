<?php
namespace Phoenix\Storage;

class Registry
{
    
    private static $_registry = null;
    private static $_instance = null;
    
    
    private function __construct()
    {
        if(self::$_registry === null)
        {
            self::$_registry = array();
            self::$_registry['defaultRegistry'] = array();
        }
    }
    
    public static function getInstance()
    {
        
        if(self::$_instance == null)
            self::$_instance = new Registry();
        
        
        return self::$_instance;
    }
    
    public static function setNamespace($namespace)
    {
        self::$_registry[$namespace] = array();
    }
    
    public static function set($key, &$value, $namespace = 'defaultRegistry')
    {
        if (empty(self::$_registry)) self::getInstance();
        if (array_key_exists($namespace, self::$_registry)) self::setNamespace($namespace);
        self::$_registry[$namespace][$key] = $value;
    }
    
    public static function &get($key, $namespace='defaultRegistry')
    {
        if (array_key_exists($namespace, self::$_registry)):
            if (array_key_exists($key, self::$_registry[$namespace])):
                return self::$_registry[$namespace][$key];
            else:
                return false;
            endif;
        else:
            return false;
        endif;
        
    }
    
    public static function drop($key, $namespace = 'defaultNamespace')
    {
        unset(self::$_registry[$namespace][$key]);
    }
    
    public static function clean($namespace='defaultRegistry')
    {
        unset(self::$_registry[$namespace]);
    }
    
    public static function raw($namespace = 'defaultRegistry')
    {
        if (isset($namespace) && array_key_exists($namespace, self::$_registry))
            return self::$_registry[$namespace];
        elseif (!isset($namespace))
            return self::$_registry;
        else
            return array();
    }
}