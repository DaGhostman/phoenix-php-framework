<?php

namespace Phoenix\View\Helper;

use Phoenix\Storage\Session;

class FlashMessage{
    
    const FM_ERR = 0;
    const FM_WARN = 1;
    const FM_NOTE = 2;
    const FM_INFO = 3;
    const FM_HELP = 4;
    
    private static $instance = null;
    protected $messages = null;
    
    private function __construct()
    {
        return $this;
    }
    
    public static function getInstance()
    {
        if  (self::$instance === null): 
            self::$instance = new FlashMessage();
        elseif (!self::$instance instanceof FlashMessage):
            self::$instance = new FlashMessage();
        endif;
        
        return self::$instance;
    }
    
    /**
     * Adds the message
     * 
     * @param string|array $message message or array of messages to store
     * @return \Phoenix\View\Helper\FlashMessage
     */
    public function addMessage($message)
    {
                Session::set('FlashMessage', $message);
        
        return $this;
    }
    
    public function getMessage()
    {
        $msg = Session::get('FlashMessage');
        Session::drop('FlashMessage');
        
        return $msg;
    }
    
    
}

?>