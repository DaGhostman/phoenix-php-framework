<?php
namespace Phoenix\Core;

use Phoenix\Router\Request;
use Phoenix\Router\Response;
use Phoenix\Router\Enroute;
use Phoenix\Router\Route;
use Phoenix\Router\Dispatch;
use Phoenix\Router\Mapper;

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
    
    private function __construct(){}
    
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
        
        $request = Request::getInstance();
        $request->setParams('error', $e);
        $request->setParams('header', $http_code);

        
        Mapper::getInstance()->addMap(Request::getInstance()->getUri(), array(
        	'module' => 'main',
        	'controller' => 'error',
        	'action' => 'index'
        ));
        $dispatch = new Dispatch;
        $dispatch->dispatch(
                    $request, 
                    $response
                );
    }
}

?>