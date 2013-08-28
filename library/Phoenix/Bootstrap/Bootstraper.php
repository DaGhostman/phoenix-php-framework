<?php

namespace Phoenix\Bootstrap;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;


class Bootstraper
{
    final public function __construct() {
        Manager::getInstance()->emit(Signals::SIGNAL_BOOTSTRAP);
    }
    
    final public function warmup()
    {
        
        if (version_compare(phpversion(), "5.3.0", "<"))
            trigger_error("Minimal PHP Version required is 5.3.x", E_USER_WARNING);
        
        ini_set('always_populate_raw_post_data', 1);
                ini_set('auto_detect_line_endings', TRUE);
    }
    
}