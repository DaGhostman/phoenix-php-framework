<?php

namespace Phoenix\Plugin;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;
use Phoenix\Application\Configurator;
use Phoenix\Router\Request;

class Init {
    
    private static $_instance = null;
    protected $pluginStore = array();
    
    public static function getInstance() {
        
        if (!self::$_instance instanceof Init | self::$_instance == null)
            self::$_instance = new Init();
        
        return self::$_instance;
    }
    
    private final function __construct(){
        if (file_exists(REAL_PATH . '/application/config/plugins.json') && is_readable(REAL_PATH . '/application/config/plugins.json')):
        $cfg = new Configurator('/application/config/plugins.json', Configurator::CONFIG_JSON);
        
            if (!empty($cfg->plugins->general)):
                foreach($cfg->plugins->general as $plugin):
                    Manager::getInstance()->emit(Signals::SIGNAL_PLUGIN_INIT, $plugin);
                    $this->init($plugin);
                endforeach;
            endif;
            
            $module = Request::getInstance()
                ->getModule();
            
            
            if (!empty($cfg->plugins->$module)):
                foreach($cfg->plugins->$module as $plugin):
                    Manager::getInstance()->emit(Signals::SIGNAL_PLUGIN_INIT, $plugin);
                    $this->init($plugin);
                endforeach;
            endif;
        endif;
    }
    
    public function init($plugin) {
        $reflect = new \ReflectionClass($plugin);
        if ($reflect->hasMethod('__')):
            Manager::getInstance()->emit(Signals::SIGNAL_PLUGIN_START, $plugin);
            $plugin = new $plugin;
            $plugin->__();
            Manager::getInstance()->emit(Signals::SIGNAL_PLUGIN_X, $plugin);
        else:
            Manager::getInstance()->emit(Signals::SIGNAL_PLUGIN_ERR, $plugin);
        endif;
    }
    
}

?>