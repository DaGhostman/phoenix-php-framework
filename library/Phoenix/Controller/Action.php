<?php
namespace Phoenix\Controller;
use Phoenix\Application\Autoloader;
use Phoenix\Router\Request;
use Phoenix\View\Viewer;


class Action
{
    final public function __construct($request, $config)
    {
        $this->view = new Viewer($request->getRoute(), $config);
        
        $x = Request::getInstance()->getRoute();
        $mainModuleAutoloader = new Autoloader();
        $mainModuleAutoloader->setIncludePath(APPLICATION_PATH . 
            DIRECTORY_SEPARATOR . $config['core-application.module.path'] .
            DIRECTORY_SEPARATOR . $x['module'] . DIRECTORY_SEPARATOR)
        ->register();
    }
    
        
    final public function __destruct()
    {
        unset($this->view);
    }

}