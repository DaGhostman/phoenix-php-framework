<?php

namespace Phoenix\Router;

use Phoenix\Router\Mapper;
use Phoenix\Router\Request;
use Phoenix\Router\Route;
use Phoenix\Router\Response;
use Phoenix\Core\HttpErrorsManager;

class Dispatch {
    
    public function dispatch(Request $request, Response $response)
    {
        
        if (($route = Mapper::getInstance()->map($request->getUri())) != false) {
            $route->load();
        } else {
            HttpErrorsManager::getInstance()
            ->sendError(
                $response::HTTP_404,
                new \Exception(
                    'No Route Found for requested: ' .
                    $request->getUri()
                )
            );
        }
        
        
        if ($route instanceof Route) {
            $actionName = $route->getAction().'Action';
            $controller = $route->createController();
        
            
            if ((method_exists($controller, $actionName)) && ($controller instanceof \Phoenix\Controller\Action)) {
                    if(method_exists($controller, 'preDispatch')) $controller->preDispatch();
                    $controller->$actionName();
                    if(method_exists($controller, 'postDispatch')) $controller->postDispatch();
            } else {
                if (($controller == false) || (!$controller instanceof \Phoenix\Controller\Action) ):
                    $response->sendStatusCode(503);
                    HttpErrorsManager::getInstance()->sendError(Response::HTTP_503, new \Exception('The controller for request: '.$request->getUri().' was not found'));
                elseif (!method_exists($controller, $actionName)):
                    $response->sendStatusCode(404);
                    HttpErrorsManager::getInstance()->sendError(Response::HTTP_404, new \Exception('The template for request: '.$request->getUri().' was not found'));
                endif;
            }
        }
    }
    
}