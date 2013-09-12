<?php
namespace Phoenix\Controller;
use Phoenix\Application\Autoloader;
use Phoenix\Router\Request;
use Phoenix\View\Viewer;


class Action
{
    final public function __construct()
    {
        $uri = Request::getInstance()->getParams();
        $this->view = Viewer::getInstance($uri['route']);  
        $this->view->sendOutput(true);
        
        $x = Request::getInstance()->getParam('route');
        $mainModuleAutoloader = new Autoloader();
        $mainModuleAutoloader->setIncludePath(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' .
            DIRECTORY_SEPARATOR . $x['module'] . DIRECTORY_SEPARATOR . 'models')->register();
    }
    
        
    final public function __destruct()
    {
        $uri = Request::getInstance()->getParams();
        $this->view = Viewer::getInstance($uri['route']); 
        $this->view->render();
    }

}