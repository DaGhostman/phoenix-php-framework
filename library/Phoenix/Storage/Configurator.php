<?php

namespace Phoenix\Storage;

class Configurator {
    
    const SESSION_LIFE         = 3600;
    const SESSION_PATH         = '/application/data/sessions';
    const SESSION_NAME         = 'PHPSESSID';
    
    static private $instance = null;
    private $options = null;
    
    private function __construct($options){
        
        
        $opt = $options ? $options : array(
                'savePath' => self::SESSION_PATH,
                'cookieName' => self::SESSION_NAME,
                'lifetime' => self::SESSION_LIFE
            );
            
        $this->options = new \stdClass();
        
        foreach($opt as $key => $value)
        {
            $this->options->$key = $value;
        }
        
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
        $sp = true;
        $dir = $this->options->savePath ? $this->options->savePath : self::SESSION_PATH;
        $path = str_replace('/', DIRECTORY_SEPARATOR, $dir);
        
        if (is_readable(REAL_PATH . $path) && is_writable(REAL_PATH . $path)):
                        ini_set('session.save_path', REAL_PATH . $path);
        else:
            if (mkdir(REAL_PATH . $path, '0755', true)):
                        ini_set('session.save_path', REAL_PATH . $path);
            endif;
        endif;
        
    }
    
    private function setSessionLifetime()
    {
        @$ttl = (int) ($this->options->lifetime ? $this->options->lifetime : self::SESSION_LIFE);
        $cp = session_set_cookie_params($ttl);
    }
    
    private function setSessionName()
    {
        @$name = ($this->options->cookieName ? $this->options->cookieName : self::SESSION_NAME);
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