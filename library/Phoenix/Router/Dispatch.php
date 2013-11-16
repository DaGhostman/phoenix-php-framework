<?php

namespace Phoenix\Router;

use Phoenix\Router\Mapper;
use Phoenix\Router\Request;
use Phoenix\Router\Route;
use Phoenix\Router\Response;
use Phoenix\Application\ErrorManager;
use Phoenix\View\Viewer;

class Dispatch {
    
    private $_config = null;
    
    public function dispatch(Request $request, Response $response, $configuration = null)
    {
        if ($configuration != null) {
            $this->_config = $configuration;
        }
        
        if (($route = Mapper::getInstance()->map($request->getUri(), $this->_config)) != false) {
            $route->load($request);
        } else {
            $error = sprintf('Unable to map request to \'%s\'', 
                $request->getUri());
                
            throw new \RuntimeException($error, 404);
        }
        
        
        if ($route instanceof Route) {
            $actionName = $route->getAction().'Action';
            $controller = $route->createController($request, $configuration);
            
            if ((method_exists($controller, $actionName)) && ($controller instanceof \Phoenix\Controller\Action)) {
                    if(method_exists($controller, 'preDispatch')) $controller->preDispatch();
                    $controller->$actionName();
                    if(method_exists($controller, 'postDispatch')) $controller->postDispatch();
            } else {
                if (($controller == false) || 
                    (!$controller instanceof \Phoenix\Controller\Action)) {
                       throw new \Exception('The controller for request: ' . 
                            $request->getUri().' was not found', 503);
               } elseif (!method_exists($controller, $actionName)) {
                   throw new \Exception('The template for request: ' . 
                            $request->getUri().' was not found', 404);
               }
            }
        }
    }
    
}