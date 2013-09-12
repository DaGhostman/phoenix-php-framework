<?php

namespace Phoenix\Storage;

use Phoenix\Storage\Configurator;

class Session
{
    
    
    public function __construct($options = array()){
        
        $cfg = Configurator::getInstance($options);
        $cfg->prepare();
    }
    
    public static function start($options = array())
    {
        new Session($options);
        
        if (!session_id()) { session_start(); }
    }
    
    public static function set($key, $value)
    {
        if(session_id())
        {
            $_SESSION['DEFAULT'][$key] = serialize($value);
            return true;
        } else {
            return false;
        }
    }
    
    public static function get($key)
    {
        if(session_id() && $_SESSION['DEFAULT'][$key])
        {
            return unserialize($_SESSION['DEFAULT'][$key]);
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