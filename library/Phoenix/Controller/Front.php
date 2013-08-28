<?php
namespace Phoenix\Controller;

use Phoenix\Router\Enroute;
use Phoenix\Router\Response;
use Phoenix\Router\Request;
use Phoenix\Router\Dispatch;
use Phoenix\Core\HttpErrorsManager;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;

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
    
    
    public function run() {
        $request = Request::getInstance($_SERVER['REQUEST_URI'], 
                ($_GET ? $_GET : ($_POST ? $_POST : array()))
                );
        $response = Response::getInstance();
        $dispatch = new Dispatch;
        
        try {
            Manager::getInstance()->emit(Signals::SIGNAL_DISPATCH);
        $dispatch->dispatch( 
                Enroute::getInstance()->route($request, $response), 
                $request, $response
                );
        } catch (\OutOfRangeException $e)
        {
            Manager::getInstance()->emit(Signals::SIGNAL_DISPATCH_ERR, $e);
            HttpErrorsManager::getInstance()->sendError(Response::HTTP_404, $e);
        }
    }

}