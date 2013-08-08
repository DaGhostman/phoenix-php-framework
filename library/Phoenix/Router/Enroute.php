<?php

namespace Phoenix\Router;

use Phoenix\Storage\Registry;
use Phoenix\Application\Core;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;
use Phoenix\Core\HttpErrorsManager;

class Enroute
{
    
    protected $routes = array();
    protected $storage = null;
    
    private static $instance = null;
    
    public static function getInstance($routes = array())
    {
        if (!self::$instance) self::$instance = new Enroute($routes);
    
        return self::$instance;
    }
    
    private function __construct(array $routes = array())
    {
        if (!empty($routes)) $this->addRoutes($routes);
    }
    
    
    public function addRoute($route)
    {
        $reg = Registry::getInstance();
        $reg::set(count($this->routes), $route, 'defaultRouteStorage');
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
        
        $raw = Registry::getInstance()->raw('defaultRouteStorage') ? 
                Registry::getInstance()->raw('defaultRouteStorage') : array();
        foreach ($raw as $route):
            if ($route->match($request)):
                $request->setParams('route', $route->route);
                $route->load();
                
                Manager::getInstance()->emit(Signals::SIGNAL_ENROUTE);
                return $route;
                break;
            endif;
        endforeach;
        
        Core::writelog('error.log', 'No route found for: ' . $request->getUri());
        
        HttpErrorsManager::getInstance()->sendError(
                        $response::HTTP_404, 
                        new \Exception(
                                'No Route Found for requested: ' . 
                                $request->getUri()
                                )
                        );
    }
    
}