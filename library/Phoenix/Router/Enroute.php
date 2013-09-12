<?php

namespace Phoenix\Router;
use Phoenix\Core\HttpErrorsManager;

class Enroute
{
    
    protected $routes = array();
    protected $storage = null;
    
    private static $instance = null;
    
    public static function getInstance()
    {
        if (!self::$instance) self::$instance = new Enroute();
    
        return self::$instance;
    }
    
    private function __construct(){}

    
    public function route($request, $response)
    {
            
            if (($route = Mapper::getInstance()->map($request->getUri())) != false):
                $request->setParams('route', $route->route);
                
                $route->load();
                
                return $route;
                break;
            endif;
        
        
        HttpErrorsManager::getInstance()->sendError(
                        $response::HTTP_404, 
                        new \Exception(
                                'No Route Found for requested: ' . 
                                $request->getUri()
                                )
                        );
    }
    
}