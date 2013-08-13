<?php
namespace Phoenix\Application;

use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;
use Phoenix\Application\Cache\APC;
use Phoenix\Core\HttpErrorsManager;
use Phoenix\File;

// Remove
use Phoenix\Application\Core;

class Configurator {
    
    
    const CONFIG_JSON = 1;
    const CONFIG_INI = 2;
    protected $filename = '', $cfg = null;
    
    public function __construct($filename = '/application/config/application.ini', $type = self::CONFIG_INI)
    {
        $this->filename = $filename;
        
        if (defined('SYSTEM_CACHE') && SYSTEM_CACHE === 'APC'){
            $key = md5($this->filename);
            $cfg = APC::getInstance()->get($key);
            
            
            
            if ($cfg != false) {
                $this->cfg = unserialize($cfg);
                return;
            }
        }
        
        


        switch($type):
            case self::CONFIG_INI:
                $cfg = new File\Ini(REAL_PATH.$this->filename, true);
                break;
            case self::CONFIG_JSON:
                $cfg = new File\Json(REAL_PATH.$this->filename, true);
                break;
            default:
                HttpErrorsManager::getInstance()->sendError(
                        \Phoenix\Router\Response::HTTP_503, 
                        new \RuntimeException("Unable to get the type of the configuration file")
                        );
                break;
        endswitch;
        
        
        $this->cfg = $cfg;
    }
    
    public function __get($name) {
        return $this->cfg->$name;
    }
    
    public function raw()
    {
        return $this->cfg->raw();
    }

        public function __destruct() {
        if (defined('SYSTEM_CACHE') && SYSTEM_CACHE === 'APC'){
            if (!APC::getInstance()->exists(md5($this->filename))) {
            Manager::getInstance()->emit(Signals::SIGNAL_CACHE_STORE, array(
                'key' => md5($this->filename),
                'value' => serialize($this->cfg)
                ));
            }
        }
    }
    
}

?>
