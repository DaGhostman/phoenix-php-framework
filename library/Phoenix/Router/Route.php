<?php

namespace Phoenix\Router;

use Phoenix\Router\Request;
use Phoenix\Storage\Registry;


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
        
        $conf = Registry::get('config', 'SystemCFG');
        
        $this->defaultAction = 'index';
        $this->defaultController = 'index';
        $this->defaultModule  = $conf['application.default.module'];
        $this->controllerPath = $conf['application.controller.path'];
        
        $this->modulePath = $conf['application.module.path'];
        

        @$this->module = $route['module'] ? 
        $route['module'] : ($this->module ? $this->module : ':module');
        @$this->controller = ($route['controller'] ? 
        $route['controller'] : ':controller');
        @$this->action = $route['action'] ? 
        $route['action'] : ':action';
        
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
        
        $entries=array_merge(array(), array_filter(explode('/', str_replace(rtrim($this->path,'*'),'',Request::getInstance()->getUri()))));
        if ($this->module == ':module') {
            $module = isset($entries[0]) ? $entries[0] : $this->defaultModule;
            unset($entries[0]);
        } else {
            $module = &$this->module;
        }
        
        if ($this->controller == ':controller') {
            $controller = isset($entries[1]) ? $entries[1] : $this->defaultController;
            unset($entries[1]);
        } else {
            $controller = &$this->controller;
        }
        
        if ($this->action == ':action') {
            $action = isset($entries[2]) ? $entries[2] : $this->defaultAction;
            unset($entries[2]);
        } else {
            $action = $this->action;
        }
        
        
        for ($i=0; $i<count(array_values($entries)); $i++):
        $slice = array_values(array_slice(array_values($entries), $i*2, 2));
        if (!empty($slice))
            Request::getInstance()->setParams($slice[0], $slice[1]);
        endfor;
        
        $this->module = $module;
        $this->controller = $controller;
        $this->action = $action;
        
        
        if (is_readable(APPLICATION_PATH . $this->modulePath .
            DIRECTORY_SEPARATOR . $this->module .
            $this->controllerPath . DIRECTORY_SEPARATOR .
            ucfirst($this->controller) . 'Controller.php'))
        {
            $this->controllerClass = ucfirst($this->controller).'Controller';
        
            if (is_readable(APPLICATION_PATH . $this->modulePath . DIRECTORY_SEPARATOR .$this->module.'/Bootstrap.php'))
            {
                require_once APPLICATION_PATH . $this->modulePath . DIRECTORY_SEPARATOR .$this->module.'/Bootstrap.php';
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
            Request::getInstance()->setParams('route', $this->route);
        
        
        $controllerClass = REAL_PATH .'/application/modules/';
        $controllerClass .= $this->module. '/controllers/';
        $controllerClass .= ucfirst($this->controller).'Controller.php';
        
        
        if (is_readable($controllerClass)) require_once $controllerClass;
        }
    }

    public function getPath()
    {
        return $this->path;
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