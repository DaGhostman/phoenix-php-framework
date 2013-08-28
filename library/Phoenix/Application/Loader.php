<?php
namespace Phoenix\Application;

use Phoenix\Controller\Front;
use Phoenix\Application\Core;
use Phoenix\Plugin\Init;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;


class Loader
{

    private $controllerPath = null;
    private $modulePath = null;
    private $viewPath = null;
    
    private $applicationBootstrap = null;
    
    
    /**
     * @param array|Object $options associative array or Forge\Configuration\Broker object
     * @param string $appPath The name of the configuration folder, no directory separators
     * @param array $options Array of configuration options
     * @throws \Phoenix\Application\Exception
     * @return \Phoenix\Application\Loader
     */
    public function __construct($appPath =  'application', $options = array())
    {
        
        Manager::getInstance()->emit(Signals::SIGNAL_INIT);
        Core::getInstance();
        
        if (!in_array('log', stream_get_wrappers())) {
            stream_register_wrapper("log", 
                    "Phoenix\Core\Streams\LogStream");
            
        }
        
        if (!defined('APPLICATION_PATH')) 
            define('APPLICATION_PATH', REAL_PATH . '/application');
        
        $this->applicationPath = $appPath;
        $this->modulePath = $options['modulePath'] ? 
                $options['modulePath'] : 'modules/';
        
        $this->controllerPath = $options['controllerPath'] ? 
                $options['controllerPath'] : 'controllers/';
        
        $this->viewPath = $options['viewPath'] ? 
                $options['viewPath'] : 'views/';
        
        return $this;
    }
    
    public function bootstrap()
    {
        Manager::getInstance()->emit(Signals::SIGNAL_BOOTSTRAP);
        
<<<<<<< HEAD
=======
        set_error_handler(array('Phoenix\Core\Handler','error_handler'));
        set_exception_handler(array('Phoenix\Core\Handler', 'exception_handler'));
        
        
>>>>>>> 23cab747523c4fea45f463711070042265c1d323
        if(is_readable(REAL_PATH . DIRECTORY_SEPARATOR . $this->applicationPath . DIRECTORY_SEPARATOR . 'Bootstrap.php'))
        {
            require_once(REAL_PATH . DIRECTORY_SEPARATOR . $this->applicationPath . DIRECTORY_SEPARATOR . 'Bootstrap.php');
            $this->applicationBootstrap = new \Bootstrap();
                
            foreach(get_class_methods($this->applicationBootstrap) as $method)
            {
                $this->applicationBootstrap->$method();
            }
        }
        
        return $this; 
    }
    
    public function run()
    {
        try {
            Front::getInstance()->run();
            
            Init::getInstance();
            Manager::getInstance()->emit(Signals::SIGNAL_RUN);
        } catch (\Exception $e) {
            throw new \RuntimeException("Exception occured while trying to run the application", null, $e);
        }
    }
}