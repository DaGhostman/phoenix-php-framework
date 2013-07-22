<?php

namespace Phoenix\Router;

use Phoenix\Storage\Registry;
use Phoenix\Core\HttpErrorsManager;


class MockEnroute
{
    
    protected $routes = array();
    protected $storage = null;
    
    private static $instance = null;
    
    public static function getInstance($routes = array())
    {
        if (!self::$instance) self::$instance = new MockEnroute($routes);
    
        return self::$instance;
    }
    
    private function __construct(array $routes = array())
    {
        if (!empty($routes)) $this->addRoutes($routes);
    }
    
    
    public function addRoute($route)
    {
        $this->routes[] = $route;
        return $this;
    }
    
    
    public function addRoutes(array $routes)
    {
        foreach ($routes as $route) $this->addRoute($route);
        
        return $this;
    }
    
    public function getRoutes()
    {
        return $this->routes;
    }
    
    public function route($request, $response)
    {    
        foreach ($this->routes as $route):
            if ($route->match($request)):
                $request->setParams('route', $route->route);
                $route->load();
                return $route;
                break;
            endif;
        endforeach;
        
        HttpErrorsManager::getInstance()->sendError(Response::HTTP_404, new \OutOfRangeException('No route found for URI : '.$request->getUri()));
        
    }
    
}