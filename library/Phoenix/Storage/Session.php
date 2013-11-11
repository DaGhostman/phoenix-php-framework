<?php

namespace Phoenix\Storage;

use Phoenix\Storage\Configurator;

class Session
{
    protected static $sessInit = null;
    
    private function __construct($options = array()){
        
    	
        $cfg = Configurator::getInstance($options);
        $cfg->prepare();
        
        session_start();
        
    if (!array_key_exists('DEFAULT', $_SESSION)) { 
            $_SESSION['DEFAULT'] = array(); 
        }
    }
    
    public static function start($options = array())
    {
    	
    	if (self::$sessInit == null) {
        	new Session($options);
    	}
        
        
    }
    
    public static function set($key, $value)
    {
        if(array_key_exists('DEFAULT', $_SESSION))
        {
        	$_SESSION['DEFAULT'][$key] = array();
            $_SESSION['DEFAULT'][$key] = $value;
            return true;
        } else {
            return false;
        }
    }
    
    public static function get($key)
    {
        if(session_id() && $_SESSION['DEFAULT'][$key])
        {
            return $_SESSION['DEFAULT'][$key];
        } else {
            return false;
        }
    }
    
    
    public static function drop($key)
    {
        unset($_SESSION['DEFAULT'][$key]);
    }
    
    
    public static function clean()
    {
        session_unset();
    }
    
    public static function kill()
    {
        session_unset();
        setcookie(session_name(), '', time() - 42000);
        session_destroy();
    }
    
    public function setCookieName($name)
    {
        session_name($name);
    }
    
}

?>