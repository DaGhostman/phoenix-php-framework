<?php

namespace Phoenix\Bootstrap;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;


class Bootstraper
{
    public function __construct() {
        Manager::getInstance()->emit(Signals::SIGNAL_BOOTSTRAP);
    }
    
    public function warmup()
    {
        
        if (version_compare(phpversion(), "5.3.0", "<"))
            trigger_error("Minimal PHP Version required is 5.3.x", E_USER_WARNING);
        
        ini_set('always_populate_raw_post_data', 1);
                ini_set('auto_detect_line_endings', TRUE);
        
        if (!defined('APPLICATION_PATH')) 
            define('APPLICATION_PATH', REAL_PATH . '/application');
        
        if (!in_array('log', stream_get_wrappers())) {
            Manager::getInstance()->emit(Signals::SIGNAL_STREAM_REGISTER, 
                    array("stream" => "log", 
                        "handler" => "Phoenix\Core\Streams\LogStream")
                    );
            stream_register_wrapper("log", 
                    "Phoenix\Core\Streams\LogStream");
            
        }
    }
    
}