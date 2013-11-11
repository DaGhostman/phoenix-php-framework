<?php

namespace Phoenix\Storage;

use Phoenix\Application\Exception\Runtime;

class Configurator {
    
    const SESSION_LIFE         = 3600;
    const SESSION_PATH         = '/application/data/sessions';
    const SESSION_NAME         = 'PHPSESSID';
    
    static private $instance = null;
    private $options = array();
    
    private function __construct($options) 
    {
        $this->options = $options ? $options : array();
    }
    
    public static function getInstance($options = array())
    {
        if (self::$instance == null)
            self::$instance = new Configurator($options);
        elseif (!self::$instance instanceof Configurator)
            self::$instance = new Configurator($options);
        
        return self::$instance;
    }
    
    private function setSessionPath()
    {
        $path = (array_key_exists('core-application.session.path', $this->options) ? 
                $this->options['core-application.session.path'] : 
                self::SESSION_PATH);
        
        if (is_readable(REAL_PATH . $path) && is_writable(REAL_PATH . $path)):
                        ini_set('session.save_path', REAL_PATH . $path);
        else:
            throw new \RuntimeException("Unable to utilize '/application/data/session' make sure the directory exists and accessible.", null, null);
        endif;
        
    }
    
    private function setSessionLifetime()
    {
        $ttl = (int) (array_key_exists('core-application.session.ttl', $this->options) ? 
                    $this->options['core-application.session.ttl'] : 
                    self::SESSION_LIFE);
                    
        $cp = session_set_cookie_params($ttl);
    }
    
    private function setSessionName()
    {
        $name = (array_key_exists('core-application.session.name',$this->options) ? 
                $this->options['core-application.session.name'] : 
                self::SESSION_NAME);
        session_name($name);
    }
    
    
    public function prepare()
    {
        $this->setSessionPath();
        $this->setSessionLifetime();
        $this->setSessionName();
    }
    
    
}

?>