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
<<<<<<< HEAD
        $this->view = Viewer::getInstance($uri['route']);  
        $this->view->sendOutput(true);
=======
        $this->view = Viewer::getInstance($uri['route']);    
        
        spl_autoload_register(array($this, 'autoload'), true);
    }
    
    final public function autoload($className)
    {
        
        $this->_includePath = APPLICATION_PATH;
        $this->_namespace = null;
        $this->_namespaceSeparator = '\\';
        $this->_fileExtension = '.php';
        $fileName = '';
        $namespace = '';
        
        if (null === $this->_namespace || 
                $this->_namespace.$this->_namespaceSeparator === 
                substr($className, 0, strlen($this->_namespace.$this->_namespaceSeparator))) {
            if (false !== ($lastNsPos = strripos($className, 
                    $this->_namespaceSeparator))) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName .= str_replace($this->_namespaceSeparator, 
                        DIRECTORY_SEPARATOR, strtolower($namespace)) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . 
                            $this->_fileExtension;
            
            if (is_readable(($this->_includePath ? 
                    $this->_includePath . DIRECTORY_SEPARATOR :
                    '') . $fileName)) {
                            require_once (($this->_includePath ? 
                                    $this->_includePath . DIRECTORY_SEPARATOR :
                                     '') . $fileName);
            }
            
        }
>>>>>>> 23cab747523c4fea45f463711070042265c1d323
    }
    
        
    final public function __destruct()
    {
        $uri = Request::getInstance()->getParams();
        $this->view = Viewer::getInstance($uri['route']); 
        $this->view->render();
        Manager::getInstance()->emit(Signals::SIGNAL_SHUTDOWN);
    }

}