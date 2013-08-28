<?php

namespace Phoenix\Plugin;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;
use Phoenix\Application\Configurator;
use Phoenix\Router\Request;

class Init {
    
    private static $_instance = null;
    
    public static function getInstance() {
        
        if (!self::$_instance instanceof Init | self::$_instance == null)
            self::$_instance = new Init();
        
        return self::$_instance;
    }
    
    private final function __construct(){
        $cfg = new Configurator();
        
    }
    
}

?>