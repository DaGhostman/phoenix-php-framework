<?php

namespace Phoenix\Router;

use Phoenix\Router\Request;
use Phoenix\Router\Route;
use Phoenix\Router\Response;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;
use Phoenix\Core\HttpErrorsManager;

class Dispatch {
    
    public function dispatch(Route $route, Request $request, Response $response)
    {
        
        $actionName = $route->getAction().'Action';
        $controller = $route->createController();
                
        if (($controller != false) && (method_exists($controller, $actionName))) {
                    $reflection = new \ReflectionClass($controller);
                    Manager::getInstance()->emit(Signals::SIGNAL_DISPATCH);
                    if($reflection->hasMethod('init')) $controller->init($request, $response);
                    if($reflection->hasMethod('preDispatch')) $controller->preDispatch();
                    Manager::getInstance()->emit(Signals::SIGNAL_TRIGGER);
                    $controller->$actionName();
                    if($reflection->hasMethod('postDispatch')) $controller->postDispatch();
                } else {
                    if ($controller == false):
                        HttpErrorsManager::getInstance()->sendError(Response::HTTP_503, new \Exception('The controller for request: '.$request->getUri().' was not found'));
                    elseif (!method_exists($controller, $actionName)):
                        HttpErrorsManager::getInstance()->sendError(Response::HTTP_404, new \Exception('The template for request: '.$request->getUri().' was not found'));
                    endif;
                }
    }
    
}