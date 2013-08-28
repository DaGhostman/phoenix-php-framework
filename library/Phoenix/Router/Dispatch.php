<?php

namespace Phoenix\Router;

use Phoenix\Router\Request;
use Phoenix\Router\Route;
use Phoenix\Router\Response;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;
use Phoenix\Core\HttpErrorsManager;

class Dispatch {
    
    public function dispatch($route, Request $request, Response $response)
    {
        
        if ($route instanceof Route) {
        $actionName = $route->getAction().'Action';
        $controller = $route->createController();
        $reflection = new \ReflectionClass($controller);
        
        if (($reflection->hasMethod($actionName)) && ($controller instanceof \Phoenix\Controller\Action)) {
                    Manager::getInstance()->emit(Signals::SIGNAL_DISPATCH);
                    if($reflection->hasMethod('init')) $controller->init($request, $response);
                    if($reflection->hasMethod('preDispatch')) $controller->preDispatch();
                    Manager::getInstance()->emit(Signals::SIGNAL_TRIGGER);
                    $controller->$actionName();
                    if($reflection->hasMethod('postDispatch')) $controller->postDispatch();
                } else {
                    if ((true == empty($controller)) || (!$controller instanceof \Phoenix\Controller\Action) ):
                        HttpErrorsManager::getInstance()->sendError(Response::HTTP_503, new \Exception('The controller for request: '.$request->getUri().' was not found'));
                    elseif (!$reflection->hasMethod($actionName)):
                        HttpErrorsManager::getInstance()->sendError(Response::HTTP_404, new \Exception('The template for request: '.$request->getUri().' was not found'));
                    endif;
                }
        } else {
            if ((true == empty($controller)) || (!$controller instanceof \Phoenix\Controller\Action) ):
                HttpErrorsManager::getInstance()->sendError(Response::HTTP_503, new \Exception('The controller for request: '.$request->getUri().' was not found'));
            elseif (!$reflection->hasMethod($actionName)):
                HttpErrorsManager::getInstance()->sendError(Response::HTTP_404, new \Exception('The template for request: '.$request->getUri().' was not found'));
            endif;
        }
    }
    
}