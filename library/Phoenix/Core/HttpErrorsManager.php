<?php
namespace Phoenix\Core;

use Phoenix\Router\Request;
use Phoenix\Router\Response;
use Phoenix\Router\MockEnroute;
use Phoenix\Router\Route;
use Phoenix\Router\Dispatch;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;

class HttpErrorsManager {
    private static $_instance = null;
    
    private $errorModule = 'main';
    private $errorController = 'error';
    private $errorAction = 'index';
    
    public static function getInstance()
    {
        
        if (self::$_instance == null | !self::$_instance instanceof HttpErrorsManager)
        {
            self::$_instance = new HttpErrorsManager();
        }
        
        return self::$_instance;
    }
    
    private function __construct(){
        Manager::getInstance()->bind(Signals::SIGNAL_ERROR, $this->sendError());
        Manager::getInstance()->bind(Signals::SIGNAL_EXCEPTION, $this->sendError());
    }
    
    public function setErrorModule($module)
    {
        $this->errorModule = $module;
        return $this;
    }
    
    public function setErrorController($controller)
    {
        $this->errorController = $controller;
        return $this;
    }
    
    public function setErrorAction($action)
    {
        $this->errorAction = $action;
    }
   
    
    public function sendError($http_code = 500, $e = null)
    {
        $e = $e ? $e : new \Exception('Error Occured');
        
        $response = Response::getInstance();
        
        $request = Request::getInstance($_SERVER['REQUEST_URI'], array());
        $request->setParams('error', $e);
        $request->setParams('header', $http_code);
            
        $route = new Route($_SERVER['REQUEST_URI'], array(
            'module' => $this->errorModule,
            'controller' => $this->errorController,
            'action' => $this->errorAction
        ));
                
        MockEnroute::getInstance(array($route))->addRoute($route);
        $dispatch = new Dispatch;
        $dispatch->dispatch(
                MockEnroute::getInstance()->route(
                    $request, 
                    $response
                ),
                    $request, 
                    $response
                );
    }
}

?>