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
    /**
     * Configuration object, that is used for internal configuration 
     * inside the framework. If you need to use separate config file
     * please use Configurator::getConfigurator();
     * 
     * @see Configurator::getConfigurator
     * @return Configurator The configurator instance.
     */
    public static function getInstance()
    {
        if (self::$_instance == null)
        {
            self::$_instance = new Configurator;
        }
        
        return self::$_instance;
    }
    
    /**
     * Returns a cloned configurator instance. Usefull
     * when there is a need of parsing external config files
     * 
     * @return Configurator
     */
    public static function getConfigurator()
    {
        return clone Configurator::getInstance();
    }
    
    /**
     * Factory method that returns COnfiguration file object.
     * All file objects are stored in array, against a hash of the filename
     * if the same file is required multiple times it will return the 
     * already used one. It will not presist the objects between requests
     * 
     * @param string $filename Relative to REAL_PATH destination of the file to be parsed with leading '/'
     * @param const $type CONFIG_JSON or CONFIG_INI to specify the config file type
     * @return Phoenix\File\Ini|Phoenix\File\Json object for accessing the configuration file
     */
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
