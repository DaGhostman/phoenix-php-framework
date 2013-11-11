<?php

namespace Phoenix\Router;

use Phoenix\Router\Mapper;
use Phoenix\Router\Request;
use Phoenix\Router\Route;
use Phoenix\Router\Response;
use Phoenix\Application\ErrorManager;
use Phoenix\View\Viewer;

class Dispatch {
    
    public function dispatch(Request $request, Response $response, $configuration)
    {
        
        if (($route = Mapper::getInstance()->map($request->getUri(), $configuration)) != false) {
            $route->load($request);
        } else {
            ErrorManager::getInstance()
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
            $controller = $route->createController($request, $configuration);
            
            
            
            
            if ((method_exists($controller, $actionName)) && ($controller instanceof \Phoenix\Controller\Action)) {
                    if(method_exists($controller, 'preDispatch')) $controller->preDispatch();
                    $controller->$actionName();
                    if(method_exists($controller, 'postDispatch')) $controller->postDispatch();
            } else {
                if (($controller == false) || (!$controller instanceof \Phoenix\Controller\Action) ):
                    $response->sendStatusCode(503);
                    ErrorManager::getInstance()->sendError(Response::HTTP_503, new \Exception('The controller for request: '.$request->getUri().' was not found'));
                elseif (!method_exists($controller, $actionName)):
                    Viewer::resetInstance();
                    $response->sendStatusCode(404);
                    ErrorManager::getInstance()->sendError(Response::HTTP_404, new \Exception('The template for request: '.$request->getUri().' was not found'));
                endif;
            }
        }
    }
    
}