<?php
namespace Phoenix\Application;

use Phoenix\Controller\Front;
use Phoenix\Storage\Registry;


class Loader
{

    private $controllerPath = null;
    private $modulePath = null;
    private $viewPath = null;
    
    private $applicationBootstrap = null;
    
    
    /**
     * Accepts a configuration object prepares required configuration
     * for further execution. Example: sets APPLICATION_PATH, module path and etc.
     * 
     * @param array|Object $options associative array or Forge\Configuration\Broker object
     * @param string $appPath The name of the configuration folder, no directory separators
     * @param array $options Array of configuration options
     * @throws \Phoenix\Application\Exception
     * @return \Phoenix\Application\Loader
     */
    public function __construct($config)
    {
        $conf = $config->raw();
        
        Registry::set('config', $conf['core'], 'SystemCFG');
        
        if (array_key_exists('application.path', $conf['core'])){
            if (!defined('APPLICATION_PATH')) {
                define ('APPLICATION_PATH', $conf['core']['application.path']);
            }
        } else {
            if (!defined('APPLICATION_PATH')) 
            define('APPLICATION_PATH', REAL_PATH . '/application');
        }
        
        if (array_key_exists('application.module.path', $conf['core'])) {
            $this->modulePath = $conf['core']['application.module.path'];
        } else {
            $this->modulePath = 'modules/';
        }
        
        if (array_key_exists('application.controller.path', $conf['core'])) {
            $this->controllerPath = $conf['core']['application.controller.path'];
        } else {
            $this->controllerPath = 'controllers/';
        }
        
        if (array_key_exists('application.view.path', $conf['core'])) {
            $this->viewPath = $conf['core']['application.view.path'];
        } else {
            $this->viewPath = 'views/';
        }
        
        return $this;
    }
    
    /**
     * This triggeres the general Bootstrap file which should be located 
     * inside your application path. Does not get affected by the setting
     * in the configuration file, about per module bootstrap file.
     * 
     * @return \Phoenix\Application\Loader for chaining
     */
    public function bootstrap()
    {
        
        if(is_readable(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'Bootstrap.php'))
        {
            require_once(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'Bootstrap.php');
            $this->applicationBootstrap = new \Bootstrap();
                
            foreach(get_class_methods($this->applicationBootstrap) as $method)
            {
                $this->applicationBootstrap->$method();
            }
        }
        
        return $this; 
    }
    
    /**
     * Wrapper of the Phoenix\Controller\Front::run();
     * 
     * @see Phoenix\Controller\Front
     * @throws \RuntimeException
     */
    public function run()
    {
        try {
            Front::getInstance()->run();
        } catch (\Exception $e) {
            throw new \RuntimeException("Exception occured while trying to run the application", null, $e);
        }
    }
}