<?php
namespace Phoenix\Application;

use Phoenix\Controller\Front;


class Loader
{

    private $controllerPath = null;
    private $modulePath = null;
    private $viewPath = null;

    protected $_configuration = array();
    
    
    public function __construct() {}
    
    /**
     * This triggeres the general Bootstrap file which should be located 
     * inside your application path. Does not get affected by the setting
     * in the configuration file, about per module bootstrap file.
     * 
     * @return \Phoenix\Application\Loader for chaining
     */
    private function bootstrap($configuration)
    {
        if (array_key_exists('core-application.path', $configuration)){
            if (!defined('APPLICATION_PATH')) {
                define ('APPLICATION_PATH', $configuration['core-application.path']);
            }
        } else {
            if (!defined('APPLICATION_PATH')) 
            define('APPLICATION_PATH', REAL_PATH . '/application');
        }
        
    if(is_readable(APPLICATION_PATH . DIRECTORY_SEPARATOR . 
            				'Bootstrap.php')) {

            require_once(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'Bootstrap.php');
            $bootstrap = new \Bootstrap();
                
            foreach(get_class_methods($bootstrap) as $method)
            {
                $bootstrap->$method();
            }
        }
        
        
        if (array_key_exists('core-application.module.path', $configuration)) {
            $this->modulePath = $configuration['core-application.module.path'];
        } else {
            $this->modulePath = 'modules/';
        }
        
        if (array_key_exists('core-application.controller.path', $configuration)) {
            $this->controllerPath = $configuration['core-application.controller.path'];
        } else {
            $this->controllerPath = 'controllers/';
        }
        
        if (array_key_exists('core-application.view.path', $configuration)) {
            $this->viewPath = $configuration['core-application.view.path'];
        } else {
            $this->viewPath = 'views/';
        }
        
        return $this; 
    }
    
    /**
     * Wrapper of the Phoenix\Controller\Front::run();
     * 
     * @see Phoenix\Controller\Front
     * @throws \RuntimeException
     */
    public function run($frontController, $configuration)
    {
        try {
            $this->bootstrap($configuration);
            $frontController->run($configuration);
        } catch (\Exception $e) {
            throw new \RuntimeException("Exception occured while trying to run the application", null, $e);
        }
    }
}