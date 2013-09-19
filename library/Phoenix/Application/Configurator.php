<?php
namespace Phoenix\Application;

use Phoenix\Core\HttpErrorsManager;
use Phoenix\File;

class Configurator {
    
    
    const CONFIG_JSON = 1;
    const CONFIG_INI = 2;
    protected $filename = '', $cfg = null, $storage = array();
    private static $_instance = null;
    
    private function __construct(){}
    public static function getInstance()
    {
        if (self::$_instance == null)
        {
            self::$_instance = new Configurator;
        }
        
        return self::$_instance;
    }
    
    public function getConfigurator()
    {
        return clone Configurator::getInstance();
    }
    
    public function parse($filename = '/application/config/application.ini', $type = self::CONFIG_INI)
    {
        
        $hash_key = hash('crc32', $filename);
        
        if (array_key_exists($hash_key, $this->storage))
        {
            return $this->storage[$hash_key];
        } else {
        
            switch($type):
                case self::CONFIG_INI:
                    $cfg = new File\Ini(REAL_PATH.$filename, true);
                    break;
                case self::CONFIG_JSON:
                    $cfg = new File\Json(REAL_PATH.$filename, true);
                    break;
                default:
                    HttpErrorsManager::getInstance()->sendError(
                        \Phoenix\Router\Response::HTTP_503, 
                        new \RuntimeException("Unable to get the type of the configuration file")
                        );
                    break;
            endswitch;
            
            $this->storage[$hash_key] = $cfg;
            return $this->storage[$hash_key];
        }
        
    }

    
}

?>
