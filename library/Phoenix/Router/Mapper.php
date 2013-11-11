<?php
namespace Phoenix\Router;
use Phoenix\Router\Route;
class Mapper
{

    protected static $_instance = null;
    
    protected $map = array();
    
    private function __construct(){}
    
    public static function getInstance()
    {
        if (self::$_instance == null) 
            self::$_instance = new Mapper();
        
        return self::$_instance;
    }
    
    public function addMap($uri, $route)
    {
        if (!array_key_exists($uri, $this->map))
        {
            $this->map[$uri] = $route;
        }
        
         return $this;
    }
    
    public function reMap($uri, $route)
    {
        if (array_key_exists($uri, $this->map))
        {
            $this->map[$uri] = $route;
        }
        
        return $this;
    }
    
    public function unMap($uri)
    {
        if (array_key_exists($uri, $this->map))
        {
            unset($this->map[$uri]);
        }
        
        return $this;
    }
    
    public function clearMap() {
        $this->map = array();
        
        return $this;
    }
    
    public function map($uri, $conf)
    {
        foreach($this->map as $url => $route)
        {
            if (fnmatch($url, $uri)) {
               return new Route($url, $route, $conf);
            }
        }
        
        return false;
    }
    
}

?>