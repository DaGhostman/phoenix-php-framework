<?php
namespace Phoenix\Controller;

use Phoenix\Router\Response;
use Phoenix\Router\Request;
use Phoenix\Router\Dispatch;
use Phoenix\Core\HttpErrorsManager;


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
    
    /**
     * Triggers the Dispatcher and inits the instances
     * of Request and Response.
     */
    public function run() {
        $request = Request::getInstance( 
                ($_GET ? $_GET : ($_POST ? $_POST : array()))
                );
        $response = Response::getInstance();
        $dispatch = new Dispatch;
        
        try {
            $dispatch->dispatch(
                $request, $response
            );
        } catch (\OutOfRangeException $e)
        {
            HttpErrorsManager::getInstance()->sendError(Response::HTTP_404, $e);
        }
    }

}