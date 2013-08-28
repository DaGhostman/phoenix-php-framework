<?php
namespace Phoenix\Controller;
use Phoenix\Application\Autoloader;
use Phoenix\Router\Request;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;
use Phoenix\View\Viewer;


class Action
{
    
    
    final public function __construct()
    {
        $uri = Request::getInstance()->getParams();
        $this->view = Viewer::getInstance($uri['route']);  
        $this->view->sendOutput(true);
        
        $autoloader = new Autoloader();
        $route =  Request::getInstance()->getParam('route');
        $autoloader->setIncludePath(
                APPLICATION_PATH . DIRECTORY_SEPARATOR . $route['module'] . 
                DIRECTORY_SEPARATOR . 'models'
                )->register();
    }
    
        
    final public function __destruct()
    {
        $uri = Request::getInstance()->getParams();
        $this->view = Viewer::getInstance($uri['route']); 
        $this->view->render();
        Manager::getInstance()->emit(Signals::SIGNAL_SHUTDOWN);
    }

}