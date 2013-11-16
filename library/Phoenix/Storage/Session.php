<?php

namespace Phoenix\Storage;

use Phoenix\Storage\Configurator;

class Session
{
    const SESSION_LIFE         = 3600;
    const SESSION_PATH         = '/application/data/sessions';
    const SESSION_NAME         = 'PHPSESSID';
    protected static $sessInit = null;
    
    /**
     * 
     * Configures the session with the supplied options or its defaults:
     * Session lifetime 3600
     * Session save path /application/data/sessions
     * Session Name PHPSESSID
     * @param array $options the options passed to Session::getInstance();
     * @throws \RuntimeException
     */
    private function __construct($options = array()) {
        
        $path = (array_key_exists('core-application.session.path', $options) ? 
                $options['core-application.session.path'] : 
                self::SESSION_PATH);

        $ttl = (int) (array_key_exists('core-application.session.ttl', $options) ? 
                    $options['core-application.session.ttl'] : 
                    self::SESSION_LIFE);
        
        $name = (array_key_exists('core-application.session.name',$options) ? 
                $options['core-application.session.name'] : 
                self::SESSION_NAME);

        if (is_readable(REAL_PATH . $path) && is_writable(REAL_PATH . $path)):
                        ini_set('session.save_path', REAL_PATH . $path);
        else:
            throw new \RuntimeException("Unable to utilize '/application/data/session' make sure the directory exists and accessible.", null, null);
        endif;
        
        session_set_cookie_params($ttl);
        session_name($name);
        
        session_start();
        
        if (!array_key_exists('DEFAULT', $_SESSION)) { 
            $_SESSION['DEFAULT'] = array(); 
        }
    }
    
    /**
     * 
     * Starts and bootstraps the session
     * @param mixed $options array or COnfiguration object
     */
    public static function start($options = array())
    {
    	
    	if (self::$sessInit == null) {
        	new Session($options);
    	}
        
        
    }
    
    /**
     * 
     * Adds am entry in the session
     * @param string $key entry key
     * @param mixed $value entry value
     */
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
    
    /**
     * 
     * Fetches an entry from the session
     * @param string $key key of the entry to fetch
     * @return string|bool The value of the $key or false on failure
     */
    public static function get($key)
    {
        if(session_id() && $_SESSION['DEFAULT'][$key])
        {
            return $_SESSION['DEFAULT'][$key];
        } else {
            return false;
        }
    }
    
    /**
     * 
     * Removes an etry from the session
     * @param string $key the key to remove
     */
    public static function drop($key)
    {
        unset($_SESSION['DEFAULT'][$key]);
    }
    
    /**
     * 
     * Flushes all session variables
     * @return void
     */
    public static function clean()
    {
        session_unset();
    }
    
    /**
     * 
     * Kills the session
     * @return void
     */
    public static function kill()
    {
        session_unset();
        setcookie(session_name(), '', time() - 42000);
        session_destroy();
    }
    
}

?>