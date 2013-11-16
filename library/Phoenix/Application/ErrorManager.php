<?php
namespace Phoenix\Application;

use Phoenix\Router\Request;
use Phoenix\Router\Response;
use Phoenix\Router\Enroute;
use Phoenix\Router\Route;
use Phoenix\Router\Dispatch;
use Phoenix\Router\Mapper;

class ErrorManager {
    private static $_instance = null;
    
    private $errorModule = 'main';
    private $errorController = 'error';
    private $errorAction = 'index';
    
    public static function getInstance()
    {
        
        if (self::$_instance == null | !self::$_instance instanceof ErrorManager)
        {
            self::$_instance = new ErrorManager();
        }
        
        return self::$_instance;
    }
    
    private function __construct(){}
    
    /**
     * 
     * Sets the error module to use for processing the error
     * @param string $module the name of the module. Default: 'main'
     */
    public function setErrorModule($module)
    {
        $this->errorModule = $module;
        return $this;
    }
    
    /**
     * 
     * Sets the error controller to use for processing the error
     * @param string $controller the name of the controller. Default: 'error'
     */
    public function setErrorController($controller)
    {
        $this->errorController = $controller;
        return $this;
    }
    
    /**
     * 
     * Sets the error action to use for processing the error
     * @param string $action the name of the action. Default: 'index'
     */
    public function setErrorAction($action)
    {
        $this->errorAction = $action;
    }
   
    /**
     * 
     * Accepts the error and triggers the appropriate response
     * and the error controller to be used.
     * Sets the $http_code as a parameter 'header' in the Request
     * and the $e is set as 'error' in the parameters in Request 
     * @param int $http_code HTTP reponse code, defaults to 500
     * @param Exception $e the exception of the error
     */
    public function sendError($http_code = 500, $conf = null, $e = null)
    {
        $e = $e ? $e : new \Exception('Error Occured');
        
        $response = Response::getInstance();
        
        $request = Request::getInstance();
        $request->setParams('error', $e);
        $request->setParams('header', $http_code);
        
        Mapper::getInstance()->clearMap();
        Mapper::getInstance()->addMap(Request::getInstance()->getUri(), array(
        	'module' => 'main',
        	'controller' => 'error',
        	'action' => 'index'
        ));
        Response::getInstance()->sendStatusCode($http_code);
        $dispatch = new Dispatch;
        $dispatch->dispatch(
                    $request, 
                    $response,
                    $conf
                );
    }
}

?>