<?php

namespace Phoenix\Router;

use Phoenix\Router\Request;
use Phoenix\Core\HttpErrorsManager;
class Route
{
    
    protected $path;
    protected $controllerClass;
    
    protected $defaultAction,
        $defaultController,
        $defaultModule;
    
    
    public function __construct($path, $route = array(
            'module' => ':module',
            'controller' => ':controller',
            'action' => ':action'
            ))
    {
        
        
        
        $this->defaultAction = 'index';
        $this->defaultController = 'index';
        $this->defaultModule = 'main';
        $this->controllerPath = 'controllers/';
        
        

        @$this->module = $route['module'] ? 
        $route['module'] : ($this->module ? $this->module : $this->defaultModule);
        @$this->controller = ($route['controller'] ? 
        $route['controller'] : $this->defaultController);
        @$this->action = $route['action'] ? 
        $route['action'] : $this->defaultAction;
        
        $this->path = $path;
        $this->route = array(
                'module' => $this->module,
                'controller' => $this->controller,
                'action' => $this->action
                );
        $this->controllerClass = ucfirst($this->controller).'Controller';
        
    }
    
    public function load()
    {
        $controllerClass = REAL_PATH .'/application/modules/';
        $controllerClass .= $this->module. '/controllers/';
        $controllerClass .= ucfirst($this->controller).'Controller.php';
        
        
        if (is_readable($controllerClass)) require_once $controllerClass;

    }

    public function getPath()
    {
        return $this->path;
    }
    
    public function match(Request $request)
    {

        if (fnmatch($this->path, $request->getUri()))
        {
            if (is_readable(APPLICATION_PATH . '/modules/'.$this->module.'/Bootstrap.php'))
            {
                require_once APPLICATION_PATH . '/modules/'.$this->module.'/Bootstrap.php';
                $this->moduleBootstrap = new \ModuleBootstrap();
            
                foreach(get_class_methods($this->moduleBootstrap) as $method)
                {
                    $this->moduleBootstrap->$method();
                }
            }
        
            return true;
        } else {
            $entries=array_merge(array(), array_filter(explode('/', $_SERVER['REQUEST_URI'])));
            $module = isset($entries[0]) ? $entries[0] : $this->defaultModule;
            $controller = isset($entries[1]) ? $entries[1] : $this->defaultController;
            $action = isset($entries[2]) ? $entries[2] : $this->defaultAction;
            
            if (isset($entries[0])) unset($entries[0]);
            if (isset($entries[1])) unset($entries[1]);
            if (isset($entries[2])) unset($entries[2]);
            
            for ($i=0; $i<count(array_values($entries)); $i++):
                $slice = array_values(array_slice(array_values($entries), $i*2, 2));
                if (!empty($slice))
                    $request->setParams($slice[0], $slice[1]);
            endfor;
            
            $this->module = $module;
            $this->controller = $controller;
            $this->action = $action;
            
            if (is_readable(APPLICATION_PATH . '/modules/' .
                     $this->module . '/controllers/' .
                     ucfirst($this->controller) . 'Controller.php'))
            {
                $this->controllerClass = ucfirst($this->controller).'Controller';
                if (is_readable(APPLICATION_PATH . '/modules/'.$this->module.'/Bootstrap.php'))
                {
                    require_once APPLICATION_PATH . '/modules/'.$this->module.'/Bootstrap.php';
                    $this->moduleBootstrap = new \ModuleBootstrap();
                        
                    foreach(get_class_methods($this->moduleBootstrap) as $method)
                    {
                        $this->moduleBootstrap->$method();
                    }
                }
                
                $this->route = array(
                        'module' => $this->module,
                        'controller' => $this->controller,
                        'action' => $this->action
                );
                
                return true;
            }
        }
    }
    
    public function createController()
    {
            if (class_exists($this->controllerClass))
        return new $this->controllerClass;
            else 
                return false;
    }
    
    public function getModule()
    {
        return $this->module;
    }
    
    public function getController()
    {
        return $this->controller;
    }
    
    public function getAction()
    {
        return $this->action;
    }
}