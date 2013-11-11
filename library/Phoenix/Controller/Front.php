<?php
namespace Phoenix\Controller;

use Phoenix\Router\Response;
use Phoenix\Router\Request;
use Phoenix\Router\Dispatch;
use Phoenix\Core\ErrorManager;


class Front
{
    
    protected $module = 'main';
    protected $controller = 'index';
    protected $action = 'index';
    protected $params = array();
    
    protected static $_instance;
    
    public static function getInstance()
    {
        if (!self::$_instance instanceof Front)
        {
            self::$_instance = new Front();
        }
        
        return self::$_instance;
    }
    
    private function __construct(){}
    public function __clone(){
        throw new \BadMethodCallException('The front controller should not be cloned.');
    }
    /**
     * Triggers the Dispatcher and inits the instances
     * of Request and Response.
     */
    public function run($configuration) {
        $request = Request::getInstance( 
                ($_GET ? $_GET : ($_POST ? $_POST : array()))
                );
        $response = Response::getInstance();
        $dispatch = new Dispatch;
        
        try {
            $dispatch->dispatch(
                $request, $response, $configuration
            );
        } catch (\OutOfRangeException $e)
        {
            ErrorManager::getInstance()->sendError(Response::HTTP_404, $e);
        }
    }

}